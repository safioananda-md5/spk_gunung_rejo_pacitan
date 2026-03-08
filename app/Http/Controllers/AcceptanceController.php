<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Alternative;
use App\Models\Criteria;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CriteriaAlternative;
use App\Models\Penerimaan;
use App\Models\PenerimaanAlternative;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AcceptanceController extends Controller
{
    public function index()
    {
        $penerimaans = Penerimaan::with([
            'rank' => function ($query) {
                $query->withTrashed()->with(['alternative' => function ($qAlt) {
                    $qAlt->withTrashed();
                }]);
            }
        ])->get();

        return view('admin.penerimaan', compact(['penerimaans']));
    }

    public function post(Request $request)
    {
        try {
            $request->validate(
                [
                    'count' => 'required',
                ],
                [
                    'count.required' => 'Jumlah Calon Penerima wajib diisi!',
                ]
            );

            // Matriks Keputusan
            $alternatives = Alternative::all();
            $criterias = Criteria::all();

            $alternativemath = [];
            foreach ($alternatives as $alternative) {
                $alternativemath[] = $alternative->id;
            }

            $matriks_x = [];
            foreach ($alternatives as $alternative) {
                $alternativesCriterias = CriteriaAlternative::where('alternative_id', $alternative->id)->get();
                foreach ($alternativesCriterias as $alternativesCriteria) {
                    $matriks_x[$alternativesCriteria->criteria_id][$alternative->id] = $alternativesCriteria->value;
                }
            }

            //Matriks Ternormalisasi (R)
            $matriks_r = [];
            foreach ($matriks_x as $id_kriteria => $penilaians) {
                $jumlah_kuadrat = 0;
                foreach ($penilaians as $penilaian) {
                    $jumlah_kuadrat += pow($penilaian, 2);
                }
                $akar_kuadrat = sqrt($jumlah_kuadrat);
                foreach ($penilaians as $id_alternatif => $penilaian) {
                    $matriks_r[$id_kriteria][$id_alternatif] = $penilaian / $akar_kuadrat;
                }
            }

            //Matriks Normalisasi Terbobot
            $matriks_rb = [];
            foreach ($alternatives as $alternative) {
                foreach ($criterias as $criteria) {
                    $bobot = (float) $criteria->weight / 100;
                    $id_alternatif = $alternative->id;
                    $id_kriteria = $criteria->id;
                    $nilai_r = $matriks_r[$id_kriteria][$id_alternatif];
                    $matriks_rb[$id_kriteria][$id_alternatif] = $bobot * $nilai_r;
                }
            }

            //Nilai Yi
            $nilai_y_max = [];
            $nilai_y_min = [];
            foreach ($alternatives as $alternative) {
                $total_max = 0;
                $total_min = 0;
                foreach ($criterias as $criteria) {
                    $id_alternatif = $alternative->id;
                    $id_kriteria = $criteria->id;
                    $type_kriteria = $criteria->category;
                    $nilai_rb = $matriks_rb[$id_kriteria][$id_alternatif];
                    if ($type_kriteria == 'benefit') {
                        $total_max += $nilai_rb;
                    } elseif ($type_kriteria == 'cost') {
                        $total_min += $nilai_rb;
                    }
                }
                $nilai_y_max[$id_alternatif] = $total_max;
                $nilai_y_min[$id_alternatif] = $total_min;
            }

            $nilai_yi = [];
            foreach ($alternatives as $alternative) {
                $nilai_yi[$alternative->id] = bcsub($nilai_y_max[$alternative->id], $nilai_y_min[$alternative->id], 6);
            }

            arsort($nilai_yi);
            $DataRank = [];
            $Rank = 1;
            foreach ($nilai_yi as $indexYi => $nilai_yii) {
                $DataRank[$indexYi] = [
                    'rank' => $Rank,
                    'value' => $nilai_yii
                ];
                $Rank++;
            }

            if ($request->count > count($alternatives)) {
                throw new Exception('Jumlah penerimaan tidak boleh lebih besar dari jumlah data tersedia');
            }

            $tops = array_slice($DataRank, 0, $request->count, true);

            Carbon::setLocale('id');
            $tanggal = Carbon::now()->translatedFormat('d F Y, H:i:s');

            DB::beginTransaction();
            $penerimaan = Penerimaan::create([
                'tanggal' => $tanggal,
                'status' => 'Draft',
                'alternativemath' => json_encode($alternativemath),
            ]);

            foreach ($tops as $indextop => $top) {
                PenerimaanAlternative::create([
                    'penerimaan_id' => $penerimaan->id,
                    'alternative_id' => $indextop,
                    'rank' => $top['rank'],
                    'value' => $top['value'],
                ]);
                Alternative::where('id', $indextop)->delete();
            }
            DB::commit();
            flash()->success('Data penerimaan berhasil dibuat.');
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
            $request->validate(
                [
                    'id' => 'required',
                ],
                [
                    'id.required' => 'Data Penerimaan tidak ditemukan!',
                ]
            );

            DB::beginTransaction();
            $penerimaan = Penerimaan::with(['rank'])->where('id', $request->id)->firstOrFail();

            foreach ($penerimaan->rank as $rank) {
                Alternative::withTrashed()->find($rank->alternative_id)->restore();
            }

            PenerimaanAlternative::where('penerimaan_id', $penerimaan->id)->delete();
            $penerimaan->delete();
            DB::commit();
            flash()->success('Data penerimaan berhasil dihapus.');
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

    public function acc(Request $request)
    {
        try {
            $request->validate(
                [
                    'id' => 'required',
                ],
                [
                    'id.required' => 'Data Penerimaan tidak ditemukan!',
                ]
            );

            DB::beginTransaction();
            $penerimaan = Penerimaan::with(['rank'])->where('id', $request->id)->firstOrFail();
            $penerimaan->rank()->delete();
            $penerimaan->update([
                'status' => 'Setuju'
            ]);
            DB::commit();
            flash()->success('Data penerimaan berhasil disetujui.');
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
}
