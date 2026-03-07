@extends('layouts.master')

@push('page')
    Perangkingan
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="pills-rank-tab" href="{{ route(Auth::user()->role . '.rank') }}" role="tab"
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
                <a class="nav-link active" id="pills-coresecondary-tab" href="{{ route(Auth::user()->role . '.CSF') }}"
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
                Perhitungan Core & Secondary Factor Tiap Alternatif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Siswa / Alternatif</th>
                                <th class="text-center">Perhitungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $alternative)
                                @php
                                    $cf = 0;
                                    $totalcf = 0;
                                    $cfWeight = [];
                                    foreach ($alternative->criteria_alternative as $criteria_alternative) {
                                        if ($criteria_alternative->criteria->category == 'core') {
                                            $cf++;
                                            $bobotcf =
                                                $weight_array[
                                                    bcsub(
                                                        $criteria_alternative->value,
                                                        $criteria_alternative->criteria->sub_criteria->first()->scale,
                                                    )
                                                ];
                                            $totalcf = bcadd($totalcf, $bobotcf, 2);
                                            $cfWeight[] =
                                                $weight_array[
                                                    bcsub(
                                                        $criteria_alternative->value,
                                                        $criteria_alternative->criteria->sub_criteria->first()->scale,
                                                    )
                                                ];
                                        }
                                    }

                                    $sf = 0;
                                    $totalsf = 0;
                                    $sfWeight = [];
                                    foreach ($alternative->criteria_alternative as $criteria_alternative) {
                                        if ($criteria_alternative->criteria->category == 'secondary') {
                                            $sf++;
                                            $bobotsf =
                                                $weight_array[
                                                    bcsub(
                                                        $criteria_alternative->value,
                                                        $criteria_alternative->criteria->sub_criteria->first()->scale,
                                                    )
                                                ];
                                            $totalsf = bcadd($totalsf, $bobotsf, 2);
                                            $sfWeight[] =
                                                $weight_array[
                                                    bcsub(
                                                        $criteria_alternative->value,
                                                        $criteria_alternative->criteria->sub_criteria->first()->scale,
                                                    )
                                                ];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $alternative->name }}</td>
                                    <td class="text-center">
                                        <div class="mb-3">
                                            <em>NCF</em>
                                            &nbsp;=&nbsp;
                                            <div class="fraction">
                                                <span class="upper">
                                                    {{ implode(' + ', $cfWeight) }}
                                                </span>
                                                <span class="lower">
                                                    {{ $cf }}
                                                </span>
                                            </div>
                                            &nbsp;=&nbsp;
                                            <div class="fraction">
                                                <span class="upper">{{ $totalcf }}</span>
                                                <span class="lower">{{ $cf }}</span>
                                            </div>
                                            &nbsp;=&nbsp;
                                            {{ bcdiv($totalcf, $cf, 2) }}
                                        </div>
                                        <div class="mt-3">
                                            <em>NSF</em>
                                            &nbsp;=&nbsp;
                                            <div class="fraction">
                                                <span class="upper">
                                                    {{ implode(' + ', $sfWeight) }}
                                                </span>
                                                <span class="lower">{{ $sf }}</span>
                                            </div>
                                            &nbsp;=&nbsp;
                                            <div class="fraction">
                                                <span class="upper">{{ $totalsf }}</span>
                                                <span class="lower">{{ $sf }}</span>
                                            </div>
                                            &nbsp;=&nbsp;
                                            {{ bcdiv($totalsf, $sf, 2) }}
                                        </div>
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
                        "searchable": true
                    },
                    {
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
        });
    </script>
@endsection
