<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $labels = [];
            $counts = [];

            // Loop untuk 5 tahun terakhir (termasuk tahun ini)
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $labels[] = $year;

                // Hitung jumlah data yang soft delete pada tahun tersebut
                $count = Alternative::onlyTrashed()
                    ->whereYear('deleted_at', $year)
                    ->count();

                $counts[] = $count;
            }

            $data1 = Alternative::onlyTrashed()->where('value', '>', 3)->count();

            // Data 2: Value antara 2 sampai 3 (termasuk 2 dan 3)
            $data2 = Alternative::onlyTrashed()->whereBetween('value', [2, 3])->count();

            // Data 3: Value < 2
            $data3 = Alternative::onlyTrashed()->where('value', '<', 2)->count();

            // Satukan ke dalam satu array
            $valueDistribution = [$data1, $data2, $data3];

            return view('admin.dashboard', compact('labels', 'counts', 'valueDistribution'));
        } else {
            $uniqueDates = Alternative::onlyTrashed()
                ->get()
                ->groupBy(function ($item) {
                    return $item->deleted_at->translatedFormat('d F Y,H:i:s') . '|' . $item->description;
                });

            return view('user.dashboard', compact(['uniqueDates']));
        }
    }
}
