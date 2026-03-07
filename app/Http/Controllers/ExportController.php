<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Alternative;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AlternativeExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function excel($date)
    {
        return Excel::download(new AlternativeExport($date), 'laporan-penerimaan-bantuan-' . $date . '.xlsx');
    }

    public function pdf($date)
    {
        // dd($date);
        // 1. Konversi slug "28-desember-2025" ke format DB
        $dateString = str_replace('-', ' ', strtolower($date));
        $monthMap = [
            'januari' => 'january',
            'februari' => 'february',
            'maret' => 'march',
            'april' => 'april',
            'mei' => 'may',
            'juni' => 'june',
            'juli' => 'july',
            'agustus' => 'august',
            'september' => 'september',
            'oktober' => 'october',
            'november' => 'november',
            'desember' => 'december'
        ];
        $dateEn = strtr($dateString, $monthMap);
        $dbDate = Carbon::parse($dateEn)->format('Y-m-d H:i:s');

        // 2. Ambil data
        $data = Alternative::onlyTrashed()
            ->where('deleted_at', $dbDate)
            ->orderBy('value', 'desc')
            ->get();

        // 3. Load view PDF dan kirim data serta variabel tanggalnya
        $pdf = Pdf::loadView('pdf.alternative_report', [
            'data' => $data,
            'tanggal' => Carbon::parse($dateEn)->locale('id')->translatedFormat('d F Y')
        ]);

        // 4. Download atau Stream
        return $pdf->stream('Laporan-Siswa-' . $date . '.pdf');
    }
}
