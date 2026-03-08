@extends('layouts.master')

@push('page')
    Input Data
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title">Data Alternatif</h4>
                </div>
                @if (Auth::user()->role == 'rt')
                    <div>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">
                            <i class="typcn typcn-plus"></i> Tambah Alternatif
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content modal-dialog-scrollable">
                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title" id="staticBackdropLabel">Formulir Tambah Kriteria</h5>
                                            <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                                    dipilih.</em></small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal" aria-label="Close"><i
                                                class="mdi mdi-close-box"></i></button>
                                    </div>
                                    <form action="{{ route('rt.post.data') }}" method="POST" id="uploadFileForm">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="name">Nama<span style="color: red">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    placeholder="Masukkan nama alternatif" required>
                                            </div>
                                            @foreach ($criterias as $criteria)
                                                <div class="form-group">
                                                    <label for="criteria{{ $criteria->id }}">{{ $criteria->name }}<span
                                                            style="color: red">*</span></label>
                                                    <select class="form-control" id="criteria{{ $criteria->id }}"
                                                        name="criteria[{{ $criteria->id }}]" required>
                                                        <option value="">-- Pilih Penilaian --</option>
                                                        @foreach ($criteria->sub_criteria as $sub_criteria)
                                                            @if (isset($sub_criteria->upper_value))
                                                                <option value="{{ $sub_criteria->id }}">
                                                                    {{ '> ' . number_format($sub_criteria->upper_value, 0, ',', '.') }}
                                                                </option>
                                                            @elseif (isset($sub_criteria->under_value))
                                                                <option value="{{ $sub_criteria->id }}">
                                                                    {{ '< ' . number_format($sub_criteria->under_value, 0, ',', '.') }}
                                                                </option>
                                                            @elseif (isset($sub_criteria->initial_value) && isset($sub_criteria->final_value))
                                                                <option value="{{ $sub_criteria->id }}">
                                                                    {{ '> ' . number_format($sub_criteria->initial_value, 0, ',', '.') }}
                                                                    dan
                                                                    {!! '&le; ' . number_format($sub_criteria->final_value, 0, ',', '.') !!}
                                                                </option>
                                                            @elseif (isset($sub_criteria->sameas_value))
                                                                <option value="{{ $sub_criteria->id }}">
                                                                    {{ is_numeric($sub_criteria->sameas_value)
                                                                        ? number_format($sub_criteria->sameas_value, 0, ',', '.')
                                                                        : $sub_criteria->sameas_value }}
                                                                </option>
                                                            @else
                                                                <option value="">Data Kosong</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-info">Tambah</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3">
                    @if (Auth::user()->role == 'rt')
                        <h5>Data Alternatif RT {{ Auth::user()->rt }} RW {{ Auth::user()->rw }}</h5>
                    @else
                        <h5>Data Alternatif Keseluruhan</h5>
                    @endif
                    {{-- @if (count($alternatives) > 0)
                        <button type="button" onclick="deleteAlternativeAll()"
                            class="btn btn-outline-danger btn-icon-text">
                            <i class="typcn typcn-trash btn-icon-prepend"></i>
                            Hapus Seluruh Data
                        </button>
                    @endif --}}
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-hover" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-left">Kriteria & Nilai</th>
                                @if (Auth::user()->role == 'rt')
                                    <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $alternative)
                                <tr>
                                    <td class="text-center">{{ $alternative->name }}</td>
                                    <td class="text-left">
                                        @foreach ($alternative->criteria_alternative as $criteria_alternative)
                                            <div class="my-2 row">
                                                <div class="col-6">{{ $criteria_alternative->criteria->name }}</div>
                                                <div class="col-6">Nilai : {{ $criteria_alternative->value }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                    @if (Auth::user()->role == 'rt')
                                        <td class="text-center">
                                            @if (!in_array($alternative->id, $alternativeIds, true))
                                                <button type="button"
                                                    onclick="deleteAlternative('{{ Crypt::encrypt($alternative->id) }}')"
                                                    class="btn btn-danger btn-icon-text">
                                                    <i class="typcn typcn-trash btn-icon-prepend"></i>
                                                    Hapus
                                                </button>
                                            @else
                                                @if (isset($alternativesAcc))
                                                    @if (in_array($alternative->id, $alternativesAcc, true))
                                                        <span class="badge bg-success text-light">Penerima
                                                            Bantuan</span>
                                                    @else
                                                        <span class="badge bg-warning text-light">Penunggu
                                                            Persetujuan</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                    @endif
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
            <h3 class="mt-5 text-primary">Sedang Mengunggah...</h3>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .modal-dialog-scrollable {
            overflow-y: auto !important
        }
    </style>
@endsection

@section('scripts')
    <script>
        @if (Auth::user()->role == 'rt')
            function deleteAlternativeAll() {
                Swal.fire({
                    title: "Yakin ingin menghapus keseluruhan data?",
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
                            url: "{{ route(Auth::user()->role . '.delete.all.data') }}",
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: "Keseluruhan Alternatif berhasil dihapus.",
                                    icon: "success",
                                    didClose: () => {
                                        window.location.reload();
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

            function deleteAlternative(id) {
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
                            url: "{{ route(Auth::user()->role . '.delete.data') }}",
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: "Alternatif berhasil dihapus.",
                                    icon: "success",
                                    didClose: () => {
                                        window.location.reload();
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

            $('.file-upload-browse').on('click', function() {
                var file = $(document).find('.file-upload-default');
                file.trigger('click');
            });

            $(document).on('change', '.file-upload-default', function() {
                $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
            });

            $(document).on('click', 'button[type="submit"]', function(e) {
                e.preventDefault();
                $(this).prop('disabled', true);
                $(this).html('Proses...');
                $('.loader').removeClass('hidden');

                var form = $('#uploadFileForm')[0];
                if (form.checkValidity()) {
                    form.submit();
                } else {
                    $(this).prop('disabled', false);
                    $(this).html('Tambah');
                    $('.loader').addClass('hidden');
                    form.reportValidity();
                }
            });
        @endif

        $(document).ready(function() {
            $('#alternativeTable').DataTable();
        });
    </script>
@endsection
