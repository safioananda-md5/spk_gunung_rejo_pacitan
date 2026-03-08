<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\PenerimaanAlternative;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            return view(Auth::user()->role . '.dashboard');
        } elseif (Auth::user()->role == 'rt') {
            $alternativeIds = PenerimaanAlternative::select('alternative_id')->distinct()
                ->pluck('alternative_id')->toArray();

            $alternativesAvailable = Alternative::where('rt', Auth::user()->rt)->where('rw', Auth::user()->rw)->whereNotIn('id', $alternativeIds)->count();
            $alternativesProcess = Alternative::withTrashed()->where('rt', Auth::user()->rt)->where('rw', Auth::user()->rw)->whereIn('id', $alternativeIds)->count();
            $alternativeIdsTrashed = PenerimaanAlternative::onlyTrashed()->select('alternative_id')->distinct()
                ->pluck('alternative_id')->toArray();
            $alternativesAcc = Alternative::withTrashed()
                ->with([
                    'pengajuan' => function ($query) {
                        $query->withTrashed()
                            ->whereNotNull('deleted_at')
                            ->latest('deleted_at')
                            ->limit(1);
                    }
                ])
                ->where('rt', Auth::user()->rt)
                ->where('rw', Auth::user()->rw)
                ->whereIn('id', $alternativeIdsTrashed)
                ->latest('deleted_at')
                ->get();
            $alternativesAcc = $alternativesAcc->unique('id');
            return view(Auth::user()->role . '.dashboard', compact(['alternativesAvailable', 'alternativesProcess', 'alternativesAcc']));
        } elseif (Auth::user()->role == 'kades') {
            $criterias = Criteria::with(['sub_criteria'])->get();
            $CountPenerima = PenerimaanAlternative::onlyTrashed()
                ->distinct('alternative_id')
                ->count('alternative_id');
            $ArrPenerima = PenerimaanAlternative::onlyTrashed()
                ->distinct('alternative_id')
                ->pluck('alternative_id')->toArray();

            $CountCalon = Alternative::withTrashed()->whereNotIn('id', $ArrPenerima)->count();
            return view(Auth::user()->role . '.dashboard', compact(['CountPenerima', 'CountCalon', 'criterias']));
        }
    }
}
