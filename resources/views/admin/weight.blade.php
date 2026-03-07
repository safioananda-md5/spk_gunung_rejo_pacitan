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
                <a class="nav-link active" id="pills-weight-tab" href="{{ route(Auth::user()->role . '.weight') }}"
                    role="tab" aria-controls="pills-weight" aria-selected="false">Pembobotan</a>
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
                Perhitungan Pembobotan
                <p class="mb-0 mr-2 small text-muted text-nowrap">Halaman
                    {{ $alternatives->currentPage() }}</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Siswa / Alternatif</th>
                                <th class="text-left">Kriteria</th>
                                <th class="text-center">Gap</th>
                                <th class="text-center">Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $alternative)
                                <tr>
                                    <td class="text-center" rowspan="{{ $alternative->criteria_alternative->count() }}">
                                        {{ $alternative->name }}</td>
                                    @foreach ($alternative->criteria_alternative as $CS)
                                        @if ($loop->first)
                                            <td>
                                                {{ $CS->criteria->name }}
                                            </td>
                                            <td class="text-center">
                                                {{ bcsub($CS->value, $CS->criteria->sub_criteria->first()->scale) }}
                                            </td>
                                            <td class="text-center">
                                                {{ $weight_array[bcsub($CS->value, $CS->criteria->sub_criteria->first()->scale)] }}
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                                @foreach ($alternative->criteria_alternative as $CS)
                                    @if (!$loop->first)
                                        <tr>
                                            <td>
                                                {{ $CS->criteria->name }}
                                            </td>
                                            <td class="text-center">
                                                {{ bcsub($CS->value, $CS->criteria->sub_criteria->first()->scale) }}
                                            </td>
                                            <td class="text-center">
                                                {{ $weight_array[bcsub($CS->value, $CS->criteria->sub_criteria->first()->scale)] }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3" style="gap: 15px;">
                    <p class="mb-0 mr-2 small text-muted text-nowrap">Halaman
                        {{ $alternatives->currentPage() }} / {{ $alternatives->lastPage() }}</p>
                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <div class="d-flex align-items-center border-right pr-3 mr-2">
                            <label for="jumpInput" class="mb-0 mr-2 small text-muted text-nowrap">Lompat ke:</label>
                            <div class="input-group input-group-sm" style="width: 110px;">
                                <input type="number" id="jumpInput" class="form-control" min="1"
                                    max="{{ $alternatives->lastPage() }}" value="{{ $alternatives->currentPage() }}"
                                    placeholder="Hal...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" onclick="executeJump()">
                                        Go
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="laravel-pagination">
                            {{ $alternatives->links() }}
                        </div>
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
        function executeJump() {
            const page = $('#jumpInput').val();
            const lastPage = {{ $alternatives->lastPage() }};

            if (page >= 1 && page <= lastPage) {
                let url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.href;
            } else {
                alert('Silahkan masukkan halaman antara 1 sampai ' + lastPage);
            }
        }

        $(document).ready(function() {
            $('#jumpInput').on('input', function(e) {
                var value = $(this).val();
                if (value > {{ $alternatives->lastPage() }}) {
                    $(this).val({{ $alternatives->lastPage() }});
                }
            });

            $('#jumpInput').on('keypress', function(e) {
                if (e.which === 13) {
                    executeJump();
                }
            });

            $('#btnJump').on('click', function() {
                executeJump();
            });
        });
    </script>
@endsection
