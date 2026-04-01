<!doctype html>
<html lang="id" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Sistem Informasi Perparkiran') - SPKP</title>
    <meta name="description" content="Sistem Informasi Perparkiran Kota Pekanbaru" />
    <meta name="robots" content="noindex, nofollow" />

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    {{-- ✅ FONTS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}" />

    {{-- ✅ CORE CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

    {{-- ✅ GLOBAL CUSTOM STYLES --}}
    <style>
        /* Paksa Font Outfit ke Seluruh Elemen */
        body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, 
        .card-title, .card-header, .btn, .form-label, .form-control, 
        .menu-link, .select2-container {
            font-family: 'Outfit', sans-serif !important;
        }

        /* Smooth Fade-in Content */
        #main-content {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        #main-content.show-content {
            opacity: 1;
        }

        /* Animasi Skeleton Premium */
        @keyframes pulse { 50% { opacity: 0.5; } }
        .skeleton {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            background-color: var(--bs-gray-300);
            border-radius: 8px;
        }
        .card .skeleton { background-color: var(--bs-gray-200); }
        [data-bs-theme="dark"] .skeleton { background-color: rgba(255, 255, 255, 0.1); }
        [data-bs-theme="dark"] .card .skeleton { background-color: rgba(255, 255, 255, 0.05); }

        .skeleton-text { width: 100%; height: 1rem; margin-bottom: 0.5rem; }
        .skeleton-text-sm { height: 0.75rem; }
        .skeleton-input { width: 100%; height: 54px; }
        .skeleton-button { height: 38px; width: 120px; }
        .skeleton-avatar { width: 45px; height: 45px; border-radius: 50%; }
        .skeleton-avatar-lg { width: 120px; height: 120px; border-radius: 50%; }

        /* Quill Content Reset */
        .ql-editor-content ul { padding-left: 1.5rem !important; list-style-type: disc !important; margin-bottom: 1rem; }
        .ql-editor-content ol { padding-left: 1.5rem !important; list-style-type: decimal !important; margin-bottom: 1rem; }
    </style>

    @stack('styles')

    {{-- ✅ SCRIPT ANTI FOUC (Mencegah Layar Putih Berkedip Saat Dark Mode) --}}
    <script>
        try {
            const storedTheme = localStorage.getItem('spkp-theme') || 'light';
            const activeTheme = storedTheme === 'system' ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') : storedTheme;
            document.documentElement.setAttribute('data-bs-theme', activeTheme);
            document.documentElement.classList.add(activeTheme + '-style');
        } catch (e) {}
    </script>

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>const assetsPath = document.documentElement.getAttribute('data-assets-path');</script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    @include('layouts.partials._alerts')
    
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- 1. Sidebar --}}
            @if (!isset($hideSidebar) || !$hideSidebar)
                @include('layouts.partials._sidebar')
            @endif

            <div class="layout-page">

                {{-- 2. Navbar --}}
                @include('layouts.partials._navbar')

                {{-- 3. Main Content --}}
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        {{-- Skeleton Loader --}}
                        <div id="skeleton-loader">
                            @section('skeleton')
                                @include('layouts.partials._skeleton-default')
                            @show
                        </div>

                        {{-- Konten Asli --}}
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

    {{-- ✅ CORE JS SCRIPTS (Duplicate jQuery Dihapus) --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

    @stack('vendors-js')
    
    <script src="{{ asset('assets/js/main.js') }}"></script>

    {{-- ✅ CUSTOM GLOBAL SCRIPTS --}}
    <script>
        // 1. Logika Smooth Skeleton Loader
        window.addEventListener('load', function() {
            const skeleton = document.getElementById('skeleton-loader');
            const content = document.getElementById('main-content');

            if (skeleton && content) {
                setTimeout(() => {
                    skeleton.style.display = 'none';
                    content.style.display = 'block';
                    // Paksa browser render ulang sebelum kasih class animasi
                    void content.offsetWidth; 
                    content.classList.add('show-content');
                }, 300); // 300ms jeda elegan
            }
        });

        // 2. Modal Changelog Global
        document.addEventListener('DOMContentLoaded', function() {
            const changelogLink = document.getElementById('changelog-link');
            const changelogContent = document.getElementById('changelog-content');

            if(changelogLink && changelogContent) {
                changelogLink.addEventListener('click', function() {
                    changelogContent.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Memuat data versi...</p></div>`;

                    fetch('{{ route("app.versions") }}')
                        .then(response => response.json())
                        .then(data => {
                            let html = '';
                            if (data.length > 0) {
                                html += '<div class="accordion" id="changelogAccordion">';
                                data.forEach((version, index) => {
                                    const releaseDate = new Date(version.release_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                                    const showClass = index === 0 ? 'show' : '';
                                    const collapsedClass = index === 0 ? '' : 'collapsed';
                                    const expanded = index === 0 ? 'true' : 'false';

                                    html += `
                                    <div class="accordion-item card mb-2 border shadow-none">
                                        <h2 class="accordion-header" id="heading-${version.id}">
                                            <button type="button" class="accordion-button ${collapsedClass}" data-bs-toggle="collapse" data-bs-target="#collapse-${version.id}" aria-expanded="${expanded}" aria-controls="collapse-${version.id}">
                                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                    <span class="fw-bold text-primary fs-6">Versi ${version.version}</span>
                                                    <small class="badge bg-label-secondary rounded-pill">${releaseDate}</small>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-${version.id}" class="accordion-collapse collapse ${showClass}" aria-labelledby="heading-${version.id}" data-bs-parent="#changelogAccordion">
                                            <div class="accordion-body pt-3 text-dark ql-editor-content" style="font-size: 0.95rem;">
                                                ${version.changelog}
                                            </div>
                                        </div>
                                    </div>`;
                                });
                                html += '</div>';
                            } else {
                                html = '<div class="text-center py-4"><i class="ri-file-info-line ri-3x text-muted opacity-50 mb-2"></i><p class="text-muted">Belum ada catatan versi.</p></div>';
                            }
                            changelogContent.innerHTML = html;
                        })
                        .catch(() => {
                            changelogContent.innerHTML = '<div class="text-center py-4"><i class="ri-error-warning-line ri-3x text-danger opacity-50 mb-2"></i><p class="text-danger">Gagal memuat histori versi.</p></div>';
                        });
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>