@extends('layouts.master')

@push('page')
    Penerimaan
@endpush

@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    @php
                        $formatted = explode('|', $formatted);
                    @endphp
                    @if (!isset($formatted[1]))
                        <script>
                            @if (Auth::user()->role == 'admin')
                                window.location.href = "{{ route('admin.acceptance') }}";
                            @elseif (Auth::user()->role == 'user')
                                window.location.href = "{{ route('user.dashboard') }}";
                            @endif
                        </script>
                        @php exit; @endphp
                    @endif
                    <div class="d-flex flex-column">
                        @php
                            $DTT = explode(',', $formatted[0]);
                        @endphp
                        <h4 class="card-title">Detail Penerimaan {{ $DTT[0] }}</h4>
                        <p class="card-title">{{ $formatted[1] == '' ? '-' : $formatted[1] }}</p>
                    </div>
                    <div>
                        <a href="{{ route('export.excel', $formatted[0]) }}" class="btn btn-outline-success btn-icon-text">
                            <i class="mdi mdi-file-excel-box btn-icon-prepend"></i>
                            Ekspor Excel
                        </a>
                        <a href="{{ route('export.pdf', $formatted[0]) }}" target="_blank"
                            class="btn btn-outline-danger btn-icon-text">
                            <i class="mdi mdi-file-pdf-box btn-icon-prepend"></i>
                            Ekspor PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="acceptanceTable">
                        <thead>
                            <tr>
                                <th class="text-center">Rank</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Nilai</th>
                                @if (Auth::user()->role == 'admin')
                                    <th class="text-center">Hapus</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penerimaan as $alternative)
                                <tr>
                                    <td class="text-center">{{ $alternative['rank'] }}</td>
                                    <td class="text-center">{{ $alternative['name'] }}</td>
                                    <td class="text-center">{{ $alternative['value'] }}</td>
                                    @if (Auth::user()->role == 'admin')
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="HapusSatu('{{ $alternative['id'] }}')">Hapus</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                @if (Auth::user()->role == 'admin')
                    <a href="{{ route(Auth::user()->role . '.acceptance') }}"
                        class="btn btn-outline-danger btn-fw">Kembali</a>
                @else
                    <a href="{{ route(Auth::user()->role . '.dashboard') }}"
                        class="btn btn-outline-danger btn-fw">Kembali</a>
                @endif
            </div>
        </div>
    </div>
    @if (Auth::user()->role == 'admin')
        <form action="{{ route('admin.delete.acceptance') }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" id="idDelete">
        </form>
    @endif
@endsection

@section('css')
@endsection

@section('scripts')
    <script>
        @if (Auth::user()->role == 'admin')
            function HapusSatu(id) {
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
        @endif

        $(document).ready(function() {
            $('#acceptanceTable').DataTable();
        });
    </script>
@endsection
