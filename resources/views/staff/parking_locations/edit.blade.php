@extends('layouts.app')

@section('title', 'Edit Lokasi Parkir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Lokasi Parkir</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}">Lokasi Parkir</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
            <ul class="mt-2 mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('masterdata.parking-locations.update', $parkingLocation->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="row g-6">
                    <div class="col-12">
                        <h5 class="mb-0">Informasi Dasar</h5>
                        <hr class="mt-2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4"><input name="zone_filter" class="form-check-input" type="radio"
                                    value="Zona 2" id="zone2"
                                    {{ $parkingLocation->roadSection->zone == 'Zona 2' ? 'checked' : '' }} /><label
                                    class="form-check-label" for="zone2"> Zona 2</label></div>
                            <div class="form-check"><input name="zone_filter" class="form-check-input" type="radio"
                                    value="Zona 3" id="zone3"
                                    {{ $parkingLocation->roadSection->zone == 'Zona 3' ? 'checked' : '' }} /><label
                                    class="form-check-label" for="zone3"> Zona 3</label></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="road_section_id" class="form-label">2. Pilih Ruas Jalan</label>
                        <select class="form-select select2" id="road_section_id" name="road_section_id" required>
                            @foreach ($roadSectionsInZone as $section)
                                <option value="{{ $section->id }}"
                                    {{ $parkingLocation->road_section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <h5 class="mb-0">Detail Lokasi</h5>
                        <hr class="mt-2">
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                id="name" name="name" placeholder="Contoh: Depan Toko ABC"
                                value="{{ old('name', $parkingLocation->name) }}" required /><label for="name">Nama
                                Lokasi Parkir</label></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline"><input type="number" class="form-control"
                                id="daily_deposit" name="daily_deposit" placeholder="Contoh: 15000"
                                value="{{ old('daily_deposit', $parkingLocation->daily_deposit) }}" required
                                min="0" /><label for="daily_deposit">Setoran / Hari (Rp)</label></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                id="latitude" name="latitude" placeholder="Contoh: 0.507067"
                                value="{{ old('latitude', $parkingLocation->latitude) }}" /><label
                                for="latitude">Latitude</label></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                id="longitude" name="longitude" placeholder="Contoh: 101.447779"
                                value="{{ old('longitude', $parkingLocation->longitude) }}" /><label
                                for="longitude">Longitude</label></div>
                    </div>

                    <div class="col-12 mt-4">
                        <h5 class="mb-0">Dokumen Pendukung</h5>
                        <hr class="mt-2">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto Lokasi</label>
                        <div class="card">
                            <div class="card-body text-center p-3">
                                <img src="{{ $parkingLocation->image ? asset('storage/' . $parkingLocation->image) : asset('assets/img/illustrations/image-light.png') }}"
                                    alt="location-placeholder" class="d-block rounded-3 mx-auto mb-3" id="image-preview"
                                    style="max-height: 120px;" />
                                <label for="image-upload" class="btn btn-sm btn-primary"><i
                                        class="icon-base ri-upload-2-line me-1"></i>Ubah Foto<input type="file"
                                        id="image-upload" name="image" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" /></label>
                                <div id="image-error" class="mt-2 text-danger text-sm"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="proposal_document" class="form-label">PDF Pengajuan</label>
                        <input class="form-control" type="file" id="proposal_document" name="proposal_document"
                            accept=".pdf">
                        @if ($parkingLocation->proposal_document)
                            <a href="{{ asset('storage/' . $parkingLocation->proposal_document) }}" target="_blank"
                                class="form-text text-primary">Lihat file saat ini</a>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="official_report_document" class="form-label">PDF Berita Acara</label>
                        <input class="form-control" type="file" id="official_report_document"
                            name="official_report_document" accept=".pdf">
                        @if ($parkingLocation->official_report_document)
                            <a href="{{ asset('storage/' . $parkingLocation->official_report_document) }}"
                                target="_blank" class="form-text text-primary">Lihat file saat ini</a>
                        @endif
                    </div>
                </div>

                <div class="pt-6 text-end">
                    <a href="{{ route('masterdata.parking-locations.index') }}"
                        class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endpush

@push('scripts')
    <script>
        $(function() {
            const roadSectionSelect = $('#road_section_id');
            if (roadSectionSelect.length) {
                roadSectionSelect.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Ruas Jalan',
                    dropdownParent: roadSectionSelect.parent()
                });
            }
            $('input[name="zone_filter"]').on('change', function() {
                const selectedZone = $(this).val();
                roadSectionSelect.empty().append('<option value="">Memuat...</option>').prop('disabled',
                    true).trigger('change');
                if (selectedZone) {
                    const url = `{{ route('masterdata.road-sections.getByZone', ':zone') }}`.replace(
                        ':zone', selectedZone);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            roadSectionSelect.empty().append(
                                '<option value="">Pilih Ruas Jalan</option>').prop(
                                'disabled', false);
                            if (data.length > 0) {
                                $.each(data, (key, value) => {
                                    roadSectionSelect.append($('<option></option>')
                                        .attr('value', value.id).text(value.name));
                                });
                            } else {
                                roadSectionSelect.empty().append(
                                    '<option value="">Tidak ada ruas jalan</option>').prop(
                                    'disabled', true);
                            }
                            roadSectionSelect.trigger('change');
                        },
                        error: function() {
                            roadSectionSelect.empty().append(
                                '<option value="">Gagal memuat</option>').prop('disabled',
                                true).trigger('change');
                        }
                    });
                }
            });

            const fileInput = document.getElementById('image-upload');
            const imagePreview = document.getElementById('image-preview');
            const errorDiv = document.getElementById('image-error');
            const defaultSrc = imagePreview.src;

            if (fileInput) {
                fileInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) {
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya JPG/PNG.';
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    const options = {
                        maxSizeMB: 0.3,
                        maxWidthOrHeight: 1024,
                        useWebWorker: true
                    };
                    try {
                        const compressedFile = await imageCompression(imageFile, options);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                            type: compressedFile.type
                        }));
                        fileInput.files = dataTransfer.files;
                        imagePreview.src = URL.createObjectURL(compressedFile);
                    } catch (error) {
                        errorDiv.textContent = "Gagal kompres.";
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                    }
                });
            }
        });
    </script>
@endpush
