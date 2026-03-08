@extends('layouts.master')

@push('page')
    User
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title">Data User</h4>
                <div>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        <i class="typcn typcn-plus"></i> Tambah User
                    </button>
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content modal-dialog-scrollable">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title" id="staticBackdropLabel">Formulir Tambah User</h5>
                                        <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                                dipilih.</em></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"
                                        aria-label="Close"><i class="mdi mdi-close-box"></i></button>
                                </div>
                                <form action="{{ route('admin.store.user') }}" method="POST" id="uploadFileForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Nama User<span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Masukkan nama user" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email User<span style="color: red">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Masukkan email user" autocomplete="off" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role<span style="color: red">*</span></label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin">Sekretaris Desa</option>
                                                <option value="kades">Kepala Desa</option>
                                                <option value="rt">Ketua RT</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="no_rt">
                                            <label for="rt">No. RT<span style="color: red">*</span> <small>(wajib
                                                    untuk role rt)</small></label>
                                            <input type="number" class="form-control" id="rt" name="rt"
                                                placeholder="Masukkan nomor rt">
                                        </div>
                                        <div class="form-group" id="no_rw">
                                            <label for="rw">No. RW<span style="color: red">*</span> <small>(wajib
                                                    untuk role rt)</small></label>
                                            <input type="number" class="form-control" id="rw" name="rw"
                                                placeholder="Masukkan nomor rw">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password<span style="color: red">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Masukkan password user" autocomplete="new-password" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-info">Tambah</button>
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
                                <th class="text-start">Email</th>
                                <th class="text-start">Name</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td class="text-center">{{ Str::upper($user->role) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-fw"
                                            onclick="hapusALL('{{ $user->id }}')">Hapus</button>
                                        <button type="button" class="btn btn-warning text-light BTNEDIT"
                                            data-bs-toggle="modal" data-bs-target="#edit"
                                            data-id="{{ $user->id }}">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
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
    <div class="modal fade" id="edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-dialog-scrollable">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="staticBackdropLabel">Formulir Edit User</h5>
                        <small><em><span style="color: red">*</span> Menandakan kolom wajib diisi atau
                                dipilih.</em></small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"
                        aria-label="Close"><i class="mdi mdi-close-box"></i></button>
                </div>
                <form action="{{ route('admin.edit.user') }}" method="POST" id="uploadFileForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id_edit" id="id_edit">
                        <div class="form-group">
                            <label for="name_edit">Nama User<span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="name_edit" name="name_edit"
                                placeholder="Masukkan nama user" required>
                        </div>
                        <div class="form-group">
                            <label for="email_edit">Email User<span style="color: red">*</span></label>
                            <input type="email" class="form-control" id="email_edit" name="email_edit"
                                placeholder="Masukkan email user" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="role_edit">Role<span style="color: red">*</span></label>
                            <select class="form-control" id="role_edit" name="role_edit" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Sekretaris Desa</option>
                                <option value="kades">Kepala Desa</option>
                                <option value="rt">Ketua RT</option>
                            </select>
                        </div>
                        <div class="form-group" id="no_rt_edit">
                            <label for="rt_edit">No. RT<span style="color: red">*</span> <small>(wajib
                                    untuk role rt)</small></label>
                            <input type="number" class="form-control" id="rt_edit" name="rt_edit"
                                placeholder="Masukkan nomor rt">
                        </div>
                        <div class="form-group" id="no_rw_edit">
                            <label for="rw_edit">No. RW<span style="color: red">*</span> <small>(wajib
                                    untuk role rt)</small></label>
                            <input type="number" class="form-control" id="rw_edit" name="rw_edit"
                                placeholder="Masukkan nomor rw">
                        </div>
                        <div class="form-group">
                            <label for="password_edit">Password <small>(Massukan password untuk mengganti)</small></label>
                            <input type="password" class="form-control" id="password_edit" name="password_edit"
                                placeholder="Masukkan password user" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

            $('#role').on('change', function() {
                var role = $(this).val();
                const nort = $('#no_rt');
                const norw = $('#no_rw');

                if (role != 'rt') {
                    nort.hide();
                    norw.hide();
                } else {
                    nort.show();
                    norw.show();
                }
            })

            $('#role').val('').trigger('change');

            $('#role_edit').on('change', function() {
                var role_edit = $(this).val();
                const nort_edit = $('#no_rt_edit');
                const norw_edit = $('#no_rw_edit');

                if (role_edit != 'rt') {
                    nort_edit.hide();
                    norw_edit.hide();
                } else {
                    nort_edit.show();
                    norw_edit.show();
                }
            })

            $('#role_edit').val('').trigger('change');

            const userEditData = @json($userEdit);
            $('.BTNEDIT').on('click', function() {
                var id = $(this).data('id');
                $('#id_edit').val(id).trigger('change');
                $('#name_edit').val(userEditData[id]['NAME']).trigger('change');
                $('#email_edit').val(userEditData[id]['EMAIL']).trigger('change');
                $('#role_edit').val(userEditData[id]['ROLE']).trigger('change');
                $('#rt_edit').val(userEditData[id]['NORT']).trigger('change');
                $('#rw_edit').val(userEditData[id]['NORW']).trigger('change');
            });
        });

        $(document).on('click', 'button[type="submit"]', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true);
            $(this).html('Proses...');
            $('.loader').removeClass('hidden');

            var form = $(this).closest('form')[0];
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
