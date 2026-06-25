@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Pengajuan Titik')

@section('page-style')
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

    /* ===== HERO HEADER ===== */
    .page-hero {
        background: linear-gradient(135deg, #ff9f43 0%, #ffb673 100%);
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
        height: 350px;
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        z-index: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* ===== IMAGE PREVIEW ===== */
    .image-preview-wrapper {
        position: relative;
        display: inline-block;
        transition: all 0.3s ease;
    }
    .image-preview-wrapper img {
        max-height: 220px;
        border-radius: 1rem;
        object-fit: cover;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    .image-preview-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        z-index: 2;
    }

    /* ===== SECTION DIVIDER ===== */
    .section-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
    }
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, rgba(255,159,67,0.3), transparent);
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-1 { animation: fadeInUp 0.5s ease 0.1s both; }
    .anim-2 { animation: fadeInUp 0.5s ease 0.2s both; }
</style>
@endsection

@section('content')

{{-- HERO HEADER --}}
<div class="page-hero text-white mb-4 shadow-lg anim-1">
    <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-warning rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.7rem;">
                    <i class="ti tabler-edit me-1"></i> MODE EDIT
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Edit Pengajuan Titik</h4>
            <p class="text-white-50 mb-0" style="font-size: 0.8rem;">
                Hanya pengajuan berstatus <strong class="text-white">Pending</strong> yang dapat diedit.
            </p>
        </div>
        <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-white text-warning fw-bold shadow-sm rounded-pill px-4 mt-3 mt-md-0">
            <i class="ti tabler-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <i class="ti tabler-pencil position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
</div>

{{-- FORM CARD --}}
<div class="glass-card anim-2">
    <div class="p-4">
        <form action="{{ route('field_coordinator.location-requests.update', $locationRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="request_type" value="{{ $locationRequest->request_type }}">

            <div class="row g-4">
                <div class="col-12">
                    @php
                        $alertColor = $locationRequest->request_type == 'add' ? 'success' : 'danger';
                        $alertIcon = $locationRequest->request_type == 'add' ? 'tabler-map-pin-plus' : 'tabler-map-pin-minus';
                        $alertText = $locationRequest->request_type == 'add' ? 'Penambahan Titik Baru' : 'Pencabutan Titik';
                    @endphp
                    <div class="alert d-flex align-items-center mb-0 border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, rgba(var(--bs-{{ $alertColor }}-rgb), 0.1) 0%, rgba(var(--bs-{{ $alertColor }}-rgb), 0.02) 100%); border-left: 4px solid var(--bs-{{ $alertColor }}) !important;">
                        <div class="avatar avatar-sm bg-{{ $alertColor }} rounded-circle me-3 d-flex align-items-center justify-content-center text-white">
                            <i class="ti {{ $alertIcon }}"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading fw-bold text-{{ $alertColor }} mb-1" style="font-size: 0.85rem;">Tipe Pengajuan: {{ $alertText }}</h6>
                            <span class="mb-0 text-muted" style="font-size: 0.75rem;">Tipe pengajuan tidak dapat diubah pada mode Edit.</span>
                        </div>
                    </div>
                </div>

                @if($locationRequest->request_type == 'remove')
                <div class="col-md-12">
                    <div class="p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(234, 84, 85, 0.04) 0%, rgba(234, 84, 85, 0.01) 100%); border: 1px solid rgba(234, 84, 85, 0.15);">
                        <label for="parking_location_id" class="form-label fw-bold text-dark">Pilih Titik Parkir <span class="text-danger">*</span></label>
                        <select name="parking_location_id" id="parking_location_id" class="form-select select2" required>
                            <option value="">-- Pilih Titik Parkir Anda --</option>
                            @foreach($activeLocations as $location)
                            <option value="{{ $location->id }}" {{ $locationRequest->parking_location_id == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} (Jl. {{ $location->roadSection->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <div class="row g-4 p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(255, 159, 67, 0.04) 0%, rgba(255, 159, 67, 0.01) 100%); border: 1px solid rgba(255, 159, 67, 0.15);">

                        <div class="col-12">
                            <div class="section-divider">
                                <span class="fw-bold text-warning" style="font-size: 0.8rem;"><i class="ti tabler-user me-1"></i> Informasi Dasar</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Nama Ruas Jalan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="road_section_name" value="{{ $locationRequest->road_section_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Nama Titik/Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $locationRequest->name }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Penawaran Setoran Harian (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text fw-bold bg-white">Rp</span>
                                <input type="number" class="form-control form-control-lg border-start-0" name="offered_daily_deposit" value="{{ (int) $locationRequest->offered_daily_deposit }}" min="0" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="section-divider">
                                <span class="fw-bold text-warning" style="font-size: 0.8rem;"><i class="ti tabler-map-pin me-1"></i> Pemetaan & Visualisasi</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Koordinat Map (Latitude, Longitude)</label>
                            <div class="input-group mb-3 shadow-sm">
                                <span class="input-group-text bg-white text-warning"><i class="ti tabler-map-pin"></i></span>
                                <input type="text" class="form-control border-start-0" id="koordinat_gabungan" name="koordinat_gabungan" placeholder="Misal: 0.507, 101.447">
                                <button class="btn btn-warning text-white" type="button" id="btn-my-location"><i class="ti tabler-focus"></i> Titik Saya</button>
                            </div>
                            <div id="map"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ $locationRequest->latitude }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ $locationRequest->longitude }}">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Update Foto Lokasi</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*">

                            <div id="compression-wrapper" class="mt-3 p-3 rounded-3 bg-white shadow-sm" style="display: none; border: 1px solid rgba(0,0,0,0.06);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-primary" id="compression-status"><i class="ti tabler-loader-4 ti tabler-spin me-1"></i> Mengompresi...</small>
                                    <small class="fw-bold text-dark" id="compression-size"></small>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div id="compression-progress" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>

                            <div id="image_preview_container" class="mt-3 text-center" style="{{ $locationRequest->image ? 'display:block;' : 'display:none;' }}">
                                <div class="image-preview-wrapper">
                                    <img id="image_preview" src="{{ $locationRequest->image ? asset('storage/'.$locationRequest->image) : '' }}" alt="Preview">
                                    <span class="badge bg-success image-preview-badge p-2 rounded-circle shadow"><i class="ti tabler-check"></i></span>
                                </div>
                                @if($locationRequest->image)
                                <small class="text-muted d-block mt-2" id="preview-text" style="font-size: 0.72rem;">Menampilkan foto tersimpan saat ini.</small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold" style="font-size: 0.8rem;">Update Surat Permohonan</label>
                            <input class="form-control" type="file" name="proposal_document" accept=".pdf,.doc,.docx">
                            @if($locationRequest->proposal_document)
                            <div class="d-flex align-items-center mt-3 p-3 rounded-3 bg-white shadow-sm" style="border: 1px solid rgba(0,0,0,0.06);">
                                <div class="avatar avatar-sm bg-info bg-opacity-10 text-info me-3 rounded"><i class="ti tabler-file-text fs-4"></i></div>
                                <div>
                                    <span class="text-dark fw-bold d-block mb-1" style="font-size: 0.8rem;">Dokumen Lama Tersimpan</span>
                                    <a href="{{ asset('storage/' . $locationRequest->proposal_document) }}" target="_blank" class="btn btn-xs btn-outline-info rounded-pill"><i class="ti tabler-external-link me-1"></i> Buka File</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-12 mt-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.8rem;">Alasan Pengajuan <span class="text-danger">*</span></label>
                    <textarea class="form-control shadow-sm" name="reason" rows="4" required>{{ $locationRequest->reason }}</textarea>
                </div>
            </div>

            <div class="pt-4 mt-4 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="ti tabler-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" id="submit-btn" class="btn btn-warning text-white btn-lg px-5 shadow-sm rounded-pill fw-bold">
                    <i class="ti tabler-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
@vite(["resources/assets/vendor/libs/select2/select2.js"])
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endsection

@section('page-script')
<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        if ($('.select2').length) {
            $('.select2').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({ placeholder: '-- Pilih Titik Parkir --', dropdownParent: $(this).parent() });
            });
        }

        @if($locationRequest->request_type == 'add')
            let map, marker;
            const coordInput = document.getElementById('koordinat_gabungan');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            let initLat = {{ $locationRequest->latitude ?: '0.5070677' }};
            let initLng = {{ $locationRequest->longitude ?: '101.4477793' }};

            map = L.map('map').setView([initLat, initLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            var redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]});
            marker = L.marker([initLat, initLng], {icon: redIcon, draggable: true}).addTo(map);

            @if($locationRequest->latitude && $locationRequest->longitude)
                coordInput.value = `${initLat}, ${initLng}`;
            @endif

            function updateMapData(lat, lng) {
                marker.setLatLng([lat, lng]); map.flyTo([lat, lng], 16);
                latInput.value = lat; lngInput.value = lng; coordInput.value = `${lat}, ${lng}`;
            }

            map.on('click', function(e) { updateMapData(e.latlng.lat, e.latlng.lng); });
            marker.on('dragend', function(e) { updateMapData(marker.getLatLng().lat, marker.getLatLng().lng); });

            coordInput.addEventListener('input', function(e) {
                let parts = e.target.value.split(',');
                if(parts.length >= 2) {
                    let lat = parseFloat(parts[0].trim()); let lng = parseFloat(parts[1].trim());
                    if(!isNaN(lat) && !isNaN(lng)) updateMapData(lat, lng);
                }
            });

            document.getElementById('btn-my-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<i class="ti tabler-loader-4 ti tabler-spin"></i>';
                    navigator.geolocation.getCurrentPosition(pos => updateMapData(pos.coords.latitude, pos.coords.longitude));
                    setTimeout(() => this.innerHTML = '<i class="ti tabler-focus"></i> Titik Saya', 1500);
                }
            });

            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image_preview');
            const imageContainer = document.getElementById('image_preview_container');
            const compWrapper = document.getElementById('compression-wrapper');
            const compProgress = document.getElementById('compression-progress');
            const compStatus = document.getElementById('compression-status');
            const compSize = document.getElementById('compression-size');
            const submitBtn = document.getElementById('submit-btn');
            const previewText = document.getElementById('preview-text');

            imageInput.addEventListener('change', async function() {
                const file = this.files[0];
                if (!file) { compWrapper.style.display = 'none'; return; }

                const reader = new FileReader();
                reader.onload = function(e) { imagePreview.src = e.target.result; imageContainer.style.display = 'block'; }
                reader.readAsDataURL(file);
                if(previewText) previewText.innerText = 'Preview foto baru.';

                compWrapper.style.display = 'block';
                compStatus.innerHTML = '<i class="ti tabler-loader-4 ti tabler-spin me-1"></i> Mengompresi...';
                compStatus.className = 'fw-bold text-primary';
                compProgress.style.width = '10%';
                compProgress.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
                compSize.innerText = `Asli: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                submitBtn.disabled = true;

                const options = { maxSizeMB: 0.3, maxWidthOrHeight: 1280, useWebWorker: true, onProgress: function(progress) { compProgress.style.width = progress + '%'; } };

                try {
                    const compressedFile = await imageCompression(file, options);
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(new File([compressedFile], file.name, { type: file.type || compressedFile.type }));
                    imageInput.files = dataTransfer.files;

                    compProgress.className = 'progress-bar bg-success';
                    compStatus.innerHTML = '<i class="ti tabler-check me-1"></i> Selesai!';
                    compStatus.className = 'fw-bold text-success';
                    compSize.innerHTML = `Asli: ${(file.size/1024/1024).toFixed(2)}MB ➔ <b>Hasil: ${(compressedFile.size/1024/1024).toFixed(2)}MB</b>`;
                    submitBtn.disabled = false;
                } catch (error) {
                    compStatus.innerHTML = '<i class="ti tabler-alert-triangle me-1"></i> Gagal';
                    compStatus.className = 'fw-bold text-danger';
                    compProgress.className = 'progress-bar bg-danger';
                    submitBtn.disabled = false;
                }
            });
        @endif
    });
</script>
@endsection