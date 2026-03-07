@extends('layouts.master')

@push('page')
    Perangkingan
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-rank-tab" href="{{ route(Auth::user()->role . '.rank') }}" role="tab"
                    aria-controls="pills-rank" aria-selected="true">Hasil Ranking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-gap-tab" href="{{ route(Auth::user()->role . '.gap') }}" role="tab"
                    aria-controls="pills-gap" aria-selected="false">Pemetaan Gap</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-weight-tab" href="{{ route(Auth::user()->role . '.weight') }}" role="tab"
                    aria-controls="pills-weight" aria-selected="false">Pembobotan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-coresecondary-tab" href="{{ route(Auth::user()->role . '.CSF') }}"
                    role="tab" aria-controls="pills-coresecondary" aria-selected="false">Core & Secondary Factor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-total-tab" href="{{ route(Auth::user()->role . '.total') }}" role="tab"
                    aria-controls="pills-total" aria-selected="false">Nilai Total</a>
            </li>
        </ul>
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="card-title">Hasil Ranking Perhitungan Sistem</div>
                    <button type="button" class="btn btn-success btn-icon-text" data-toggle="modal" data-target="#get">
                        Pilih Pemenerima
                    </button>
                    <div class="modal fade" id="get" tabindex="-1" role="dialog" aria-labelledby="getLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header d-flex flex-column">
                                    <h5 class="modal-title" id="getLabel">Formulir Pilih Penerima</h5>
                                    <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                            dipilih.</em></small>
                                </div>
                                <form action="{{ route(Auth::user()->role . '.post.rank') }}" method="post" id="rankForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="count">Jumlah Ranking Penerima<span
                                                            style="color: red">*</span></label>
                                                    <input type="number" class="form-control" id="count" name="count"
                                                        placeholder="Masukkan jumlah ranking penerima">
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Keterangan<span
                                                            style="color: red">*</span></label>
                                                    <input type="text" class="form-control" id="description"
                                                        name="description" placeholder="Masukkan keterangan penerimaan">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Pilih</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Ranking</th>
                                <th class="text-center">Siswa / Alternatif</th>
                                <th class="text-center">Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($arrayRank as $rank)
                                <tr>
                                    <td class="text-center">{{ $rank['rank'] }}</td>
                                    <td class="text-center">{{ $rank['name'] }}</td>
                                    <td class="text-center">{{ $rank['value'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Loader --}}
    <div class="loader hidden">
        <div class="custom-loader d-flex flex-column justify-content-center align-items-center">
            <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.5rem;"
                role="status">
            </div>
            <h3 class="mt-5 text-primary">Sedang Memproses...</h3>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .fraction {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }

        .fraction>span {
            display: block;
            padding: 0 0.2em;
        }

        .fraction span.upper {
            border-bottom: 1px solid #000;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#alternativeTable').DataTable({
                "order": [],
                "columnDefs": [{
                        "targets": 0,
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 1,
                        "orderable": false,
                        "searchable": true
                    },
                    {
                        "targets": 2,
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });

            $('button[type="submit"]').on('click', function(e) {
                e.preventDefault();

                const button = $(this);
                button.prop('disabled', true).html('Memproses...');

                $('.loader').removeClass('hidden');
                $('#rankForm').submit();
            });
        });
    </script>
@endsection
