@extends('layouts.app')

@section('title', 'Edit Lokasi Parkir')

@section('skeleton')
    @include('layouts.partials._skeleton-parking-locations-form')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .dropzone-area { border: 2px dashed #696cff; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: #f8f8ff; }
        .dropzone-area:hover { background: #e0e0ff; }
    </style>
@endpush

@php
    $isTersedia = $parkingLocation->status === 'tersedia';
@endphp

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 d-inline-block me-2">Edit Lokasi Parkir</h4>
            <span class="badge bg-{{ $isTersedia ? 'success' : 'secondary' }}" style="vertical-align: super;">
                {{ strtoupper(str_replace('_', ' ', $parkingLocation->status)) }}
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('masterdata.parking-locations.update', $parkingLocation->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                @if(!$isTersedia)
                    <input type="hidden" name="road_section_id" value="{{ $parkingLocation->road_section_id }}">
                    <input type="hidden" name="daily_deposit" value="{{ $parkingLocation->daily_deposit }}">
                @endif

                <div class="row g-6">
                    <div class="col-12"><h5 class="mb-0">Informasi Dasar</h5><hr class="mt-2"></div>

                    <div class="col-md-6">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4"><input name="zone_filter" class="form-check-input" type="radio" value="Zona 2" id="zone2" {{ $parkingLocation->roadSection->zone == 'Zona 2' ? 'checked' : '' }} {{ !$isTersedia ? 'disabled' : '' }} /><label class="form-check-label" for="zone2"> Zona 2</label></div>
                            <div class="form-check"><input name="zone_filter" class="form-check-input" type="radio" value="Zona 3" id="zone3" {{ $parkingLocation->roadSection->zone == 'Zona 3' ? 'checked' : '' }} {{ !$isTersedia ? 'disabled' : '' }} /><label class="form-check-label" for="zone3"> Zona 3</label></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="road_section_id" class="form-label">2. Pilih Ruas Jalan</label>
                        <select class="form-select select2" id="road_section_id" name="road_section_id" {{ !$isTersedia ? 'disabled' : '' }} required>
                            @foreach ($roadSectionsInZone as $section)
                                <option value="{{ $section->id }}" {{ $parkingLocation->road_section_id == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                            @endforeach
                        </select>
                        @if(!$isTersedia) <small class="text-danger">Zona dan Ruas Jalan tidak bisa diubah karena status tidak tersedia.</small> @endif
                    </div>

                    <div class="col-12 mt-4"><h5 class="mb-0">Detail Lokasi</h5><hr class="mt-2"></div>

                    <div class="col-md-8">
                        <div class="form-floating form-floating-outline"><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $parkingLocation->name) }}" required /><label for="name">Nama Lokasi Parkir</label></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline"><input type="number" class="form-control" id="daily_deposit" name="daily_deposit" value="{{ old('daily_deposit', $parkingLocation->daily_deposit) }}" required min="0" {{ !$isTersedia ? 'disabled' : '' }} /><label for="daily_deposit">Setoran / Hari (Rp)</label></div>
                        @if(!$isTersedia) <small class="text-danger">Setoran Harian tidak bisa diubah karena lokasi sudah terikat PKS.</small> @endif
                    </div>

                    <div class="col-12 mt-2">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="toggleMap" {{ ($parkingLocation->latitude && $parkingLocation->longitude) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="toggleMap">Ubah/Tambahkan Titik Koordinat</label>
                        </div>

                        <div class="row" id="mapSection" style="{{ ($parkingLocation->latitude && $parkingLocation->longitude) ? '' : 'display: none;' }}">
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="coordinate_input" value="{{ $parkingLocation->latitude ? $parkingLocation->latitude . ', ' . $parkingLocation->longitude : '' }}" />
                                    <label for="coordinate_input">Paste Koordinat Google Maps (Lat, Lng)</label>
                                </div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $parkingLocation->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $parkingLocation->longitude) }}">
                            </div>
                            <div class="col-md-12">
                                <div id="leafletMap" style="height: 300px; border-radius: 8px; z-index: 1;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4"><h5 class="mb-0">Dokumen Pendukung</h5><hr class="mt-2"></div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Foto Lokasi</label>
                        <div class="dropzone-area" id="dropzoneBox" onclick="document.getElementById('image-upload').click()">
                            <img src="{{ $parkingLocation->image ? asset('storage/' . $parkingLocation->image) : asset('assets/img/map.png') }}" id="image-preview" class="d-block mx-auto mb-2" style="max-height: 100px; object-fit: cover; border-radius: 6px;" />
                            <div id="upload-placeholder" style="{{ $parkingLocation->image ? 'display: none;' : '' }}">
                                <i class="ri icon-base ri-upload-cloud-2-line ri-22px" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-1">Tarik foto baru kesini</p>
                            </div>
                            <input type="file" id="image-upload" name="image" hidden accept="image/png, image/jpeg" />
                        </div>
                        <div id="image-status" class="mt-2 text-center text-sm fw-semibold"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="proposal_document" class="form-label fw-bold">PDF Pengajuan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri icon-base ri-file-pdf-line ri-22px"></i></span>
                            <input class="form-control" type="file" id="proposal_document" name="proposal_document" accept=".pdf">
                        </div>
                        @if ($parkingLocation->proposal_document)
                            <a href="{{ asset('storage/' . $parkingLocation->proposal_document) }}" target="_blank" class="form-text text-primary mt-1 d-block"><i class="ri-external-link-line"></i> Lihat file saat ini</a>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="official_report_document" class="form-label fw-bold">PDF Berita Acara</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri icon-base ri-file-pdf-line ri-22px"></i></span>
                            <input class="form-control" type="file" id="official_report_document" name="official_report_document" accept=".pdf">
                        </div>
                        @if ($parkingLocation->official_report_document)
                            <a href="{{ asset('storage/' . $parkingLocation->official_report_document) }}" target="_blank" class="form-text text-primary mt-1 d-block"><i class="ri-external-link-line"></i> Lihat file saat ini</a>
                        @endif
                    </div>
                </div>

                <div class="pt-6 text-end mt-4">
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endpush

@push('scripts')
<script>
    $(function() {
        // --- Setup Select2 ---
        $('#road_section_id').select2({ placeholder: 'Pilih Ruas Jalan' });

        // --- Leaflet Logic (Auto Init if lat/lng exists) ---
        let map, marker;
        let dbLat = {{ $parkingLocation->latitude ?? '0.507067' }};
        let dbLng = {{ $parkingLocation->longitude ?? '101.447779' }};

        function initMap() {
            if (!map) {
                map = L.map('leafletMap').setView([dbLat, dbLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([dbLat, dbLng], {draggable: true}).addTo(map);

                marker.on('dragend', function(e) {
                    let pos = marker.getLatLng();
                    $('#coordinate_input').val(`${pos.lat}, ${pos.lng}`);
                    $('#latitude').val(pos.lat);
                    $('#longitude').val(pos.lng);
                });
            }
            
            // Selalu panggil invalidateSize agar peta tidak render warna abu/abu (grey bug)
            setTimeout(() => {
                map.invalidateSize();
                map.setView(marker.getLatLng(), 15);
            }, 100);
        }

        if($('#toggleMap').is(':checked')) { 
            setTimeout(() => { initMap(); }, 200);
        }

        $('#toggleMap').on('change', function() {
            if($(this).is(':checked')) {
                $('#mapSection').slideDown(300, () => {
                    initMap();
                });
                $('#coordinate_input').prop('disabled', false);
            } else {
                $('#mapSection').slideUp();
                $('#coordinate_input').prop('disabled', true).val('');
                $('#latitude').val('');
                $('#longitude').val('');
            }
        });

        $('#coordinate_input').on('input paste', function() {
            setTimeout(() => {
                let val = $(this).val().split(',');
                if (val.length === 2) {
                    let lat = parseFloat(val[0].trim());
                    let lng = parseFloat(val[1].trim());
                    if (!isNaN(lat) && !isNaN(lng)) {
                        marker.setLatLng([lat, lng]);
                        map.flyTo([lat, lng], 17);
                        $('#latitude').val(lat);
                        $('#longitude').val(lng);
                    }
                }
            }, 100);
        });

        // --- Drag & Drop Image Compression ---
        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        const dropzoneBox = document.getElementById('dropzoneBox');
        const fileInput = document.getElementById('image-upload');
        const imagePreview = document.getElementById('image-preview');
        const imageStatus = document.getElementById('image-status');
        const placeholder = document.getElementById('upload-placeholder');

        dropzoneBox.addEventListener('dragover', (e) => { e.preventDefault(); dropzoneBox.style.background = '#d0d0ff'; });
        dropzoneBox.addEventListener('dragleave', () => dropzoneBox.style.background = '#f8f8ff');
        dropzoneBox.addEventListener('drop', (e) => {
            e.preventDefault(); dropzoneBox.style.background = '#f8f8ff';
            if(e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; fileInput.dispatchEvent(new Event('change')); }
        });

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                imageStatus.innerHTML = '<span class="text-danger">Hanya format JPG/PNG!</span>'; return;
            }

            placeholder.style.display = 'none';
            imagePreview.style.display = 'block';
            imagePreview.src = URL.createObjectURL(file);
            imageStatus.innerHTML = `<span class="text-warning">Mengompresi...</span>`;

            try {
                const options = { maxSizeMB: 0.08, maxWidthOrHeight: 1200, useWebWorker: true };
                const compressedFile = await imageCompression(file, options);

                const dt = new DataTransfer();
                dt.items.add(new File([compressedFile], file.name, { type: compressedFile.type }));
                fileInput.files = dt.files;

                imagePreview.src = URL.createObjectURL(compressedFile);
                imageStatus.innerHTML = `<span class="text-success">${formatBytes(file.size)} ➔ ${formatBytes(compressedFile.size)} 🚀</span>`;
            } catch (error) {
                imageStatus.innerHTML = `<span class="text-danger">Gagal kompresi.</span>`;
            }
        });
    });
</script>
@endpush
