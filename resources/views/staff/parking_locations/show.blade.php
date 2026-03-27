@extends('layouts.app')

@section('title', 'Detail Lokasi Parkir: ' . $parkingLocation->name)

@section('skeleton')
    @include('layouts.partials._skeleton-parking-locations-show')
@endsection

@push('styles')
    {{-- CSS untuk Leaflet Map & Perfect Scrollbar --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <style>
        /* Modern Map Styling */
        #map {
            height: 350px;
            border-radius: 0.75rem;
            z-index: 1;
        }
        .leaflet-popup-content-wrapper { border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .leaflet-popup-content { margin: 12px; text-align: center; }
        .leaflet-popup-content .popup-image {
            width: 100%; max-height: 120px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 10px;
        }

        /* PDF Viewer Styling */
        .pdf-viewer-wrapper {
            position: relative; padding-bottom: 56.25%; /* 16:9 Aspect Ratio */ height: 0; overflow: hidden; border-radius: 0.75rem; border: 1px solid #e7e7e8;
        }
        .pdf-viewer-wrapper iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        }

        /* ✅ FIX: Custom Scrollbar for Timeline agar tidak terpotong */
        .timeline-scrollable {
            max-height: 700px;
            position: relative;
            overflow: hidden;
            padding-right: 15px;
            padding-bottom: 2rem;
            padding-left: 15px; /* <--- INI OBATNYA WAK */
        }

        /* ✅ BOX HISTORY CHANGES (Premium Diff View) */
        .history-changes-box {
            background-color: rgba(105, 108, 255, 0.04);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            border-left: 3px solid #696cff;
        }

        /* Hero Image Location */
        .location-hero-img {
            width: 120px; height: 120px; object-fit: cover; border-radius: 1rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@php
    // Helper function untuk merapikan text di riwayat perubahan
    function formatHistoryValue($key, $value) {
        if ($value === null || $value === '') return '-';
        if (in_array($key, ['image', 'proposal_document', 'official_report_document'])) {
            return '[File Terlampir]';
        }
        if ($key == 'daily_deposit') {
            return 'Rp ' . number_format((float)$value, 0, ',', '.');
        }
        return Str::limit($value, 30);
    }
@endphp

@section('content')
    {{-- Header Premium --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-lg bg-label-primary rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm">
                <i class="ri icon-base ri-map-pin-user-line ri-30px"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">{{ $parkingLocation->name }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}">Lokasi Parkir</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">
                <i class="ri icon-base ri-arrow-left-line me-1"></i> Kembali
            </a>
            <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-primary shadow-sm">
                <i class="ri icon-base ri-pencil-line me-1"></i> Edit Lokasi
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI (Detail, PKS, Peta, Dokumen) - 8 Kolom --}}
        <div class="col-xl-8 col-lg-7">

            {{-- 1. Kartu Informasi Utama --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">

                    {{-- ✅ Pengecekan Gambar: Jika ada tampilkan img, jika tidak tampilkan kotak Icon --}}
                    @if ($parkingLocation->image)
                        <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="{{ $parkingLocation->name }}" class="location-hero-img">
                    @else
                        <div class="location-hero-img bg-label-secondary d-flex align-items-center justify-content-center">
                            <i class="ri icon-base ri-image-line text-secondary" style="font-size: 3.5rem;"></i>
                        </div>
                    @endif

                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1 text-dark">Informasi Lokasi</h5>
                                <p class="text-muted mb-0 small">Kode ID: #LOC-{{ str_pad($parkingLocation->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>

                            {{-- ✅ FIX: Menggunakan class badge bawaan template agar warna pas --}}
                            @if ($parkingLocation->status == 'tersedia')
                                <span class="badge bg-label-success fw-bold px-3 py-2 rounded-pill"><i class="ri-check-line me-1"></i> TERSEDIA</span>
                            @else
                                <span class="badge bg-label-danger fw-bold px-3 py-2 rounded-pill"><i class="ri-lock-line me-1"></i> TERIKAT PKS</span>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2"><span class="avatar-initial rounded bg-label-info"><i class="ri icon-base ri-route-line"></i></span></div>
                                    <div><small class="text-muted d-block">Ruas Jalan</small><span class="fw-medium">{{ $parkingLocation->roadSection->name ?? '-' }}</span></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2"><span class="avatar-initial rounded bg-label-warning"><i class="ri icon-base ri-focus-2-line"></i></span></div>
                                    <div><small class="text-muted d-block">Zona Wilayah</small><span class="fw-medium">{{ $parkingLocation->roadSection->zone ?? '-' }}</span></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2"><span class="avatar-initial rounded bg-label-success"><i class="ri icon-base ri-money-dollar-circle-line"></i></span></div>
                                    <div><small class="text-muted d-block">Setoran Harian</small><span class="fw-bold text-success">Rp {{ number_format($parkingLocation->daily_deposit, 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Kartu Perjanjian Kerja Sama (PKS) --}}
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0"><i class="ri icon-base ri-file-paper-2-line text-primary me-2"></i> Perjanjian Aktif Terhubung</h6>
                </div>
                <div class="card-body pt-4">
                    @if ($activeAgreement)
                        @php
                            $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';
                            $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                ? asset($activeAgreement->fieldCoordinator->user->img)
                                : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=48&rounded=true&bold=true";
                        @endphp

                        {{-- ✅ Wrapper <div> diubah menjadi <a> agar seluruh kotak bisa diklik --}}
                        <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}"
                           class="d-flex align-items-center justify-content-between p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 text-decoration-none"
                           style="transition: all 0.2s ease;"
                           onmouseover="this.classList.add('shadow-sm'); this.style.transform='scale(1.01)';"
                           onmouseout="this.classList.remove('shadow-sm'); this.style.transform='scale(1)';">

                            <div class="d-flex align-items-center gap-3">
                                {{-- ✅ Avatar diganti dengan foto profil Korlap / UI Avatar --}}
                                <div class="avatar avatar-md">
                                    <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                </div>
                                <div>
                                    <span class="fw-bold text-primary mb-0 d-block fs-6">{{ $activeAgreement->agreement_number }}</span>
                                    <small class="text-muted">Korlap: <span class="fw-medium text-dark">{{ $cName }}</span></small>
                                </div>
                            </div>
                            <div class="text-end">
                                @php
                                    $pksBadge = 'bg-secondary';
                                    $pksLabel = 'Tidak Diketahui';

                                    if ($activeAgreement->status == 'active') {
                                        $pksBadge = 'bg-success';
                                        $pksLabel = 'Aktif';
                                    } elseif ($activeAgreement->status == 'pending_renewal') {
                                        $pksBadge = 'bg-warning';
                                        $pksLabel = 'Menunggu Perpanjangan';
                                    } elseif ($activeAgreement->status == 'expired') {
                                        $pksBadge = 'bg-danger';
                                        $pksLabel = 'Kedaluwarsa';
                                    } elseif ($activeAgreement->status == 'terminated') {
                                        $pksBadge = 'bg-dark';
                                        $pksLabel = 'Diputus';
                                    }
                                @endphp
                                <span class="badge {{ $pksBadge }} mb-1 shadow-sm">Status: {{ $pksLabel }}</span><br>
                                <small class="text-muted">Pimpinan: <span class="text-dark">{{ $activeAgreement->leader->user->name ?? 'N/A' }}</span></small>
                            </div>
                        </a>
                    @else
                        <div class="text-center py-4">
                            <i class="ri icon-base ri-link-unlink-m text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Lokasi ini <strong>Tersedia</strong> dan belum terikat PKS aktif manapun.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Kartu Peta Koordinat --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0"><i class="ri icon-base ri-map-2-line text-danger me-2"></i> Peta Koordinat Lokasi</h6>
                </div>
                <div class="card-body pt-4">
                    @if ($parkingLocation->latitude && $parkingLocation->longitude)
                        <div id="map" class="shadow-sm"></div>
                        <div class="d-flex justify-content-center gap-4 mt-3 bg-light rounded-pill py-2">
                            <span class="text-muted small"><i class="ri-map-pin-line text-danger"></i> Lat: <strong class="text-dark" id="lat-display">{{ $parkingLocation->latitude }}</strong></span>
                            <span class="text-muted small"><i class="ri-map-pin-line text-danger"></i> Lng: <strong class="text-dark" id="lng-display">{{ $parkingLocation->longitude }}</strong></span>
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded-3 border-dashed">
                            <i class="ri icon-base ri-map-pin-add-line text-muted mb-2" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mb-0">Titik koordinat belum ditambahkan di peta.</p>
                            <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-sm btn-outline-secondary mt-3">Set Koordinat</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4. Kartu Dokumen PDF --}}
            @if ($parkingLocation->proposal_document || $parkingLocation->official_report_document)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="card-title fw-bold mb-0"><i class="ri icon-base ri-folder-zip-line text-warning me-2"></i> Arsip Dokumen Digital</h6>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-4">
                            @if ($parkingLocation->proposal_document)
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-dark"><i class="ri-file-pdf-2-line text-danger"></i> PDF Pengajuan</span>
                                        <a href="{{ asset('storage/' . $parkingLocation->proposal_document) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">Buka Penuh</a>
                                    </div>
                                    <div class="pdf-viewer-wrapper shadow-sm">
                                        <iframe src="{{ asset('storage/' . $parkingLocation->proposal_document) }}#toolbar=0" frameborder="0"></iframe>
                                    </div>
                                </div>
                            @endif
                            @if ($parkingLocation->official_report_document)
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-dark"><i class="ri-file-pdf-2-line text-danger"></i> PDF Berita Acara</span>
                                        <a href="{{ asset('storage/' . $parkingLocation->official_report_document) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">Buka Penuh</a>
                                    </div>
                                    <div class="pdf-viewer-wrapper shadow-sm">
                                        <iframe src="{{ asset('storage/' . $parkingLocation->official_report_document) }}#toolbar=0" frameborder="0"></iframe>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN (Timeline History) - 4 Kolom --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0"><i class="ri icon-base ri-history-line text-info me-2"></i> Riwayat Aktivitas</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="timeline-scrollable" id="historyTimeline">
                        <ul class="timeline timeline-dashed mt-3 pb-4">

                            @forelse($parkingLocation->histories as $history)
                                @php
                                    $color = 'primary';
                                    $icon = 'ri-record-circle-fill';
                                    if($history->action == 'created') { $color = 'success'; $icon = 'ri-add-circle-fill'; }
                                    elseif($history->action == 'updated') { $color = 'warning'; $icon = 'ri-edit-circle-fill'; }
                                    elseif($history->action == 'owner_changed') { $color = 'info'; $icon = 'ri-exchange-box-fill'; }
                                    elseif($history->action == 'deleted') { $color = 'danger'; $icon = 'ri-delete-bin-fill'; }
                                @endphp

                                <li class="timeline-item timeline-item-{{ $color }} mb-4">
                                    <span class="timeline-indicator timeline-indicator-{{ $color }} bg-white">
                                        <i class="ri icon-base {{ $icon }}"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-{{ $color }}">{{ strtoupper(str_replace('_', ' ', $history->action)) }}</h6>
                                            <small class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-dark" style="font-size: 0.9rem;">{{ $history->description }}</p>

                                        {{-- ✅ FIX: Memunculkan Rincian Data Before-After --}}
                                        @if($history->action == 'updated' && !empty($history->new_values))
                                            <div class="history-changes-box">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($history->new_values as $key => $newValue)
                                                        @php
                                                            $oldValue = $history->old_values[$key] ?? null;
                                                            $displayOld = formatHistoryValue($key, $oldValue);
                                                            $displayNew = formatHistoryValue($key, $newValue);
                                                            $fieldName = ucwords(str_replace('_', ' ', $key));
                                                        @endphp
                                                        <li class="mb-1 text-truncate" title="{{ $displayOld }} ➔ {{ $displayNew }}">
                                                            <span class="fw-semibold text-muted">{{ $fieldName }}:</span>
                                                            <span class="text-danger text-decoration-line-through mx-1">{{ $displayOld }}</span>
                                                            <i class="ri icon-base ri-arrow-right-line ri-22px text-primary mx-1"></i>
                                                            <span class="text-warning fw-medium">{{ $displayNew }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center mt-3 bg-light rounded-pill px-2 py-1 d-inline-flex">
                                            @php
                                                $uName = $history->user->name ?? 'Sistem Server';
                                                $uAvatar = ($history->user && $history->user->img && file_exists(public_path('storage/' . $history->user->img)))
                                                    ? asset('storage/' . $history->user->img)
                                                    : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=24&rounded=true&bold=true";
                                            @endphp
                                            <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle me-2" width="20" height="20">
                                            <small class="fw-medium text-muted">{{ $uName }}</small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <div class="text-center py-5">
                                    <i class="ri-history-line text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat aktivitas yang tercatat.</p>
                                </div>
                            @endforelse

                            {{-- Titik awal data (dummy end indicator) --}}
                            @if($parkingLocation->histories->isNotEmpty())
                                <li class="timeline-item timeline-item-transparent border-0">
                                    <span class="timeline-indicator timeline-indicator-secondary"><i class="ri-checkbox-blank-circle-line"></i></span>
                                    <div class="timeline-event pb-0">
                                        <small class="text-muted text-uppercase fw-bold">Awal Rekaman</small>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('vendors-js')
    {{-- JS Perfect Scrollbar untuk Timeline --}}
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
@endpush

@push('scripts')
    {{-- JS untuk Leaflet Map --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Perfect Scrollbar untuk Timeline History
            const timelineEl = document.getElementById('historyTimeline');
            if (timelineEl) {
                new PerfectScrollbar(timelineEl, {
                    wheelPropagation: false,
                    suppressScrollX: true
                });
            }

            // 2. Logic Peta (Map)
            @if ($parkingLocation->latitude && $parkingLocation->longitude)
                let lat = {{ $parkingLocation->latitude }};
                let lng = {{ $parkingLocation->longitude }};

                // Peta tidak bisa di-drag di halaman detail, hanya untuk view
                const map = L.map('map', {
                    scrollWheelZoom: false,
                    dragging: false
                }).setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                const popupContent = `
                    <div class="text-center">
                        @if ($parkingLocation->image)
                            <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="Lokasi" class="popup-image shadow-sm">
                        @else
                            <div class="bg-label-secondary rounded-3 d-flex align-items-center justify-content-center mb-2 mx-auto" style="width: 100%; height: 100px;">
                                <i class="ri-image-line text-secondary" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif
                        <h6 class="mb-1 fw-bold text-primary mt-2">{{ $parkingLocation->name }}</h6>
                        <span class="badge bg-label-warning">${lat}, ${lng}</span>
                    </div>
                `;

                L.marker([lat, lng]).addTo(map).bindPopup(popupContent).openPopup();
            @endif
        });
    </script>
@endpush
