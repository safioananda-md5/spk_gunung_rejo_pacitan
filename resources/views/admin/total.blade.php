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
                <a class="nav-link" id="pills-coresecondary-tab" href="{{ route(Auth::user()->role . '.CSF') }}"
                    role="tab" aria-controls="pills-coresecondary" aria-selected="false">Core & Secondary Factor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="pills-total-tab" href="{{ route(Auth::user()->role . '.total') }}"
                    role="tab" aria-controls="pills-total" aria-selected="false">Nilai Total</a>
            </li>
        </ul>
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                Perhitungan Nilai Total Tiap Alternatif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Siswa / Alternatif</th>
                                <th class="text-center">NCF<br><small>(Bobot {{ bcdiv($weightTotalNCF, 100, 1) }})</small>
                                </th>
                                <th class="text-center">NSF<br><small>(Bobot {{ bcdiv($weightTotalNSF, 100, 1) }})</small>
                                </th>
                                <th class="text-center">Perhitungan</th>
                                <th class="text-center">Hasil</th>
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
                                            $totalcf = bcadd($totalcf, $bobotcf, 3);
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
                                            $totalsf = bcadd($totalsf, $bobotsf, 3);
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
                                    <td class="text-center">{{ bcdiv($totalcf, $cf, 3) }}</td>
                                    <td class="text-center">{{ bcdiv($totalsf, $sf, 3) }}</td>
                                    <td class="text-center">
                                        ({{ bcdiv($weightTotalNCF, 100, 1) . ' × ' . bcdiv($totalcf, $cf, 3) }})
                                        + ({{ bcdiv($weightTotalNSF, 100, 1) . ' × ' . bcdiv($totalsf, $sf, 3) }})</td>
                                    <td class="text-center">
                                        @php
                                            $weighcf = bcdiv($weightTotalNCF, 100, 3);
                                            $totalcf = bcdiv($totalcf, $cf, 3);
                                            $finalcf = bcmul($weighcf, $totalcf, 3);
                                            $weighsf = bcdiv($weightTotalNSF, 100, 3);
                                            $totalsf = bcdiv($totalsf, $sf, 3);
                                            $finalsf = bcmul($weighsf, $totalsf, 3);
                                            $totalvalue = bcadd($finalcf, $finalsf, 3);
                                        @endphp
                                        {{ $totalvalue }}
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
                    },
                    {
                        "targets": 2,
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 3,
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 4,
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
        });
    </script>
@endsection
