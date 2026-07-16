@extends('layouts.contentNavbarLayout')

@section('title', 'Tambah Lokasi Parkir Baru')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .dropzone-area { border: 2px dashed #696cff; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: 0.3s; background: #f8f8ff; }
        .dropzone-area:hover { background: #e0e0ff; }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Tambah Lokasi Parkir Baru</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('masterdata.parking-locations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-6">
                    <div class="col-12"><h5 class="mb-0">Informasi Dasar</h5><hr class="mt-2"></div>

                    <div class="col-md-6">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4"><input name="zone_filter" class="form-check-input" type="radio" value="Zona 2" id="zone2" /><label class="form-check-label" for="zone2"> Zona 2</label></div>
                            <div class="form-check"><input name="zone_filter" class="form-check-input" type="radio" value="Zona 3" id="zone3" /><label class="form-check-label" for="zone3"> Zona 3</label></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="road_section_id" class="form-label">2. Pilih Ruas Jalan</label>
                        <select class="form-select select2" id="road_section_id" name="road_section_id" required disabled>
                            <option value="">Pilih Zona terlebih dahulu</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4"><h5 class="mb-0">Detail Lokasi</h5><hr class="mt-2"></div>

                    <div class="col-md-8">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Depan Toko ABC" value="{{ old('name') }}" required />
                            <label for="name">Nama Lokasi Parkir</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" id="daily_deposit" name="daily_deposit" placeholder="Contoh: 15000" value="{{ old('daily_deposit') }}" required min="0" />
                            <label for="daily_deposit">Setoran / Hari (Rp)</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="number" step="0.01" class="form-control" id="estimated_area" name="estimated_area" placeholder="Contoh: 150.5" value="{{ old('estimated_area') }}" min="0" />
                            <label for="estimated_area">Est. Luas Wilayah (m²)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" id="estimated_srp_r2" name="estimated_srp_r2" placeholder="Contoh: 50" value="{{ old('estimated_srp_r2') }}" min="0" />
                            <label for="estimated_srp_r2">Est. Jumlah SRP Roda 2</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" id="estimated_srp_r4" name="estimated_srp_r4" placeholder="Contoh: 20" value="{{ old('estimated_srp_r4') }}" min="0" />
                            <label for="estimated_srp_r4">Est. Jumlah SRP Roda 4</label>
                        </div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Status Lokasi Aktif / Berpotensi</label>
                        </div>
                        <small class="text-muted">Matikan jika lokasi sudah tutup atau tidak berpotensi.</small>
                    </div>

                    <div class="col-12 mt-2" id="keteranganSection" style="display: {{ old('is_active', true) ? 'none' : 'block' }};">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control h-px-100" id="keterangan" name="keterangan" placeholder="Berikan keterangan (misal: lokasi sudah tutup, tidak berpotensi, dll)">{{ old('keterangan') }}</textarea>
                            <label for="keterangan">Keterangan Tutup/Tidak Aktif</label>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="toggleMap">
                            <label class="form-check-label fw-bold" for="toggleMap">Tambahkan Titik Koordinat (Opsional)</label>
                        </div>

                        <div class="row" id="mapSection" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="coordinate_input" placeholder="Contoh: 0.507067, 101.447779" />
                                    <label for="coordinate_input">Paste Koordinat Google Maps (Lat, Lng)</label>
                                </div>
                                <small class="text-muted">Bisa copas dari Google Maps, peta akan otomatis terbang ke lokasi.</small>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            </div>
                            <div class="col-md-12">
                                <div id="leafletMap" style="height: 300px; border-radius: 8px; z-index: 1;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4"><h5 class="mb-0">Dokumen Pendukung</h5><hr class="mt-2"></div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Foto Lokasi</label>
                        <div class="dropzone-area position-relative" id="dropzoneBox" onclick="document.getElementById('image-upload').click()">
                            <img src="{{ asset('assets/img/map.png') }}" alt="preview" id="image-preview" class="d-block mx-auto mb-2" style="max-height: 100px; object-fit: cover; border-radius: 6px; display: none;" />
                            <div id="upload-placeholder">
                                <i class="ri icon-base ti tabler-cloud-upload icon-22px" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-1">Tarik foto ke sini atau klik</p>
                            </div>
                            <input type="file" id="image-upload" name="image" hidden accept="image/png, image/jpeg" />
                        </div>
                        <div id="image-status" class="mt-2 text-center text-sm fw-semibold"></div>
                        
                        <!-- Premium Compression Progress Bar -->
                        <div id="compression-progress-container" class="mt-3 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-xs fw-semibold text-primary"><i class="ti tabler-loader ti-spin me-1"></i>Mengompresi...</span>
                                <span id="compression-percentage" class="text-xs fw-bold text-primary">0%</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background-color: #e0e0ff;">
                                <div id="compression-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; border-radius: 10px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="proposal_document" class="form-label fw-bold">PDF Pengajuan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri icon-base ti tabler-file-type-pdf ti-md"></i></span>
                            <input class="form-control" type="file" id="proposal_document" name="proposal_document" accept=".pdf">
                        </div>
                        <small class="text-muted">Otomatis di-rename saat disimpan.</small>
                    </div>
                    <div class="col-md-4">
                        <label for="official_report_document" class="form-label fw-bold">PDF Berita Acara</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri icon-base ti tabler-file-type-pdf ti-md"></i></span>
                            <input class="form-control" type="file" id="official_report_document" name="official_report_document" accept=".pdf">
                        </div>
                    </div>
                </div>

                <div class="pt-6 text-end mt-4">
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endsection

@section('page-script')
<script>
    $(function() {
        // --- Setup Ruas Jalan (AJAX) ---
        const roadSectionSelect = $('#road_section_id');
        roadSectionSelect.select2({ placeholder: 'Pilih Ruas Jalan' });

        $('input[name="zone_filter"]').on('change', function() {
            const selectedZone = $(this).val();
            roadSectionSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change');
            if (selectedZone) {
                $.get(`{{ url('masterdata/get-road-sections-by-zone') }}/${selectedZone}`, function(data) {
                    roadSectionSelect.empty().append('<option value="">Pilih Ruas Jalan</option>').prop('disabled', false);
                    if (data.length > 0) {
                        $.each(data, (i, val) => roadSectionSelect.append(new Option(val.name, val.id)));
                    }
                }).fail(() => roadSectionSelect.empty().prop('disabled', true));
            }
        });

        // --- Leaflet Map & Koordinat Logic ---
        let map, marker;
        const defaultLat = 0.507067; // Default Pekanbaru
        const defaultLng = 101.447779;

        function initMap() {
            if (!map) {
                map = L.map('leafletMap').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

                marker.on('dragend', function(e) {
                    let position = marker.getLatLng();
                    $('#coordinate_input').val(`${position.lat}, ${position.lng}`);
                    $('#latitude').val(position.lat);
                    $('#longitude').val(position.lng);
                });
            } else {
                map.invalidateSize(); // Fix map glitch if shown from hidden state
            }
        }

        $('#toggleMap').on('change', function() {
            if($(this).is(':checked')) {
                $('#mapSection').slideDown(() => initMap());
                $('#coordinate_input').prop('disabled', false);
            } else {
                $('#mapSection').slideUp();
                $('#coordinate_input').prop('disabled', true).val('');
                $('#latitude').val('');
                $('#longitude').val('');
            }
        });

        $('#is_active').on('change', function() {
            if($(this).is(':checked')) {
                $('#keteranganSection').slideUp();
                $('#keterangan').prop('required', false);
            } else {
                $('#keteranganSection').slideDown();
                $('#keterangan').prop('required', true);
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

        // --- Image Compression & Drag Drop Logic ---
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
            e.preventDefault();
            dropzoneBox.style.background = '#f8f8ff';
            if(e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; fileInput.dispatchEvent(new Event('change')); }
        });

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const compressionContainer = document.getElementById('compression-progress-container');
            const compressionBar = document.getElementById('compression-progress-bar');
            const compressionPercentage = document.getElementById('compression-percentage');

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                imageStatus.innerHTML = '<span class="text-danger">Hanya format JPG/PNG!</span>'; return;
            }

            placeholder.style.display = 'none';
            imagePreview.style.display = 'block';
            imagePreview.src = URL.createObjectURL(file);
            imageStatus.innerHTML = `<span class="text-warning">Menyiapkan...</span>`;
            
            compressionContainer.classList.remove('d-none');
            compressionBar.style.width = '0%';
            compressionPercentage.innerText = '0%';

            try {
                const options = { 
                    maxSizeMB: 0.05, // <50Kb
                    maxWidthOrHeight: 1200, 
                    useWebWorker: true,
                    onProgress: (progress) => {
                        compressionBar.style.width = progress + '%';
                        compressionPercentage.innerText = progress + '%';
                    }
                };
                
                const compressedFile = await imageCompression(file, options);

                const dt = new DataTransfer();
                dt.items.add(new File([compressedFile], file.name, { type: compressedFile.type }));
                fileInput.files = dt.files;

                imagePreview.src = URL.createObjectURL(compressedFile);
                compressionContainer.classList.add('d-none');
                imageStatus.innerHTML = `<span class="text-success"><i class="ti tabler-check"></i> ${formatBytes(file.size)} ➔ ${formatBytes(compressedFile.size)}</span>`;
            } catch (error) {
                compressionContainer.classList.add('d-none');
                imageStatus.innerHTML = `<span class="text-danger">Gagal kompresi.</span>`;
            }
        });
    });
</script>
@endsection
