<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Criteria;
use App\Models\SubCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CriteriaController extends Controller
{
    public function index()
    {
        $criterias = Criteria::with('sub_criteria')->get();
        $core_criteria = Criteria::where('category', 'core')->get();
        $total_core = "0";
        foreach ($core_criteria as $core) {
            $total_core = bcadd($total_core, $core->weight, 2);
        }
        $secondary_criteria = Criteria::where('category', 'secondary')->get();
        $total_secondary = "0";
        foreach ($secondary_criteria as $secondary) {
            $total_secondary = bcadd($total_secondary, $secondary->weight, 2);
        }
        $total_weight = bcadd($total_core, $total_secondary, 2);
        $alert = '';
        if (bccomp($total_weight, "100", 2) === 1) {
            $alert = '
                <div class="alert alert-danger" role="alert">
                    Nilai bobot keseluruhan melebihi 100%, nilai saat ini adalah <b>' . $total_weight . '%</b>
                </div>
            ';
        }

        return view('admin.criteria', compact(['criterias', 'alert']));
    }

    public function create()
    {
        $edit = false;
        return view('admin.criteria_create', compact(['edit']));
    }

    public function post(Request $request)
    {
        try {
            $request->validate(
                [
                    'criteriaName' => 'required',
                    'criteriaCategory' => 'required',
                    'criteriaWeight' => 'required|numeric|max:100',
                    'skala' => 'required|array',
                    'skala.upperValue' => 'sometimes|required|numeric',
                    'skala.initialValue' => 'sometimes|required|numeric',
                    'skala.finalValue' => 'sometimes|required|numeric',
                    'skala.underValue' => 'sometimes|required|numeric',
                    'skala.sameasValue' => 'sometimes|required',
                ],
                [
                    'criteriaName.required' => 'Nama kriteria wajib diisi!',
                    'criteriaCategory.required' => 'Kategori kriteria wajib diisi!',
                    'criteriaWeight.required' => 'Bobot kriteria wajib diisi!',
                    'criteriaWeight.numeric' => 'Bobot kriteria wajib angka!',
                    'criteriaWeight.max' => 'Bobot kriteria maksimal 100%!',
                    'skala.required' => 'Wajib memilih sub-kriteria!',
                    'skala.array' => 'Wajib memilih sub-kriteria!',
                    'skala.skala.required' => 'Skala wajib diisi!',
                    'skala.skala.numeric' => 'Skala wajib diisi!',
                    'skala.upperValue.required' => 'Nilai diatas dari wajib diisi!',
                    'skala.upperValue.numeric' => 'Nilai diatas dari wajib angka!',
                    'skala.initialValue.required' => 'Nilai awal wajib diisi!',
                    'skala.initialValue.numeric' => 'Nilai awal wajib angka!',
                    'skala.finalValue.required' => 'Nilai akhir wajib diisi!',
                    'skala.finalValue.numeric' => 'Nilai akhir wajib angka!',
                    'skala.underValue.required' => 'Nilai dibawah dari wajib diisi!',
                    'skala.underValue.numeric' => 'Nilai dibawah dari wajib angka!',
                    'skala.sameasValue.required' => 'Nilai sama dengan wajib diisi!',
                ]
            );
            // dd($request->all());

            DB::beginTransaction();
            // Input Kriteria
            $InputCriteria = Criteria::create([
                'name' => $request->criteriaName,
                'category' => $request->criteriaCategory,
                'weight' => $request->criteriaWeight,
            ]);

            // Input Sub-Kriteria
            foreach ($request->skala as $skala => $value) {
                $upper_value = null;
                $under_value = null;
                $initial_value = null;
                $final_value = null;
                $sameas_value = null;
                if (isset($value['upperValue'])) {
                    $upper_value = $value['upperValue'];
                    $under_value = null;
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = null;
                } else if (isset($value['underValue'])) {
                    $upper_value = null;
                    $under_value = $value['underValue'];
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = null;
                } else if (isset($value['initialValue']) && isset($value['finalValue'])) {
                    $upper_value = null;
                    $under_value = null;
                    $initial_value = $value['initialValue'];
                    $final_value = $value['finalValue'];
                    $sameas_value = null;
                } else {
                    $upper_value = null;
                    $under_value = null;
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = $value['sameasValue'];
                }

                if ($skala != $value['skala']) {
                    $scale = $value['skala'];
                } else {
                    $scale = $skala;
                }
                SubCriteria::create([
                    'criteria_id' => $InputCriteria->id,
                    'scale' => $scale,
                    'upper_value' => $upper_value,
                    'under_value' => $under_value,
                    'initial_value' => $initial_value,
                    'final_value' => $final_value,
                    'sameas_value' => $sameas_value
                ]);
            }
            DB::commit();
            flash()->success('Data kriteria berhasil ditambahkan.');
            return redirect(route(Auth::user()->role . '.criteria'));
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

    public function edit($id)
    {
        $edit = true;
        // dd($id);
        $criteria = Criteria::where('id', $id)->firstOrFail();
        $subcriterias = SubCriteria::where('criteria_id', $id)->orderBy('scale', 'asc')->get();
        return view('admin.criteria_create', compact(['edit', 'criteria', 'subcriterias']));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'criteriaName' => 'required',
                    'criteriaCategory' => 'required',
                    'criteriaWeight' => 'required|numeric|max:100',
                    'skala' => 'required|array',
                    'skala.upperValue' => 'sometimes|required|numeric',
                    'skala.initialValue' => 'sometimes|required|numeric',
                    'skala.finalValue' => 'sometimes|required|numeric',
                    'skala.underValue' => 'sometimes|required|numeric',
                    'skala.sameasValue' => 'sometimes|required',
                ],
                [
                    'criteriaName.required' => 'Nama kriteria wajib diisi!',
                    'criteriaCategory.required' => 'Kategori kriteria wajib diisi!',
                    'criteriaWeight.required' => 'Bobot kriteria wajib diisi!',
                    'criteriaWeight.numeric' => 'Bobot kriteria wajib angka!',
                    'criteriaWeight.max' => 'Bobot kriteria maksimal 100%!',
                    'skala.required' => 'Wajib memilih sub-kriteria!',
                    'skala.array' => 'Wajib memilih sub-kriteria!',
                    'skala.upperValue.required' => 'Nilai diatas dari wajib diisi!',
                    'skala.upperValue.numeric' => 'Nilai diatas dari wajib angka!',
                    'skala.initialValue.required' => 'Nilai awal wajib diisi!',
                    'skala.initialValue.numeric' => 'Nilai awal wajib angka!',
                    'skala.finalValue.required' => 'Nilai akhir wajib diisi!',
                    'skala.finalValue.numeric' => 'Nilai akhir wajib angka!',
                    'skala.underValue.required' => 'Nilai dibawah dari wajib diisi!',
                    'skala.underValue.numeric' => 'Nilai dibawah dari wajib angka!',
                    'skala.sameasValue.required' => 'Nilai sama dengan wajib diisi!',
                ]
            );
            // dd($request->all());

            DB::beginTransaction();
            // Input Kriteria
            $criteria = Criteria::where('id', $id)->firstOrFail();
            $criteria->update([
                'name' => $request->criteriaName,
                'category' => $request->criteriaCategory,
                'weight' => $request->criteriaWeight,
            ]);

            // Input Sub-Kriteria
            $processedScales = [];
            foreach ($request->skala as $value) {
                $processedScales[] = $value['skala'];
                $upper_value = null;
                $under_value = null;
                $initial_value = null;
                $final_value = null;
                $sameas_value = null;
                if (isset($value['upperValue'])) {
                    $upper_value = $value['upperValue'];
                    $under_value = null;
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = null;
                } else if (isset($value['underValue'])) {
                    $upper_value = null;
                    $under_value = $value['underValue'];
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = null;
                } else if (isset($value['initialValue']) && isset($value['finalValue'])) {
                    $upper_value = null;
                    $under_value = null;
                    $initial_value = $value['initialValue'];
                    $final_value = $value['finalValue'];
                    $sameas_value = null;
                } else {
                    $upper_value = null;
                    $under_value = null;
                    $initial_value = null;
                    $final_value = null;
                    $sameas_value = $value['sameasValue'];
                }

                SubCriteria::updateOrCreate(
                    [
                        'criteria_id' => $id,
                        'scale'       => $value['skala'],
                    ],
                    [
                        'upper_value' => $upper_value,
                        'under_value' => $under_value,
                        'initial_value' => $initial_value,
                        'final_value' => $final_value,
                        'sameas_value' => $sameas_value
                    ]
                );
            }

            SubCriteria::where('criteria_id', $id)
                ->whereNotIn('scale', $processedScales)
                ->delete();

            DB::commit();
            flash()->success('Data kriteria berhasil diperbarui.');
            return redirect(route(Auth::user()->role . '.criteria'));
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

    public function delete(Request $request, $id)
    {
        try {
            Criteria::where('id', $id)->delete();
            SubCriteria::where('criteria_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data kriteria berhasil dihapus!',
                'data'    => $criteria ?? null
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
