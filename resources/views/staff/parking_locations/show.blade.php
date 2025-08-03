@extends('layouts.app')

@section('title', 'Detail Lokasi Parkir: ' . $parkingLocation->name)

@push('styles')
    {{-- CSS untuk Leaflet Map --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 450px;
            border-radius: .5rem;
        }

        .leaflet-popup-content-wrapper {
            border-radius: .5rem;
        }

        .leaflet-popup-content {
            margin: 15px;
        }

        .leaflet-popup-content .popup-image {
            max-width: 150px;
            height: auto;
            border-radius: .375rem;
            margin-bottom: 10px;
        }

        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: .5rem;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Lokasi Parkir /</span> Detail</h4>
        <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar</a>
    </div>

    <div class="row g-6">
        <!-- Kolom Kiri: Informasi Detail -->
        <div class="col-lg-5">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Lokasi</h5>
                    @if ($parkingLocation->status == 'tersedia')
                        <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}"
                            class="btn btn-sm btn-outline-primary">Edit</a>
                    @endif
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><strong>Nama Lokasi:</strong>
                            <span>{{ $parkingLocation->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Ruas Jalan:</strong>
                            <span>{{ $parkingLocation->roadSection->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Zona:</strong>
                            <span>{{ $parkingLocation->roadSection->zone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Setoran/Hari:</strong> <span
                                class="fw-bold text-success">Rp
                                {{ number_format($parkingLocation->daily_deposit, 0, ',', '.') }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Status:</strong>
                            @if ($parkingLocation->status == 'tersedia')
                                <span class="badge bg-label-success">Tersedia</span>
                            @else
                                <span class="badge bg-label-danger">Tidak Tersedia</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Perjanjian Terhubung</h5>
                </div>
                <div class="card-body">
                    @if ($activeAgreement)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><strong>No. PKS:</strong> <a
                                    href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}">{{ $activeAgreement->agreement_number }}</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Koordinator:</strong>
                                <span>{{ $activeAgreement->fieldCoordinator->user->name ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Pimpinan:</strong>
                                <span>{{ $activeAgreement->leader->user->name ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Status PKS:</strong> <span
                                    class="badge bg-label-primary">{{ Str::title($activeAgreement->status) }}</span></li>
                        </ul>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="ri-information-line me-2"></i> Lokasi ini sedang tidak terhubung dengan perjanjian
                            aktif manapun.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Peta & Foto -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Peta Lokasi</h5>
                </div>
                <div class="card-body">
                    @if ($parkingLocation->latitude && $parkingLocation->longitude)
                        <div id="map"></div>
                        <div class="d-flex justify-content-between mt-3">
                            <small><strong>Latitude:</strong> <span
                                    id="lat-display">{{ $parkingLocation->latitude }}</span></small>
                            <small><strong>Longitude:</strong> <span
                                    id="lng-display">{{ $parkingLocation->longitude }}</span></small>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="ri-map-pin-line me-2"></i> Koordinat lokasi belum diatur.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Baris Bawah: Dokumen Pendukung -->
        @if ($parkingLocation->proposal_document || $parkingLocation->official_report_document)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            @if ($parkingLocation->proposal_document)
                                <div class="col-md-6">
                                    <h6>PDF Pengajuan</h6>
                                    <embed src="{{ asset('storage/' . $parkingLocation->proposal_document) }}"
                                        type="application/pdf" class="pdf-viewer">
                                </div>
                            @endif
                            @if ($parkingLocation->official_report_document)
                                <div class="col-md-6">
                                    <h6>PDF Berita Acara</h6>
                                    <embed src="{{ asset('storage/' . $parkingLocation->official_report_document) }}"
                                        type="application/pdf" class="pdf-viewer">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {{-- JS untuk Leaflet Map --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah elemen map ada dan data koordinat tersedia
            @if ($parkingLocation->latitude && $parkingLocation->longitude)
                // Inisialisasi koordinat dari data PHP
                let lat = {{ $parkingLocation->latitude }};
                let lng = {{ $parkingLocation->longitude }};

                // Inisialisasi Peta
                const map = L.map('map').setView([lat, lng], 16); // Angka 16 adalah level zoom

                // Tambahkan Tile Layer (penyedia peta, contoh: OpenStreetMap)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Buat konten untuk Popup
                const popupContent = `
                    <div class="text-center">
                        @if ($parkingLocation->image)
                            <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="{{ $parkingLocation->name }}" class="popup-image">
                        @endif
                        <h6 class="mb-1">{{ $parkingLocation->name }}</h6>
                        <p class="mb-0" style="font-size: 0.8rem;">${lat}, ${lng}</p>
                    </div>
                `;

                // Tambahkan Marker yang bisa digeser
                const marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map)
                    .bindPopup(popupContent)
                    .openPopup();

                // Event listener saat marker selesai digeser
                marker.on('dragend', function(event) {
                    const position = marker.getLatLng();
                    document.getElementById('lat-display').textContent = position.lat.toFixed(6);
                    document.getElementById('lng-display').textContent = position.lng.toFixed(6);

                    // Update popup content with new coordinates
                    marker.setPopupContent(`
                        <div class="text-center">
                            @if ($parkingLocation->image)
                                <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="{{ $parkingLocation->name }}" class="popup-image">
                            @endif
                            <h6 class="mb-1">{{ $parkingLocation->name }}</h6>
                            <p class="mb-0" style="font-size: 0.8rem;">${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}</p>
                        </div>
                    `);
                });
            @endif
        });
    </script>
@endpush
