<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@stack('page') | SPK - {{ env('APP_NAME') }}</title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}" />
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-1.webp') }}" />
    {{-- Datatables --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap5.css') }}" />
    {{-- Sweetalert2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/dist/sweetalert2.css') }}" />
    {{-- CSRF TOKEN --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .hidden {
            display: none;
        }

        .custom-loader {
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.8);
            width: 100%;
            height: 100%;
            z-index: 99999;
        }
    </style>
    @yield('css')
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="navbar-brand-wrapper d-flex justify-content-center">
                <div class="navbar-brand-inner-wrapper d-flex align-items-center justify-content-between w-100">
                    <a class="navbar-brand brand-logo w-100" href="{{ route(Auth::user()->role . '.dashboard') }}">
                        <img src="{{ asset('assets/images/logo-2.webp') }}" style="height: 108px; width: 192px;"
                            alt="logo" />
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route(Auth::user()->role . '.dashboard') }}"><img
                            src="{{ asset('assets/images/logo-1.webp') }}" style="width: 45px" alt="logo" /></a>
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button"
                        data-toggle="minimize">
                        <span class="typcn typcn-th-menu"></span>
                    </button>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav mr-lg-2">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link" disabled>
                            <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                            <span class="nav-profile-name">{{ Auth::user()->name }}</span>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-date dropdown">
                        <a class="nav-link d-flex justify-content-center align-items-center" disabled>
                            <h6 class="date mb-0">Hari ini : {{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}
                            </h6>
                            <i class="typcn typcn-calendar"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center"
                            href="#" data-toggle="dropdown" id="settingsDropdown">
                            <i class="typcn typcn-cog-outline mx-0"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="settingsDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="typcn typcn-eject text-primary"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="typcn typcn-th-menu"></span>
                </button>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        @if (Auth::user()->role == 'user')
                            <a class="nav-link" href="{{ route(Auth::user()->role . '.penerimaan') }}">
                                <i class="typcn typcn-group menu-icon"></i>
                                <span class="menu-title">Penerimaan</span>
                            </a>
                        @else
                            <a class="nav-link" href="{{ route(Auth::user()->role . '.dashboard') }}">
                                <i class="typcn typcn-device-desktop menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        @endif
                    </li>
                    @if (Auth::user()->role == 'admin')
                        <li class="nav-item no-active">
                            <a class="nav-link" data-toggle="collapse" href="#ui-criteria" aria-expanded="false"
                                aria-controls="ui-criteria">
                                <i class="typcn typcn-document-text menu-icon"></i>
                                <span class="menu-title">Kriteria Pengujian</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="ui-criteria">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item">
                                        <a class="nav-link sub-hover {{ Route::is(Auth::user()->role . '.criteria') ? 'bg-green text-light' : '' }}"
                                            href="{{ route(Auth::user()->role . '.criteria') }}">
                                            <i
                                                class="typcn typcn-clipboard menu-icon {{ Route::is(Auth::user()->role . '.criteria') ? 'text-light' : '' }}"></i>
                                            <span class="menu-title">Kriteria</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(Auth::user()->role . '.input.data') }}">
                                <i class="typcn typcn-document-add menu-icon"></i>
                                <span class="menu-title">Input Data</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(Auth::user()->role . '.acceptance') }}">
                                <i class="typcn typcn-group menu-icon"></i>
                                <span class="menu-title">Penerimaan</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright ©
                                    {{ now()->format('Y') }} <a href="https://smkmita.sch.id/" class="text-muted"
                                        target="_blank">{{ env('APP_NAME') }}</a>. All rights reserved.</span>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <script src="{{ asset('assets/js/boostrap.js') }}"></script>
    <!-- base:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    {{-- Jquery v3.7.1 --}}
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->

    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    {{-- Datatables --}}
    <script src="{{ asset('assets/vendors/datatables/js/dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>
    {{-- Sweetalert2 --}}
    <script src="{{ asset('assets/vendors/sweetalert2/dist/sweetalert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix": "",
                    "sSearch": "Cari:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Sedang memuat...",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sLast": "Terakhir",
                        "sNext": "Selanjutnya",
                        "sPrevious": "Sebelumnya"
                    },
                    "oAria": {
                        "sSortAscending": ": aktifkan untuk mengurutkan kolom secara ascending",
                        "sSortDescending": ": aktifkan untuk mengurutkan kolom secara descending"
                    }
                }
            });

            if ($('.no-active').hasClass('active')) {
                $('.no-active').removeClass('active');
            }
        });
    </script>
    <!-- Custom js for this page-->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page-->
    @yield('scripts')
</body>

</html>
