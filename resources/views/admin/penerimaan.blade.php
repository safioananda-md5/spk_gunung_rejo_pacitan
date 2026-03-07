@extends('layouts.master')

@push('page')
    Penerimaan
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title">Data Penerimaan</h4>
                <div>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        <i class="typcn typcn-plus"></i> Buat Penerimaan
                    </button>
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content modal-dialog-scrollable">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title" id="staticBackdropLabel">Formulir Buat Penerimaan</h5>
                                        <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                                dipilih.</em></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"
                                        aria-label="Close"><i class="mdi mdi-close-box"></i></button>
                                </div>
                                <form action="{{ route('admin.post.acceptance') }}" method="POST" id="uploadFileForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="count">Jumlah Calon Penerima<span
                                                    style="color: red">*</span></label>
                                            <input type="number" class="form-control" id="count" name="count"
                                                placeholder="Masukkan jumlah calon penerima" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-info">Buat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="acceptanceTable">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Jumlah Penerimaan</th>
                                <th class="text-center">Status</th>
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
    <form action="" method="POST" id="deleteForm">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" id="idDelete">
    </form>
@endsection

@section('css')
@endsection

@section('scripts')
    <script>
        function hapusALL(id) {
            Swal.fire({
                title: "Yakin ingin menghapus data?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Batal",
                confirmButtonText: "Iya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    const Form = $('#deleteForm');
                    const IDinput = Form.find('#idDelete');
                    IDinput.val(id);
                    Form.submit();
                }
            });
        }

        $(document).ready(function() {
            $('#acceptanceTable').DataTable({
                "order": [],
                "columnDefs": [{
                    "targets": 3,
                    "orderable": false,
                    "searchable": false
                }]
            });
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
    </script>
@endsection
