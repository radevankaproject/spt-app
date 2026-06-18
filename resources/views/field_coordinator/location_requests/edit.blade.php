@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Pengajuan Titik')



@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 350px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        z-index: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .image-preview-wrapper {
        position: relative;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .image-preview-wrapper img {
        max-height: 220px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .image-preview-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        z-index: 2;
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Pengajuan Titik /</span> Edit</h4>
    <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-outline-secondary shadow-sm">
        <i class="ti tabler-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom pb-3 bg-white">
        <h5 class="card-title mb-0 fw-bold">Edit Formulir Pengajuan</h5>
        <small class="text-warning"><i class="ti tabler-info-circle me-1"></i>Hanya pengajuan berstatus Pending yang
            dapat diedit.</small>
    </div>
    <div class="card-body pt-4 bg-white">
        <form action="{{ route('field_coordinator.location-requests.update', $locationRequest->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="request_type" value="{{ $locationRequest->request_type }}">

            <div class="row g-4">
                <div class="col-12">
                    <div class="alert alert-{{ $locationRequest->request_type == 'add' ? 'success' : 'danger' }} d-flex align-items-center mb-0 border-0 shadow-sm"
                        role="alert">
                        <i
                            class="ri {{ $locationRequest->request_type == 'add' ? 'ti tabler-add-circle-filled' : 'ti tabler-delete-bin-filled' }} ti-lg me-3"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">
                                Tipe Pengajuan: {{ $locationRequest->request_type == 'add' ? 'Penambahan Titik Baru' :
                                'Pencabutan Titik' }}
                            </h6>
                            <span class="mb-0">Tipe pengajuan tidak dapat diubah pada mode Edit.</span>
                        </div>
                    </div>
                </div>

                @if($locationRequest->request_type == 'remove')
                <div class="col-md-12">
                    <div class="bg-white p-4 rounded-4 border shadow-sm">
                        <label for="parking_location_id" class="form-label fw-bold">Pilih Titik Parkir <span
                                class="text-danger">*</span></label>
                        <select name="parking_location_id" id="parking_location_id" class="form-select select2"
                            required>
                            <option value="">-- Pilih Titik Parkir Anda --</option>
                            @foreach($activeLocations as $location)
                            <option value="{{ $location->id }}" {{ $locationRequest->parking_location_id ==
                                $location->id ? 'selected' : '' }}>
                                {{ $location->name }} (Jl. {{ $location->roadSection->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <div class="row g-4 p-4 bg-white rounded-4 border shadow-sm">

                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-0"><i class="ti tabler-id me-1"></i> Informasi
                                Dasar</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Ruas Jalan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="road_section_name"
                                value="{{ $locationRequest->road_section_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Titik/Lokasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $locationRequest->name }}"
                                required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Penawaran Setoran Harian (Rp) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text fw-bold bg-white">Rp</span>
                                <input type="number" class="form-control form-control-lg border-start-0"
                                    name="offered_daily_deposit"
                                    value="{{ (int) $locationRequest->offered_daily_deposit }}" min="0" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-3 border-secondary opacity-25">
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-0"><i class="ti tabler-user-pin me-1"></i> Pemetaan
                                & Visualisasi</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Koordinat Map (Latitude, Longitude)</label>
                            <div class="input-group mb-3 shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i
                                        class="ti tabler-map-pin-2"></i></span>
                                <input type="text" class="form-control border-start-0" id="koordinat_gabungan"
                                    name="koordinat_gabungan" placeholder="Misal: 0.507, 101.447">
                                <button class="btn btn-primary" type="button" id="btn-my-location"><i
                                        class="ti tabler-focus-3"></i> Titik Saya</button>
                            </div>
                            <div id="map"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ $locationRequest->latitude }}">
                            <input type="hidden" name="longitude" id="longitude"
                                value="{{ $locationRequest->longitude }}">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold">Update Foto Lokasi</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*">

                            <div id="compression-wrapper" class="mt-3 p-3 border rounded bg-white shadow-sm"
                                style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-primary" id="compression-status"><i
                                            class="ti tabler-loader-4 ti tabler-spin me-1"></i> Mengompresi...</small>
                                    <small class="fw-bold text-dark" id="compression-size"></small>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div id="compression-progress"
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>

                            <div id="image_preview_container" class="mt-3 text-center"
                                style="{{ $locationRequest->image ? 'display:block;' : 'display:none;' }}">
                                <div class="image-preview-wrapper">
                                    <img id="image_preview"
                                        src="{{ $locationRequest->image ? asset('storage/'.$locationRequest->image) : '' }}"
                                        alt="Preview">
                                    <span class="badge bg-success image-preview-badge p-2 rounded-circle shadow"><i
                                            class="ti tabler-checks"></i></span>
                                </div>
                                @if($locationRequest->image)
                                <small class="text-muted d-block mt-2" id="preview-text">Menampilkan foto tersimpan saat
                                    ini.</small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold">Update Surat Permohonan</label>
                            <input class="form-control" type="file" name="proposal_document" accept=".pdf,.doc,.docx">
                            @if($locationRequest->proposal_document)
                            <div class="d-flex align-items-center mt-3 p-3 border rounded bg-white shadow-sm">
                                <div class="avatar avatar-sm bg-label-info me-3 rounded"><i
                                        class="ti tabler-file-text fs-4"></i></div>
                                <div>
                                    <span class="text-dark fw-bold d-block mb-1">Dokumen Lama Tersimpan</span>
                                    <a href="{{ asset('storage/' . $locationRequest->proposal_document) }}"
                                        target="_blank" class="btn btn-xs btn-outline-info"><i
                                            class="ti tabler-external-link me-1"></i> Buka File</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-12 mt-4">
                    <label class="form-label fw-bold text-dark">Alasan Pengajuan <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control shadow-sm" name="reason" rows="4"
                        required>{{ $locationRequest->reason }}</textarea>
                </div>
            </div>

            <div class="pt-4 mt-4 border-top text-end">
                <button type="submit" id="submit-btn" class="btn btn-primary btn-lg px-5 shadow"><i
                        class="ti tabler-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
@vite(["resources/assets/vendor/libs/select2/select2.js"])
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
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
                        setTimeout(() => this.innerHTML = '<i class="ti tabler-focus-3"></i> Titik Saya', 1500);
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
                    if (!file) {
                        compWrapper.style.display = 'none'; return;
                    }

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
                        
                        // ✅ FIX: Gunakan file.name dan file.type asli
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], file.name, { type: file.type || compressedFile.type }));
                        imageInput.files = dataTransfer.files;

                        compProgress.className = 'progress-bar bg-success';
                        compStatus.innerHTML = '<i class="ti tabler-checks me-1"></i> Selesai!';
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