<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\CriteriaAlternative;
use App\Models\SubCriteria;
use App\Models\WeightValue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RankingController extends Controller
{
    public function index()
    {
        $CountAlternative = Alternative::count();
        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        return redirect(route(Auth::user()->role . '.rank'));
    }

    public function rank()
    {
        $CountAlternative = Alternative::count();

        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::with([
            'criteria_alternative',
            'criteria_alternative.criteria.sub_criteria' => function ($query) {
                $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
            }
        ])->get();

        $weight_array = WeightValue::pluck('weight', 'gap')->toArray();

        $NCF = Criteria::where('category', 'core')->get();
        $weightTotalNCF = 0;
        foreach ($NCF as $CF) {
            $weightTotalNCF = bcadd($weightTotalNCF, $CF->weight, 2);
        }

        $NSF = Criteria::where('category', 'secondary')->get();
        $weightTotalNSF = 0;
        foreach ($NSF as $SF) {
            $weightTotalNSF = bcadd($weightTotalNSF, $SF->weight, 2);
        }

        foreach ($alternatives as $alternative) {
            $cf = 0;
            $totalcf = 0;
            $cfWeight = [];
            foreach ($alternative->criteria_alternative as $criteria_alternative) {
                if ($criteria_alternative->criteria->category == 'core') {
                    $cf++;
                    $bobotcf =
                        $weight_array[bcsub(
                            $criteria_alternative->value,
                            $criteria_alternative->criteria->sub_criteria->first()->scale,
                        )];
                    $totalcf = bcadd($totalcf, $bobotcf, 3);
                    $cfWeight[] =
                        $weight_array[bcsub(
                            $criteria_alternative->value,
                            $criteria_alternative->criteria->sub_criteria->first()->scale,
                        )];
                }
            }

            $sf = 0;
            $totalsf = 0;
            $sfWeight = [];
            foreach ($alternative->criteria_alternative as $criteria_alternative) {
                if ($criteria_alternative->criteria->category == 'secondary') {
                    $sf++;
                    $bobotsf =
                        $weight_array[bcsub(
                            $criteria_alternative->value,
                            $criteria_alternative->criteria->sub_criteria->first()->scale,
                        )];
                    $totalsf = bcadd($totalsf, $bobotsf, 3);
                    $sfWeight[] =
                        $weight_array[bcsub(
                            $criteria_alternative->value,
                            $criteria_alternative->criteria->sub_criteria->first()->scale,
                        )];
                }
            }

            $weighcf = bcdiv($weightTotalNCF, 100, 3);
            $totalcf = bcdiv($totalcf, $cf, 3);
            $finalcf = bcmul($weighcf, $totalcf, 3);
            $weighsf = bcdiv($weightTotalNSF, 100, 3);
            $totalsf = bcdiv($totalsf, $sf, 3);
            $finalsf = bcmul($weighsf, $totalsf, 3);
            $totalvalue = bcadd($finalcf, $finalsf, 3);

            $arrayRank[] = [
                'id' => $alternative->id,
                'name' => $alternative->name,
                'value' => $totalvalue,
            ];
        }

        usort($arrayRank, function ($a, $b) {
            $valCompare = $b['value'] <=> $a['value'];
            if ($valCompare === 0) {
                return $a['name'] <=> $b['name'];
            }

            return $valCompare;
        });

        foreach ($arrayRank as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        // dd($arrayRank);
        return view('admin.ranking', compact(['alternatives', 'weight_array', 'weightTotalNCF', 'weightTotalNSF', 'arrayRank']));
    }

    public function post(Request $request)
    {
        try {
            $CountAlternative = Alternative::count();

            if ($CountAlternative == 0) {
                flash()->error('Data alternatif tidak tersedia!');
                return redirect(route(Auth::user()->role . '.dashboard'));
            }

            $countCriteria = Criteria::count();
            $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
            if ($CountProfileIdeal != $countCriteria) {
                flash()->error('Data profil ideal tidak tersedia!');
                return redirect(route(Auth::user()->role . '.dashboard'));
            }

            $criterias = Criteria::with('sub_criteria')->get();
            if ($criterias->count() == 0) {
                flash()->error('Data kriteria tidak tersedia!');
                return redirect(route(Auth::user()->role . '.dashboard'));
            }

            $request->validate(
                [
                    'count' => 'required|numeric',
                    'description' => 'required'
                ],
                [
                    'count.required' => "Jumlah ranking penerima wajib diisi!",
                    'count.numeric' => "Jumlah ranking penerima wajib angka!",
                    'description' => 'Keterangan penerimaan wajib diisi!'
                ]
            );
            $alternatives = Alternative::with([
                'criteria_alternative',
                'criteria_alternative.criteria.sub_criteria' => function ($query) {
                    $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
                }
            ])->get();

            $weight_array = WeightValue::pluck('weight', 'gap')->toArray();

            $NCF = Criteria::where('category', 'core')->get();
            $weightTotalNCF = 0;
            foreach ($NCF as $CF) {
                $weightTotalNCF = bcadd($weightTotalNCF, $CF->weight, 2);
            }

            $NSF = Criteria::where('category', 'secondary')->get();
            $weightTotalNSF = 0;
            foreach ($NSF as $SF) {
                $weightTotalNSF = bcadd($weightTotalNSF, $SF->weight, 2);
            }

            foreach ($alternatives as $alternative) {
                $cf = 0;
                $totalcf = 0;
                $cfWeight = [];
                foreach ($alternative->criteria_alternative as $criteria_alternative) {
                    if ($criteria_alternative->criteria->category == 'core') {
                        $cf++;
                        $bobotcf =
                            $weight_array[bcsub(
                                $criteria_alternative->value,
                                $criteria_alternative->criteria->sub_criteria->first()->scale,
                            )];
                        $totalcf = bcadd($totalcf, $bobotcf, 3);
                        $cfWeight[] =
                            $weight_array[bcsub(
                                $criteria_alternative->value,
                                $criteria_alternative->criteria->sub_criteria->first()->scale,
                            )];
                    }
                }

                $sf = 0;
                $totalsf = 0;
                $sfWeight = [];
                foreach ($alternative->criteria_alternative as $criteria_alternative) {
                    if ($criteria_alternative->criteria->category == 'secondary') {
                        $sf++;
                        $bobotsf =
                            $weight_array[bcsub(
                                $criteria_alternative->value,
                                $criteria_alternative->criteria->sub_criteria->first()->scale,
                            )];
                        $totalsf = bcadd($totalsf, $bobotsf, 3);
                        $sfWeight[] =
                            $weight_array[bcsub(
                                $criteria_alternative->value,
                                $criteria_alternative->criteria->sub_criteria->first()->scale,
                            )];
                    }
                }

                $weighcf = bcdiv($weightTotalNCF, 100, 3);
                $totalcf = bcdiv($totalcf, $cf, 3);
                $finalcf = bcmul($weighcf, $totalcf, 3);
                $weighsf = bcdiv($weightTotalNSF, 100, 3);
                $totalsf = bcdiv($totalsf, $sf, 3);
                $finalsf = bcmul($weighsf, $totalsf, 3);
                $totalvalue = bcadd($finalcf, $finalsf, 3);

                $arrayRank[] = [
                    'id' => $alternative->id,
                    'name' => $alternative->name,
                    'value' => $totalvalue,
                ];
            }

            usort($arrayRank, function ($a, $b) {
                $valCompare = $b['value'] <=> $a['value'];

                if ($valCompare === 0) {
                    return $a['name'] <=> $b['name'];
                }

                return $valCompare;
            });

            foreach ($arrayRank as $index => &$item) {
                $item['rank'] = $index + 1;
            }

            if ($request->count > count($arrayRank)) {
                throw new Exception('Jumlah ranking tidak boleh melebihi jumlah keseluruhan data pada sistem!');
            }

            $filteredRank = collect($arrayRank)
                ->where('rank', '<=', $request->count)
                ->all();
            $ids = collect($filteredRank)->pluck('id');

            DB::beginTransaction();
            foreach ($filteredRank as $Rank) {
                Alternative::where('id', $Rank['id'])->update([
                    'value' => $Rank['value'],
                    'description' => $request->description,
                ]);
            }
            foreach ($ids as $IID) {
                $alternativeEE = Alternative::where('id', $IID)->first();
                $alternativeCRT = Alternative::create([
                    'name' => $alternativeEE->name,
                ]);
                $criteriaAA = CriteriaAlternative::where('alternative_id', $IID)->get();
                foreach ($criteriaAA as $CAA) {
                    CriteriaAlternative::create([
                        'alternative_id' => $alternativeCRT->id,
                        'criteria_id' => $CAA->criteria_id,
                        'value' => $CAA->value,
                    ]);
                }
            }
            Alternative::whereIn('id', $ids)->delete();
            CriteriaAlternative::whereIn('alternative_id', $ids)->delete();
            DB::commit();
            flash()->success('Penerima bantuan berhasil dipilih!');
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

    public function gap()
    {
        $CountAlternative = Alternative::count();

        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::with([
            'criteria_alternative',
            'criteria_alternative.criteria.sub_criteria' => function ($query) {
                $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
            }
        ])->paginate(5);

        return view('admin.gap', compact(['alternatives']));
    }

    public function weight()
    {
        $CountAlternative = Alternative::count();

        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::with([
            'criteria_alternative',
            'criteria_alternative.criteria.sub_criteria' => function ($query) {
                $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
            }
        ])->paginate(5);

        $weight_array = WeightValue::pluck('weight', 'gap')->toArray();

        return view('admin.weight', compact(['alternatives', 'weight_array']));
    }

    public function CSF()
    {
        $CountAlternative = Alternative::count();

        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::with([
            'criteria_alternative',
            'criteria_alternative.criteria.sub_criteria' => function ($query) {
                $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
            }
        ])->get();

        $weight_array = WeightValue::pluck('weight', 'gap')->toArray();

        return view('admin.csf', compact(['alternatives', 'weight_array']));
    }

    public function total()
    {
        $CountAlternative = Alternative::count();

        if ($CountAlternative == 0) {
            flash()->error('Data alternatif tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $countCriteria = Criteria::count();
        $CountProfileIdeal = SubCriteria::where('profile_ideal', true)->count();
        if ($CountProfileIdeal != $countCriteria) {
            flash()->error('Data profil ideal tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $criterias = Criteria::with('sub_criteria')->get();
        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }

        $alternatives = Alternative::with([
            'criteria_alternative',
            'criteria_alternative.criteria.sub_criteria' => function ($query) {
                $query->select('id', 'criteria_id', 'scale')->where('profile_ideal', true);
            }
        ])->get();

        $weight_array = WeightValue::pluck('weight', 'gap')->toArray();

        $NCF = Criteria::where('category', 'core')->get();
        $weightTotalNCF = 0;
        foreach ($NCF as $CF) {
            $weightTotalNCF = bcadd($weightTotalNCF, $CF->weight, 2);
        }

        $NSF = Criteria::where('category', 'secondary')->get();
        $weightTotalNSF = 0;
        foreach ($NSF as $SF) {
            $weightTotalNSF = bcadd($weightTotalNSF, $SF->weight, 2);
        }

        return view('admin.total', compact(['alternatives', 'weight_array', 'weightTotalNCF', 'weightTotalNSF']));
    }
}
