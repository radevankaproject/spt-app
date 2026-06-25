@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Lokasi Parkir: ' . $parkingLocation->name)



@section('page-style')
    {{-- CSS untuk Leaflet Map & Perfect Scrollbar --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <style>
        /* Modern Map & Card Styling */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
        }
        #map {
            height: 450px;
            border-radius: 0 0 1rem 1rem;
            z-index: 1;
            width: 100%;
        }
        .leaflet-popup-content-wrapper { border-radius: 0.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .leaflet-popup-content { margin: 12px; text-align: center; }
        .leaflet-popup-content .popup-image {
            width: 100%; max-height: 120px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 10px;
        }

        /* PDF Viewer Styling */
        .pdf-viewer-wrapper {
            position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 1rem; border: 1px solid rgba(0,0,0,0.05);
        }
        .pdf-viewer-wrapper iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        }

        /* Timeline Custom */
        .timeline-scrollable {
            max-height: 600px; position: relative; overflow-y: auto; padding-right: 15px; padding-bottom: 1rem; padding-left: 15px;
        }
        .history-changes-box {
            background: linear-gradient(145deg, rgba(105, 108, 255, 0.05) 0%, rgba(105, 108, 255, 0.02) 100%);
            border-radius: 0.75rem; padding: 12px 16px; margin-top: 10px; margin-bottom: 10px;
            font-size: 0.85rem; border-left: 4px solid #696cff;
        }
        
        .user-profile-img {
            width: 160px; height: 160px; object-fit: cover; border-radius: 1rem; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Premium Tabs */
        .nav-pills.premium-tabs .nav-link {
            border-radius: 0.75rem; padding: 0.6rem 1.2rem; font-weight: 600; color: #697a8d; transition: all 0.3s ease;
        }
        .nav-pills.premium-tabs .nav-link.active {
            background-color: #696cff; color: #fff; box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3); transform: translateY(-2px);
        }
        .nav-pills.premium-tabs .nav-link:hover:not(.active) {
            background-color: rgba(105, 108, 255, 0.08); color: #696cff;
        }
        
        .hover-bg-light:hover {
            background-color: rgba(0,0,0,0.02);
        }
        .transition-all {
            transition: all 0.3s ease;
        }
        
        .timeline-item-transparent .timeline-event {
            padding: 1.5rem !important;
            border-radius: 1rem;
            background: rgba(0,0,0,0.02);
        }
    </style>
@endsection

@php
    function formatHistoryValue($key, $value) {
        if ($value === null || $value === '') return '-';
        if (in_array($key, ['image', 'proposal_document', 'official_report_document'])) {
            return '[File Terlampir]';
        }
        if ($key == 'daily_deposit') {
            return 'Rp ' . number_format((float)$value, 0, ',', '.');
        }
        if ($key == 'estimated_area') {
            return number_format((float)$value, 2, ',', '.') . ' m²';
        }
        if (in_array($key, ['estimated_srp_r2', 'estimated_srp_r4'])) {
            return (int)$value . ' SRP';
        }
        return Str::limit($value, 30);
    }
@endphp

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 animate__animated animate__fadeInDown">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-xl bg-primary bg-opacity-10 rounded-4 me-3 d-flex align-items-center justify-content-center shadow-sm border border-primary border-opacity-25">
                    <i class="ti tabler-map-pin-bolt text-primary" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Detail Lokasi: {{ $parkingLocation->name }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-style1 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}" class="text-muted">Lokasi Parkir</a></li>
                            <li class="breadcrumb-item active fw-bold text-primary">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-label-secondary rounded-pill shadow-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- User Sidebar / Location Detail --}}
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="glass-card mb-4 shadow-sm border-0 animate__animated animate__fadeInLeft">
            <div class="card-body p-4">
                <div class="user-avatar-section mb-4">
                    <div class="d-flex align-items-center flex-column position-relative">
                        <div class="position-absolute top-0 end-0 opacity-25" style="z-index: 0;">
                            <i class="ti tabler-map-pin text-primary" style="font-size: 100px; transform: rotate(15deg) translate(20px, -20px);"></i>
                        </div>
                        @if ($parkingLocation->image)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#imageModal" class="d-block position-relative z-1" title="Klik untuk memperbesar gambar">
                                <img src="{{ asset('storage/' . $parkingLocation->image) }}" alt="{{ $parkingLocation->name }}" class="user-profile-img mb-3" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        @else
                            <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mb-3 rounded-4 shadow-sm border border-primary border-opacity-25 position-relative z-1" style="width: 160px; height: 160px;">
                                <i class="ti tabler-photo-circle-minus text-primary" style="font-size: 5rem;"></i>
                            </div>
                        @endif
                        <div class="user-info text-center position-relative z-1">
                            <h5 class="mb-2 fw-bold text-dark">{{ $parkingLocation->name }}</h5>
                            @if ($parkingLocation->status == 'tersedia')
                                <span class="badge bg-label-success rounded-pill px-3 py-2 mb-2 d-inline-flex align-items-center fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="ti tabler-check me-1 fs-6"></i> TERSEDIA</span>
                            @else
                                <span class="badge bg-label-secondary rounded-pill px-3 py-2 mb-2 d-inline-flex align-items-center fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="ti tabler-lock me-1 fs-6"></i> TERIKAT PKS</span>
                            @endif

                            @if(!$parkingLocation->is_active)
                                <br>
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 mb-2 mt-1 d-inline-flex align-items-center fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="ti tabler-circle-x me-1 fs-6"></i> NONAKTIF / TUTUP</span>
                                <div class="mt-2 text-start p-3 bg-danger bg-opacity-10 rounded-4 border border-danger border-opacity-25 shadow-sm" style="font-size: 0.85rem;">
                                    <span class="fw-bold text-danger d-block mb-1"><i class="ti tabler-info-circle me-1"></i>Keterangan Tutup:</span>
                                    <span class="text-dark">{{ $parkingLocation->keterangan ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="p-3 bg-success bg-opacity-10 rounded-4 border border-success border-opacity-25 d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-success rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                        <i class='ti tabler-currency-dollar text-white fs-4'></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold fs-4 text-success">Rp {{ number_format($parkingLocation->daily_deposit, 0, ',', '.') }}</p>
                        <small class="text-success fw-medium">Setoran Harian</small>
                    </div>
                </div>

                <h6 class="pb-2 border-bottom mb-3 text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Detail Informasi</h6>
                <div class="info-container">
                    <ul class="list-unstyled mb-4">
                        <li class="mb-3 d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-qrcode text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">Kode ID</small>
                                <span class="fw-bold text-dark">#LOC-{{ str_pad($parkingLocation->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                            <div class="avatar avatar-sm bg-info bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-route text-info"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">Ruas Jalan</small>
                                <span class="fw-bold text-dark">{{ $parkingLocation->roadSection->name ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                            <div class="avatar avatar-sm bg-warning bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-focus-2 text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">Zona</small>
                                <span class="fw-bold text-dark">{{ $parkingLocation->roadSection->zone ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                            <div class="avatar avatar-sm bg-danger bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-map-pin text-danger"></i>
                            </div>
                            <div class="text-truncate w-100">
                                <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">Koordinat</small>
                                @if($parkingLocation->latitude && $parkingLocation->longitude)
                                    <span class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">{{ $parkingLocation->latitude }}, {{ $parkingLocation->longitude }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Belum Diatur</span>
                                @endif
                            </div>
                        </li>
                    </ul>

                    <h6 class="pb-2 border-bottom mt-2 mb-3 text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Estimasi Wilayah & SRP</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 border rounded-4 border-dashed bg-lighter">
                                <div class="avatar avatar-sm bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti tabler-ruler-2"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">Luas Wilayah</small>
                                    <span class="fw-bold text-dark fs-5">{{ $parkingLocation->estimated_area ? number_format($parkingLocation->estimated_area, 2, ',', '.') . ' m²' : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 border rounded-4 border-dashed bg-lighter">
                                <div class="avatar avatar-sm bg-warning text-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti tabler-motorbike"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block fw-medium" style="font-size: 0.7rem;">SRP R2</small>
                                    <span class="fw-bold text-dark">{{ $parkingLocation->estimated_srp_r2 ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 border rounded-4 border-dashed bg-lighter">
                                <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti tabler-car"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block fw-medium" style="font-size: 0.7rem;">SRP R4</small>
                                    <span class="fw-bold text-dark">{{ $parkingLocation->estimated_srp_r4 ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center rounded-4 border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, rgba(0,201,255,0.1) 0%, rgba(146,254,157,0.1) 100%);">
                        <i class="ti tabler-info-circle text-info fs-3 me-3"></i>
                        <span style="font-size: 0.85rem;" class="text-dark">Jumlah Setoran <strong>tidak bergantung</strong> pada luas wilayah parkir maupun jumlah SRP R2/R4.</span>
                    </div>
                    
                    @if(Auth::user()->role !== 'leader')
                    <div class="d-flex justify-content-center pt-2">
                        <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-primary rounded-pill w-100 shadow-sm fw-bold">
                            <i class="ti tabler-pencil me-2"></i> Edit Data Lokasi
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
        <div class="nav-align-top mb-4 animate__animated animate__fadeInRight">
            <ul class="nav nav-pills premium-tabs flex-column flex-md-row mb-4 gap-2" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pks" aria-controls="navs-pks" aria-selected="true">
                        <i class="ti tabler-file-description me-1"></i> Perjanjian Aktif
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-map" aria-controls="navs-map" aria-selected="false">
                        <i class="ti tabler-map-2 me-1"></i> Peta Posisi
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-docs" aria-controls="navs-docs" aria-selected="false">
                        <i class="ti tabler-file-zip me-1"></i> Dokumen Digital
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-history" aria-controls="navs-history" aria-selected="false">
                        <i class="ti tabler-history me-1"></i> Riwayat Aktivitas
                    </button>
                </li>
            </ul>

            <div class="tab-content bg-transparent p-0 shadow-none">
                {{-- TAB 1: PKS --}}
                <div class="tab-pane fade show active" id="navs-pks" role="tabpanel">
                    <div class="glass-card mb-4 border-0 shadow-sm animate__animated animate__fadeIn">
                        <div class="card-header border-bottom bg-transparent p-4 d-flex align-items-center">
                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-file-description text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold text-dark">Perjanjian Kerja Sama (PKS)</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($activeAgreement)
                                @php
                                    $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';
                                    $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                        ? asset('storage/'.$activeAgreement->fieldCoordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=48&rounded=true&bold=true";
                                @endphp

                                <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}"
                                   class="d-flex align-items-center justify-content-between p-4 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 text-decoration-none position-relative overflow-hidden"
                                   style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                   onmouseover="this.classList.add('shadow-md'); this.style.transform='translateY(-3px)';"
                                   onmouseout="this.classList.remove('shadow-md'); this.style.transform='translateY(0)';">
                                   
                                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                        <i class="ti tabler-writing-sign text-info" style="font-size: 80px; transform: rotate(-15deg);"></i>
                                    </div>

                                    <div class="d-flex align-items-center gap-4 position-relative z-1">
                                        <div class="avatar avatar-lg">
                                            <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm border border-2 border-white" style="object-fit: cover;">
                                        </div>
                                        <div>
                                            <span class="text-primary fw-bold mb-1 d-block" style="font-size: 1.25rem; letter-spacing: 0.5px;">{{ $activeAgreement->agreement_number }}</span>
                                            <div class="d-flex align-items-center text-dark fw-medium">
                                                <i class="ti tabler-user-pin text-muted me-1 fs-6"></i> Korlap: {{ $cName }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end position-relative z-1">
                                        @php
                                            $pksBadge = 'bg-label-secondary';
                                            $pksLabel = 'Tidak Diketahui';
                                            if ($activeAgreement->status == 'active') { $pksBadge = 'bg-label-success'; $pksLabel = 'Aktif'; }
                                            elseif ($activeAgreement->status == 'pending_renewal') { $pksBadge = 'bg-label-warning'; $pksLabel = 'Menunggu Perpanjangan'; }
                                            elseif ($activeAgreement->status == 'expired') { $pksBadge = 'bg-label-danger'; $pksLabel = 'Kedaluwarsa'; }
                                            elseif ($activeAgreement->status == 'terminated') { $pksBadge = 'bg-label-dark'; $pksLabel = 'Diputus'; }
                                        @endphp
                                        <span class="badge {{ $pksBadge }} rounded-pill px-3 py-2 mb-2 d-inline-block fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="ti tabler-circle-check me-1"></i>{{ $pksLabel }}</span><br>
                                        <small class="text-muted d-flex align-items-center justify-content-end">
                                            <i class="ti tabler-building-bank me-1"></i>Pimpinan: <span class="text-dark fw-bold ms-1">{{ $activeAgreement->leader->user->name ?? 'N/A' }}</span>
                                        </small>
                                    </div>
                                </a>
                            @else
                                <div class="text-center py-5">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                        <i class="ti tabler-unlink text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Lokasi Tersedia</h5>
                                    <p class="text-muted mb-0">Lokasi ini belum terikat dengan Perjanjian Kerja Sama aktif manapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 2: PETA --}}
                <div class="tab-pane fade" id="navs-map" role="tabpanel">
                    <div class="glass-card mb-4 border-0 shadow-sm">
                        <div class="card-header border-bottom bg-transparent p-4 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="ti tabler-map-2 text-info"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Peta Koordinat</h5>
                            </div>
                            @if($parkingLocation->latitude && $parkingLocation->longitude)
                                <span class="badge bg-label-info rounded-pill px-3 py-2 font-monospace shadow-sm" style="letter-spacing: 0.5px;">
                                    <i class="ti tabler-map-pin me-1"></i>{{ $parkingLocation->latitude }}, {{ $parkingLocation->longitude }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if ($parkingLocation->latitude && $parkingLocation->longitude)
                                <div id="map"></div>
                            @else
                                <div class="text-center py-5 bg-light m-4 rounded-4 border-dashed">
                                    <div class="avatar avatar-xl bg-secondary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                        <i class="ti tabler-map-pin-off text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Koordinat Tidak Tersedia</h5>
                                    <p class="text-muted mb-0">Titik koordinat belum ditambahkan di peta.</p>
                                    @if(Auth::user()->role !== 'leader')
                                    <a href="{{ route('masterdata.parking-locations.edit', $parkingLocation->id) }}" class="btn btn-sm btn-primary rounded-pill mt-3 shadow-sm"><i class="ti tabler-map-pin-plus me-1"></i>Set Koordinat</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 3: DOKUMEN --}}
                <div class="tab-pane fade" id="navs-docs" role="tabpanel">
                    <div class="glass-card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-transparent p-4 d-flex align-items-center">
                            <div class="avatar avatar-sm bg-danger bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-file-zip text-danger"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold text-dark">Arsip Dokumen Digital</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($parkingLocation->proposal_document || $parkingLocation->official_report_document)
                                <div class="row g-4">
                                    @if ($parkingLocation->proposal_document)
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti tabler-file-pdf text-danger fs-3 me-2"></i>
                                                    <span class="fw-bold text-dark">Dokumen Pengajuan</span>
                                                </div>
                                                <a href="{{ asset('storage/' . $parkingLocation->proposal_document) }}" target="_blank" class="btn btn-xs btn-outline-danger rounded-pill"><i class="ti tabler-external-link me-1"></i>Buka</a>
                                            </div>
                                            <div class="pdf-viewer-wrapper shadow-sm border-danger border-opacity-25">
                                                <iframe src="{{ asset('storage/' . $parkingLocation->proposal_document) }}#toolbar=0" frameborder="0"></iframe>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($parkingLocation->official_report_document)
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti tabler-file-pdf text-danger fs-3 me-2"></i>
                                                    <span class="fw-bold text-dark">Berita Acara</span>
                                                </div>
                                                <a href="{{ asset('storage/' . $parkingLocation->official_report_document) }}" target="_blank" class="btn btn-xs btn-outline-danger rounded-pill"><i class="ti tabler-external-link me-1"></i>Buka</a>
                                            </div>
                                            <div class="pdf-viewer-wrapper shadow-sm border-danger border-opacity-25">
                                                <iframe src="{{ asset('storage/' . $parkingLocation->official_report_document) }}#toolbar=0" frameborder="0"></iframe>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                        <i class="ti tabler-folder-open text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Belum Ada Dokumen</h5>
                                    <p class="text-muted mt-2 mb-0">Belum ada dokumen yang diunggah untuk lokasi ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 4: RIWAYAT --}}
                <div class="tab-pane fade" id="navs-history" role="tabpanel">
                    <div class="glass-card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-transparent p-4 d-flex align-items-center">
                            <div class="avatar avatar-sm bg-warning bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                <i class="ti tabler-history text-warning"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold text-dark">Riwayat Aktivitas Lokasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline-scrollable" id="historyTimeline">
                                <ul class="timeline timeline-center mt-3 pb-4">
                                    @forelse($parkingLocation->histories as $history)
                                        @php
                                            $color = 'primary';
                                            $icon = 'ti tabler-map-pin-star';
                                            if($history->action == 'created') { $color = 'success'; $icon = 'ti tabler-map-pin-plus'; }
                                            elseif($history->action == 'updated') { $color = 'warning'; $icon = 'ti tabler-map-pin-share'; }
                                            elseif($history->action == 'owner_changed') { $color = 'info'; $icon = 'ti tabler-map-pin-pin'; }
                                            elseif($history->action == 'deleted') { $color = 'danger'; $icon = 'ti tabler-map-pin-x'; }
                                        @endphp
        
                                        <li class="timeline-item timeline-item-{{ $color }} mb-4">
                                            <span class="timeline-indicator timeline-indicator-{{ $color }} bg-white shadow border border-3 border-white">
                                                <i class="{{ $icon }}"></i>
                                            </span>
                                            <div class="timeline-event shadow-sm rounded-4 border p-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); border-color: rgba(255, 255, 255, 0.5);">
                                                <div class="timeline-header mb-1 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold text-{{ $color }} d-flex align-items-center text-uppercase" style="letter-spacing: 0.5px;"><i class="ti tabler-circle-filled me-2" style="font-size: 0.5rem;"></i>{{ str_replace('_', ' ', $history->action) }}</h6>
                                                    <small class="text-muted"><i class="ti tabler-clock me-1"></i>{{ $history->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 text-dark fw-medium mt-3" style="font-size: 0.95rem; line-height: 1.6;">{{ $history->description }}</p>
        
                                                @if($history->action == 'updated' && !empty($history->new_values))
                                                    <div class="history-changes-box shadow-sm mt-3">
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($history->new_values as $key => $newValue)
                                                                @php
                                                                    $oldValue = $history->old_values[$key] ?? null;
                                                                    $displayOld = formatHistoryValue($key, $oldValue);
                                                                    $displayNew = formatHistoryValue($key, $newValue);
                                                                    $fieldName = ucwords(str_replace('_', ' ', $key));
                                                                @endphp
                                                                <li class="mb-2 text-truncate d-flex align-items-center" title="{{ $displayOld }} ➔ {{ $displayNew }}">
                                                                    <span class="fw-bold text-dark me-2" style="min-width: 120px;">{{ $fieldName }}</span>
                                                                    <span class="badge bg-label-danger text-decoration-line-through me-2 rounded-pill px-2 py-1">{{ $displayOld }}</span>
                                                                    <i class="ti tabler-arrow-right text-muted me-2"></i>
                                                                    <span class="badge bg-label-success rounded-pill px-2 py-1">{{ $displayNew }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
        
                                                <div class="d-flex align-items-center mt-3 bg-white shadow-sm border rounded-pill px-2 py-1 d-inline-flex">
                                                    @php
                                                        $uName = $history->user->name ?? 'Sistem Server';
                                                        $uAvatar = ($history->user && $history->user->img && file_exists(public_path('storage/' . $history->user->img)))
                                                            ? asset('storage/' . $history->user->img)
                                                            : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=24&rounded=true&bold=true";
                                                    @endphp
                                                    <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle me-2" width="24" height="24">
                                                    <small class="fw-bold text-dark">{{ $uName }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <div class="text-center py-5">
                                            <div class="avatar avatar-xl bg-label-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="ti tabler-history text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-1">Riwayat Kosong</h5>
                                            <p class="text-muted mt-2 mb-0">Belum ada riwayat aktivitas yang tercatat untuk lokasi ini.</p>
                                        </div>
                                    @endforelse
        
                                    @if($parkingLocation->histories->isNotEmpty())
                                        <li class="timeline-item timeline-item-transparent border-0">
                                            <span class="timeline-indicator timeline-indicator-secondary bg-white"><i class="ti tabler-map-pin-check"></i></span>
                                            <div class="timeline-event pb-0">
                                                <small class="text-muted text-uppercase fw-bold"><i class="ti tabler-flag me-1"></i>Awal Rekaman</small>
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

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
@endsection

@section('page-script')
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
                                <i class="ri icon-base ti tabler-image text-secondary" style="font-size: 2.5rem;"></i>
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
@endsection
