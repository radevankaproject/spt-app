@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Survey Lokasi Parkir')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <a href="{{ route('admin.survey-parking-locations.index') }}" class="btn btn-sm btn-light text-primary rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Kembali
                </a>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-clipboard-text me-2"></i>Edit Data Survey</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Perbarui data survey titik lokasi parkir di bawah ini.</p>
            </div>
        </div>
        <i class="ti tabler-clipboard-text position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <div class="glass-card anim-2 border-0 overflow-hidden mb-4 p-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                <i class="ti tabler-alert-triangle me-1"></i> Periksa kembali isian Anda
                <ul class="mb-0 mt-2 text-sm fw-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.survey-parking-locations.update', $survey_parking_location->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                {{-- Data Survey Utama --}}
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="ti tabler-info-circle me-1"></i> Informasi Survey</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Titik Lokasi Parkir <span class="text-danger">*</span></label>
                    <select name="parking_location_id" class="select2 form-select" required>
                        <option value="">Pilih Lokasi Parkir</option>
                        @foreach($parkingLocations as $location)
                            <option value="{{ $location->id }}" {{ (old('parking_location_id', $survey_parking_location->parking_location_id) == $location->id) ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="monthPicker" name="survey_date" value="{{ old('survey_date', $survey_parking_location->survey_date ? \Carbon\Carbon::parse($survey_parking_location->survey_date)->format('Y-m') : '') }}" placeholder="YYYY-MM" required />
                        <label>Bulan & Tahun Survey <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        @php
                            $tajuk = old('survey_tajuk', $survey_parking_location->survey_tajuk);
                            $tajukFormatted = ($tajuk !== null && $tajuk !== '') ? number_format((float)$tajuk, 0, ',', '.') : '';
                        @endphp
                        <input type="text" class="form-control survey-currency" name="survey_tajuk" value="{{ $tajukFormatted }}" placeholder="Rp 0">
                        <label>Survey Tajuk (Tanya Jukir)</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        @php
                            $tanam = old('survey_tanam', $survey_parking_location->survey_tanam);
                            $tanamFormatted = ($tanam !== null && $tanam !== '') ? number_format((float)$tanam, 0, ',', '.') : '';
                        @endphp
                        <input type="text" class="form-control survey-currency" name="survey_tanam" value="{{ $tanamFormatted }}" placeholder="Rp 0">
                        <label>Survey Tanam (Pantauan Seharian)</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" name="surveyor" value="{{ old('surveyor', $survey_parking_location->surveyor) }}" placeholder="Nama Surveyor" />
                        <label>Nama Surveyor</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control" name="notes" style="height: 50px;">{{ old('notes', $survey_parking_location->notes) }}</textarea>
                        <label>Keterangan / Catatan</label>
                    </div>
                </div>

                {{-- Data Jukir --}}
                <div class="col-12 mt-5">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="ti tabler-user-circle me-1"></i> Data Jukir</h6>
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="ti tabler-info-circle me-2"></i>
                        <div>
                            Ubah pilihan Jukir bila perlu. Memilih "Buat Jukir Baru" akan menampilkan form untuk menambahkan Jukir baru.
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium">Pilih Jukir (Bila sudah terdaftar)</label>
                    <select name="jukir_id" id="jukir_id" class="select2 form-select">
                        <option value="">-- Buat Jukir Baru --</option>
                        @foreach($jukirs as $jukir)
                            <option value="{{ $jukir->id }}" {{ (old('jukir_id', $survey_parking_location->jukir_id) == $jukir->id) ? 'selected' : '' }}>
                                {{ $jukir->nama_jukir }} - {{ $jukir->phone_number ?? 'No HP Kosong' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="new-jukir-form" class="row g-4 w-100 m-0 p-0">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="nama_jukir" value="{{ old('nama_jukir') }}" placeholder="Nama Jukir Baru" />
                            <label>Nama Jukir Baru</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="no_ktp" value="{{ old('no_ktp') }}" placeholder="Nomor KTP" />
                            <label>Nomor KTP</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" placeholder="Nomor HP/WA" />
                            <label>Nomor HP / WhatsApp</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="file" class="form-control" name="image" accept="image/*" />
                            <label>Foto Jukir (Opsional)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill btn-action">
                    <i class="ti tabler-device-floppy me-1"></i> Update Survey
                </button>
                <a href="{{ route('admin.survey-parking-locations.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </form>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js", "resources/assets/vendor/libs/flatpickr/flatpickr.js"])
    <!-- Include Flatpickr Month Plugin via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <script type="module">
        $(document).ready(function() {
            $('.select2').select2();

            // Format Currency
            $('.survey-currency').on('keyup', function() {
                let val = $(this).val();
                val = val.replace(/[^0-9]/g, '');
                if(val) {
                    val = parseInt(val, 10).toLocaleString('id-ID');
                }
                $(this).val(val);
            });

            // Month picker
            flatpickr('#monthPicker', {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true, // defaults to false
                        dateFormat: "Y-m", // defaults to "F Y"
                        altFormat: "F Y", // defaults to "F Y"
                    })
                ],
                disableMobile: true
            });

            // Toggle form Jukir Baru
            function toggleNewJukirForm() {
                var selected = $('#jukir_id').val();
                if(selected) {
                    $('#new-jukir-form').slideUp();
                } else {
                    $('#new-jukir-form').slideDown();
                }
            }

            $('#jukir_id').on('change', function() {
                toggleNewJukirForm();
            });

            // Run on load
            toggleNewJukirForm();
        });
    </script>
@endsection
