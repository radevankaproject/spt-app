@extends('layouts.app')

@section('title', 'Buat Pengajuan Titik Baru')

@section('skeleton')
@include('layouts.partials._skeleton-fc-lr-form')
@endsection

@push('styles')
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
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <span class="text-muted fw-light">Pengajuan Titik /</span> Buat Baru
    </h4>
    <a href="{{ route('field_coordinator.location-requests.index') }}"
        class="btn btn-outline-secondary mt-3 mt-md-0 shadow-sm">
        <i class="ri ri-arrow-left-line me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom pb-3 bg-white">
        <h5 class="card-title mb-0 fw-bold">Formulir Pengajuan Titik Parkir</h5>
        <small class="text-muted">PKS Aktif Anda: <b class="text-primary">{{ $activeAgreement->agreement_number
                }}</b></small>
    </div>
    <div class="card-body pt-4 bg-white">
        <form action="{{ route('field_coordinator.location-requests.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="agreement_id" value="{{ $activeAgreement->id }}">

            <div class="row g-4">
                {{-- JENIS PENGAJUAN --}}
                <div class="col-md-12">
                    <label class="form-label fw-bold text-dark text-uppercase">Jenis Pengajuan <span
                            class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6 mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon shadow-sm">
                                <label class="form-check-label custom-option-content bg-white" for="typeAdd">
                                    <span class="custom-option-body">
                                        <i class="ri ri-add-circle-line ri-24px text-success mb-2"></i>
                                        <span class="custom-option-title fw-bold fs-6">Penambahan Titik Baru</span>
                                        <small>Ajukan lokasi baru untuk dikelola.</small>
                                    </span>
                                    <input name="request_type" class="form-check-input" type="radio" value="add"
                                        id="typeAdd" checked />
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check custom-option custom-option-icon shadow-sm">
                                <label class="form-check-label custom-option-content bg-white" for="typeRemove">
                                    <span class="custom-option-body">
                                        <i class="ri ri-delete-bin-line ri-24px text-danger mb-2"></i>
                                        <span class="custom-option-title fw-bold fs-6">Pencabutan Titik</span>
                                        <small>Berhenti mengelola titik yang sudah ada.</small>
                                    </span>
                                    <input name="request_type" class="form-check-input" type="radio" value="remove"
                                        id="typeRemove" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM PENCABUTAN --}}
                <div class="col-md-12" id="form-remove" style="display: none;">
                    <div class="bg-white p-4 rounded-4 border shadow-sm">
                        <label for="parking_location_id" class="form-label fw-bold text-dark">Pilih Titik Parkir yang
                            Ingin Dicabut <span class="text-danger">*</span></label>
                        <select name="parking_location_id" id="parking_location_id" class="form-select select2">
                            <option value="">-- Pilih Titik Parkir Anda --</option>
                            @foreach($activeLocations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }} (Jl. {{
                                $location->roadSection->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- FORM PENAMBAHAN --}}
                <div class="col-md-12" id="form-add">
                    <div class="row g-4 p-4 bg-white rounded-4 border shadow-sm">

                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-0"><i class="ri ri-profile-line me-1"></i> Informasi
                                Dasar</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="road_section_name" class="form-label fw-bold">Nama Ruas Jalan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="road_section_name" name="road_section_name"
                                placeholder="Contoh: Jl. Jend. Sudirman">
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">Nama Titik/Lokasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Contoh: Depan Toko Mas Abadi">
                        </div>
                        <div class="col-md-12">
                            <label for="offered_daily_deposit" class="form-label fw-bold">Penawaran Setoran Harian <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text fw-bold bg-white">Rp</span>
                                <input type="number" class="form-control form-control-lg border-start-0"
                                    id="offered_daily_deposit" name="offered_daily_deposit" placeholder="0" min="0">
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="ri ri-information-line"></i> Angka ini akan
                                dinegosiasikan dengan petugas Dinas.</small>
                        </div>

                        <div class="col-12">
                            <hr class="my-3 border-secondary opacity-25">
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-0"><i class="ri ri-map-pin-user-line me-1"></i> Pemetaan
                                & Visualisasi</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Koordinat Map (Latitude, Longitude)</label>
                            <div class="input-group mb-3 shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i
                                        class="ri ri-map-pin-2-line"></i></span>
                                <input type="text" class="form-control border-start-0" id="koordinat_gabungan"
                                    name="koordinat_gabungan"
                                    placeholder="Paste koordinat G-Maps di sini (Misal: 0.507, 101.447)">
                                <button class="btn btn-primary" type="button" id="btn-my-location"
                                    data-bs-toggle="tooltip" title="Gunakan GPS HP Anda">
                                    <i class="ri ri-focus-3-line"></i> Titik Saya
                                </button>
                            </div>
                            <small class="text-muted d-block mb-3">Ketikan/Paste koordinat, atau <b>klik langsung pada
                                    peta</b> untuk menandai lokasi.</small>
                            <div id="map"></div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="image" class="form-label fw-bold">Foto Lokasi</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*">

                            <div id="compression-wrapper" class="mt-3 p-3 border rounded bg-white shadow-sm"
                                style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-primary" id="compression-status"><i
                                            class="ri ri-loader-4-line ri-spin me-1"></i> Mengompresi gambar...</small>
                                    <small class="fw-bold text-dark" id="compression-size"></small>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div id="compression-progress"
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>

                            <div id="image_preview_container" class="mt-3 text-center" style="display: none;">
                                <div class="image-preview-wrapper">
                                    <img id="image_preview" src="" alt="Preview">
                                    <span class="badge bg-success image-preview-badge p-2 rounded-circle shadow"><i
                                            class="ri ri-check-double-line"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="proposal_document" class="form-label fw-bold">Surat Permohonan /
                                Proposal</label>
                            <input class="form-control" type="file" id="proposal_document" name="proposal_document"
                                accept=".pdf,.doc,.docx">
                            <small class="text-muted">Format: PDF/DOCX (Maks 5MB).</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <label for="reason" class="form-label fw-bold text-dark">Alasan Pengajuan <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control shadow-sm" id="reason" name="reason" rows="4"
                        placeholder="Jelaskan alasan pengajuan ini..." required></textarea>
                </div>
            </div>

            <div class="pt-4 mt-4 border-top text-end">
                <button type="submit" id="submit-btn" class="btn btn-primary btn-lg px-5 shadow"><i
                        class="ri ri-send-plane-fill me-1"></i> Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('vendors-js')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
            $('.select2').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({
                    placeholder: '-- Pilih Titik Parkir --', dropdownParent: $(this).parent()
                });
            });

            let map, marker;
            const coordInput = document.getElementById('koordinat_gabungan');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            map = L.map('map').setView([0.5070677, 101.4477793], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });
            marker = L.marker([0.5070677, 101.4477793], {icon: redIcon, draggable: true}).addTo(map);

            function updateMapData(lat, lng) {
                const latlng = new L.LatLng(lat, lng);
                marker.setLatLng(latlng);
                map.flyTo(latlng, 16, { animate: true, duration: 1 });
                latInput.value = lat; lngInput.value = lng;
                coordInput.value = `${lat}, ${lng}`;
            }

            map.on('click', function(e) { updateMapData(e.latlng.lat, e.latlng.lng); });
            marker.on('dragend', function(e) { updateMapData(marker.getLatLng().lat, marker.getLatLng().lng); });

            coordInput.addEventListener('input', function(e) {
                let parts = e.target.value.split(',');
                if(parts.length >= 2) {
                    let lat = parseFloat(parts[0].trim()); let lng = parseFloat(parts[1].trim());
                    if(!isNaN(lat) && !isNaN(lng)) { updateMapData(lat, lng); }
                }
            });

            document.getElementById('btn-my-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<i class="ri ri-loader-4-line ri-spin"></i> Mencari...';
                    navigator.geolocation.getCurrentPosition(function(position) {
                        updateMapData(position.coords.latitude, position.coords.longitude);
                        document.getElementById('btn-my-location').innerHTML = '<i class="ri ri-focus-3-line"></i> Titik Saya';
                    }, function() {
                        alert("Gagal mengambil lokasi GPS Anda.");
                        document.getElementById('btn-my-location').innerHTML = '<i class="ri ri-focus-3-line"></i> Titik Saya';
                    });
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

            imageInput.addEventListener('change', async function() {
                const file = this.files[0];
                if (!file) {
                    imageContainer.style.display = 'none'; compWrapper.style.display = 'none'; return;
                }

                const reader = new FileReader();
                reader.onload = function(e) { imagePreview.src = e.target.result; imageContainer.style.display = 'block'; }
                reader.readAsDataURL(file);

                compWrapper.style.display = 'block';
                compStatus.innerHTML = '<i class="ri ri-loader-4-line ri-spin me-1"></i> Mengompresi gambar...';
                compStatus.className = 'fw-bold text-primary';
                compProgress.style.width = '10%';
                compProgress.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
                compSize.innerText = `Asli: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                submitBtn.disabled = true;

                const options = { maxSizeMB: 0.3, maxWidthOrHeight: 1280, useWebWorker: true,
                    onProgress: function(progress) { compProgress.style.width = progress + '%'; }
                };

                try {
                    const compressedFile = await imageCompression(file, options);
                    
                    // ✅ FIX: Gunakan file.name dan file.type asli agar Laravel bisa mendeteksi extension-nya
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(new File([compressedFile], file.name, { type: file.type || compressedFile.type }));
                    imageInput.files = dataTransfer.files;

                    compProgress.className = 'progress-bar bg-success';
                    compStatus.innerHTML = '<i class="ri ri-check-double-line me-1"></i> Kompresi Selesai!';
                    compStatus.className = 'fw-bold text-success';
                    compSize.innerHTML = `Asli: ${(file.size/1024/1024).toFixed(2)} MB ➔ <b>Hasil: ${(compressedFile.size/1024/1024).toFixed(2)} MB</b>`;
                    submitBtn.disabled = false;
                } catch (error) {
                    console.error(error);
                    compStatus.innerHTML = '<i class="ri ri-error-warning-line me-1"></i> Gagal Mengompresi';
                    compStatus.className = 'fw-bold text-danger';
                    compProgress.className = 'progress-bar bg-danger';
                    submitBtn.disabled = false;
                }
            });

            const radioAdd = document.getElementById('typeAdd');
            const formAdd = document.getElementById('form-add');
            const formRemove = document.getElementById('form-remove');
            const addInputs = ['road_section_name', 'name', 'offered_daily_deposit'];
            const removeInputs = ['parking_location_id'];

            function toggleForms() {
                if (radioAdd.checked) {
                    formAdd.style.display = 'block'; formRemove.style.display = 'none';
                    addInputs.forEach(id => document.getElementById(id).setAttribute('required', 'required'));
                    removeInputs.forEach(id => document.getElementById(id).removeAttribute('required'));
                    setTimeout(() => map.invalidateSize(), 200);
                } else {
                    formAdd.style.display = 'none'; formRemove.style.display = 'block';
                    addInputs.forEach(id => document.getElementById(id).removeAttribute('required'));
                    removeInputs.forEach(id => document.getElementById(id).setAttribute('required', 'required'));
                }
            }
            document.getElementById('typeAdd').addEventListener('change', toggleForms);
            document.getElementById('typeRemove').addEventListener('change', toggleForms);
            toggleForms();
        });
</script>
@endpush