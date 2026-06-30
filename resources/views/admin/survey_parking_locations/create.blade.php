@extends('layouts.contentNavbarLayout')

@section('title', 'Input Survey Lokasi Parkir')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <style>
        .custom-radio-zone .form-check-input {
            display: none;
        }
        .custom-radio-zone .form-check-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #6c757d;
        }
        .custom-radio-zone .form-check-input:checked + .form-check-label {
            border-color: #7367f0; /* Primary color */
            background-color: rgba(115, 103, 240, 0.1);
            color: #7367f0;
            box-shadow: 0 4px 6px -1px rgba(115, 103, 240, 0.1), 0 2px 4px -1px rgba(115, 103, 240, 0.06);
        }
        .custom-radio-zone .form-check-input:hover:not(:checked) + .form-check-label {
            border-color: #b9b5f9;
            background-color: rgba(115, 103, 240, 0.05);
        }
    </style>
@endsection

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <a href="{{ route('admin.survey-parking-locations.index') }}" class="btn btn-sm btn-light text-primary rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Kembali
                </a>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-clipboard-plus me-2"></i>Input Data Survey</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Input hasil survey secara kolektif per Ruas Jalan.</p>
            </div>
        </div>
        <i class="ti tabler-clipboard-plus position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <!-- FILTER RUAS JALAN -->
    <div class="glass-card anim-2 border-0 mb-4 p-4">
        <form action="{{ route('admin.survey-parking-locations.create') }}" method="GET" id="filterForm">
            <h5 class="fw-bold text-primary mb-3"><i class="ti tabler-filter me-1"></i> Filter Lokasi & Waktu Survey</h5>
            <div class="row g-3 align-items-end">
                <div class="col-12 mb-2">
                    <label class="form-label fw-bold text-primary mb-2">Zona (Opsional)</label>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="form-check custom-radio-zone p-0 m-0">
                            <input type="radio" name="zone" id="zone_all" class="form-check-input" value="" {{ empty($selected_zone) ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                            <label class="form-check-label" for="zone_all">Semua Zona</label>
                        </div>
                        @foreach($zones as $z)
                            <div class="form-check custom-radio-zone p-0 m-0">
                                <input type="radio" name="zone" id="zone_{{ $z }}" class="form-check-input" value="{{ $z }}" {{ $selected_zone == $z ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label" for="zone_{{ $z }}">Zona {{ $z }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Bulan Survey (Global)</label>
                    <input type="text" class="form-control" id="monthPicker" name="survey_date" value="{{ old('survey_date', $selected_survey_date) }}" placeholder="Pilih Bulan" onchange="document.getElementById('filterForm').submit()" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Ruas Jalan</label>
                    <select name="road_section_id" id="road_section_id" class="select2 form-select">
                        <option value="">-- Pilih Ruas Jalan --</option>
                        @foreach($roadSections as $rs)
                            <option value="{{ $rs->id }}" {{ $selected_road_section_id == $rs->id ? 'selected' : '' }}>
                                {{ $rs->name }} - {{ $rs->zone }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary rounded-pill btn-action w-100">
                        <i class="ti tabler-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($selected_road_section_id && $parkingLocations->isEmpty())
        <div class="alert alert-warning fw-bold d-flex align-items-center" role="alert">
            <i class="ti tabler-alert-circle me-2 fs-4"></i>
            <div>Tidak ada titik lokasi parkir aktif pada ruas jalan ini.</div>
        </div>
    @endif

    @if($selected_road_section_id && $parkingLocations->isNotEmpty())
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                <i class="ti tabler-alert-triangle me-1"></i> Terdapat kesalahan pada inputan Anda.
                <ul class="mb-0 mt-2 text-sm fw-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.survey-parking-locations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="road_section_id" value="{{ $selected_road_section_id }}">
            <input type="hidden" name="survey_date" value="{{ $selected_survey_date }}">

            <div class="glass-card anim-3 border-0 overflow-hidden p-4 mb-4">

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="15%">Nama Titik Parkir</th>
                                <th width="15%">Survey Tajuk</th>
                                <th width="15%">Survey Tanam</th>
                                <th width="15%">Nama Jukir</th>
                                <th width="15%">Surveyor</th>
                                <th width="20%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parkingLocations as $index => $location)
                                @php
                                    $existingSurvey = $location->surveys->first();
                                    $tajuk = ($existingSurvey && $existingSurvey->survey_tajuk !== null) ? number_format((float)$existingSurvey->survey_tajuk, 0, ',', '.') : '';
                                    $tanam = ($existingSurvey && $existingSurvey->survey_tanam !== null) ? number_format((float)$existingSurvey->survey_tanam, 0, ',', '.') : '';
                                    $jukir = $existingSurvey && $existingSurvey->jukir ? $existingSurvey->jukir->nama_jukir : '';
                                    $surveyor = $existingSurvey ? $existingSurvey->surveyor : '';
                                    $notes = $existingSurvey ? $existingSurvey->notes : '';
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-medium text-wrap" style="min-width: 200px;">
                                        {{ $location->name }}
                                        <div class="text-muted small">{{ $location->keterangan }}</div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">Rp</span>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="surveys[{{ $location->id }}][survey_tajuk]" class="form-control rupiah-input" placeholder="0" value="{{ $tajuk }}">
                                                <label>Survey Tajuk</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">Rp</span>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="surveys[{{ $location->id }}][survey_tanam]" class="form-control rupiah-input" placeholder="0" value="{{ $tanam }}">
                                                <label>Survey Tanam</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="surveys[{{ $location->id }}][nama_jukir]" class="form-control" placeholder="Nama Jukir (Bila ada)" value="{{ $jukir }}">
                                            <label>Nama Jukir</label>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Otomatis dibuat jika belum ada</small>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="surveys[{{ $location->id }}][surveyor]" class="form-control" placeholder="Nama Surveyor" value="{{ $surveyor }}">
                                            <label>Nama Surveyor</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="surveys[{{ $location->id }}][notes]" class="form-control" placeholder="Catatan..." value="{{ $notes }}">
                                            <label>Keterangan</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill btn-action px-5">
                        <i class="ti tabler-device-floppy me-1"></i> Simpan Semua Data Survey
                    </button>
                </div>
            </div>
        </form>
    @endif
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js", "resources/assets/vendor/libs/flatpickr/flatpickr.js"])
    <!-- Include Flatpickr Month Plugin via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <script type="module">
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Pilih Ruas Jalan --",
                allowClear: true
            });

            // Flatpickr for month and year only
            flatpickr('#monthPicker', {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true, // defaults to false
                        dateFormat: "Y-m", // defaults to "F Y"
                        altFormat: "F Y", // defaults to "F Y"
                        theme: "light" // defaults to "light"
                    })
                ],
                altInput: true,
                defaultDate: "{{ $selected_survey_date }}"
            });

            // Format Rupiah logic
            const formatRupiah = (value) => {
                let number_string = value.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            };

            $('.rupiah-input').on('input', function() {
                this.value = formatRupiah(this.value);
            });
        });
    </script>
@endsection
