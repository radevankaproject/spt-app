<!doctype html>
<html lang="id" class="light-style layout-wide customizer-hide" dir="ltr" data-skin="default" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title') - SiPKS</title>
    <meta name="description" content="Sistem Informasi Perjanjian Kerjasama Perparkiran Pekanbaru" />

    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    
    {{-- Page CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}" />

    <style>
        body, .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6, .btn {
            font-family: 'Outfit', sans-serif !important;
        }
        .misc-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }
        .misc-wrapper img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>
        const assetsPath = document.documentElement.getAttribute('data-assets-path');
    </script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="font-size: 8rem; font-weight: 800; color: var(--bs-primary); line-height: 1;">@yield('code')</h1>
            <h3 class="mb-2 fw-bold" style="font-size: 2rem;">@yield('title') ⚠️</h3>
            <p class="mb-4 mx-2 text-muted" style="font-size: 1.15rem; max-width: 600px; line-height: 1.6;">@yield('message')</p>
            
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded-pill mt-3">
                <i class="ti tabler-home-4 me-2"></i>Kembali ke Beranda
            </a>
            
            <div class="mt-5">
                <img src="{{ asset('assets/img/illustrations/misc-error-illustration.png') }}" alt="Error Illustration" class="img-fluid" width="200" style="opacity: 0.8;">
            </div>
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
</body>
</html>
