@extends('layouts.master')

@push('page')
    Kriteria
@endpush

@section('content')
    <div class="row">
        @if ($alert)
            <div class="col-lg-12 grid-margin">
                {!! $alert !!}
            </div>
        @endif
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Daftar Kriteria</h4>
                        <a href="{{ route('admin.create.criteria') }}" class="btn btn-outline-info btn-icon-text">
                            <i class="typcn typcn-plus btn-icon-append"></i>
                            Tambah Kriteria
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="criteriaTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Kriteria</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $weight = 0;
                                    foreach ($criterias as $value) {
                                        $weight = bcadd($weight, $value->weight, 0);
                                    }
                                @endphp
                                @if ($weight < 100)
                                    <div class="alert alert-warning" role="alert">
                                        Total Bobot Kurang Dari 100%!
                                    </div>
                                @elseif($weight > 100)
                                    <div class="alert alert-danger" role="alert">
                                        Total Bobot Melebihi 100%!
                                    </div>
                                @endif
                                @foreach ($criterias as $criteria)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $criteria->name }}</td>
                                        <td class="text-center">{{ $criteria->category }}</td>
                                        <td class="text-center">{{ $criteria->weight . '%' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route(Auth::user()->role . '.edit.criteria', Crypt::encrypt($criteria->id)) }}"
                                                class="btn btn-sm btn-outline-warning btn-icon-text">
                                                <i class="typcn typcn-document btn-icon-prepend"></i>
                                                Edit
                                            </a>
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
            $('#criteriaTable').DataTable({
                "columnDefs": [{
                    "targets": [4],
                    "orderable": false,
                    "searchable": false
                }]
            });
        });
    </script>
@endsection
