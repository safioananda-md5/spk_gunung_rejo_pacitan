<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Alternative;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlternativeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $date;
    private $rowNumber = 0;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        $dateString = str_replace('-', ' ', strtolower($this->date));

        $monthMap = [
            'januari'   => 'january',
            'februari'  => 'february',
            'maret'     => 'march',
            'april'     => 'april',
            'mei'       => 'may',
            'juni'      => 'june',
            'juli'      => 'july',
            'agustus'   => 'august',
            'september' => 'september',
            'oktober'   => 'october',
            'november'  => 'november',
            'desember'  => 'december',
        ];

        $dateEn = strtr($dateString, $monthMap);

        try {
            $dbDate = \Carbon\Carbon::parse($dateEn)->format('Y-m-d H:i:s');

            return Alternative::onlyTrashed()
                ->where('deleted_at', $dbDate)
                ->orderBy('value', 'desc')
                ->get(['name', 'value', 'deleted_at']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $lastColumn . $lastRow;

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4E73DF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            $range => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function map($alternative): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $alternative->name,
            $alternative->value,
            Carbon::parse($alternative->deleted_at)->translatedFormat('d F Y'),
        ];
    }

    public function headings(): array
    {
        return ["Ranking", "Nama Siswa", "Nilai Total", "Tanggal Penerimaan"];
    }
}
