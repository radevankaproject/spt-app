@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Pengajuan Titik')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 320px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        z-index: 1;
    }

    .doc-preview-container {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        height: 600px;
    }

    .doc-preview-container iframe,
    .doc-preview-container embed {
        width: 100%;
        height: 100%;
        border: none;
    }

    .img-zoom-container {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .img-zoom-container img {
        transition: transform 0.4s ease;
        width: 100%;
        height: 320px;
        object-fit: cover;
    }

    .img-zoom-container:hover img {
        transform: scale(1.05);
    }

    .img-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .img-zoom-container:hover .img-overlay {
        opacity: 1;
    }

    .status-timeline {
        border-left: 2px dashed #e5e7eb;
        padding-left: 20px;
        margin-left: 10px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-indicator {
        position: absolute;
        left: -29px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
    }

    /* DYNAMIC ISLAND CSS */
    /* ✅ DYNAMIC ISLAND CSS (PERBAIKAN CENTER MUTLAK) */
    .dynamic-island {
        position: fixed;
        top: 20px;
        /* Jurus Center Mutlak */
        left: 0;
        right: 0;
        margin: 0 auto;

        /* Animasi cuma main di sumbu Y (atas-bawah) */
        transform: translateY(-100px);

        background: #000;
        color: #fff;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        /* Pastikan isi ke tengah */
        gap: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 9999;

        /* Anti Tiang Listrik */
        white-space: nowrap;
        width: max-content;
        max-width: 90vw;
    }

    .dynamic-island.show {
        transform: translateY(0);
        /* Hilangkan translateX di sini juga */
    }

    /* Opsional: Kalau mau persis di tengah KONTEN (mengabaikan sidebar kiri) */
    @media (min-width: 1200px) {
        .dynamic-island {
            /* 260px adalah estimasi lebar sidebar template antum */
            margin-left: calc(260px / 2);
        }
    }

    .hover-link {
        transition: all 0.2s ease;
    }

    .hover-link:hover {
        color: #696cff !important;
        transform: translateX(3px);
    }
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

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Persetujuan Titik /</span> Detail Pengajuan</h4>
        <span class="text-muted small">ID Pengajuan: #REQ-{{ str_pad($locationRequest->id, 5, '0', STR_PAD_LEFT)
            }}</span>
    </div>
    <a href="{{ route('masterdata.location-requests.index') }}"
        class="btn btn-outline-secondary shadow-sm mt-3 mt-md-0">
        <i class="ti tabler-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="ti tabler-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="ti tabler-alert-triangle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
    {{-- KOLOM KIRI --}}
    <div class="col-xl-8">
        {{-- CARD 1: INFORMASI --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3 bg-label-primary rounded p-2">
                            <i
                                class="ri {{ $locationRequest->request_type == 'add' ? 'ti tabler-map-pin-plus' : 'ti tabler-trash' }} ti-lg text-primary"></i>
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
                                class="ti tabler-checks me-1"></i> Disetujui</span>
                        @else <span class="badge bg-label-danger fs-6"><i class="ti tabler-circle-x me-1"></i>
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
            class="card border-0 shadow-sm mb-4 rounded-4 {{ $similarLocations->count() > 0 ? 'border-start border-4 border-warning' : 'border-start border-4 border-success' }}">
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
                    <i class="ti tabler-alert-triangle-filled ti tabler-xl me-3 mt-1"></i>
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
                        <i class="ti tabler-checks ti tabler-xl"></i>
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom py-3 bg-transparent">
                <h6 class="mb-0 fw-bold"><i class="ti tabler-camera-lens me-2 text-info"></i>Visual & Lokasi Geografis
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-dark mb-0">Peta Satelit</label>
                            @if($locationRequest->latitude && $locationRequest->longitude)
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill"
                                onclick="copyToClipboard('https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}')">
                                <i class="ti tabler-file-copy me-1"></i> Salin Link
                            </button>
                            @endif
                        </div>

                        @if($locationRequest->latitude && $locationRequest->longitude)
                        <div id="map" class="mb-3 shadow-sm"></div>
                        <a href="https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}"
                            target="_blank" class="btn btn-sm btn-primary w-100 shadow-sm">
                            <i class="ti tabler-google-filled me-1"></i> Buka di Google Maps
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
                        <div class="text-center mt-2 text-muted small"><i class="ti tabler-drag-move me-1"></i> Klik
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
        <div class="card border-0 shadow-sm">
            <div
                class="card-header border-bottom py-3 bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="ti tabler-file-pdf-2 me-2 text-danger"></i>Dokumen Proposal /
                    Permohonan</h6>
                @if($locationRequest->proposal_document)
                <a href="{{ asset('storage/'.$locationRequest->proposal_document) }}" download
                    class="btn btn-sm btn-outline-danger rounded-pill">
                    <i class="ti tabler-cloud-download me-1"></i> Unduh Asli
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
                    <i class="ti tabler-file-warning ti-xl text-muted opacity-50 mb-2 d-block"></i>
                    <span class="text-muted">Dokumen proposal tidak dilampirkan.</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- KOLOM KANAN: WORKFLOW & TINDAKAN --}}
    <div class="col-xl-4">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body pt-4">
                <h6 class="fw-bold text-uppercase text-muted mb-4"><i class="ti tabler-guide me-1"></i> Status Proses
                </h6>
                <div class="status-timeline">
                    <div class="timeline-item">
                        <div class="timeline-indicator bg-primary"></div>
                        <h6 class="mb-0 fw-bold text-primary">Diajukan</h6>
                        <small class="text-muted">{{ $locationRequest->created_at->format('d M Y') }}</small>
                    </div>
                    @if($locationRequest->request_type == 'add')
                    <div class="timeline-item">
                        <div
                            class="timeline-indicator {{ $locationRequest->status == 'pending' ? 'bg-secondary opacity-50' : 'bg-info' }}">
                        </div>
                        <h6
                            class="mb-0 fw-bold {{ $locationRequest->status == 'pending' ? 'text-muted' : 'text-info' }}">
                            Survey Dinas</h6>
                        <small class="text-muted">{{ $locationRequest->review ?
                            $locationRequest->review->created_at->format('d M Y') : 'Menunggu' }}</small>
                    </div>
                    @endif
                    <div class="timeline-item mb-0">
                        @php
                        $finalColor = 'bg-secondary opacity-50'; $finalText = 'Menunggu Eksekusi';
                        if($locationRequest->status == 'approved') { $finalColor = 'bg-success'; $finalText =
                        'Disetujui'; }
                        if($locationRequest->status == 'rejected') { $finalColor = 'bg-danger'; $finalText = 'Ditolak';
                        }
                        @endphp
                        <div class="timeline-indicator {{ $finalColor }}"></div>
                        <h6
                            class="mb-0 fw-bold {{ str_replace('bg-', 'text-', str_replace(' opacity-50', '', $finalColor)) }}">
                            {{ $finalText }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ KARTU TINDAKAN PENGAJUAN (BISA TOLAK KAPAN SAJA) --}}
        @if(in_array($locationRequest->status, ['pending', 'surveyed']) && Auth::user()->role !== 'leader')
        <div class="card border-0 shadow-sm border-start border-4 border-warning mb-4">
            <div class="card-header pb-2 bg-transparent">
                <h5 class="card-title mb-0 fw-bold text-warning"><i class="ti tabler-alert-triangle me-1"></i> Tindakan
                    Pengajuan</h5>
            </div>
            <div class="card-body p-4">

                @if($locationRequest->request_type == 'add' && $locationRequest->status == 'pending')
                <div class="alert alert-info p-3 small mb-4 shadow-sm border-0">
                    <i class="ti tabler-info-circle me-1 ti tabler-lg"></i> Silakan input <b>Hasil Survey</b> di bawah untuk
                    menyetujui pengajuan ini. Atau klik <b>Tolak</b> jika data tidak valid.
                </div>
                @elseif($locationRequest->request_type == 'add' && $locationRequest->status == 'surveyed')
                <div class="alert alert-success p-3 small mb-4 shadow-sm border-0">
                    <i class="ti tabler-check me-1 ti tabler-lg"></i> Survey selesai. Silakan Setujui untuk memetakan lokasi
                    ini ke Ruas Jalan.
                </div>
                @else
                <div class="alert alert-danger p-3 small mb-4 shadow-sm border-0">
                    <i class="ti tabler-alert-triangle me-1 ti tabler-lg"></i> <strong>Awas!</strong> Menyetujui ini akan
                    langsung <b>mencabut</b> titik parkir & mengurangi setoran.
                </div>
                @endif

                <div class="d-grid gap-3">
                    {{-- Tombol Approve hanya muncul jika (Remove) ATAU (Add + Sudah Survey) --}}
                    @if($locationRequest->request_type == 'remove' || ($locationRequest->request_type == 'add' &&
                    $locationRequest->status == 'surveyed'))
                    <button type="button" class="btn btn-success fw-bold py-2 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#approveModal">
                        <i class="ti tabler-check-filled me-1"></i> Setujui & Update PKS
                    </button>
                    @endif

                    {{-- ✅ Tombol Tolak selalu standby! --}}
                    <button type="button" class="btn btn-outline-danger fw-bold" data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                        <i class="ti tabler-x me-1"></i> Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($locationRequest->review)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-header pb-2 bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-info"><i class="ti tabler-file-search me-1"></i> Keputusan
                    Survey</h6>
                @if($locationRequest->review->report_document)
                <button type="button" class="btn btn-xs btn-outline-info rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#surveyDocModal" title="Lihat Dokumen Hasil Survey/BA">
                    <i class="ti tabler-file-text"></i> B.A Survey
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                        <img src="{{ $locationRequest->review->reviewer->img ? asset('storage/'.$locationRequest->review->reviewer->img) : 'https://ui-avatars.com/api/?name='.urlencode($locationRequest->review->reviewer->name).'&background=random&color=fff' }}"
                            class="rounded-circle">
                    </div>
                    <div>
                        <span class="d-block fw-bold text-dark fs-6">{{ $locationRequest->review->reviewer->name ??
                            'N/A' }}</span>
                        <small class="text-muted">Petugas Survey ({{ $locationRequest->review->survey_date->format('d M
                            Y') }})</small>
                    </div>
                </div>

                <div class="bg-lighter p-3 rounded mb-3 text-center">
                    <span class="d-block text-muted small fw-bold text-uppercase mb-1">Setoran Deal (Disetujui)</span>
                    <h3 class="text-success fw-bold mb-0">Rp {{
                        number_format($locationRequest->review->recommended_deposit, 0, ',', '.') }}</h3>
                </div>

                <label class="fw-bold text-dark small text-uppercase">Catatan Lapangan:</label>
                <p class="text-dark mb-0 fst-italic">"{{ $locationRequest->review->survey_notes }}"</p>
            </div>
        </div>
        @endif

        {{-- FORM SURVEY (Hanya muncul jika belum survey) --}}
        @if($locationRequest->status == 'pending' && $locationRequest->request_type == 'add' && Auth::user()->role !== 'leader')
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header pb-2 bg-transparent">
                <h6 class="card-title mb-0 fw-bold text-primary"><i class="ti tabler-map-pin-time me-1"></i> Input
                    Hasil Survey</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('masterdata.location-requests.review', $locationRequest->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-floating form-floating-outline mb-4">
                        <input type="date" class="form-control" id="survey_date" name="survey_date" required
                            value="{{ date('Y-m-d') }}">
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
                        <small class="text-muted d-block mt-1">Dokumen ini akan dilampirkan resmi ke Profil Titik
                            Parkir.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                        <i class="ti tabler-save me-1"></i> Simpan Hasil Survey
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- SUDAH SELESAI --}}
        @if(in_array($locationRequest->status, ['approved', 'rejected']))
        <div
            class="card border-0 shadow-sm border-start border-4 {{ $locationRequest->status == 'approved' ? 'border-success' : 'border-danger' }}">
            <div class="card-body text-center p-5">
                @if($locationRequest->status == 'approved')
                <div class="avatar avatar-xl mx-auto bg-label-success rounded-circle mb-3"><i
                        class="ti tabler-checks ti-xl"></i></div>
                <h4 class="fw-bold text-success mb-2">Telah Disetujui</h4>
                <p class="text-muted small mb-0">Sistem telah memproses titik parkir dan mengupdate nilai setoran pada
                    PKS secara otomatis.</p>
                @else
                <div class="avatar avatar-xl mx-auto bg-label-danger rounded-circle mb-3"><i
                        class="ti tabler-x ti-xl"></i></div>
                <h4 class="fw-bold text-danger mb-2">Telah Ditolak</h4>
                <p class="text-muted small mb-0">Alasan: "{{ $locationRequest->admin_note }}"</p>
                @endif
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
                        class="ti tabler-user-pin me-2 text-primary"></i>Detail Titik Serupa</h5>
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
                            class="ti tabler-file-description me-1"></i> Informasi Pengelola Saat Ini</h6>
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
                <h6 class="fw-bold text-dark mb-2" style="font-size: 0.8rem;"><i class="ti tabler-map-2 me-1"></i>
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
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white fw-bold"><i class="ti tabler-checks me-1"></i> Konfirmasi
                    Persetujuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('masterdata.location-requests.approve', $locationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-dark mb-4">Sistem akan langsung mengeksekusi perubahan data pada PKS dan mengirimkan
                        pesan WhatsApp otomatis ke Pihak Kedua. Lanjutkan?</p>

                    @if($locationRequest->request_type == 'add')
                    <div class="form-floating form-floating-outline mb-4">
                        <select name="road_section_id" id="road_section_id" class="form-select select2" required>
                            <option value="">-- Cari & Pilih Ruas Jalan --</option>
                            @foreach($roadSections as $rs)
                            <option value="{{ $rs->id }}">{{ $rs->name }} (Zona {{ $rs->zone }})</option>
                            @endforeach
                        </select>
                        <label for="road_section_id">Pilih Ruas Jalan Resmi <span class="text-danger">*</span></label>
                    </div>
                    @endif

                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control" id="admin_note_approve" name="admin_note" style="height: 80px"
                            placeholder="Misal: Sesuai hasil rapat..."></textarea>
                        <label for="admin_note_approve">Catatan Keputusan (Opsional)</label>
                    </div>
                </div>
                <div class="modal-footer bg-lighter border-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold"><i class="ti tabler-whatsapp me-1"></i>
                        Setujui & Beritahu Korlap</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 5. MODAL REJECT --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white fw-bold"><i class="ti tabler-circle-x me-1"></i> Konfirmasi
                    Penolakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('masterdata.location-requests.reject', $locationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-dark mb-4">Pengajuan akan dibatalkan permanen dan notifikasi WhatsApp penolakan akan
                        otomatis dikirimkan.</p>
                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control border-danger" id="admin_note_reject" name="admin_note"
                            style="height: 100px" placeholder="Alasan penolakan..." required></textarea>
                        <label for="admin_note_reject" class="text-danger">Alasan Penolakan <span
                                class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="modal-footer bg-lighter border-0">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="ti tabler-whatsapp me-1"></i> Tolak
                        & Beritahu Korlap</button>
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
<script type="module">
        document.addEventListener("DOMContentLoaded", function() {
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