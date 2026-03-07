@extends('layouts.master')

@push('page')
    Profil Ideal
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pilih Profil Ideal</h4>
                <small class="text-muted"><em><span style="color: red">*</span>Menandakan bahwa kolom ini wajib diisi atau
                        dipilih</em></small>
            </div>
            <form action="{{ route(Auth::user()->role . '.post.ideal') }}" method="post" id="idealForm">
                @csrf
                <div class="card-body">
                    @foreach ($criterias as $criteria)
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="{{ strtolower(str_replace([' ', '-'], '_', $criteria->name)) }}">Nama
                                        Kriteria<span style="color: red">*</span></label>
                                    <input type="text" class="form-control"
                                        id="{{ strtolower(str_replace([' ', '-'], '_', $criteria->name)) }}"
                                        placeholder="Masukkan nama kriteria" value="{{ $criteria->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label
                                        for="profil_ideal_{{ strtolower(str_replace([' ', '-'], '_', $criteria->name)) }}">Pilih
                                        Profil Ideal<span style="color: red">*</span></label>
                                    <select class="form-control"
                                        id="profil_ideal_{{ strtolower(str_replace([' ', '-'], '_', $criteria->name)) }}"
                                        name="ideal[{{ $criteria->name }}]">
                                        <option value="">-- Pilih Sub-Kriteria --</option>
                                        @foreach ($criteria->sub_criteria as $sub)
                                            <option value="{{ $sub->id }}"
                                                @if ($sub->profile_ideal) selected @endif>
                                                Skala {{ $sub->scale }} ||
                                                @if ($sub->upper_value)
                                                    > {{ $sub->upper_value }}
                                                @elseif ($sub->under_value)
                                                < {{ $sub->under_value }} @elseif ($sub->initial_value && $sub->final_value)>
                                                        {{ $sub->initial_value }} dan &le; {{ $sub->final_value }}
                                                    @elseif ($sub->sameas_value)
                                                        {{ $sub->sameas_value }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success btn-icon-text">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Loader --}}
    <div class="loader hidden">
        <div class="custom-loader d-flex flex-column justify-content-center align-items-center">
            <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.5rem;"
                role="status">
            </div>
            <h3 class="mt-5 text-primary">Sedang Menyimpan...</h3>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('button[type="submit"]').on('click', function(e) {
                e.preventDefault();

                const button = $(this);
                button.prop('disabled', true).html('Menyimpan...')
                $('.loader').removeClass('hidden');
                $('#idealForm').submit();
            });
        });
    </script>
@endsection
