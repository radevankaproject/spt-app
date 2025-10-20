<!doctype html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-skin="default"
    data-bs-theme="light">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <meta name="robots" content="noindex, nofollow" />
        <title>@yield('title', 'Selamat Datang') - SiPKS</title>
        <meta name="description" content="Sistem Informasi Perjanjian Kerjasama Perparkiran Pekanbaru" />

        <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

        {{-- Fonts & Icons --}}
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" /> -->
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

        {{-- Core CSS --}}
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

        <style>
            body,
            .h1,
            .h2,
            .h3,
            .h4,
            .h5,
            .h6,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            .card-title,
            .card-header,
            .btn,
            .form-label,
            .form-control {
                font-family: 'Outfit', sans-serif !important;
            }
        </style>


        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <script>
            const assetsPath = document.documentElement.getAttribute('data-assets-path');
        </script>
        <script src="{{ asset('assets/js/config.js') }}"></script>
    </head>

    <body>
        {{-- ✅ Layout Sederhana untuk Menengahkan Form --}}
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">

                @yield('content')

            </div>
        </div>

        {{-- Core JS --}}
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        @stack('scripts')
    </body>

</html>
