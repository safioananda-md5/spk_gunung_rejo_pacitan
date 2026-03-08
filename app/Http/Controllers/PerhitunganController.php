<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\CriteriaAlternative;
use App\Models\Penerimaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PerhitunganController extends Controller
{
    public function index($id)
    {

        $penerimaans = Penerimaan::with(['rank'])->where('id', $id)->firstOrFail();
        $alternativemath = json_decode($penerimaans->alternativemath, true);

        // Matriks Keputusan
        $alternatives = Alternative::withTrashed()->whereIn('id', $alternativemath)->get();
        $criterias = Criteria::all();

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
                'name' => Alternative::withTrashed()->where('id', $indexYi)->value('name'),
                'rank' => $Rank,
                'value' => $nilai_yii
            ];
            $Rank++;
        }

        $count = count($penerimaans->rank);
        $tops = array_slice($DataRank, 0, $count, true);

        return view('perhitungan', compact(['penerimaans', 'count', 'alternatives', 'criterias', 'matriks_x', 'matriks_r', 'matriks_rb', 'nilai_y_max', 'nilai_y_min', 'tops']));
    }
}
