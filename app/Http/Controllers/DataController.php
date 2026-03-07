<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\SubCriteria;
use App\Imports\CacheImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CriteriaAlternative;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Encryption\EncryptException;

class DataController extends Controller
{
    public function index()
    {
        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::withCount('criteria_alternative')->with('criteria_alternative.criteria')->orderBy('id', 'asc')->get();
        return view('admin.data_input', compact(['alternatives', 'criterias']));
    }

    public function post(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'criteria' => 'required|array',
                'criteria.*' => 'required',
            ], [
                'name.required' => 'Nama Alternatif wajib diisi.',
                'criteria.required' => 'Kriteria tidak valid.',
                'criteria.*.required' => 'Data Kriteria wajib dipilih.',
            ]);

            DB::beginTransaction();
            $alternative = Alternative::create([
                'name' => $request->name,
            ]);

            foreach ($request->criteria as $criteria => $subCriteria) {
                CriteriaAlternative::create([
                    'alternative_id' => $alternative->id,
                    'criteria_id' => $criteria,
                    'value' => SubCriteria::where('id', $subCriteria)->value('scale'),
                ]);
            }
            DB::commit();
            flash()->success('Data alternatif berhasil ditambahkan.');
            return redirect()->back();
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $allErrors = collect($errors)->flatten()->implode('<br> • ');
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> • ' . $allErrors);
            return redirect()->back();
        } catch (Throwable $e) {
            DB::rollback();
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function postexcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xls,xlsx|max:15360',
            ], [
                'file.required' => 'File wajib diunggah.',
                'file.mimes' => 'Format file harus Excel (.xls atau .xlsx).',
                'file.max' => 'Ukuran file maksimal 15MB.',
            ]);

            $file = $request->file('file');
            $sheets = Excel::toCollection(new CacheImport, $file);
            $dataCollection = $sheets->first();
            $header = [];

            $criterias = Criteria::all();
            $i = 0;
            DB::beginTransaction();
            foreach ($dataCollection as $index_data => $item) {
                $alternative = Alternative::create([
                    'name' => $item['nama'],
                ]);

                foreach ($criterias as $criteria) {
                    $criteriaName = strtolower(str_replace([' ', '-'], '_', $criteria->name));
                    $item_value = $item[$criteriaName];

                    $subcriterias = SubCriteria::where('criteria_id', $criteria->id)->get();
                    $scale = 0;
                    foreach ($subcriterias as $subcriteria) {
                        if ($subcriteria->upper_value) {
                            if ($item_value > $subcriteria->upper_value) {
                                $scale = $subcriteria->scale;
                            }
                        } else if ($subcriteria->under_value) {
                            if ($item_value < $subcriteria->under_value) {
                                $scale = $subcriteria->scale;
                            }
                        } else if ($subcriteria->initial_value && $subcriteria->final_value) {
                            if ($item_value >= $subcriteria->initial_value && $item_value <= $subcriteria->final_value) {
                                $scale = $subcriteria->scale;
                            }
                        } else {
                            if ($item_value == $subcriteria->sameas_value) {
                                $scale = $subcriteria->scale;
                            }
                        }
                    }

                    $header[$item['nama']][$criteriaName] = $item[$criteriaName] . " | " . $scale;
                    CriteriaAlternative::create([
                        'alternative_id' => $alternative->id,
                        'criteria_id' => $criteria->id,
                        'value' => $scale,
                    ]);
                }
            }

            if (empty($header)) {
                throw new Exception('• Data kriteria tidak ditemukan!');
            }
            DB::commit();
            // dd($header);
            flash()->success('Data alternatif berhasil diunggah.');
            return redirect()->back();
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $allErrors = collect($errors)->flatten()->implode('<br> • ');
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> • ' . $allErrors);
            return redirect()->back();
        } catch (Throwable $e) {
            DB::rollback();
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
            DB::beginTransaction();
            CriteriaAlternative::where('alternative_id', $id)->forceDelete();
            Alternative::where('id', $id)->forceDelete();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus.'
            ], 200);
        } catch (EncryptException $e) {
            return response()->json([
                'message' => 'Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage()
            ], 500);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAll()
    {
        try {
            DB::beginTransaction();
            CriteriaAlternative::whereNull('deleted_at')->forceDelete();
            Alternative::whereNull('deleted_at')->forceDelete();
            DB::commit();
            return response()->json([
                'message' => 'Kerseluruhan data berhasil dihapus.'
            ], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage()
            ], 500);
        }
    }
}
