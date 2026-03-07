@extends('layouts.master')

@push('page')
    Input Data
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Alternatif</h4>
                <small class="text-muted"><em><span style="color: red">*</span>Menandakan bahwa kolom ini wajib diisi atau
                        dipilih</em></small>
            </div>
            <div class="card-body">
                <form action="{{ route(Auth::user()->role . '.post.data') }}" method="post" class="border-bottom mb-3"
                    id="uploadFileForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Unggah File Excel Data Alternatif<span style="color: red">*</span></label>
                        <input type="file" name="file" class="file-upload-default"
                            accept=".xls, .xlsx, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" placeholder="Unggah file .xlsx"
                                disabled>
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Pilih
                                    File</button>
                                <button type="submit" class="btn btn-success btn-icon-text">
                                    Unggah File
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3">
                    <h5>Data Alternatif Keseluruhan</h5>
                    @if (count($alternatives) > 0)
                        <button type="button" onclick="deleteAlternativeAll()"
                            class="btn btn-outline-danger btn-icon-text">
                            <i class="typcn typcn-trash btn-icon-prepend"></i>
                            Hapus Seluruh Data
                        </button>
                    @endif
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-hover" id="alternativeTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-left">Kriteria & Skala</th>
                                <th class="text-center">Aksi</th>
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
                                                <div class="col-6">Skala : {{ $criteria_alternative->value }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            onclick="deleteAlternative('{{ Crypt::encrypt($alternative->id) }}')"
                                            class="btn btn-danger btn-icon-text">
                                            <i class="typcn typcn-trash btn-icon-prepend"></i>
                                            Hapus
                                        </button>
                                    </td>
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
@endsection

@section('scripts')
    <script>
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
            $('#uploadFileForm').submit();
        });

        $(document).ready(function() {
            $('#alternativeTable').DataTable();
        });
    </script>
@endsection
