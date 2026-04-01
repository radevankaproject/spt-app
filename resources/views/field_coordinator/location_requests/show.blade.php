@extends('layouts.app')

@section('title', 'Detail Pengajuan Titik')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 320px; border-radius: 12px; border: 1px solid #e5e7eb; z-index: 1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .doc-preview-container { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #f8f9fa; height: 600px; }
        .doc-preview-container iframe, .doc-preview-container embed { width: 100%; height: 100%; border: none; }
        
        /* Premium Image Hover Effect */
        .img-zoom-container { position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 2px solid #fff; }
        .img-zoom-container img { transition: transform 0.4s ease; width: 100%; height: 320px; object-fit: cover; }
        .img-zoom-container:hover img { transform: scale(1.05); }
        .img-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .img-zoom-container:hover .img-overlay { opacity: 1; }
        
        /* Status Timeline Tracker */
        .status-timeline { border-left: 2px dashed #e5e7eb; padding-left: 25px; margin-left: 15px; position: relative; }
        .timeline-item { position: relative; margin-bottom: 25px; }
        .timeline-indicator { position: absolute; left: -35px; top: 0; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 0 0 3px #f1f5f9; }
        
        /* ✅ DYNAMIC ISLAND CSS */
        .dynamic-island {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px);
            background: rgba(15, 23, 42, 0.95); color: #fff; padding: 12px 24px; border-radius: 50px;
            font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 9999; backdrop-filter: blur(10px);
        }
        .dynamic-island.show { transform: translateX(-50%) translateY(0); }
    </style>
@endpush

@section('content')

{{-- ✅ DYNAMIC ISLAND COMPONENT --}}
<div id="dynamicIsland" class="dynamic-island">
    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
        <i class="ri ri-check-line text-dark"></i>
    </div>
    <span>Tersalin ke Papan Klip!</span>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Pengajuan /</span> Detail Status</h4>
        <span class="text-muted small">Nomor Tiket: #REQ-{{ str_pad($locationRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
    </div>
    <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-outline-secondary shadow-sm mt-3 mt-md-0 rounded-pill px-4">
        <i class="ri ri-arrow-left-line me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    {{-- ======================================================= --}}
    {{-- KOLOM KIRI: DATA & VISUAL (KORLAP VIEW) --}}
    {{-- ======================================================= --}}
    <div class="col-xl-8">
        {{-- CARD 1: INFORMASI UMUM --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="ri ri-information-line me-2 text-primary"></i>Informasi Pengajuan</h6>
                @if($locationRequest->status == 'pending') <span class="badge bg-label-warning rounded-pill px-3 py-2"><i class="ri ri-time-line me-1"></i> Menunggu Review Dinas</span>
                @elseif($locationRequest->status == 'surveyed') <span class="badge bg-label-info rounded-pill px-3 py-2"><i class="ri ri-clipboard-line me-1"></i> Telah Disurvey</span>
                @elseif($locationRequest->status == 'approved') <span class="badge bg-label-success rounded-pill px-3 py-2"><i class="ri ri-check-double-line me-1"></i> Disetujui</span>
                @else <span class="badge bg-label-danger rounded-pill px-3 py-2"><i class="ri ri-close-circle-line me-1"></i> Ditolak</span> @endif
            </div>
            
            <div class="card-body p-4 bg-white">
                <div class="row g-4">
                    <div class="col-md-6 border-end-md">
                        <small class="text-muted fw-bold text-uppercase d-block mb-2">Tipe Permohonan</small>
                        @if($locationRequest->request_type == 'add')
                            <span class="d-inline-flex align-items-center px-3 py-2 rounded-3 bg-lighter fw-bold text-success border border-success-subtle">
                                <i class="ri ri-add-circle-line ri-xl me-2"></i> Penambahan Titik Baru
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center px-3 py-2 rounded-3 bg-lighter fw-bold text-danger border border-danger-subtle">
                                <i class="ri ri-delete-bin-line ri-xl me-2"></i> Pencabutan Titik
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block mb-2">Nomor PKS Terkait</small>
                        <span class="fw-bold text-primary fs-5">{{ $locationRequest->agreement->agreement_number }}</span>
                    </div>

                    <div class="col-12"><hr class="my-2 border-light"></div>

                    @if($locationRequest->request_type == 'add')
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Nama Lokasi (Usulan)</small>
                            <span class="fw-bold text-dark fs-6">{{ $locationRequest->name }}</span><br>
                            <small class="text-muted">Jl. {{ $locationRequest->road_section_name }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Penawaran Setoran Anda</small>
                            <span class="fw-bold text-warning fs-5">Rp {{ number_format($locationRequest->offered_daily_deposit, 0, ',', '.') }}</span> <small class="text-muted">/ hari</small>
                        </div>
                    @else
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Titik yang Dicabut</small>
                            <span class="fw-bold text-dark fs-6">{{ $locationRequest->parkingLocation->name ?? 'N/A' }}</span><br>
                            <small class="text-muted">ID Ruas: {{ $locationRequest->parkingLocation->roadSection->name ?? '-' }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Nilai Setoran</small>
                            <span class="fw-bold text-danger fs-5">Rp {{ number_format($locationRequest->parkingLocation->daily_deposit ?? 0, 0, ',', '.') }}</span> <small class="text-muted">/ hari</small>
                        </div>
                    @endif

                    <div class="col-12">
                        <div class="bg-lighter p-3 rounded-3 border-dashed">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Alasan Anda</small>
                            <p class="mb-0 text-dark">"{{ $locationRequest->reason }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($locationRequest->request_type == 'add')
        {{-- CARD 2: PETA & GAMBAR (PREMIUM) --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="ri ri-map-2-line me-2 text-primary"></i>Visualisasi Lokasi</h6>
                @if($locationRequest->latitude && $locationRequest->longitude)
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm" onclick="copyToClipboard('https://www.google.com/maps?q={{ $locationRequest->latitude }},{{ $locationRequest->longitude }}')">
                        <i class="ri ri-links-line me-1"></i> Salin Link G-Maps
                    </button>
                @endif
            </div>
            <div class="card-body p-4 bg-white">
                <div class="row g-4">
                    {{-- MAP --}}
                    <div class="col-md-6">
                        @if($locationRequest->latitude && $locationRequest->longitude)
                            <div id="map" class="mb-3"></div>
                            <div class="text-center text-muted small"><i class="ri ri-map-pin-2-fill text-danger"></i> {{ $locationRequest->latitude }}, {{ $locationRequest->longitude }}</div>
                        @else
                            <div class="alert bg-lighter text-center py-5 border-dashed rounded-3 h-100 d-flex flex-column justify-content-center">
                                <i class="ri ri-map-pin-off-line ri-3x text-muted opacity-50 mb-2"></i>
                                <span class="text-muted">Anda tidak melampirkan koordinat peta.</span>
                            </div>
                        @endif
                    </div>

                    {{-- FOTO LOKASI --}}
                    <div class="col-md-6">
                        @if($locationRequest->image)
                            <div class="img-zoom-container" data-bs-toggle="modal" data-bs-target="#imageModal">
                                <img src="{{ asset('storage/'.$locationRequest->image) }}" alt="Foto Lapangan">
                                <div class="img-overlay">
                                    <div class="bg-white rounded-circle p-3 shadow text-primary">
                                        <i class="ri ri-zoom-in-line ri-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3 text-muted small"><i class="ri ri-drag-move-line align-middle"></i> Klik untuk memperbesar</div>
                        @else
                            <div class="alert bg-lighter text-center py-5 border-dashed rounded-3 h-100 d-flex flex-column justify-content-center">
                                <i class="ri ri-image-line ri-3x text-muted opacity-50 mb-2"></i>
                                <span class="text-muted">Anda tidak melampirkan foto.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: DOKUMEN PROPOSAL ANDA --}}
        @if($locationRequest->proposal_document)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header border-bottom py-3 px-4 bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="ri ri-file-text-line me-2 text-primary"></i>Dokumen Proposal Anda</h6>
                <a href="{{ asset('storage/'.$locationRequest->proposal_document) }}" download class="btn btn-sm btn-primary rounded-pill shadow-sm">
                    <i class="ri ri-download-cloud-2-line me-1"></i> Unduh File
                </a>
            </div>
            <div class="card-body p-4 bg-white">
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
    {{-- KOLOM KANAN: STATUS & FEEDBACK DINAS --}}
    {{-- ======================================================= --}}
    <div class="col-xl-4">
        
        {{-- CARD: STATUS TIMELINE --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-muted mb-4"><i class="ri ri-guide-line me-1"></i> Pelacakan Pengajuan</h6>
                <div class="status-timeline">
                    {{-- 1. Diajukan --}}
                    <div class="timeline-item">
                        <div class="timeline-indicator bg-primary text-white"><i class="ri ri-check-line text-white"></i></div>
                        <h6 class="mb-0 fw-bold text-primary">Pengajuan Diterima</h6>
                        <small class="text-muted">{{ $locationRequest->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    
                    {{-- 2. Survey (Khusus Add) --}}
                    @if($locationRequest->request_type == 'add')
                        <div class="timeline-item">
                            @php 
                                $isSurveyed = $locationRequest->review ? true : false; 
                                $sColor = $isSurveyed ? 'bg-info' : 'bg-lighter border-secondary opacity-50';
                                $sIcon = $isSurveyed ? '<i class="ri ri-check-line text-white"></i>' : '<i class="ri ri-time-line text-muted"></i>';
                            @endphp
                            <div class="timeline-indicator {{ $sColor }}">{!! $sIcon !!}</div>
                            <h6 class="mb-0 fw-bold {{ $isSurveyed ? 'text-info' : 'text-muted' }}">Survey Dinas</h6>
                            <small class="text-muted">{{ $isSurveyed ? $locationRequest->review->created_at->format('d M Y') : 'Menunggu Jadwal' }}</small>
                        </div>
                    @endif

                    {{-- 3. Finalisasi --}}
                    <div class="timeline-item mb-0">
                        @php
                            $fColor = 'bg-lighter border-secondary opacity-50'; $fText = 'Menunggu Keputusan'; $fIcon = '<i class="ri ri-time-line text-muted"></i>';
                            if($locationRequest->status == 'approved') { $fColor = 'bg-success text-white'; $fText = 'Disetujui'; $fIcon = '<i class="ri ri-check-double-line text-white"></i>'; }
                            if($locationRequest->status == 'rejected') { $fColor = 'bg-danger text-white'; $fText = 'Ditolak'; $fIcon = '<i class="ri ri-close-line text-white"></i>'; }
                        @endphp
                        <div class="timeline-indicator {{ $fColor }}">{!! $fIcon !!}</div>
                        <h6 class="mb-0 fw-bold {{ str_replace('bg-', 'text-', str_replace(' opacity-50', '', str_replace(' text-white', '', $fColor))) }}">{{ $fText }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: HASIL SURVEY (Jika Ada) --}}
        @if($locationRequest->review)
            <div class="card border-0 shadow-sm mb-4 rounded-4 border-start border-4 border-info">
                <div class="card-header bg-white border-bottom pb-2 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-info"><i class="ri ri-clipboard-line me-1"></i> Hasil Survey Dinas</h6>
                    @if($locationRequest->review->report_document)
                        <button type="button" class="btn btn-xs btn-info rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#surveyDocModal" title="Lihat B.A">
                            <i class="ri ri-file-list-3-line"></i> Dokumen B.A
                        </button>
                    @endif
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <img src="{{ $locationRequest->review->reviewer->img ? asset('storage/'.$locationRequest->review->reviewer->img) : 'https://ui-avatars.com/api/?name='.urlencode($locationRequest->review->reviewer->name).'&background=random&color=fff' }}" class="rounded-circle">
                            </div>
                            <div class="lh-1">
                                <span class="d-block fw-bold text-dark fs-6">{{ $locationRequest->review->reviewer->name ?? 'Petugas UPT' }}</span>
                                <small class="text-muted">Disurvey pd: {{ $locationRequest->review->survey_date->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-lighter p-3 rounded-3 mb-3 text-center border-dashed">
                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Setoran Disepakati (Deal)</span>
                        <h3 class="text-success fw-bold mb-0">Rp {{ number_format($locationRequest->review->recommended_deposit, 0, ',', '.') }}</h3>
                    </div>
                    
                    <label class="fw-bold text-dark small text-uppercase">Catatan Petugas Lapangan:</label>
                    <p class="text-dark mb-0 p-3 bg-label-info rounded-3 mt-1">"{{ $locationRequest->review->survey_notes }}"</p>
                </div>
            </div>
        @endif

        {{-- CARD: FINAL KEPUTUSAN (Jika Sudah Selesai) --}}
        @if(in_array($locationRequest->status, ['approved', 'rejected']))
            <div class="card border-0 shadow-sm rounded-4 border-start border-4 {{ $locationRequest->status == 'approved' ? 'border-success' : 'border-danger' }}">
                <div class="card-body p-4 bg-white">
                    <div class="text-center mb-3">
                        @if($locationRequest->status == 'approved')
                            <div class="avatar avatar-xl mx-auto bg-label-success rounded-circle mb-3"><i class="ri ri-check-double-line ri-3x"></i></div>
                            <h5 class="fw-bold text-success mb-1">Pengajuan Disetujui!</h5>
                            <p class="text-muted small">Data PKS Anda telah diperbarui secara otomatis di sistem.</p>
                        @else
                            <div class="avatar avatar-xl mx-auto bg-label-danger rounded-circle mb-3"><i class="ri ri-close-line ri-3x"></i></div>
                            <h5 class="fw-bold text-danger mb-1">Pengajuan Ditolak</h5>
                        @endif
                    </div>
                    
                    <div class="bg-lighter p-3 rounded-3 border-dashed">
                        <span class="d-block fw-bold text-dark small text-uppercase mb-1">Catatan Pimpinan/Admin:</span>
                        <p class="mb-0 {{ $locationRequest->status == 'approved' ? 'text-success' : 'text-danger' }}">
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

{{-- 1. MODAL IMAGE VIEWER --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-2 justify-content-end">
                <button type="button" class="btn btn-icon btn-light rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close"><i class="ri ri-close-line fs-4"></i></button>
            </div>
            <div class="modal-body text-center p-0">
                @if($locationRequest->image)
                    <img src="{{ asset('storage/'.$locationRequest->image) }}" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; border: 4px solid #fff;">
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL DOKUMEN HASIL SURVEY (B.A) --}}
@if($locationRequest->review && $locationRequest->review->report_document)
<div class="modal fade" id="surveyDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 pb-3">
                <h5 class="modal-title fw-bold text-dark"><i class="ri ri-file-text-line me-2 text-info"></i>Berita Acara (B.A) Survey Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-lighter" style="height: 75vh;">
                @php $extBA = strtolower(pathinfo($locationRequest->review->report_document, PATHINFO_EXTENSION)); @endphp
                @if($extBA == 'pdf')
                    <embed src="{{ asset('storage/'.$locationRequest->review->report_document) }}#toolbar=0" type="application/pdf" width="100%" height="100%">
                @else
                    <iframe src="https://docs.google.com/gview?url={{ asset('storage/'.$locationRequest->review->report_document) }}&embedded=true" width="100%" height="100%" border="0"></iframe>
                @endif
            </div>
            <div class="modal-footer border-0 bg-white justify-content-between">
                <small class="text-muted">Diunggah oleh Petugas UPT Perparkiran.</small>
                <a href="{{ asset('storage/'.$locationRequest->review->report_document) }}" download class="btn btn-info fw-bold rounded-pill px-4">
                    <i class="ri ri-download-line me-1"></i> Unduh Arsip
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('vendors-js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Leaflet Map Init (Hanya jalan jika ada koordinat)
            @if($locationRequest->request_type == 'add' && $locationRequest->latitude && $locationRequest->longitude)
                var lat = {{ $locationRequest->latitude }};
                var lng = {{ $locationRequest->longitude }};
                
                var map = L.map('map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Custom Icon Premium
                var redIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
                });

                var marker = L.marker([lat, lng], {icon: redIcon}).addTo(map);
                marker.bindPopup("<div class='text-center fw-bold'>{{ $locationRequest->name }}</div>").openPopup();
            @endif
        });

        // ✅ FUNGSI COPY TO CLIPBOARD DENGAN ANIMASI DYNAMIC ISLAND
        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            
            // Panggil Animasi Dynamic Island
            var island = document.getElementById('dynamicIsland');
            island.classList.add('show');
            
            // Sembunyikan otomatis setelah 3 detik
            setTimeout(function() {
                island.classList.remove('show');
            }, 3000);
        }
    </script>
@endpush