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
                        <h5>Jumlah Calon Diajukan</h5>
                        <i class="mdi mdi-account-star h2"></i>
                    </div>
                    <h2 id="calon">0</h2>
                    <p>Kategori untuk pertama kali diajukan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>Jumlah Calon Diproses</h5>
                        <i class="mdi mdi-account-search h2"></i>
                    </div>
                    <h2 id="progres">0</h2>
                    <p>Kategori menunggu proses persetujuan kepala desa</p>
                </div>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">Data Penerimaan</div>
                <div class="card-body">
                    <table class="table" id="penerimaanTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal Penerimaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternativesAcc as $ACC)
                                <tr>
                                    <td>{{ $ACC->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($ACC->pengajuan->first()->deleted_at)->translatedFormat('d F Y, H:i:s') }}
                                    </td>
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
        const nilaiCalon = @json($alternativesAvailable);
        const nilaiProcess = @json($alternativesProcess);

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
        const demo2 = new countUp.CountUp('progres', nilaiProcess, options);
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
