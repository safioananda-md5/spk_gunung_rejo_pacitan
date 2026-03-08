@extends('layouts.master')

@push('page')
    Perhitungan
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail Perhitungan Penerimaan Periode {{ $penerimaans->tanggal }}. TOP
                        {{ $count }}</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="mk_tab" data-bs-toggle="tab" data-bs-target="#mk"
                                type="button" role="tab">
                                Matriks Keputusan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nmk_tab" data-bs-toggle="tab" data-bs-target="#nmk"
                                type="button" role="tab">
                                Normalisasi Matriks
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="no_tab" data-bs-toggle="tab" data-bs-target="#no" type="button"
                                role="tab">
                                Nilai Optimasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="yi_tab" data-bs-toggle="tab" data-bs-target="#yi" type="button"
                                role="tab">
                                Nilai Y<em>i</em>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rank_tab" data-bs-toggle="tab" data-bs-target="#rank"
                                type="button" role="tab">
                                Perankingan
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="mk" role="tabpanel">
                            <h5 class="mb-3">Detail Matriks Keputusan</h5>
                            <div>
                                <table class="table-custom">
                                    @foreach ($alternatives as $alternative)
                                        <tr>
                                            @foreach ($criterias as $criteria)
                                                <td>{{ $matriks_x[$criteria->id][$alternative->id] }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nmk" role="tabpanel">
                            <h5 class="mb-3">Detail Normalisasi Matriks</h5>
                            <div>
                                <table class="table-custom">
                                    <thead>
                                        <tr>
                                            <th>Alternatif</th>
                                            @foreach ($criterias as $criteria)
                                                <th>{{ 'C' . $criteria->id }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alternatives as $alternative)
                                            <tr>
                                                <td>{{ 'A' . $alternative->id }}</td>
                                                @foreach ($criterias as $criteria)
                                                    <td>{{ bcadd($matriks_r[$criteria->id][$alternative->id], '0', 6) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="no" role="tabpanel">
                            <h5 class="mb-3">Detail Nilai Optimasi</h5>
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        @foreach ($criterias as $criteria)
                                            <th>{{ 'C' . $criteria->id }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alternatives as $alternative)
                                        <tr>
                                            <td>{{ 'A' . $alternative->id }}</td>
                                            @foreach ($criterias as $criteria)
                                                <td>{{ bcadd($matriks_rb[$criteria->id][$alternative->id], '0', 6) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="yi" role="tabpanel">
                            <h5 class="mb-3">Detail Nilai Y<em>i</em></h5>
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        <th>MAKSIMUM</th>
                                        <th>MINIMUM</th>
                                        <th>Yi (Max-Min)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alternatives as $alternative)
                                        <tr>
                                            <td>{{ 'A' . $alternative->id }}</td>
                                            <td>{{ bcadd($nilai_y_max[$alternative->id], '0', 6) }}</td>
                                            <td>{{ bcadd($nilai_y_min[$alternative->id], '0', 6) }}</td>
                                            <td>{{ bcsub($nilai_y_max[$alternative->id], $nilai_y_min[$alternative->id], 6) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="rank" role="tabpanel">
                            <h5 class="mb-3">Detail Perankingan (Top {{ $count }})</h5>
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        <th>Nama</th>
                                        <th>Yi (Max-Min)</th>
                                        <th>Ranking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tops as $IDDR => $DR)
                                        <tr>
                                            <td>{{ 'A' . $IDDR }}</td>
                                            <td>{{ $DR['name'] }}</td>
                                            <td>{{ $DR['value'] }}</td>
                                            <td>{{ $DR['rank'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Membuat garis luar tabel lebih tegas dan melengkung */
        .table-custom {
            border-collapse: separate !important;
            /* Diperlukan agar border-radius terlihat */
            border-spacing: 0;
            width: 100%;
            border: 2px solid #dee2e6;
            /* Warna border luar */
            border-radius: 8px;
            /* Sudut melengkung */
            overflow: hidden;
            /* Agar konten tidak keluar dari sudut yang melengkung */
        }

        /* Mengatur border pada setiap sel (header dan data) */
        .table-custom th,
        .table-custom td {
            border: 1px solid #dee2e6;
            /* Garis antar sel */
            padding: 12px;
            vertical-align: middle;
            text-align: center;
        }

        /* Menghilangkan double border pada pertemuan sel */
        .table-custom thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #343a40;
            /* Garis bawah header lebih tebal */
        }

        /* Opsional: Efek hover untuk baris */
        .table-custom tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
@endsection

@section('scripts')
@endsection
