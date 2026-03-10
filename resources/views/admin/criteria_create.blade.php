@extends('layouts.master')

@push('page')
    @if ($edit)
        Edit Kriteria
    @else
        Tambah Kriteria
    @endif
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title">
                        @if ($edit)
                            Formulir Edit Kriteria
                        @else
                            Formulir Tambah Kriteria
                        @endif
                    </h4>
                    <small class="text-muted"><em><span style="color: red">*</span>Menandakan bahwa kolom ini wajib diisi atau
                            dipilih</em></small>
                </div>
                @if ($edit)
                    <div>
                        <button type="button" onclick="deleteCriteria()" class="btn btn-danger btn-icon-text">
                            <i class="typcn typcn-trash btn-icon-prepend"></i>
                            Hapus Kriteria
                        </button>
                    </div>
                @endif
            </div>
            <form
                @if ($edit) action="{{ route(Auth::user()->role . '.update.criteria', Crypt::encrypt($criteria->id)) }}"
            @else action="{{ route(Auth::user()->role . '.post.criteria') }}" @endif
                method="post" id="createForm">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="criteriaName">Nama Kriteria<span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="criteriaName" id="criteriaName"
                            placeholder="Masukkan nama kriteria"
                            @if ($edit) value="{{ $criteria->name }}" @endif>
                    </div>
                    <div class="form-group">
                        <label for="criteriaCategory">Ketegori Kriteria<span style="color: red">*</span></label>
                        <select class="form-control" name="criteriaCategory" id="criteriaCategory">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="cost" @if ($edit && $criteria->category == 'cost') selected @endif>Cost</option>
                            <option value="benefit" @if ($edit && $criteria->category == 'benefit') selected @endif>
                                Benefit
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="criteriaWeight">Bobot Kriteria <small class="text-muted">maks. 100%</small><span
                                style="color: red">*</span></label>
                        <div class="input-group" id="criteriaWeight">
                            <input type="text" name="criteriaWeight" class="form-control input-percent" placeholder="100"
                                @if ($edit) value="{{ $criteria->weight }}" @endif>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <h6>Sub-kriteria</h6>
                        <button type="button" class="btn btn-sm btn-info btn-icon-text" id="addSub">
                            <i class="typcn typcn-plus btn-icon-append"></i>
                            Tambah Sub
                        </button>
                    </div>
                    <div class="mt-3" id="rowSub">
                        @if ($edit)
                            @foreach ($subcriterias as $sub)
                                <div class="row sub" data-number="{{ $sub->scale }}">
                                    <div class="col-lg-3 d-flex align-items-center">
                                        <a href="#" onclick="deleteSub({{ $sub->scale }})"
                                            class="text-danger mr-3"><i
                                                class="typcn typcn-trash btn-icon-append h2 m-0 p-0"></i></a>
                                        <div class="form-group">
                                            <label for="skalasub{{ $sub->scale }}">Skala<span
                                                    style="color: red">*</span></label>
                                            <input type="number" class="form-control"
                                                name="skala[{{ $sub->scale }}][skala]" id="skalasub{{ $sub->scale }}"
                                                value="{{ $sub->scale }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="typeSub{{ $sub->scale }}">Jenis Sub-kriteria<span
                                                    style="color: red">*</span></label>
                                            <select class="form-control typeSub" id="typeSub{{ $sub->scale }}">
                                                <option value="">-- Pilih Jenis --</option>
                                                <option value="upper" @if (isset($sub->upper_value)) selected @endif>
                                                    Diatas
                                                </option>
                                                <option value="under" @if (isset($sub->under_value)) selected @endif>
                                                    Dibawah</option>
                                                <option value="range" @if (isset($sub->initial_value) && isset($sub->final_value)) selected @endif>
                                                    Range</option>
                                                <option value="sameas" @if (isset($sub->sameas_value)) selected @endif>
                                                    Sama Dengan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6" id="datasub{{ $sub->scale }}">
                                        @if (isset($sub->upper_value))
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="upperValue{{ $sub->scale }}">Nilai diatas dari ( > )
                                                            <small class="text-muted">hanya angka</small><span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control number-only"
                                                            name="skala[{{ $sub->scale }}][upperValue]"
                                                            id="upperValue{{ $sub->scale }}" placeholder="1.000"
                                                            value="{{ $sub->upper_value }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif (isset($sub->under_value))
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="underValue{{ $sub->scale }}">Nilai dibawah dari ( < )
                                                                <small class="text-muted">hanya angka</small><span
                                                                    style="color: red">*</span></label>
                                                        <input type="text" class="form-control number-only"
                                                            name="skala[{{ $sub->scale }}][underValue]"
                                                            id="underValue{{ $sub->scale }}" placeholder="1.000"
                                                            value="{{ $sub->under_value }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif (isset($sub->initial_value) && isset($sub->final_value))
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="initialValue{{ $sub->scale }}">Nilai awal ( > )
                                                            <small class="text-muted">hanya angka</small><span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control number-only"
                                                            name="skala[{{ $sub->scale }}][initialValue]"
                                                            id="initialValue{{ $sub->scale }}" placeholder="100"
                                                            value="{{ $sub->initial_value }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="finalValue{{ $sub->scale }}">Nilai akhir ( &le; )
                                                            <small class="text-muted">hanya angka</small><span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control number-only"
                                                            name="skala[{{ $sub->scale }}][finalValue]"
                                                            id="finalValue{{ $sub->scale }}" placeholder="1.000"
                                                            value="{{ $sub->final_value }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="sameasValue{{ $sub->scale }}">Nilai sama dengan ( =
                                                            ) <small class="text-muted">angka / text</small><span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control"
                                                            name="skala[{{ $sub->scale }}][sameasValue]"
                                                            id="sameasValue{{ $sub->scale }}"
                                                            placeholder="1.000 / Tidak Ada"
                                                            value="{{ $sub->sameas_value }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <a href="{{ route(Auth::user()->role . '.criteria') }}"
                            class="btn btn-outline-danger btn-icon-text my-1">
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-icon-text my-1">
                            @if ($edit)
                                Perbarui
                            @else
                                Tambah
                            @endif
                        </button>
                    </div>
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
            <h3 class="mt-5 text-primary">Sedang Memproses...</h3>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('scripts')
    <script>
        function getMaxNumber() {
            var elements = $('.sub');
            if (elements.length > 0) {
                var max = Math.max.apply(null, elements.map(function() {
                    return parseFloat($(this).attr('data-number')) || 0;
                }).get());

            } else {
                var max = 0;
            }

            return max + 1;
        }

        function formSub(number) {
            const FormSub = `
                <div class="row sub" data-number="${number}">
                    <div class="col-lg-3 d-flex align-items-center">
                        <a href="#" onclick="deleteSub(${number})" class="text-danger mr-3"><i class="typcn typcn-trash btn-icon-append h2 m-0 p-0"></i></a>
                        <div class="form-group">
                            <label for="skalasub${number}">Skala<span style="color: red">*</span></label>
                            <input type="number" class="form-control" name="skala[${number}][skala]" id="skalasub${number}" value="${number}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="typeSub${number}">Jenis Sub-kriteria<span
                                    style="color: red">*</span></label>
                            <select class="form-control typeSub" id="typeSub${number}">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="upper">Diatas</option>
                                <option value="under">Dibawah</option>
                                <option value="range">Range</option>
                                <option value="sameas">Sama Dengan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6" id="datasub${number}">
                    </div>
                </div>
            `;

            return FormSub;
        }

        function deleteSub(number) {
            var maxNumber = getMaxNumber();
            for (let i = number; i < maxNumber; i++) {
                const sub = $(`.sub[data-number="${i}"]`);
                sub.remove();
            }
        }

        function formRange(number) {
            const Range = `
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="initialValue${number}">Nilai awal ( > ) <small class="text-muted">hanya angka</small><span style="color: red">*</span></label>
                            <input type="text" class="form-control number-only" name="skala[${number}][initialValue]" id="initialValue${number}" placeholder="100">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="finalValue${number}">Nilai akhir ( &le; ) <small class="text-muted">hanya angka</small><span style="color: red">*</span></label>
                            <input type="text" class="form-control number-only" name="skala[${number}][finalValue]" id="finalValue${number}" placeholder="1.000">
                        </div>
                    </div>
                </div>
            `;

            return Range;
        }

        function formUnder(number) {
            const Under = `
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="underValue${number}">Nilai dibawah dari ( < ) <small class="text-muted">hanya angka</small><span style="color: red">*</span></label>
                            <input type="text" class="form-control number-only" name="skala[${number}][underValue]" id="underValue${number}" placeholder="1.000">
                        </div>
                    </div>
                </div>
            `;

            return Under;
        }

        function formUpper(number) {
            const Upper = `
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="upperValue${number}">Nilai diatas dari ( > ) <small class="text-muted">hanya angka</small><span style="color: red">*</span></label>
                            <input type="text" class="form-control number-only" name="skala[${number}][upperValue]" id="upperValue${number}" placeholder="1.000">
                        </div>
                    </div>
                </div>
            `;

            return Upper;
        }

        function formSameAs(number) {
            const SameAs = `
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="sameasValue${number}">Nilai sama dengan ( = ) <small class="text-muted">angka / text</small><span style="color: red">*</span></label>
                            <input type="text" class="form-control" name="skala[${number}][sameasValue]" id="sameasValue${number}" placeholder="1.000 / Tidak Ada">
                        </div>
                    </div>
                </div>
            `;

            return SameAs;
        }

        @if ($edit)
            function deleteCriteria() {
                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Batal",
                    confirmButtonText: "Iya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route(Auth::user()->role . '.delete.criteria', Crypt::encrypt($criteria->id)) }}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: null,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: "Kriteria berhasil dihapus.",
                                    icon: "success",
                                    didClose: () => {
                                        window.location.href =
                                            "{{ route(Auth::user()->role . '.criteria') }}";
                                    }
                                });
                            },
                            error: function(xhr) {
                                let errorMsg = xhr.responseJSON.message || "Terjadi kesalahan.";
                                Swal.fire("Gagal!", errorMsg, "error");
                            }
                        });
                    }
                });
            }
        @endif

        $(document).ready(function() {
            $('.input-percent').on('input', function() {
                let val = $(this).val().replace(/[^0-9]/g, '');
                if (val !== '') {
                    let num = parseInt(val, 10);
                    if (num > 100) {
                        val = 100;
                    } else {
                        val = num;
                    }
                }
                $(this).val(val);
            });

            $('#addSub').on('click', function() {
                var maxNumber = getMaxNumber();
                $('#rowSub').append(formSub(maxNumber));
            });

            $('#rowSub').on('input', '.number-only', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#rowSub').on('change', '.typeSub', function() {
                const typeSub = $(this);
                var dataNumber = $(this).closest('.row').data('number');
                const datasub = $('#rowSub').find('#datasub' + dataNumber);
                var typeSubVal = typeSub.val();

                datasub.empty();

                switch (true) {
                    case (typeSubVal == 'range'):
                        datasub.append(formRange(dataNumber));
                        break;
                    case (typeSubVal == 'under'):
                        datasub.append(formUnder(dataNumber));
                        break;
                    case (typeSubVal == 'upper'):
                        datasub.append(formUpper(dataNumber));
                        break;
                    case (typeSubVal == 'sameas'):
                        datasub.append(formSameAs(dataNumber));
                        break;
                    default:
                        null;
                }
            });

            $('button[type="submit"]').on('click', function(e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                $(this).html('Proses...');
                $('.loader').removeClass('hidden');
                $('#createForm').submit();
            })
        });
    </script>
@endsection
