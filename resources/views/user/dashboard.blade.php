@extends('layouts.master')

@push('page')
    Dashboard
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="alert alert-info" role="alert">
                Selamat datang pada Platform Digital Sistem Pendukung Keputusan Penetapan Penerima Bantuan Sosial Siswa SMK
                Muhammadiyah 1 Taman.
            </div>
        </div>
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Penerimaan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="acceptanceTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uniqueDates as $date => $items)
                                    @php
                                        $part = explode('|', $date);
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            @php
                                                $part2 = explode(',', $part[0]);
                                            @endphp
                                            {{ $part2[0] }}
                                        </td>
                                        <td class="text-center">{{ $part[1] == '' ? '-' : $part[1] }}</td>
                                        <td class="text-center">{{ $items->count() }} Siswa</td>
                                        <td class="text-center">
                                            <a href="{{ route(Auth::user()->role . '.detail.acceptance', Str::slug($part[0])) }}"
                                                class="btn btn-outline-info btn-fw">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#acceptanceTable').DataTable({
                "order": [],
                "columnDefs": [{
                        "targets": 0,
                        "orderable": true,
                        "searchable": true
                    },
                    {
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 2,
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
        });
    </script>
@endsection
