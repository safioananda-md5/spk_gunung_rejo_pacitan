@extends('layouts.master')

@push('page')
    Nilai Bobot
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title">Data Nilai Bobot</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-info btn-icon-text" data-toggle="modal"
                        data-target="#addData">
                        <i class="typcn typcn-plus btn-icon-append"></i>
                        Tambah Data
                    </button>
                    <div class="modal fade" id="addData" tabindex="-1" role="dialog" aria-labelledby="addDataLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header d-flex flex-column">
                                    <h5 class="modal-title" id="addDataLabel">Formulir Tambah Data</h5>
                                    <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                            dipilih.</em></small>
                                </div>
                                <form action="{{ route(Auth::user()->role . '.post.weight.value') }}" method="post"
                                    id="weightvalueForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="gap">Selisih<span style="color: red">*</span></label>
                                                    <input type="number" class="form-control" id="gap" name="gap"
                                                        placeholder="0" step="any">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="weight">Nilai Bobot<span
                                                            style="color: red">*</span></label>
                                                    <input type="number" class="form-control" id="weight" name="weight"
                                                        placeholder="5" step="any">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="weightvalueTable">
                        <thead>
                            <tr>
                                <th class="text-center">Selisih</th>
                                <th class="text-center">Nilai Bobot</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
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
@endsection

@section('scripts')
    <script>
        function hapus(id) {
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
                        url: "{{ route(Auth::user()->role . '.weight.value') }}",
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
                                text: "Data berhasil dihapus.",
                                icon: "success",
                                didClose: () => {
                                    $('#weightvalueTable').DataTable().ajax.reload();
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

        $(document).ready(function() {
            $('#weightvalueTable').DataTable({
                "ajax": {
                    "url": "{{ route(Auth::user()->role . '.data.weight.value') }}",
                    "type": "GET"
                },
                "columns": [{
                        "data": "gap",
                        "className": "text-center"
                    },
                    {
                        "data": "weight",
                        "className": "text-center"
                    },
                    {
                        "data": null,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            return `<button class="btn btn-sm btn-danger" onclick="hapus(${data.id})">Hapus</button>`;
                        }
                    }
                ]
            });

            $('#weightvalueForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let action = $(this).attr('action');
                $.ajax({
                    url: action,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message,
                            icon: "success",
                            didClose: () => {
                                $('[data-dismiss="modal"]').click();
                                $('input[type="number"]').val("");
                                $('#weightvalueTable').DataTable().ajax.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Gagal!",
                            html: xhr.responseJSON.message,
                            icon: "error",
                            didClose: () => {
                                $('[data-dismiss="modal"]').click();
                                $('input[type="number"]').val("");
                                $('#weightvalueTable').DataTable().ajax.reload();
                            }
                        });
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).text('Simpan');
                    }
                });
            });
        });
    </script>
@endsection
