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
        {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
            rel="stylesheet" /> --}}

        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

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
            .form-control,
            .menu-link,
            .select2-container {
                font-family: 'Outfit', sans-serif !important;
            }
        </style>

        @stack('styles')

        <style>
            /* Animasi berkedip pelan untuk efek loading */
            @keyframes pulse {
                50% {
                    opacity: 0.6;
                }
            }

            /* Gaya dasar untuk semua elemen skeleton */
            .skeleton {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                background-color: #e5e7eb;
                /* Warna abu-abu dasar yang terlihat */
                border-radius: 6px;
            }

            /* Khusus untuk skeleton-card, kita tidak ingin card-nya ikut berkedip */
            .card .skeleton {
                background-color: #f3f4f6;
                /* Abu-abu lebih terang di dalam card */
            }

            /* Gaya untuk teks (garis horizontal) */
            .skeleton-text {
                width: 100%;
                height: 1rem;
            }

            .skeleton-text-sm {
                height: 0.75rem;
            }

            /* Gaya untuk input form (ini yang kamu mau) */
            .skeleton-input {
                width: 100%;
                height: 54px;
                /* Sesuaikan dengan tinggi form-floating */
            }

            /* Gaya untuk elemen lainnya */
            .skeleton-button {
                height: 38px;
            }

            .skeleton-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }

            .skeleton-avatar-lg {
                width: 120px;
                height: 120px;
                border-radius: 50%;
            }

            .skeleton-badge {
                width: 70px;
                height: 24px;
                border-radius: 12px;
            }

            .skeleton-icon {
                width: 22px;
                height: 22px;
            }
        </style>

        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

        <script>
            // Mendefinisikan path root untuk aset agar main.js tidak salah jalan
            const assetsPath = document.documentElement.getAttribute('data-assets-path');
        </script>

        {{-- <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script> --}}
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

                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">

                            <div id="skeleton-loader">
                                @section('skeleton')
                                    @include('layouts.partials._skeleton-default')
                                @show
                            </div>

                            <div id="main-content" style="display: none;">
                                @yield('content')
                            </div>

                        </div>

                        @include('layouts.partials._footer')

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>

            <div class="layout-overlay layout-menu-toggle"></div>
            <div class="drag-target"></div>
        </div>
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

        @stack('vendors-js')

        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
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

            document.addEventListener('DOMContentLoaded', function() {
                const changelogLink = document.getElementById('changelog-link');
                const changelogContent = document.getElementById('changelog-content');
                const changelogModal = new bootstrap.Modal(document.getElementById('changelogModal'));

                changelogLink.addEventListener('click', function() {
                    // Tampilkan loading spinner
                    changelogContent.innerHTML =
                        `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

                    // Ambil data dari server
                    fetch('{{ route('app.versions') }}')
                        .then(response => response.json())
                        .then(data => {
                            let html = '';
                            if (data.length > 0) {
                                data.forEach(version => {
                                    // Format tanggal
                                    const releaseDate = new Date(version.release_date)
                                        .toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        });

                                    // Ubah changelog (asumsi Markdown sederhana) menjadi list HTML
                                    const changelogItems = version.changelog.split('\\n').map(
                                        item => {
                                            if (item.trim().startsWith('- ')) {
                                                return `<li>${item.trim().substring(2)}</li>`;
                                            }
                                            return '';
                                        }).join('');

                                    html += `
                                    <div class="mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-1"><strong>Versi ${version.version}</strong></h5>
                                            <small class="text-muted">${releaseDate}</small>
                                        </div>
                                        <ul class="list-unstyled mt-2 mb-0 ps-3">
                                            ${changelogItems}
                                        </ul>
                                    </div>
                                `;
                                });
                            } else {
                                html =
                                    '<p class="text-center">Belum ada catatan versi yang ditambahkan.</p>';
                            }
                            changelogContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error fetching changelog:', error);
                            changelogContent.innerHTML =
                                '<p class="text-center text-danger">Gagal memuat histori. Silakan coba lagi.</p>';
                        });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const logoutButton = document.getElementById('logout-button');
                const logoutForm = document.getElementById('logout-form');

                if (logoutButton && logoutForm) {
                    logoutButton.addEventListener('click', function(event) {
                        event.preventDefault(); // Mencegah aksi default tombol

                        Swal.fire({
                            title: 'Anda yakin ingin logout?',
                            text: "Sesi Anda akan diakhiri.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Logout!',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'btn btn-danger me-3',
                                cancelButton: 'btn btn-label-secondary'
                            },
                            buttonsStyling: false
                        }).then(function(result) {
                            if (result.value) {
                                // Jika user klik "Ya", submit form logout
                                logoutForm.submit();
                            }
                        });
                    });
                }
            });
        </script>
        @stack('scripts')
    </body>

</html>
