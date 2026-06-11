@extends('layouts.app')

@section('title', 'Impor Data Lokasi Parkir')

{{-- Skeleton loading akan dieksekusi saat halaman dimuat --}}
@section('skeleton')
    @include('layouts.partials._skeleton-import-parking-location')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .premium-upload-area {
            border: 2px dashed #d9dee3;
            border-radius: 1rem;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            background: #f8f9fa;
            position: relative;
            overflow: hidden;
        }
        .premium-upload-area::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, rgba(105, 108, 255, 0.05), rgba(105, 108, 255, 0));
            z-index: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .premium-upload-area:hover::before, .premium-upload-area.dragover::before {
            opacity: 1;
        }
        .premium-upload-area:hover, .premium-upload-area.dragover {
            border-color: #696cff;
            background-color: #f8f9fa;
            box-shadow: 0 8px 20px rgba(105, 108, 255, 0.1);
            transform: translateY(-2px);
        }
        .premium-upload-area > * {
            position: relative;
            z-index: 1;
        }
        .file-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f0f2f8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            transition: all 0.3s ease;
        }
        .premium-upload-area:hover .file-icon-wrapper, .premium-upload-area.dragover .file-icon-wrapper {
            background: #e7e7ff;
            transform: scale(1.1);
        }
        .file-icon {
            font-size: 2.5rem;
            color: #696cff;
        }

        .progress-wrapper {
            display: none;
            margin-top: 1.5rem;
        }

        /* Premium Radio Cards */
        .zone-radio-label {
            cursor: pointer;
            margin: 0;
        }
        .zone-radio-label input {
            display: none;
        }
        .zone-radio-card {
            border: 2px solid #e7e7e8;
            border-radius: 0.75rem;
            transition: all 0.25s ease-in-out;
            background-color: #fff;
        }
        .zone-radio-card:hover {
            border-color: #c7c8cb;
            background-color: #fcfcfd;
            transform: translateY(-2px);
        }
        .zone-radio-label input:checked + .zone-radio-card {
            border-color: #696cff;
            background-color: rgba(105, 108, 255, 0.05);
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.15);
            transform: translateY(-2px);
        }
        .zone-radio-label input:checked + .zone-radio-card .icon-unselected {
            display: none !important;
        }
        .zone-radio-label input:checked + .zone-radio-card .icon-selected {
            display: block !important;
        }
        .zone-radio-label input:checked + .zone-radio-card span {
            color: #696cff;
            font-weight: 700 !important;
        }

        /* Select2 Floating Label Fix */
        .form-floating .select2-container--bootstrap-5 .select2-selection {
            height: calc(3.5rem + 2px) !important;
            padding: 1rem 1.25rem 0 1.25rem !important;
            line-height: 1.5;
            border-radius: 0.5rem;
            border: 1px solid #d9dee3 !important; /* Force border */
            background-color: transparent !important;
        }
        .form-floating .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .form-floating .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #696cff !important;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1) !important;
        }
        .form-floating .select2-container--bootstrap-5 .select2-selection__rendered {
            padding-top: 0.625rem !important;
        }
        .form-floating .select2-container--bootstrap-5 .select2-selection__arrow {
            top: 0.85rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Impor Data Massal (Bulk Import)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}">Lokasi Parkir</a>
                </li>
                <li class="breadcrumb-item active">Impor</li>
            </ol>
        </nav>
    </div>

    <div id="errorAlert" class="alert alert-danger alert-dismissible d-none" role="alert">
        <h5 class="alert-heading mb-2">Oops! Gagal Melakukan Impor</h5>
        <p class="mb-0" id="errorAlertMessage">Terjadi kesalahan.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="card-title mb-1">Upload File CSV/Excel</h5>
                    <p class="text-muted mb-0">Pastikan format file sesuai template. Header wajib: <code>name</code>,
                        <code>daily_deposit</code>.
                    </p>
                </div>
                {{-- [ICON UPDATE] --}}
                <a href="#" onclick="downloadTemplate(event)" class="btn btn-outline-secondary btn-sm">
                    <i class="icon-base ri ri-download-2-line me-2"></i> Download Template
                </a>
            </div>

            <form id="importForm" enctype="multipart/form-data">
                @csrf
                {{-- ====================================================== --}}
                {{-- 1. AREA FILTER (PREMIUM LAYOUT) --}}
                {{-- ====================================================== --}}
                <div class="row g-4 mb-4 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block mb-3">1. Pilih Zona <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <label class="zone-radio-label w-50">
                                <input name="zone_filter" type="radio" value="Zona 2" id="zone2" required />
                                <div class="zone-radio-card text-center d-flex flex-column justify-content-center align-items-center py-3">
                                    <i class="ri ri-map-pin-2-line fs-3 mb-2 text-muted icon-unselected"></i>
                                    <i class="ri ri-map-pin-2-fill fs-3 mb-2 text-primary icon-selected d-none"></i>
                                    <span class="fw-semibold text-muted">Zona 2</span>
                                </div>
                            </label>
                            <label class="zone-radio-label w-50">
                                <input name="zone_filter" type="radio" value="Zona 3" id="zone3" required />
                                <div class="zone-radio-card text-center d-flex flex-column justify-content-center align-items-center py-3">
                                    <i class="ri ri-map-pin-user-line fs-3 mb-2 text-muted icon-unselected"></i>
                                    <i class="ri ri-map-pin-user-fill fs-3 mb-2 text-primary icon-selected d-none"></i>
                                    <span class="fw-semibold text-muted">Zona 3</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- [PERUBAHAN] Select2 dengan Floating Label --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold d-block mb-3">2. Pilih Ruas Jalan <span class="text-danger">*</span></label>
                        <div class="form-floating form-floating-outline">
                            <select class="form-select select2" id="road_section_id" name="road_section_id" required
                                disabled>
                                <option value="">Pilih Zona terlebih dahulu</option>
                            </select>
                            <label for="road_section_id">Ruas Jalan</label>
                        </div>
                    </div>
                </div>

                {{-- ====================================================== --}}
                {{-- 2. AREA UPLOAD FILE (MODERN) --}}
                {{-- ====================================================== --}}
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold mb-3">3. Upload File <span class="text-danger">*</span></label>
                        <div class="premium-upload-area" id="dropZone">
                            <input type="file" id="import_file" name="import_file" class="d-none"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                required>
                            <div id="uploadText">
                                <div class="file-icon-wrapper">
                                    <i class="ri ri-file-excel-2-line file-icon"></i>
                                </div>
                                <h5 class="fw-bold mb-1">Klik atau Seret File ke Sini</h5>
                                <p class="text-muted mb-0">Support format CSV, XLSX, XLS (Max 10MB)</p>
                            </div>
                            <div id="fileInfo" class="d-none">
                                <div class="file-icon-wrapper bg-success bg-opacity-10 text-success">
                                    <i class="ri ri-file-check-line file-icon text-success"></i>
                                </div>
                                <h5 id="fileName" class="fw-bold text-dark mb-1">filename.csv</h5>
                                <p class="text-muted mb-0">Klik untuk mengganti file</p>
                            </div>
                        </div>
                    </div>


                    {{-- Tombol Aksi --}}
                    <div class="col-12 d-flex gap-3 pt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnSubmit">
                            <i class="ri ri-upload-cloud-2-line me-2"></i> Mulai Proses Impor
                        </button>
                        <a href="{{ route('masterdata.parking-locations.index') }}"
                            class="btn btn-label-secondary btn-lg">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

                    {{-- Premium Progress Area (Hidden by default) --}}
                    <div class="col-12 mt-2">
                        <div id="premiumProgressArea" class="d-none p-4 rounded-3" style="background-color: #f8f9fa; border: 1px solid #e7e7e8;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="spinner-border text-primary me-3" role="status" id="progressSpinner" style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <i class="ri ri-checkbox-circle-fill text-success d-none" id="progressSuccessIcon" style="font-size: 2.5rem; margin-right: 1rem;"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold text-primary" id="progressTitle">Mengunggah File...</h5>
                                    <p class="mb-0 text-muted" id="progressSubtitle">Mohon jangan tutup halaman ini.</p>
                                </div>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 10px;" id="progressContainer">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="progressBar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <span class="fw-bold text-primary" id="progressPercent">0%</span>
                            </div>
                        </div>
                    </div>

    {{-- Data Ruas Jalan untuk JS Filtering --}}
    <script>
        // Data diambil dari controller (Hanya Zona 2 & 3)
        const roadSectionsByZone = @json($roadSectionsByZone);
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Select2
            const $roadSelect = $('#road_section_id');
            $roadSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Zona terlebih dahulu'
            });

            // 2. Logika Filter Zona -> Ruas Jalan
            const zoneRadios = document.querySelectorAll('input[name="zone_filter"]');
            zoneRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedZone = this.value;
                    const roads = roadSectionsByZone[selectedZone] || [];

                    $roadSelect.empty();

                    if (roads.length > 0) {
                        $roadSelect.append('<option value="">-- Pilih Ruas Jalan --</option>');
                        roads.forEach(road => {
                            $roadSelect.append(new Option(road.name, road.id));
                        });
                        $roadSelect.prop('disabled', false);
                        $roadSelect.select2('open'); // Langsung buka dropdown
                    } else {
                        $roadSelect.append(
                            '<option value="" disabled>Tidak ada ruas jalan di zona ini</option>'
                        );
                        $roadSelect.prop('disabled', true);
                    }

                    $roadSelect.trigger('change');
                });
            });

            // 3. UI Upload File (Drag & Drop + Click)
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('import_file');
            const uploadText = document.getElementById('uploadText');
            const fileInfo = document.getElementById('fileInfo');
            const fileNameDisplay = document.getElementById('fileName');

            dropZone.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    showFileInfo(this.files[0].name);
                }
            });

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    showFileInfo(e.dataTransfer.files[0].name);
                }
            });

            function showFileInfo(name) {
                uploadText.classList.add('d-none');
                fileInfo.classList.remove('d-none');
                fileNameDisplay.textContent = name;

                // [ICON UPDATE] Logika di JS untuk ganti icon
                const icon = fileInfo.querySelector('i');
                if (name.endsWith('.csv')) {
                    icon.className = 'icon-base ri ri-file-text-line file-icon text-success';
                } else if (name.endsWith('.xlsx') || name.endsWith('.xls')) {
                    icon.className = 'icon-base ri ri-file-excel-2-line file-icon text-success';
                } else {
                    icon.className = 'icon-base ri ri-file-check-line file-icon text-success';
                }
            }

            // 4. Handle Submit dengan Axios & Premium Inline Progress
            const form = document.getElementById('importForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const errorAlert = document.getElementById('errorAlert');
            const errorAlertMessage = document.getElementById('errorAlertMessage');

            // Progress Elements
            const premiumProgressArea = document.getElementById('premiumProgressArea');
            const progressSpinner = document.getElementById('progressSpinner');
            const progressSuccessIcon = document.getElementById('progressSuccessIcon');
            const progressTitle = document.getElementById('progressTitle');
            const progressSubtitle = document.getElementById('progressSubtitle');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(this);

                // Reset UI
                errorAlert.classList.add('d-none');
                btnSubmit.disabled = true;

                // Tampilkan Progress Area
                premiumProgressArea.classList.remove('d-none');
                progressSpinner.classList.remove('d-none');
                progressSuccessIcon.classList.add('d-none');
                
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-success', 'bg-danger');
                progressBar.classList.add('bg-primary', 'progress-bar-striped', 'progress-bar-animated');
                
                progressTitle.textContent = 'Mengunggah File...';
                progressTitle.className = 'mb-1 fw-bold text-primary';
                progressSubtitle.textContent = 'Mohon tunggu, jangan tutup halaman ini.';
                progressPercent.textContent = '0%';
                progressPercent.className = 'fw-bold text-primary';

                axios.post("{{ route('masterdata.parking-locations.importStore') }}", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function(progressEvent) {
                            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            progressBar.style.width = percentCompleted + '%';
                            progressPercent.textContent = percentCompleted + '%';

                            if (percentCompleted === 100) {
                                progressTitle.textContent = 'Memproses Data...';
                                progressSubtitle.textContent = 'Menyimpan baris data ke database... Ini mungkin memerlukan beberapa saat.';
                            }
                        }
                    })
                    .then(function(response) {
                        // Sukses 
                        progressBar.style.width = '100%';
                        progressBar.classList.remove('bg-primary', 'progress-bar-striped', 'progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        
                        progressSpinner.classList.add('d-none');
                        progressSuccessIcon.classList.remove('d-none');
                        
                        progressTitle.textContent = 'Selesai!';
                        progressTitle.className = 'mb-1 fw-bold text-success';
                        progressSubtitle.textContent = 'Data berhasil diproses. Mengalihkan...';
                        progressPercent.textContent = '100%';
                        progressPercent.className = 'fw-bold text-success';

                        // Tunggu sebentar biar user lihat bar hijau 100%
                        setTimeout(() => {
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                window.location.href = "{{ route('masterdata.parking-locations.index') }}";
                            }
                        }, 1000);
                    })
                    .catch(function(error) {
                        // Jika backend mengirimkan redirect (seperti untuk pesan Toastr)
                        if (error.response && error.response.data && error.response.data.redirect) {
                            window.location.href = error.response.data.redirect;
                            return;
                        }

                        premiumProgressArea.classList.add('d-none');
                        btnSubmit.disabled = false; 

                        let errorMessage = 'Terjadi kesalahan pada server.';
                        if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message || errorMessage;

                            if (error.response.data.errors) {
                                let errors = error.response.data.errors;
                                let errorList = '<ul>';
                                for (const key in errors) {
                                    errors[key].forEach(msg => {
                                        let cleanMsg = msg.replace(/^file(\.\d+)?\s*/, '');
                                        errorList += `<li>${cleanMsg}</li>`;
                                    });
                                }
                                errorList += '</ul>';
                                errorMessage = errorList;
                            }
                        }

                        errorAlertMessage.innerHTML = errorMessage;
                        errorAlert.classList.remove('d-none');
                        window.scrollTo(0, 0);
                    });
            });
        });

        // Fungsi helper untuk download template
        function downloadTemplate(event) {
            event.preventDefault();
            let csvContent =
                "data:text/csv;charset=utf-8,name,daily_deposit,latitude,longitude\nContoh Lokasi 1,5000,-6.200000,106.816666\nContoh Lokasi 2,10000,,";
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "template_import_lokasi.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endpush
