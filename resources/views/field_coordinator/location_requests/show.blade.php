@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Pengajuan Titik')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.6);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        box-shadow: var(--glass-shadow);
    }

    /* ===== HERO HEADER ===== */
    .page-hero {
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
        padding: 2rem 2.5rem;
    }
    .page-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -15%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* ===== MAP ===== */
    #map {
        height: 320px;
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        z-index: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* ===== IMAGE HOVER ===== */
    .img-zoom-container {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 2px solid #fff;
    }
    .img-zoom-container img {
        transition: transform 0.4s ease;
        width: 100%;
        height: 320px;
        object-fit: cover;
    }
    .img-zoom-container:hover img { transform: scale(1.05); }
    .img-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .img-zoom-container:hover .img-overlay { opacity: 1; }

    /* ===== TIMELINE ===== */
    .status-timeline {
        position: relative;
        padding-left: 2.5rem;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: linear-gradient(to bottom, #696cff, #e5e7eb 40%, #e5e7eb);
        border-radius: 2px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
        padding-left: 0.5rem;
    }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-indicator {
        position: absolute;
        left: -2.15rem;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-size: 0.65rem;
    }

    /* ===== SECTION HEADER ===== */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .section-header .section-icon {
        width: 28px;
        height: 28px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    /* ===== DOC PREVIEW ===== */
    .doc-preview-container {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 1rem;
        overflow: hidden;
        background: #f8f9fa;
        height: 550px;
    }
    .doc-preview-container iframe,
    .doc-preview-container embed { width: 100%; height: 100%; border: none; }

    /* ===== DYNAMIC ISLAND ===== */
    .dynamic-island {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        background: rgba(15, 23, 42, 0.95);
        color: #fff;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 9999;
        backdrop-filter: blur(10px);
    }
    .dynamic-island.show { transform: translateX(-50%) translateY(0); }

    /* ===== INFO ROW ===== */
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .info-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-1 { animation: fadeInUp 0.5s ease 0.1s both; }
    .anim-2 { animation: fadeInUp 0.5s ease 0.2s both; }
    .anim-3 { animation: fadeInUp 0.5s ease 0.3s both; }
    .anim-4 { animation: fadeInUp 0.5s ease 0.4s both; }
</style>
@endsection

@section('content')

{{-- DYNAMIC ISLAND --}}
<div id="dynamicIsland" class="dynamic-island">
    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
        <i class="ti tabler-check text-dark"></i>
    </div>
    <span>Tersalin ke Papan Klip!</span>
</div>

{{-- ============================================= --}}
{{-- HERO HEADER --}}
{{-- ============================================= --}}
@php
    $heroGradient = $locationRequest->request_type == 'add'
        ? 'linear-gradient(135deg, #28c76f 0%, #48da89 100%)'
        : 'linear-gradient(135deg, #ea5455 0%, #f08182 100%)';
    $heroIcon = $locationRequest->request_type == 'add' ? 'tabler-map-pin' : 'tabler-map-pin';
    $heroLabel = $locationRequest->request_type == 'add' ? 'PENAMBAHAN TITIK' : 'PENCABUTAN TITIK';
    $heroBadgeText = $locationRequest->request_type == 'add' ? 'text-success' : 'text-danger';
@endphp
<div class="page-hero text-white mb-4 shadow-lg anim-1" style="background: {{ $heroGradient }};">
    <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white {{ $heroBadgeText }} rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.65rem; letter-spacing: 1px;">
                    <i class="ti {{ $heroIcon }} me-1"></i>{{ $heroLabel }}
                </span>
                @if($locationRequest->status == 'pending')
                    <span class="badge bg-white text-warning rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem;"><i class="ti tabler-clock me-1"></i>Pending</span>
                @elseif($locationRequest->status == 'surveyed')
                    <span class="badge bg-white text-info rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem;"><i class="ti tabler-clipboard me-1"></i>Disurvey</span>
                @elseif($locationRequest->status == 'approved')
                    <span class="badge bg-white text-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem;"><i class="ti tabler-check me-1"></i>Disetujui</span>
                @else
                    <span class="badge bg-white text-danger rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem;"><i class="ti tabler-x me-1"></i>Ditolak</span>
                @endif
            </div>
            <h4 class="fw-bold text-white mb-1">Detail Pengajuan</h4>
            <p class="text-white-50 mb-0" style="font-size: 0.8rem;">Nomor Tiket: <strong class="text-white">#REQ-{{ str_pad($locationRequest->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
        </div>
        <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-white fw-bold shadow-sm rounded-pill px-4 mt-3 mt-md-0" style="color: {{ $locationRequest->request_type == 'add' ? '#28c76f' : '#ea5455' }};">
            <i class="ti tabler-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <i class="ti ti-{{ $heroIcon }} position-absolute text-white" style="font-size: 180px; right: -20px; bottom: -40px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
</div>

<div class="row g-4">
    {{-- ======================================================= --}}
    {{-- KOLOM KIRI: DATA & VISUAL --}}
    {{-- ======================================================= --}}
    <div class="col-xl-8">
        {{-- CARD 1: INFORMASI UMUM --}}
        <div class="glass-card mb-4 anim-2">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-info-circle text-primary" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Informasi Pengajuan</h6>
                </div>
            </div>

            <div class="p-4">
                <div class="row g-3">
                    {{-- Baris Info --}}
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-icon bg-primary bg-opacity-10 text-primary">
                                <i class="ti tabler-file-description"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">NOMOR PKS TERKAIT</small>
                                <span class="fw-bold text-primary" style="font-size: 0.95rem;">{{ $locationRequest->agreement->agreement_number }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                <i class="ti tabler-calendar-event"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">TANGGAL PENGAJUAN</small>
                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $locationRequest->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    @if($locationRequest->request_type == 'add')
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-success bg-opacity-10 text-success">
                                    <i class="ti tabler-map-pin"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">NAMA LOKASI (USULAN)</small>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $locationRequest->name }}</span>
                                    <small class="text-muted d-block">Jl. {{ $locationRequest->road_section_name }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon" style="background: linear-gradient(135deg, rgba(255, 159, 67, 0.15) 0%, rgba(255, 159, 67, 0.05) 100%); color: #ff9f43;">
                                    <i class="ti tabler-cash"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">PENAWARAN SETORAN</small>
                                    <span class="fw-bold text-warning" style="font-size: 1.1rem;">Rp {{ number_format($locationRequest->offered_daily_deposit, 0, ',', '.') }}</span>
                                    <small class="text-muted"> / hari</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="ti tabler-map-pin"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">TITIK YANG DICABUT</small>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $locationRequest->parkingLocation->name ?? 'N/A' }}</span>
                                    <small class="text-muted d-block">{{ $locationRequest->parkingLocation->roadSection->name ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="ti tabler-cash"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">NILAI SETORAN</small>
                                    <span class="fw-bold text-danger" style="font-size: 1.1rem;">Rp {{ number_format($locationRequest->parkingLocation->daily_deposit ?? 0, 0, ',', '.') }}</span>
                                    <small class="text-muted"> / hari</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ALASAN --}}
                    <div class="col-12">
                        <div class="p-3 rounded-3 mt-2" style="background: linear-gradient(135deg, rgba(105,108,255,0.05) 0%, rgba(105,108,255,0.02) 100%); border: 1px dashed rgba(105,108,255,0.2);">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="ti tabler-quote me-1"></i>ALASAN PENGAJUAN
                            </small>
                            <p class="mb-0 text-dark" style="font-size: 0.85rem; line-height: 1.6;">"{{ $locationRequest->reason }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($locationRequest->request_type == 'add')
        {{-- CARD 2: PETA & GAMBAR --}}
        <div class="glass-card mb-4 anim-3">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-success bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-map-2 text-success" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Visualisasi Lokasi</h6>
                </div>
                @if($locationRequest->latitude && $locationRequest->longitude)
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" onclick="copyToClipboard('https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}')">
                        <i class="ti tabler-link me-1"></i> Salin G-Maps
                    </button>
                @endif
            </div>
            <div class="p-4">
                <div class="row g-4">
                    {{-- MAP --}}
                    <div class="col-md-6">
                        @if($locationRequest->latitude && $locationRequest->longitude)
                            <div id="map" class="mb-2"></div>
                            <div class="text-center">
                                <span class="badge bg-label-danger rounded-pill px-3 py-1" style="font-size: 0.7rem;">
                                    <i class="ti tabler-map-pin me-1"></i> {{ $locationRequest->latitude }}, {{ $locationRequest->longitude }}
                                </span>
                            </div>
                        @else
                            <div class="text-center py-5 h-100 d-flex flex-column justify-content-center rounded-3" style="background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.01) 100%); border: 2px dashed rgba(0,0,0,0.08);">
                                <i class="ti tabler-map-pin text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                                <span class="text-muted" style="font-size: 0.85rem;">Koordinat tidak dilampirkan.</span>
                            </div>
                        @endif
                    </div>

                    {{-- FOTO LOKASI --}}
                    <div class="col-md-6">
                        @if($locationRequest->image)
                            <div class="img-zoom-container" data-bs-toggle="modal" data-bs-target="#imageModal">
                                <img src="{{ asset('storage/'.$locationRequest->image) }}" alt="Foto Lapangan">
                                <div class="img-overlay">
                                    <div class="bg-white rounded-circle shadow text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="ti tabler-zoom-in fs-3"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2 text-muted" style="font-size: 0.72rem;"><i class="ti tabler-pointer me-1"></i>Klik untuk memperbesar</div>
                        @else
                            <div class="text-center py-5 h-100 d-flex flex-column justify-content-center rounded-3" style="background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.01) 100%); border: 2px dashed rgba(0,0,0,0.08);">
                                <i class="ti tabler-camera-off text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                                <span class="text-muted" style="font-size: 0.85rem;">Foto tidak dilampirkan.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: DOKUMEN PROPOSAL --}}
        @if($locationRequest->proposal_document)
        <div class="glass-card mb-4 anim-4">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-file-text text-info" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Dokumen Proposal</h6>
                </div>
                <a href="{{ asset('storage/'.$locationRequest->proposal_document) }}" download class="btn btn-sm btn-primary rounded-pill shadow-sm px-3">
                    <i class="ti tabler-download me-1"></i> Unduh
                </a>
            </div>
            <div class="p-4">
                @php $ext = strtolower(pathinfo($locationRequest->proposal_document, PATHINFO_EXTENSION)); @endphp
                <div class="doc-preview-container shadow-sm">
                    @if($ext == 'pdf')
                        <embed src="{{ asset('storage/'.$locationRequest->proposal_document) }}#toolbar=0" type="application/pdf">
                    @else
                        <iframe src="https://docs.google.com/gview?url={{ asset('storage/'.$locationRequest->proposal_document) }}&embedded=true"></iframe>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- ======================================================= --}}
    {{-- KOLOM KANAN: STATUS & FEEDBACK --}}
    {{-- ======================================================= --}}
    <div class="col-xl-4">

        {{-- CARD: STATUS TIMELINE --}}
        <div class="glass-card mb-4 anim-2">
            <div class="p-3 px-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-git-commit text-primary" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Pelacakan Status</h6>
                </div>
            </div>
            <div class="p-4">
                <div class="status-timeline">
                    {{-- 1. Diajukan --}}
                    <div class="timeline-item">
                        <div class="timeline-indicator bg-primary text-white"><i class="ti tabler-check"></i></div>
                        <h6 class="mb-0 fw-bold text-primary" style="font-size: 0.85rem;">Pengajuan Diterima</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $locationRequest->created_at->translatedFormat('d M Y, H:i') }}</small>
                    </div>

                    {{-- 2. Survey (Khusus Add) --}}
                    @if($locationRequest->request_type == 'add')
                        <div class="timeline-item">
                            @php
                                $isSurveyed = $locationRequest->review ? true : false;
                                $sColor = $isSurveyed ? 'bg-info' : 'bg-light border border-2 border-secondary-subtle';
                            @endphp
                            <div class="timeline-indicator {{ $sColor }}">
                                @if($isSurveyed)
                                    <i class="ti tabler-check text-white"></i>
                                @else
                                    <i class="ti tabler-clock text-muted" style="font-size: 0.55rem;"></i>
                                @endif
                            </div>
                            <h6 class="mb-0 fw-bold {{ $isSurveyed ? 'text-info' : 'text-muted' }}" style="font-size: 0.85rem;">Survey Dinas</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $isSurveyed ? $locationRequest->review->created_at->translatedFormat('d M Y') : 'Menunggu Jadwal' }}</small>
                        </div>
                    @endif

                    {{-- 3. Finalisasi --}}
                    <div class="timeline-item mb-0">
                        @php
                            $fColor = 'bg-light border border-2 border-secondary-subtle'; $fText = 'Menunggu Keputusan';
                            if($locationRequest->status == 'approved') { $fColor = 'bg-success'; $fText = 'Disetujui'; }
                            if($locationRequest->status == 'rejected') { $fColor = 'bg-danger'; $fText = 'Ditolak'; }
                        @endphp
                        <div class="timeline-indicator {{ $fColor }}">
                            @if($locationRequest->status == 'approved')
                                <i class="ti tabler-check text-white"></i>
                            @elseif($locationRequest->status == 'rejected')
                                <i class="ti tabler-x text-white"></i>
                            @else
                                <i class="ti tabler-clock text-muted" style="font-size: 0.55rem;"></i>
                            @endif
                        </div>
                        <h6 class="mb-0 fw-bold {{ $locationRequest->status == 'approved' ? 'text-success' : ($locationRequest->status == 'rejected' ? 'text-danger' : 'text-muted') }}" style="font-size: 0.85rem;">{{ $fText }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: HASIL SURVEY --}}
        @if($locationRequest->review)
            <div class="glass-card mb-4 anim-3" style="border-left: 4px solid #00bad1;">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                            <i class="ti tabler-clipboard text-info" style="font-size: 0.875rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Hasil Survey</h6>
                    </div>
                    @if($locationRequest->review->report_document)
                        <button type="button" class="btn btn-xs btn-info rounded-pill shadow-sm px-2" data-bs-toggle="modal" data-bs-target="#surveyDocModal" style="font-size: 0.65rem;">
                            <i class="ti tabler-file-text me-1"></i> B.A
                        </button>
                    @endif
                </div>
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <img src="{{ $locationRequest->review->reviewer->img ? asset('storage/'.$locationRequest->review->reviewer->img) : 'https://ui-avatars.com/api/?name='.urlencode($locationRequest->review->reviewer->name).'&background=00bad1&color=fff&bold=true' }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ $locationRequest->review->reviewer->name ?? 'Petugas' }}</span>
                            <small class="text-muted" style="font-size: 0.7rem;">Disurvey: {{ $locationRequest->review->survey_date->translatedFormat('d M Y') }}</small>
                        </div>
                    </div>

                    <div class="text-center p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.08) 0%, rgba(40, 199, 111, 0.03) 100%); border: 1px dashed rgba(40, 199, 111, 0.25);">
                        <small class="d-block text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">Setoran Disepakati (Deal)</small>
                        <h4 class="text-success fw-bold mb-0">Rp {{ number_format($locationRequest->review->recommended_deposit, 0, ',', '.') }}</h4>
                    </div>

                    <label class="fw-bold text-dark small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Catatan Petugas:</label>
                    <p class="text-dark mb-0 p-3 rounded-3 mt-1" style="background: linear-gradient(135deg, rgba(0, 186, 209, 0.06) 0%, rgba(0, 186, 209, 0.02) 100%); font-size: 0.82rem; line-height: 1.6; border: 1px solid rgba(0, 186, 209, 0.1);">
                        "{{ $locationRequest->review->survey_notes }}"
                    </p>
                </div>
            </div>
        @endif

        {{-- CARD: KEPUTUSAN FINAL --}}
        @if(in_array($locationRequest->status, ['approved', 'rejected']))
            @php
                $isApproved = $locationRequest->status == 'approved';
                $finalGradient = $isApproved
                    ? 'linear-gradient(135deg, rgba(40, 199, 111, 0.06) 0%, rgba(40, 199, 111, 0.02) 100%)'
                    : 'linear-gradient(135deg, rgba(234, 84, 85, 0.06) 0%, rgba(234, 84, 85, 0.02) 100%)';
                $finalBorder = $isApproved ? '#28c76f' : '#ea5455';
            @endphp
            <div class="glass-card anim-4" style="border-left: 4px solid {{ $finalBorder }}; background: {{ $finalGradient }};">
                <div class="p-4 text-center">
                    <div class="avatar avatar-xl mx-auto rounded-circle mb-3 d-flex align-items-center justify-content-center" style="background: {{ $isApproved ? 'linear-gradient(135deg, rgba(40, 199, 111, 0.15) 0%, rgba(40, 199, 111, 0.05) 100%)' : 'linear-gradient(135deg, rgba(234, 84, 85, 0.15) 0%, rgba(234, 84, 85, 0.05) 100%)' }};">
                        <i class="ti {{ $isApproved ? 'tabler-checks text-success' : 'tabler-x text-danger' }}" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold {{ $isApproved ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $isApproved ? 'Pengajuan Disetujui!' : 'Pengajuan Ditolak' }}
                    </h5>
                    @if($isApproved)
                        <p class="text-muted small mb-3">Data PKS Anda telah diperbarui secara otomatis di sistem.</p>
                    @endif

                    <div class="text-start p-3 rounded-3" style="border: 1px dashed {{ $isApproved ? 'rgba(40, 199, 111, 0.25)' : 'rgba(234, 84, 85, 0.25)' }};">
                        <small class="fw-bold text-dark text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Catatan Pimpinan/Admin:</small>
                        <p class="mb-0 {{ $isApproved ? 'text-success' : 'text-danger' }}" style="font-size: 0.82rem; line-height: 1.6;">
                            {{ $locationRequest->admin_note ?? 'Tidak ada catatan.' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ======================================================= --}}
{{-- MODALS --}}
{{-- ======================================================= --}}

{{-- IMAGE VIEWER --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-2 justify-content-end">
                <button type="button" class="btn btn-icon btn-light rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close"><i class="ti tabler-x fs-4"></i></button>
            </div>
            <div class="modal-body text-center p-0">
                @if($locationRequest->image)
                    <img src="{{ asset('storage/'.$locationRequest->image) }}" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; border: 4px solid #fff;">
                @endif
            </div>
        </div>
    </div>
</div>

{{-- DOKUMEN B.A SURVEY --}}
@if($locationRequest->review && $locationRequest->review->report_document)
<div class="modal fade" id="surveyDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3 px-4" style="background: linear-gradient(135deg, #f8f7fa 0%, #f1f0f4 100%);">
                <h5 class="modal-title fw-bold text-dark"><i class="ti tabler-file-text me-2 text-info"></i>Berita Acara Survey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh; background: #f8f9fa;">
                @php $extBA = strtolower(pathinfo($locationRequest->review->report_document, PATHINFO_EXTENSION)); @endphp
                @if($extBA == 'pdf')
                    <embed src="{{ asset('storage/'.$locationRequest->review->report_document) }}#toolbar=0" type="application/pdf" width="100%" height="100%">
                @else
                    <iframe src="https://docs.google.com/gview?url={{ asset('storage/'.$locationRequest->review->report_document) }}&embedded=true" width="100%" height="100%" style="border: none;"></iframe>
                @endif
            </div>
            <div class="modal-footer border-0 bg-white justify-content-between px-4">
                <small class="text-muted">Diunggah oleh Petugas UPT Perparkiran.</small>
                <a href="{{ asset('storage/'.$locationRequest->review->report_document) }}" download class="btn btn-info fw-bold rounded-pill px-4">
                    <i class="ti tabler-download me-1"></i> Unduh
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('vendor-script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('page-script')
<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        // Tooltips
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (el) {
            return new bootstrap.Tooltip(el);
        });

        // Leaflet Map Init
        @if($locationRequest->request_type == 'add' && $locationRequest->latitude && $locationRequest->longitude)
            var lat = {{ $locationRequest->latitude }};
            var lng = {{ $locationRequest->longitude }};

            var map = L.map('map').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            L.marker([lat, lng], {icon: redIcon}).addTo(map)
                .bindPopup("<div class='text-center fw-bold'>{{ $locationRequest->name }}</div>").openPopup();
        @endif
    });

    // Copy to Clipboard + Dynamic Island
    function copyToClipboard(text) {
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);

        var island = document.getElementById('dynamicIsland');
        island.classList.add('show');
        setTimeout(function() { island.classList.remove('show'); }, 3000);
    }
</script>
@endsection