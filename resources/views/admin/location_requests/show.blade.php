@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Pengajuan Titik')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
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

    #map {
        height: 320px;
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        z-index: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .doc-preview-container {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 1rem;
        overflow: hidden;
        background: #f8f9fa;
        height: 550px;
    }

    .doc-preview-container iframe,
    .doc-preview-container embed { width: 100%; height: 100%; border: none; }

    .img-zoom-container {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 2px solid #fff;
    }

    .img-zoom-container img { transition: transform 0.4s ease; width: 100%; height: 320px; object-fit: cover; }
    .img-zoom-container:hover img { transform: scale(1.05); }

    .img-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.3); display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .img-zoom-container:hover .img-overlay { opacity: 1; }

    .status-timeline { position: relative; padding-left: 2.5rem; border: none !important; margin-left: 0; }
    .status-timeline::before {
        content: ''; position: absolute; left: 15px; top: 10px; bottom: 10px; width: 2px;
        background: linear-gradient(to bottom, #696cff, #e5e7eb 40%, #e5e7eb); border-radius: 2px;
    }
    .timeline-item { position: relative; margin-bottom: 2rem; padding-left: 0.5rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-indicator {
        position: absolute; left: -2.15rem; top: 0; width: 24px; height: 24px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15); font-size: 0.65rem;
    }
    .timeline-indicator.bg-primary { box-shadow: 0 0 12px rgba(105, 108, 255, 0.5); }
    .timeline-indicator.bg-info { box-shadow: 0 0 12px rgba(3, 195, 236, 0.5); }
    .timeline-indicator.bg-success { box-shadow: 0 0 12px rgba(113, 221, 55, 0.5); }
    .timeline-indicator.bg-warning { box-shadow: 0 0 12px rgba(255, 171, 0, 0.5); }

    .dynamic-island {
        position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px);
        background: rgba(15, 23, 42, 0.95); color: #fff; padding: 12px 24px; border-radius: 50px;
        font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;
        white-space: nowrap; width: max-content;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 9999; backdrop-filter: blur(10px);
    }
    .dynamic-island.show { transform: translateX(-50%) translateY(0); }

    .hover-link { transition: all 0.2s ease; }
    .hover-link:hover { color: #00bad1 !important; transform: translateX(3px); }

    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .anim-1 { animation: fadeInUp 0.5s ease 0.1s both; }
    .anim-2 { animation: fadeInUp 0.5s ease 0.2s both; }
    .anim-3 { animation: fadeInUp 0.5s ease 0.3s both; }
    .anim-4 { animation: fadeInUp 0.5s ease 0.4s both; }
</style>
@endsection

@section('content')
<div id="dynamicIsland" class="dynamic-island">
    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
        style="width: 24px; height: 24px;">
        <i class="ti tabler-check text-dark"></i>
    </div>
    <span>Link Maps Berhasil Disalin!</span>
</div>

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
        <a href="{{ route('masterdata.location-requests.index') }}" class="btn btn-white fw-bold shadow-sm rounded-pill px-4 mt-3 mt-md-0" style="color: {{ $locationRequest->request_type == 'add' ? '#28c76f' : '#ea5455' }};">
            <i class="ti tabler-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <i class="ti ti-{{ $heroIcon }} position-absolute text-white" style="font-size: 180px; right: -20px; bottom: -40px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="ti tabler-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="ti tabler-alert-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
    {{-- KOLOM KIRI --}}
    <div class="col-xl-8">
        {{-- CARD 1: INFORMASI --}}
        <div class="glass-card mb-4 anim-2">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3 bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                            <i
                                class="ti {{ $locationRequest->request_type == 'add' ? 'tabler-map-pin' : 'tabler-trash' }} ti-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $locationRequest->request_type == 'add' ? 'Penambahan Titik
                                Baru' : 'Pencabutan Titik Parkir' }}</h5>
                            <span class="text-muted">Diajukan pada {{ $locationRequest->created_at->translatedFormat('d
                                F Y, H:i') }} WIB</span>
                        </div>
                    </div>
                    <div>
                        @if($locationRequest->status == 'pending') <span class="badge bg-label-warning fs-6"><i
                                class="ti tabler-clock me-1"></i> Pending</span>
                        @elseif($locationRequest->status == 'surveyed') <span class="badge bg-label-info fs-6"><i
                                class="ti tabler-clipboard me-1"></i> Disurvey</span>
                        @elseif($locationRequest->status == 'approved') <span class="badge bg-label-success fs-6"><i
                                class="ti tabler-check me-1"></i> Disetujui</span>
                        @else <span class="badge bg-label-danger fs-6"><i class="ti tabler-x me-1"></i>
                            Ditolak</span> @endif
                    </div>
                </div>

                <div class="bg-lighter rounded p-4 mb-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-2">Koordinator (Mitra)</small>
                            @if($locationRequest->agreement->fieldCoordinator)
                            <a href="{{ route('admin.field-coordinators.show', $locationRequest->agreement->fieldCoordinator->id) }}"
                                class="d-inline-flex align-items-center text-decoration-none hover-link"
                                data-bs-toggle="tooltip" title="Lihat Profil Korlap">
                                <div class="avatar avatar-sm me-2">
                                    <img src="{{ $locationRequest->agreement->fieldCoordinator->user->img ? asset('storage/'.$locationRequest->agreement->fieldCoordinator->user->img) : 'https://ui-avatars.com/api/?name='.urlencode($locationRequest->agreement->fieldCoordinator->user->name ?? 'M').'&background=random&color=fff' }}"
                                        class="rounded-circle shadow-sm">
                                </div>
                                <span class="fw-bold text-primary">{{
                                    $locationRequest->agreement->fieldCoordinator->user->name ?? 'N/A' }}</span>
                            </a>
                            @else
                            <span class="fw-bold text-dark">N/A</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-2">Nomor PKS Aktif</small>
                            <a href="{{ route('masterdata.agreements.show', $locationRequest->agreement->id) }}"
                                class="d-inline-flex align-items-center text-decoration-none hover-link"
                                data-bs-toggle="tooltip" title="Buka Detail PKS">
                                <span class="fw-bold text-primary fs-6">{{ $locationRequest->agreement->agreement_number
                                    }}</span>
                                <i class="ti tabler-external-link ms-1 text-primary"></i>
                            </a>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 border-light">
                        </div>

                        @if($locationRequest->request_type == 'add')
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Usulan Titik Parkir</small>
                            <span class="fw-bold text-dark fs-6">{{ $locationRequest->name }}</span><br>
                            <small class="text-muted">Jl. {{ $locationRequest->road_section_name }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Penawaran Setoran Awal</small>
                            <span class="fw-bold text-warning fs-5">Rp {{
                                number_format($locationRequest->offered_daily_deposit, 0, ',', '.') }}</span> <small
                                class="text-muted">/ hari</small>
                        </div>
                        @else
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Titik yang Dicabut</small>
                            <span class="fw-bold text-dark fs-6">{{ $locationRequest->parkingLocation->name ?? 'N/A'
                                }}</span><br>
                            <small class="text-muted">Jl. {{ $locationRequest->parkingLocation->roadSection->name ?? '-'
                                }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Setoran yang Akan
                                Hilang</small>
                            <span class="fw-bold text-danger fs-5">Rp {{
                                number_format($locationRequest->parkingLocation->daily_deposit ?? 0, 0, ',', '.')
                                }}</span> <small class="text-muted">/ hari</small>
                        </div>
                        @endif

                        <div class="col-12">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Alasan Pengajuan</small>
                            <p class="mb-0 text-dark fst-italic border-start border-3 border-secondary ps-2">"{{
                                $locationRequest->reason }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ SMART COLLISION DETECTION (Hanya untuk penambahan titik) --}}
        @if($locationRequest->request_type === 'add' && isset($similarLocations))
        <div
            class="glass-card mb-4 rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    @if($similarLocations->count() > 0)
                    <i class="ti tabler-alert me-2 text-warning"></i> Cek Duplikasi Lokasi (Ada {{
                    $similarLocations->count() }} Kemiripan)
                    @else
                    <i class="ti tabler-shield-check me-2 text-success"></i> Aman dari Duplikasi
                    @endif
                </h6>
            </div>

            <div class="card-body p-4 bg-white">
                @if($similarLocations->count() > 0)
                <div class="alert alert-warning d-flex align-items-start mb-4 border-0 shadow-sm" role="alert">
                    <i class="ti tabler-alert-circle ti tabler-xl me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Perhatian!</h6>
                        <p class="mb-0 small">Sistem mendeteksi ada <strong>{{ $similarLocations->count() }} titik
                                parkir</strong> dengan nama yang mirip. Pastikan usulan nama <strong>"{{
                                $locationRequest->name }}"</strong> ini tidak tumpang tindih dengan lokasi di bawah ini:
                        </p>
                    </div>
                </div>

                <div class="table-responsive border rounded-3">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-lighter">
                            <tr>
                                <th>Nama Titik Eksisting</th>
                                <th>Setoran Harian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($similarLocations as $simLoc)
                            <tr>
                                <td class="fw-medium text-dark">{{ $simLoc->name }}</td>
                                <td>Rp {{ number_format($simLoc->daily_deposit, 0, ',', '.') }}</td>
                                <td>
                                    {{-- ✅ TOMBOL BUKA MODAL POP-UP --}}
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalSimLoc{{ $simLoc->id }}">
                                        <i class="ti tabler-eye me-1"></i> Intip Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="d-flex align-items-center">
                    <div
                        class="avatar avatar-md bg-label-success rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="ti tabler-check ti tabler-xl"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-success">Aman dari Duplikasi Nama</h6>
                        <p class="mb-0 text-muted small">Tidak ditemukan titik parkir dengan nama yang mirip dengan
                            <strong>"{{ $locationRequest->name }}"</strong> di Master Data. Potensi duplikat sangat
                            rendah.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($locationRequest->request_type == 'add')
        {{-- CARD 2: MAP & FOTO --}}
        <div class="glass-card mb-4 anim-2">
            <div class="p-3 px-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(135deg, rgba(3, 195, 236, 0.08) 0%, rgba(3, 195, 236, 0.02) 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-camera text-info" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-info" style="font-size: 0.85rem;">Visual & Lokasi Geografis</h6>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-dark mb-0">Peta Satelit</label>
                            @if($locationRequest->latitude && $locationRequest->longitude)
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill"
                                onclick="copyToClipboard('https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}')">
                                <i class="ti tabler-copy me-1"></i> Salin Link
                            </button>
                            @endif
                        </div>

                        @if($locationRequest->latitude && $locationRequest->longitude)
                        <div id="map" class="mb-3 shadow-sm"></div>
                        <a href="https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}"
                            target="_blank" class="btn btn-sm btn-primary w-100 shadow-sm">
                            <i class="ti tabler-brand-google me-1"></i> Buka di Google Maps
                        </a>
                        @else
                        <div class="alert bg-lighter text-center py-5 border-dashed rounded-3">
                            <i class="ti tabler-map-pin-off ti-xl text-muted opacity-50 mb-2 d-block"></i>
                            <span class="text-muted">Koordinat tidak dilampirkan oleh Mitra.</span>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold text-dark mb-2">Foto Kondisi Lapangan</label>
                        @if($locationRequest->image)
                        <div class="img-zoom-container" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <img src="{{ asset('storage/'.$locationRequest->image) }}" alt="Kondisi Lapangan">
                            <div class="img-overlay">
                                <div class="bg-white rounded-circle p-3 shadow text-primary">
                                    <i class="ti tabler-zoom-in ti-lg"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2 text-muted small"><i class="ti tabler-pointer me-1"></i> Klik
                            gambar untuk memperbesar</div>
                        @else
                        <div
                            class="alert bg-lighter text-center py-5 border-dashed rounded-3 h-100 d-flex flex-column justify-content-center">
                            <i class="ti tabler-image ti-xl text-muted opacity-50 mb-2"></i>
                            <span class="text-muted">Tidak ada foto terlampir.</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: PREVIEW DOKUMEN --}}
        <div class="glass-card mb-4 anim-3">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(135deg, rgba(234, 84, 85, 0.08) 0%, rgba(234, 84, 85, 0.02) 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-danger bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-file-text text-danger" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-danger" style="font-size: 0.85rem;">Dokumen Proposal / Permohonan</h6>
                </div>
                @if($locationRequest->proposal_document)
                <a href="{{ asset('storage/'.$locationRequest->proposal_document) }}" download
                    class="btn btn-sm btn-outline-danger rounded-pill">
                    <i class="ti tabler-download me-1"></i> Unduh Asli
                </a>
                @endif
            </div>
            <div class="card-body p-4">
                @if($locationRequest->proposal_document)
                @php $ext = strtolower(pathinfo($locationRequest->proposal_document, PATHINFO_EXTENSION)); @endphp
                <div class="doc-preview-container shadow-sm">
                    @if($ext == 'pdf')
                    <embed src="{{ asset('storage/'.$locationRequest->proposal_document) }}#toolbar=0"
                        type="application/pdf">
                    @else
                    <iframe
                        src="https://docs.google.com/gview?url={{ asset('storage/'.$locationRequest->proposal_document) }}&embedded=true"></iframe>
                    @endif
                </div>
                @else
                <div class="text-center py-5 bg-lighter border-dashed rounded-3">
                    <i class="ti tabler-alert-circle ti-xl text-muted opacity-50 mb-2 d-block"></i>
                    <span class="text-muted">Dokumen proposal tidak dilampirkan.</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- KOLOM KANAN: WORKFLOW & TINDAKAN --}}
    <div class="col-xl-4">

        {{-- CARD: STATUS TIMELINE --}}
        <div class="glass-card mb-4 anim-2">
            <div class="p-3 px-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-map text-primary" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Status Proses</h6>
                </div>
            </div>
            <div class="p-4">
                <div class="status-timeline">
                    <div class="timeline-item">
                        <div class="timeline-indicator bg-primary text-white"><i class="ti tabler-check"></i></div>
                        <h6 class="mb-0 fw-bold text-primary" style="font-size: 0.85rem;">Diajukan</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $locationRequest->created_at->format('d M Y') }}</small>
                    </div>
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
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $isSurveyed ? $locationRequest->review->created_at->format('d M Y') : 'Menunggu' }}</small>
                    </div>
                    @endif
                    <div class="timeline-item mb-0">
                        @php
                        $finalColor = 'bg-light border border-2 border-secondary-subtle'; $finalText = 'Menunggu Eksekusi';
                        if($locationRequest->status == 'approved') { $finalColor = 'bg-success'; $finalText = 'Disetujui'; }
                        if($locationRequest->status == 'rejected') { $finalColor = 'bg-danger'; $finalText = 'Ditolak'; }
                        @endphp
                        <div class="timeline-indicator {{ $finalColor }}">
                            @if($locationRequest->status == 'approved')
                                <i class="ti tabler-check text-white"></i>
                            @elseif($locationRequest->status == 'rejected')
                                <i class="ti tabler-x text-white"></i>
                            @else
                                <i class="ti tabler-clock text-muted" style="font-size: 0.55rem;"></i>
                            @endif
                        </div>
                        <h6 class="mb-0 fw-bold {{ $locationRequest->status == 'approved' ? 'text-success' : ($locationRequest->status == 'rejected' ? 'text-danger' : 'text-muted') }}" style="font-size: 0.85rem;">{{ $finalText }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ KARTU TINDAKAN PENGAJUAN (BISA TOLAK KAPAN SAJA) --}}
        @if(in_array($locationRequest->status, ['pending', 'surveyed']) && Auth::user()->role !== 'leader')
        <div class="glass-card mb-4 anim-3">
            <div class="p-3 px-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(135deg, rgba(255, 171, 0, 0.08) 0%, rgba(255, 171, 0, 0.02) 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-alert-circle text-warning" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-warning" style="font-size: 0.85rem;">Tindakan Pengajuan</h6>
                </div>
            </div>
            <div class="p-4">

                @if($locationRequest->request_type == 'add' && $locationRequest->status == 'pending')
                <div class="text-start p-3 rounded-3 mb-4" style="background: linear-gradient(135deg, rgba(0, 186, 209, 0.06) 0%, rgba(0, 186, 209, 0.02) 100%); border: 1px dashed rgba(0, 186, 209, 0.25);">
                    <p class="mb-0 text-info" style="font-size: 0.82rem; line-height: 1.6;">
                        <i class="ti tabler-info-circle me-1"></i> Silakan input <b>Hasil Survey</b> di bawah untuk menyetujui pengajuan ini. Atau klik <b>Tolak</b> jika data tidak valid.
                    </p>
                </div>
                @elseif($locationRequest->request_type == 'add' && $locationRequest->status == 'surveyed')
                <div class="text-start p-3 rounded-3 mb-4" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.06) 0%, rgba(40, 199, 111, 0.02) 100%); border: 1px dashed rgba(40, 199, 111, 0.25);">
                    <p class="mb-0 text-success" style="font-size: 0.82rem; line-height: 1.6;">
                        <i class="ti tabler-check me-1"></i> Survey selesai. Silakan Setujui untuk memetakan lokasi ini ke Ruas Jalan.
                    </p>
                </div>
                @else
                <div class="text-start p-3 rounded-3 mb-4" style="background: linear-gradient(135deg, rgba(234, 84, 85, 0.06) 0%, rgba(234, 84, 85, 0.02) 100%); border: 1px dashed rgba(234, 84, 85, 0.25);">
                    <p class="mb-0 text-danger" style="font-size: 0.82rem; line-height: 1.6;">
                        <i class="ti tabler-alert-circle me-1"></i> <strong>Awas!</strong> Menyetujui ini akan langsung <b>mencabut</b> titik parkir & mengurangi setoran.
                    </p>
                </div>
                @endif

                <div class="d-grid gap-3">
                    {{-- Tombol Approve hanya muncul jika (Remove) ATAU (Add + Sudah Survey) --}}
                    @if($locationRequest->request_type == 'remove' || ($locationRequest->request_type == 'add' &&
                    $locationRequest->status == 'surveyed'))
                    <button type="button" class="btn btn-success fw-bold py-2 shadow-sm rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#approveModal">
                        <i class="ti tabler-check me-1"></i> Setujui & Update PKS
                    </button>
                    @endif

                    {{-- ✅ Tombol Tolak selalu standby! --}}
                    <button type="button" class="btn btn-outline-danger fw-bold rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                        <i class="ti tabler-x me-1"></i> Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($locationRequest->review)
        <div class="glass-card mb-4 anim-3">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(135deg, rgba(0, 186, 209, 0.08) 0%, rgba(0, 186, 209, 0.02) 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-clipboard text-info" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-info" style="font-size: 0.85rem;">Keputusan Survey</h6>
                </div>
                @if($locationRequest->review->report_document)
                    <button type="button" class="btn btn-xs btn-info rounded-pill shadow-sm px-2" data-bs-toggle="modal" data-bs-target="#surveyDocModal" style="font-size: 0.65rem;">
                        <i class="ti tabler-file-text me-1"></i> B.A Survey
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
                    <small class="d-block text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">Setoran Deal (Disetujui)</small>
                    <h4 class="text-success fw-bold mb-0">Rp {{ number_format($locationRequest->review->recommended_deposit, 0, ',', '.') }}</h4>
                </div>

                <label class="fw-bold text-dark small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Catatan Lapangan:</label>
                <p class="text-dark mb-0 p-3 rounded-3 mt-1" style="background: linear-gradient(135deg, rgba(0, 186, 209, 0.06) 0%, rgba(0, 186, 209, 0.02) 100%); font-size: 0.82rem; line-height: 1.6; border: 1px solid rgba(0, 186, 209, 0.1);">
                    "{{ $locationRequest->review->survey_notes }}"
                </p>
            </div>
        </div>
        @endif

        {{-- FORM SURVEY (Hanya muncul jika belum survey) --}}
        @if($locationRequest->status == 'pending' && $locationRequest->request_type == 'add' && Auth::user()->role !== 'leader')
        <div class="glass-card mb-4 anim-3">
            <div class="p-3 px-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(135deg, rgba(105, 108, 255, 0.08) 0%, rgba(105, 108, 255, 0.02) 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-edit text-primary" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-primary" style="font-size: 0.85rem;">Input Hasil Survey</h6>
                </div>
            </div>
            <div class="p-4">
                <form action="{{ route('masterdata.location-requests.review', $locationRequest->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-floating form-floating-outline mb-4">
                        <input type="text" class="form-control flatpickr-date bg-white" id="survey_date" name="survey_date" required
                            value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD">
                        <label for="survey_date">Tanggal Survey Lapangan <span class="text-danger">*</span></label>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text">Rp</span>
                        <div class="form-floating form-floating-outline flex-grow-1">
                            <input type="number" class="form-control" id="recommended_deposit"
                                name="recommended_deposit" value="{{ (int) $locationRequest->offered_daily_deposit }}"
                                required min="0">
                            <label for="recommended_deposit">Setoran Akhir (Deal) <span
                                    class="text-danger">*</span></label>
                        </div>
                    </div>

                    <div class="form-floating form-floating-outline mb-4">
                        <textarea class="form-control" id="survey_notes" name="survey_notes" style="height: 100px"
                            required></textarea>
                        <label for="survey_notes">Catatan Petugas Lapangan <span class="text-danger">*</span></label>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Dokumen B.A / Hasil Survey
                            (Opsional)</label>
                        <input type="file" class="form-control" name="report_document" accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Dokumen ini akan dilampirkan resmi ke Profil Titik Parkir.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm rounded-pill py-2">
                        <i class="ti tabler-device-floppy me-1"></i> Simpan Hasil Survey
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- SUDAH SELESAI --}}
        @if(in_array($locationRequest->status, ['approved', 'rejected']))
            @php
                $isApproved = $locationRequest->status == 'approved';
                $finalGradient = $isApproved
                    ? 'linear-gradient(135deg, rgba(40, 199, 111, 0.06) 0%, rgba(40, 199, 111, 0.02) 100%)'
                    : 'linear-gradient(135deg, rgba(234, 84, 85, 0.06) 0%, rgba(234, 84, 85, 0.02) 100%)';
            @endphp
            <div class="glass-card mb-4 anim-4" style="background: {{ $finalGradient }}; border: 1px solid {{ $isApproved ? 'rgba(40, 199, 111, 0.2)' : 'rgba(234, 84, 85, 0.2)' }};">
                <div class="p-4 text-center">
                    <div class="avatar avatar-xl mx-auto rounded-circle mb-3 d-flex align-items-center justify-content-center" style="background: {{ $isApproved ? 'linear-gradient(135deg, rgba(40, 199, 111, 0.15) 0%, rgba(40, 199, 111, 0.05) 100%)' : 'linear-gradient(135deg, rgba(234, 84, 85, 0.15) 0%, rgba(234, 84, 85, 0.05) 100%)' }};">
                        <i class="ti {{ $isApproved ? 'tabler-checks text-success' : 'tabler-x text-danger' }}" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold {{ $isApproved ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $isApproved ? 'Pengajuan Telah Disetujui!' : 'Pengajuan Ditolak' }}
                    </h5>
                    @if($isApproved)
                        <p class="text-muted small mb-3" style="font-size: 0.8rem;">Sistem telah memproses titik parkir dan mengupdate nilai setoran pada PKS secara otomatis.</p>
                    @endif

                    <div class="text-start p-3 rounded-3 mt-3 bg-white bg-opacity-50" style="border: 1px dashed {{ $isApproved ? 'rgba(40, 199, 111, 0.25)' : 'rgba(234, 84, 85, 0.25)' }}; backdrop-filter: blur(5px);">
                        <small class="fw-bold text-dark text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Catatan Admin/Pimpinan:</small>
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
{{-- KUMPULAN MODAL POP-UP --}}
{{-- ======================================================= --}}

{{-- ✅ 1. KUMPULAN MODAL SMART COLLISION DETECTION --}}
@if($locationRequest->request_type === 'add' && isset($similarLocations) && $similarLocations->count() > 0)
@foreach($similarLocations as $simLoc)
@php
$activeAgrm = $simLoc->agreements->first();
$pemilik = $activeAgrm ? ($activeAgrm->fieldCoordinator->user->name ?? 'Data Korlap Hilang') : 'Tidak Terikat PKS
(Kosong)';
$noPks = $activeAgrm ? $activeAgrm->agreement_number : '-';
$hasMap = $simLoc->latitude && $simLoc->longitude;
@endphp

<div class="modal fade" id="modalSimLoc{{ $simLoc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light py-3">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="ti tabler-user me-2 text-primary"></i>Detail Titik Serupa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">

                <ul class="list-group list-group-flush mb-4 border rounded">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-lighter">
                        <span class="text-muted fw-medium small">Nama Titik</span>
                        <span class="fw-bold text-dark text-end">{{ $simLoc->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-medium small">Ruas Jalan</span>
                        <span class="fw-bold text-dark text-end">{{ $simLoc->roadSection->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-medium small">Setoran Harian</span>
                        <span class="fw-bold text-primary text-end">Rp {{ number_format($simLoc->daily_deposit, 0, ',',
                            '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-medium small">Status Data</span>
                        <span
                            class="badge {{ $simLoc->status == 'tersedia' ? 'bg-label-success' : 'bg-label-danger' }} text-uppercase">{{
                            str_replace('_', ' ', $simLoc->status) }}</span>
                    </li>
                </ul>

                <div class="bg-label-primary p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-primary mb-2" style="font-size: 0.8rem;"><i
                            class="ti tabler-file me-1"></i> Informasi Pengelola Saat Ini</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-dark small">Korlap/Mitra:</span>
                        <span class="fw-bold text-primary small">{{ $pemilik }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-dark small">No. Kontrak PKS:</span>
                        <span class="fw-bold text-primary small">{{ $noPks }}</span>
                    </div>
                </div>

                @if($hasMap)
                <h6 class="fw-bold text-dark mb-2" style="font-size: 0.8rem;"><i class="ti tabler-map me-1"></i>
                    Titik Koordinat Peta</h6>
                <div id="map-simloc-{{ $simLoc->id }}" style="height: 180px; border-radius: 8px; z-index: 1;"
                    class="border shadow-sm"></div>
                @else
                <div class="alert alert-secondary mb-0 py-2 small d-flex align-items-center"><i
                        class="ti tabler-info-circle me-2"></i> Titik koordinat peta tidak didaftarkan pada Master
                    Data.</div>
                @endif

            </div>
            <div class="modal-footer border-top bg-light py-2 justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill"
                    data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('masterdata.parking-locations.show', $simLoc->id) }}" target="_blank"
                    class="btn btn-sm btn-primary fw-bold rounded-pill"><i class="ti tabler-external-link me-1"></i>
                    Buka Full Halaman</a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

{{-- 2. MODAL IMAGE VIEWER --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-2 justify-content-end">
                <button type="button" class="btn btn-icon btn-light rounded-circle shadow" data-bs-dismiss="modal"
                    aria-label="Close"><i class="ti tabler-x fs-4"></i></button>
            </div>
            <div class="modal-body text-center p-0">
                @if($locationRequest->image)
                <img src="{{ asset('storage/'.$locationRequest->image) }}" class="img-fluid rounded-3 shadow-lg"
                    style="max-height: 85vh; border: 4px solid #fff;">
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 3. MODAL DOKUMEN HASIL SURVEY (B.A) --}}
@if($locationRequest->review && $locationRequest->review->report_document)
<div class="modal fade" id="surveyDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 pb-3">
                <h5 class="modal-title fw-bold text-dark"><i class="ti tabler-file-text me-2 text-info"></i>Preview
                    Dokumen B.A Survey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-lighter" style="height: 75vh;">
                @php $extBA = strtolower(pathinfo($locationRequest->review->report_document, PATHINFO_EXTENSION));
                @endphp
                @if($extBA == 'pdf')
                <embed src="{{ asset('storage/'.$locationRequest->review->report_document) }}#toolbar=0"
                    type="application/pdf" width="100%" height="100%">
                @else
                <iframe
                    src="https://docs.google.com/gview?url={{ asset('storage/'.$locationRequest->review->report_document) }}&embedded=true"
                    width="100%" height="100%" border="0"></iframe>
                @endif
            </div>
            <div class="modal-footer border-0 bg-white justify-content-between">
                <small class="text-muted">Dokumen resmi Berita Acara Survey Lapangan.</small>
                <a href="{{ asset('storage/'.$locationRequest->review->report_document) }}" download
                    class="btn btn-info fw-bold rounded-pill">
                    <i class="ti tabler-download me-1"></i> Unduh Dokumen
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@if(in_array($locationRequest->status, ['pending', 'surveyed']))
{{-- 4. MODAL APPROVE --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success p-4">
                <h5 class="modal-title text-white fw-bold"><i class="ti tabler-check me-2"></i> Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('masterdata.location-requests.approve', $locationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-lighter">
                    <div class="alert alert-success bg-opacity-10 text-success border-success border-opacity-25 mb-4 d-flex align-items-start">
                        <i class="ti tabler-info-circle me-2 mt-1"></i>
                        <small>Sistem akan mengeksekusi perubahan data pada PKS dan mengirimkan pesan WhatsApp otomatis ke Pihak Kedua.</small>
                    </div>

                    @if($locationRequest->request_type == 'add')
                    <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
                        <label class="fw-bold text-dark small text-uppercase mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Data & Ruas Jalan (Wajib)</label>
                        <div class="form-floating form-floating-outline mb-3">
                            <select name="road_section_id" id="road_section_id" class="form-select select2" required>
                                <option value="">-- Cari & Pilih Ruas Jalan --</option>
                                @foreach($roadSections as $rs)
                                <option value="{{ $rs->id }}">{{ $rs->name }} (Zona {{ $rs->zone }})</option>
                                @endforeach
                            </select>
                            <label for="road_section_id">Pilih Ruas Jalan Resmi</label>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control" name="estimated_area" placeholder="Misal: 50.5">
                                    <label>Luas Lokasi Parkir (m²)</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" name="estimated_srp_r2" placeholder="0">
                                    <label>Estimasi SRP (Roda 2)</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" name="estimated_srp_r4" placeholder="0">
                                    <label>Estimasi SRP (Roda 4)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control bg-white" id="admin_note_approve" name="admin_note" style="height: 80px"
                            placeholder="Misal: Sesuai hasil rapat..."></textarea>
                        <label for="admin_note_approve">Catatan Keputusan (Opsional)</label>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold rounded-pill shadow-sm"><i class="ti tabler-brand-whatsapp me-1"></i>
                        Setujui & Kirim WA</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 5. MODAL REJECT --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger p-4">
                <h5 class="modal-title text-white fw-bold"><i class="ti tabler-x me-2"></i> Konfirmasi Penolakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('masterdata.location-requests.reject', $locationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-lighter">
                    <div class="alert alert-danger bg-opacity-10 text-danger border-danger border-opacity-25 mb-4 d-flex align-items-start">
                        <i class="ti tabler-alert-triangle me-2 mt-1"></i>
                        <small>Pengajuan akan dibatalkan permanen dan notifikasi WhatsApp penolakan akan otomatis dikirimkan ke Pihak Kedua.</small>
                    </div>
                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control bg-white border-danger" id="admin_note_reject" name="admin_note"
                            style="height: 120px" placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <label for="admin_note_reject" class="text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill shadow-sm"><i class="ti tabler-brand-whatsapp me-1"></i> Tolak & Kirim WA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('vendor-script')
@vite(["resources/assets/vendor/libs/select2/select2.js"])
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('page-script')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script type="module">
    document.addEventListener("DOMContentLoaded", function () {
        // Init Flatpickr
        flatpickr('.flatpickr-date', {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        // Select2 (Dalam Modal harus punya dropdownParent)
        if ($('.select2').length) {
            $('#approveModal').on('shown.bs.modal', function () {
                $('.select2').select2({ 
                    placeholder: '-- Cari & Pilih Ruas Jalan --', 
                    dropdownParent: $('#approveModal')
                });
            });
        }

        // Inisialisasi Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // ✅ MAP UTAMA (UNTUK DETAIL TITIK)
        @if($locationRequest->latitude && $locationRequest->longitude)
            var lat = {{ $locationRequest->latitude }};
            var lng = {{ $locationRequest->longitude }};
            var map = L.map('map').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup("<div class='text-center'><b>{{ $locationRequest->name }}</b><br>Jl. {{ $locationRequest->road_section_name }}</div>").openPopup();
        @endif

        // ✅ SCRIPT MAP MODAL SMART COLLISION (Render On Open)
        @if($locationRequest->request_type === 'add' && isset($similarLocations) && $similarLocations->count() > 0)
            @foreach($similarLocations as $simLoc)
                @if($simLoc->latitude && $simLoc->longitude)
                    const modalEl{{ $simLoc->id }} = document.getElementById('modalSimLoc{{ $simLoc->id }}');
                    let map{{ $simLoc->id }}Init = false;

                    modalEl{{ $simLoc->id }}.addEventListener('shown.bs.modal', function () {
                        if (!map{{ $simLoc->id }}Init) {
                            const latSim = {{ $simLoc->latitude }};
                            const lngSim = {{ $simLoc->longitude }};
                            
                            const mapSim = L.map('map-simloc-{{ $simLoc->id }}').setView([latSim, lngSim], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapSim);
                            
                            var redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]});
                            
                            L.marker([latSim, lngSim], {icon: redIcon})
                                .addTo(mapSim)
                                .bindPopup("<b class='text-primary'>{{ $simLoc->name }}</b><br>Rp {{ number_format($simLoc->daily_deposit, 0, ',', '.') }}")
                                .openPopup();
                                
                            map{{ $simLoc->id }}Init = true;
                        }
                    });
                @endif
            @endforeach
        @endif
    });

    // FUNGSI COPY TO CLIPBOARD DENGAN ANIMASI DYNAMIC ISLAND
    function copyToClipboard(text) {
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        
        const island = document.getElementById('dynamicIsland');
        island.classList.add('show');
        setTimeout(() => {
            island.classList.remove('show');
        }, 2500);
    }
</script>
@endsection