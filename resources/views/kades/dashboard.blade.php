@extends('layouts.master')

@push('page')
    Dashboard
@endpush

@section('content')
    <div class="row gy-5">
        <div class="col-lg-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>Jumlah Calon Penerima</h5>
                        <i class="mdi mdi-account-star h2"></i>
                    </div>
                    <h2 id="calon">0</h2>
                    <p>Kategori calon penerima bantuan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>Jumlah Penerima</h5>
                        <i class="mdi mdi-account-search h2"></i>
                    </div>
                    <h2 id="penerima">0</h2>
                    <p>Kategori penerima bantuan</p>
                </div>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">Data Keriteria & Bobot</div>
                <div class="card-body">
                    <table class="table" id="penerimaanTable">
                        <thead>
                            <tr>
                                <th>Nama Kriteria</th>
                                <th>Sub-Kriteria</th>
                                <th>Bobot Kriteria</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($criterias as $criteria)
                                <tr>
                                    <td>{{ $criteria->name }}</td>
                                    <td>
                                        <table class="table">
                                            @foreach ($criteria->sub_criteria as $sub_criteria)
                                                @if (isset($sub_criteria->upper_value))
                                                    <tr class="py-0">
                                                        <td class="p-1">> {{ $sub_criteria->upper_value }}</td>
                                                    </tr>
                                                @elseif(isset($sub_criteria->under_value))
                                                    <tr class="py-0">
                                                        <td class="p-1">
                                                            < {{ $sub_criteria->under_value }}</td>
                                                    </tr>
                                                @elseif(isset($sub_criteria->initial_value) && isset($sub_criteria->final_value))
                                                    <tr class="py-0">
                                                        <td class="p-1">
                                                            > {{ $sub_criteria->initial_value }} & &le;
                                                            {{ $sub_criteria->initial_value }}</td>
                                                    </tr>
                                                @elseif(isset($sub_criteria->sameas_value))
                                                    <tr class="py-0">
                                                        <td class="p-1">
                                                            {{ $sub_criteria->sameas_value }}</td>
                                                    </tr>
                                                @else
                                                    <tr class="py-0">
                                                        <td class="p-1">-</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </td>
                                    <td>{{ $criteria->weight }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/countup/countup.umd.js') }}"></script>
    <script>
        const nilaiCalon = @json($CountCalon);
        const nilaiPenerima = @json($CountPenerima);

        // Konfigurasi CountUp
        // Parameter: (ID Elemen, Nilai Akhir, Opsi)
        const options = {
            duration: 2,
            separator: '.',
        };

        const demo1 = new countUp.CountUp('calon', nilaiCalon, options);
        if (!demo1.error) {
            demo1.start();
        } else {
            console.error(demo1.error);
        }
        const demo2 = new countUp.CountUp('penerima', nilaiPenerima, options);
        if (!demo2.error) {
            demo2.start();
        } else {
            console.error(demo2.error);
        }

        $(document).ready(function() {
            $('#penerimaanTable').DataTable();
        });
    </script>
@endsection
