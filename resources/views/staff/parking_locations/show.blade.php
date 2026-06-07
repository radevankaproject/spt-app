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
            height: 450px;
            border-radius: 0 0 0.5rem 0.5rem;
            z-index: 1;
            width: 100%;
        }
        .leaflet-popup-content-wrapper { border-radius: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .leaflet-popup-content { margin: 12px; text-align: center; }
        .leaflet-popup-content .popup-image {
            width: 100%; max-height: 120px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 10px;
        }

        /* PDF Viewer Styling */
        .pdf-viewer-wrapper {
            position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 0.5rem; border: 1px solid #e7e7e8;
        }
        .pdf-viewer-wrapper iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        }

        /* Timeline Custom */
        .timeline-scrollable {
            max-height: 600px;
            position: relative;
            overflow: hidden;
            padding-right: 15px;
            padding-bottom: 1rem;
            padding-left: 15px;
        }

        .history-changes-box {
            background-color: rgba(105, 108, 255, 0.04);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            border-left: 3px solid #696cff;
        }
        
        .user-profile-img {
            width: 140px; height: 140px; object-fit: cover; border-radius: 0.5rem; border: 4px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        
        .nav-align-top .nav-tabs {
            border-bottom: 1px solid #d9dee3;
        }
    </style>
@endpush

@php
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
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-lg bg-label-primary rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm">
                    <i class="ri icon-base ri-map-pin-user-line ri-30px"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Detail Lokasi: {{ $parkingLocation->name }}</h4>
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
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- User Sidebar / Location Detail --}}
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="user-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                        @if ($parkingLocation->image)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#imageModal" class="d-block" title="Klik untuk memperbesar gambar">
                                <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="{{ $parkingLocation->name }}" class="user-profile-img mt-3 mb-3" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        @else
                            <div class="bg-label-secondary d-flex align-items-center justify-content-center mt-3 mb-3 rounded" style="width: 140px; height: 140px;">
                                <i class="ri icon-base ri-image-line text-secondary" style="font-size: 4.5rem;"></i>
                            </div>
                        @endif
                        <div class="user-info text-center">
                            <h5 class="mb-2">{{ $parkingLocation->name }}</h5>
                            @if ($parkingLocation->status == 'tersedia')
                                <span class="badge bg-label-success rounded-pill px-3"><i class="ri icon-base ri-check-line me-1"></i> TERSEDIA</span>
                            @else
                                <span class="badge bg-label-danger rounded-pill px-3"><i class="ri icon-base ri-lock-line me-1"></i> TERIKAT PKS</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center flex-wrap mt-4 pt-3 pb-4 border-bottom">
                    <div class="d-flex align-items-start mt-3 gap-3">
                        <span class="badge bg-label-success p-3 rounded"><i class='ri icon-base ri-money-dollar-circle-line ri-24px'></i></span>
                        <div>
                            <p class="mb-0 fw-medium fs-5">Rp {{ number_format($parkingLocation->daily_deposit, 0, ',', '.') }}</p>
                            <small class="text-muted">Setoran Harian</small>
                        </div>
                    </div>
                </div>

                <h6 class="pb-3 border-bottom mt-4 mb-3 text-uppercase fw-bold text-muted">Detail Informasi</h6>
                <div class="info-container">
                    <ul class="list-unstyled mb-4">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri icon-base ri-qr-code-line text-primary me-2 ri-20px"></i>
                            <span class="fw-medium text-heading me-2">Kode ID:</span>
                            <span>#LOC-{{ str_pad($parkingLocation->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri icon-base ri-route-line text-info me-2 ri-20px"></i>
                            <span class="fw-medium text-heading me-2">Ruas Jalan:</span>
                            <span>{{ $parkingLocation->roadSection->name ?? '-' }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri icon-base ri-focus-2-line text-warning me-2 ri-20px"></i>
                            <span class="fw-medium text-heading me-2">Zona:</span>
                            <span>{{ $parkingLocation->roadSection->zone ?? '-' }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri icon-base ri-map-pin-line text-danger me-2 ri-20px"></i>
                            <span class="fw-medium text-heading me-2">Koordinat:</span>
                            @if($parkingLocation->latitude && $parkingLocation->longitude)
                                <span class="text-truncate">{{ $parkingLocation->latitude }}, {{ $parkingLocation->longitude }}</span>
                            @else
                                <span class="text-muted">Belum Diatur</span>
                            @endif
                        </li>
                    </ul>
                    
                    @if(Auth::user()->role !== 'leader')
                    <div class="d-flex justify-content-center pt-3">
                        <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-primary w-100 shadow-sm">
                            <i class="ri icon-base ri-pencil-line me-2"></i> Edit Data Lokasi
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- /User Sidebar --}}

    {{-- User Content --}}
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pks" aria-controls="navs-pks" aria-selected="true">
                        <i class="ri icon-base ri-file-paper-2-line me-1"></i> Perjanjian Aktif
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-map" aria-controls="navs-map" aria-selected="false">
                        <i class="ri icon-base ri-map-2-line me-1"></i> Peta Posisi
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-docs" aria-controls="navs-docs" aria-selected="false">
                        <i class="ri icon-base ri-folder-zip-line me-1"></i> Dokumen Digital
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-history" aria-controls="navs-history" aria-selected="false">
                        <i class="ri icon-base ri-history-line me-1"></i> Riwayat
                    </button>
                </li>
            </ul>

            <div class="tab-content bg-transparent p-0 shadow-none">
                {{-- TAB 1: PKS --}}
                <div class="tab-pane fade show active" id="navs-pks" role="tabpanel">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">Perjanjian Kerja Sama (PKS)</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if ($activeAgreement)
                                @php
                                    $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';
                                    $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                        ? asset('storage/'.$activeAgreement->fieldCoordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=48&rounded=true&bold=true";
                                @endphp

                                <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}"
                                   class="d-flex align-items-center justify-content-between p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 text-decoration-none"
                                   style="transition: all 0.2s ease;"
                                   onmouseover="this.classList.add('shadow-sm'); this.style.transform='scale(1.01)';"
                                   onmouseout="this.classList.remove('shadow-sm'); this.style.transform='scale(1)';">

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md">
                                            <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                        </div>
                                        <div>
                                            <span class="fw-bold text-primary mb-0 d-block fs-5">{{ $activeAgreement->agreement_number }}</span>
                                            <span class="text-dark fw-medium">Korlap: {{ $cName }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $pksBadge = 'bg-secondary';
                                            $pksLabel = 'Tidak Diketahui';
                                            if ($activeAgreement->status == 'active') { $pksBadge = 'bg-success'; $pksLabel = 'Aktif'; }
                                            elseif ($activeAgreement->status == 'pending_renewal') { $pksBadge = 'bg-warning'; $pksLabel = 'Menunggu Perpanjangan'; }
                                            elseif ($activeAgreement->status == 'expired') { $pksBadge = 'bg-danger'; $pksLabel = 'Kedaluwarsa'; }
                                            elseif ($activeAgreement->status == 'terminated') { $pksBadge = 'bg-dark'; $pksLabel = 'Diputus'; }
                                        @endphp
                                        <span class="badge {{ $pksBadge }} mb-1">{{ $pksLabel }}</span><br>
                                        <small class="text-muted">Pimpinan: <span class="text-dark">{{ $activeAgreement->leader->user->name ?? 'N/A' }}</span></small>
                                    </div>
                                </a>
                            @else
                                <div class="text-center py-5">
                                    <i class="ri icon-base ri-link-unlink-m text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Lokasi ini <strong>Tersedia</strong> dan belum terikat PKS aktif manapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 2: PETA --}}
                <div class="tab-pane fade" id="navs-map" role="tabpanel">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Peta Koordinat (Leaflet)</h5>
                            @if($parkingLocation->latitude && $parkingLocation->longitude)
                                <span class="badge bg-label-info">{{ $parkingLocation->latitude }}, {{ $parkingLocation->longitude }}</span>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if ($parkingLocation->latitude && $parkingLocation->longitude)
                                <div id="map"></div>
                            @else
                                <div class="text-center py-5 bg-light m-4 rounded-3 border-dashed">
                                    <i class="ri icon-base ri-map-pin-add-line text-muted mb-2" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-0">Titik koordinat belum ditambahkan di peta.</p>
                                    @if(Auth::user()->role !== 'leader')
                                    <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-sm btn-outline-secondary mt-3">Set Koordinat Sekarang</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 3: DOKUMEN --}}
                <div class="tab-pane fade" id="navs-docs" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">Arsip Dokumen Digital</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if ($parkingLocation->proposal_document || $parkingLocation->official_report_document)
                                <div class="row g-4">
                                    @if ($parkingLocation->proposal_document)
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-semibold text-dark"><i class="ri icon-base ri-file-pdf-2-line text-danger"></i> PDF Pengajuan</span>
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
                                                <span class="fw-semibold text-dark"><i class="ri icon-base ri-file-pdf-2-line text-danger"></i> PDF Berita Acara</span>
                                                <a href="{{ asset('storage/' . $parkingLocation->official_report_document) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">Buka Penuh</a>
                                            </div>
                                            <div class="pdf-viewer-wrapper shadow-sm">
                                                <iframe src="{{ asset('storage/' . $parkingLocation->official_report_document) }}#toolbar=0" frameborder="0"></iframe>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ri icon-base ri-folder-open-line text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada dokumen yang diunggah untuk lokasi ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 4: RIWAYAT --}}
                <div class="tab-pane fade" id="navs-history" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">Riwayat Aktivitas Lokasi</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="timeline-scrollable" id="historyTimeline">
                                <ul class="timeline timeline-center mt-3 pb-4">
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
                                            <i class="ri icon-base ri-history-line text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">Belum ada riwayat aktivitas yang tercatat.</p>
                                        </div>
                                    @endforelse
        
                                    @if($parkingLocation->histories->isNotEmpty())
                                        <li class="timeline-item timeline-item-transparent border-0">
                                            <span class="timeline-indicator timeline-indicator-secondary"><i class="ri icon-base ri-checkbox-blank-circle-line"></i></span>
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
        </div>
    </div>
    {{-- /User Content --}}
</div>

{{-- Modal Zoom Image --}}
@if ($parkingLocation->image)
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white bg-dark rounded-circle" data-bs-dismiss="modal" aria-label="Close" style="opacity: 1; padding: 0.8rem;"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <img src="{{ asset('storage/' . $parkingLocation->image) }}" class="img-fluid rounded-3 shadow-lg" alt="{{ $parkingLocation->name }}" style="max-height: 85vh; border: 5px solid white;">
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Perfect Scrollbar untuk Timeline
            const timelineEl = document.getElementById('historyTimeline');
            if (timelineEl) {
                new PerfectScrollbar(timelineEl, {
                    wheelPropagation: false,
                    suppressScrollX: true
                });
            }

            // Logic Peta
            @if ($parkingLocation->latitude && $parkingLocation->longitude)
                let lat = {{ $parkingLocation->latitude }};
                let lng = {{ $parkingLocation->longitude }};

                const map = L.map('map', {
                    scrollWheelZoom: true,
                    dragging: true,
                    maxZoom: 19
                }).setView([lat, lng], 18);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                const popupContent = `
                    <div class="text-center">
                        @if ($parkingLocation->image)
                            <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="Lokasi" class="popup-image shadow-sm">
                        @else
                            <div class="bg-label-secondary rounded-3 d-flex align-items-center justify-content-center mb-2 mx-auto" style="width: 100%; height: 100px;">
                                <i class="ri icon-base ri-image-line text-secondary" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif
                        <h6 class="mb-1 fw-bold text-primary mt-2">{{ $parkingLocation->name }}</h6>
                        <span class="badge bg-label-warning">${lat}, ${lng}</span>
                    </div>
                `;

                L.marker([lat, lng]).addTo(map).bindPopup(popupContent).openPopup();

                // FIX map render issue saat tab di klik
                // Peta ada di tab Peta Posisitioning (#navs-map)
                const mapTab = document.querySelector('button[data-bs-target="#navs-map"]');
                if (mapTab) {
                    mapTab.addEventListener('shown.bs.tab', function () {
                        setTimeout(() => {
                            map.invalidateSize();
                        }, 300);
                    });
                }
                
                // Panggil juga sekali saat load untuk berjaga-jaga
                setTimeout(() => {
                    map.invalidateSize();
                }, 500);
            @endif
        });
    </script>
@endpush
