<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-bs-theme="light">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>@yield('title', 'Sistem Informasi Perparkiran')</title>
        <meta name="description" content="Sistem Informasi Perparkiran" />
        <meta name="robots" content="noindex, nofollow" />

        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
            rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        @stack('styles')

        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        {{-- <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script> --}}
        <script src="{{ asset('assets/js/config.js') }}"></script>
    </head>

    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">

                {{-- 1. Sidebar --}}
                @if (!isset($hideSidebar) || !$hideSidebar)
                    @include('layouts.partials._sidebar')
                @endif

                <div class="layout-page">

                    {{-- 2. Navbar --}}
                    @include('layouts.partials._navbar')

                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            {{-- ✅ 1. SKELETON LOADER DIMULAI DI SINI --}}
                            <div id="skeleton-loader">
                                {{-- Ini adalah contoh kerangka untuk halaman dashboard. --}}
                                {{-- Anda bisa membuat kerangka yang berbeda untuk layout halaman lain. --}}
                                <div class="row g-6">
                                    {{-- Skeleton untuk Info Cards --}}
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="skeleton skeleton-card" style="height: 100px;"></div>
                                        </div>
                                    @endfor

                                    {{-- Skeleton untuk Grafik Utama --}}
                                    <div class="col-lg-8">
                                        <div class="skeleton skeleton-card" style="height: 400px;"></div>
                                    </div>

                                    {{-- Skeleton untuk Info Samping --}}
                                    <div class="col-lg-4">
                                        <div class="skeleton skeleton-card mb-6" style="height: 120px;"></div>
                                        <div class="skeleton skeleton-card" style="height: 260px;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- ✅ 2. KONTEN ASLI HALAMAN (SEMBUNYIKAN PADA AWALNYA) --}}
                            <div id="main-content" style="display: none;">
                                @yield('content')
                            </div>

                        </div>
                        {{-- 4. Footer --}}
                        @include('layouts.partials._footer')

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>

            <div class="layout-overlay layout-menu-toggle"></div>
            <div class="drag-target"></div>
        </div>
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

        @stack('vendors-js')

        <script src="{{ asset('assets/js/main.js') }}"></script>

        @stack('scripts')
        <script>
            window.addEventListener('load', function() {
                const skeleton = document.getElementById('skeleton-loader');
                const content = document.getElementById('main-content');

                if (skeleton && content) {
                    // Beri sedikit jeda agar tidak terasa terlalu cepat
                    setTimeout(() => {
                        skeleton.style.display = 'none';
                        content.style.display = 'block';
                    }, 250); // Jeda 250 milidetik
                }
            });
        </script>
    </body>

</html>
