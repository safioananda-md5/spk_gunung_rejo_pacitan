@extends('layouts.master')

@push('page')
    Penerimaan
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Penerimaan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="acceptanceTable">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($uniqueDates as $date => $items)
                                @php
                                    $part = explode('|', $date);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        @php
                                            $part2 = explode(',', $part[0]);
                                        @endphp
                                        {{ $part2[0] }}
                                    </td>
                                    <td class="text-center">{{ $part[1] == '' ? '-' : $part[1] }}</td>
                                    <td class="text-center">{{ $items->count() }} Siswa</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="true">
                                                Aksi
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <a href="{{ route(Auth::user()->role . '.detail.acceptance', Str::slug($part[0])) }}"
                                                    class="dropdown-item">Detail</a>
                                                <button type="button" class="dropdown-item" style="color: red"
                                                    onclick="hapusALL('{{ $part[0] }}')">Hapus
                                                    Seluruh Data</button>
                                            </div>
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
    <form action="{{ route('admin.deleteall.acceptance') }}" method="POST" id="deleteForm">
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
                        "targets": 0,
                        "orderable": true,
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
                    }
                ]
            });
        });
    </script>
@endsection
