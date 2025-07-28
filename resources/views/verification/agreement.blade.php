<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verifikasi Dokumen PKS</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f4f7f6;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px 0;
                color: #333;
            }

            .container {
                text-align: center;
                padding: 40px;
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                width: 90%;
                max-width: 600px;
                /* Lebarkan sedikit untuk tabel */
            }

            .icon-wrapper {
                margin-bottom: 20px;
            }

            .checkmark__circle,
            .cross__circle {
                stroke-dasharray: 166;
                stroke-dashoffset: 166;
                stroke-width: 3;
                stroke-miterlimit: 10;
                stroke: #28a745;
                fill: none;
                animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
            }

            .cross__circle {
                stroke: #ea5455;
            }

            .checkmark {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: block;
                stroke-width: 3;
                stroke: #fff;
                stroke-miterlimit: 10;
                margin: 10% auto;
                box-shadow: inset 0px 0px 0px #28a745;
                animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
            }

            .cross {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: block;
                stroke-width: 3;
                stroke: #fff;
                stroke-miterlimit: 10;
                margin: 10% auto;
                box-shadow: inset 0px 0px 0px #ea5455;
                animation: fill-red .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
            }

            .checkmark__check {
                transform-origin: 50% 50%;
                stroke-dasharray: 48;
                stroke-dashoffset: 48;
                animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
            }

            .cross__line {
                transform-origin: 50% 50%;
                stroke-dasharray: 48;
                stroke-dashoffset: 48;
                animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
            }

            @keyframes stroke {
                100% {
                    stroke-dashoffset: 0;
                }
            }

            @keyframes scale {

                0%,
                100% {
                    transform: none;
                }

                50% {
                    transform: scale3d(1.1, 1.1, 1);
                }
            }

            @keyframes fill {
                100% {
                    box-shadow: inset 0px 0px 0px 40px #28a745;
                }
            }

            @keyframes fill-red {
                100% {
                    box-shadow: inset 0px 0px 0px 40px #ea5455;
                }
            }

            h2 {
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 10px;
            }

            .success h2 {
                color: #28a745;
            }

            .error h2 {
                color: #ea5455;
            }

            p {
                font-size: 16px;
                line-height: 1.6;
                margin: 0;
            }

            strong {
                font-weight: 600;
            }

            hr {
                border: 0;
                border-top: 1px solid #e9ecef;
                margin: 25px 0;
            }

            /* ✅ CSS BARU UNTUK TABEL LOKASI */
            .locations-wrapper {
                margin-top: 25px;
                text-align: left;
            }

            .toggle-button {
                background-color: #f1f1f1;
                border: 1px solid #ddd;
                padding: 10px 15px;
                width: 100%;
                text-align: left;
                cursor: pointer;
                border-radius: 8px;
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                font-weight: 600;
                display: flex;
                justify-content: space-between;
                align-items: center;
                transition: background-color 0.3s;
            }

            .toggle-button:hover {
                background-color: #e9e9e9;
            }

            .locations-table-container {
                display: none;
                /* Sembunyikan secara default */
                margin-top: 15px;
                border: 1px solid #e9ecef;
                border-radius: 8px;
            }

            .table-responsive {
                max-height: 300px;
                overflow-y: auto;
                border-radius: 8px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table td {
                padding: 12px 15px;
                border-bottom: 1px solid #e9ecef;
                vertical-align: middle;
            }

            .table tr:last-child td {
                border-bottom: none;
            }

            .badge {
                display: inline-block;
                padding: .35em .65em;
                font-size: .75em;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25rem;
            }

            .bg-label-dark {
                background-color: #e8e8e8;
                color: #5d596c;
            }

            .text-primary {
                color: #696cff !important;
            }

            .text-muted {
                color: #a1acb8 !important;
            }
        </style>
    </head>

    <body>
        <div class="container">
            @if ($agreement)
                <div class="success">
                    <div class="icon-wrapper">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                        </svg>
                    </div>
                    <h2>Dokumen Terverifikasi</h2>
                    <p>PKS dengan nomor: <strong>{{ $agreement->agreement_number }}</strong> terverifikasi sebagai
                        dokumen asli.</p>
                    @if ($agreement->status == 'active')
                        <p>
                            Status PKS: <strong style="color: #4CAF50">Aktif</strong> sampai dengan
                            <strong>{{ $agreement->end_date->translatedFormat('d F Y') }}</strong>.
                        </p>
                    @endif

                    @if ($agreement->status == 'expired')
                        <p>
                            Status PKS: <strong style="color: #F44336">Expired</strong> Pada
                            <strong>{{ $agreement->end_date->translatedFormat('d F Y') }}</strong>.
                        </p>
                    @endif

                    @if ($agreement->status == 'pending_renewal')
                        <p>
                            Status PKS: <strong style="color: #ff4800">Pending</strong> Berakhir Pada
                            <strong>{{ $agreement->end_date->translatedFormat('d F Y') }}</strong>.
                        </p>
                    @endif

                    @if ($agreement->status == 'terminated')
                        <p>
                            Status PKS: <strong style="color: #F44336">di Putus</strong> Pada
                            <strong>{{ $agreement->updated_at->translatedFormat('d F Y') }}</strong>.
                        </p>
                    @endif

                    {{-- ✅ KODE TABEL & TOMBOL DITAMBAHKAN DI SINI --}}
                    <div class="locations-wrapper">
                        <button type="button" class="toggle-button" id="toggle-locations-btn">
                            <span>Daftar Lokasi Parkir ({{ $agreement->activeParkingLocations->count() }})</span>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div class="locations-table-container" id="locations-table">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @forelse ($agreement->activeParkingLocations as $location)
                                            <tr>
                                                <td><i
                                                        class="ri-map-pin-2-fill text-primary me-2"></i>{{ $location->name }}
                                                </td>
                                                <td><span
                                                        class="text-muted">{{ $location->roadSection->name ?? 'N/A' }}</span>
                                                </td>
                                                <td><span
                                                        class="badge bg-label-dark rounded-pill">{{ $location->roadSection->zone ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">Tidak ada lokasi
                                                    parkir aktif yang terhubung.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="error">
                    <div class="icon-wrapper">
                        <svg class="cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="cross__circle" cx="26" cy="26" r="25" fill="none" />
                            <path class="cross__line" fill="none" d="M16 16 36 36 M36 16 16 36" />
                        </svg>
                    </div>
                    <h2>Verifikasi Gagal</h2>
                    <p>Dokumen PKS dengan kode ini <strong>tidak ditemukan</strong> di dalam database kami.</p>
                    <p>Dokumen ini kemungkinan tidak sah atau telah diubah.</p>
                </div>
            @endif
        </div>

        {{-- ✅ JAVASCRIPT BARU UNTUK TOGGLE --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('toggle-locations-btn');
                const locationsTable = document.getElementById('locations-table');

                if (toggleBtn && locationsTable) {
                    toggleBtn.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        if (locationsTable.style.display === 'block') {
                            locationsTable.style.display = 'none';
                            icon.classList.remove('ri-arrow-up-s-line');
                            icon.classList.add('ri-arrow-down-s-line');
                        } else {
                            locationsTable.style.display = 'block';
                            icon.classList.remove('ri-arrow-down-s-line');
                            icon.classList.add('ri-arrow-up-s-line');
                        }
                    });
                }
            });
        </script>
    </body>

</html>
