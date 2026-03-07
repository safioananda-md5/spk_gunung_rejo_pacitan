<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Seleksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Styling Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            /* Agar border tidak double */
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            /* Memberikan Border */
        }

        th {
            background-color: #4e73df;
            /* Warna Header Biru */
            color: white;
            padding: 10px;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        /* Baris Zebra (Opsional) */
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">SMK MUHAMMADIYAH 1 TAMAN</h2>
        <p style="margin: 0;">Laporan {{ $data->first()->description }}</p>
        <p style="margin: 0;">Data Penerimaan Pada Tanggal: <strong>{{ $tanggal }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">Ranking</th>
                <th>Nama Siswa</th>
                <th width="20%">Nilai Total</th>
                <th width="25%">Tanggal Penerimaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ number_format($item->value, 3) }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->deleted_at)->locale('id')->translatedFormat('d F Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data untuk tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Sidoarjo, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p><strong>Admin SPK</strong></p>
    </div>
</body>

</html>
