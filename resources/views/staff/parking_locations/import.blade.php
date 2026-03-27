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
        .upload-area {
            border: 2px dashed #d9dee3;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #696cff;
            background-color: #f1f1ff;
        }

        .file-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #696cff;
        }

        .progress-wrapper {
            display: none;
            margin-top: 1.5rem;
        }

        /* [PENTING] Ini CSS agar Select2 bisa pakai Floating Label */
        .form-floating .select2-container--bootstrap-5 .select2-selection {
            /* Samakan tinggi dengan input text floating */
            height: calc(3.5rem + 2px) !important;
            padding: 1rem 1.25rem 0 1.25rem !important;
            line-height: 1.5;
        }

        .form-floating .select2-container--bootstrap-5 .select2-selection__rendered {
            padding-top: 0.625rem !important;
            /* Vertikal centering untuk text */
        }

        .form-floating .select2-container--bootstrap-5 .select2-selection__arrow {
            top: 0.85rem !important;
            /* Posisi panah */
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
                {{-- 1. AREA FILTER (LAYOUT BARU ANDA) --}}
                {{-- ====================================================== --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4">
                                <input name="zone_filter" class="form-check-input" type="radio" value="Zona 2"
                                    id="zone2" required />
                                <label class="form-check-label" for="zone2"> Zona 2</label>
                            </div>
                            <div class="form-check">
                                <input name="zone_filter" class="form-check-input" type="radio" value="Zona 3"
                                    id="zone3" required />
                                <label class="form-check-label" for="zone3"> Zona 3</label>
                            </div>
                        </div>
                    </div>

                    {{-- [PERUBAHAN] Select2 dengan Floating Label --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select select2" id="road_section_id" name="road_section_id" required
                                disabled>
                                <option value="">Pilih Zona terlebih dahulu</option>
                            </select>
                            <label for="road_section_id">2. Pilih Ruas Jalan</label>
                        </div>
                    </div>
                </div>

                {{-- ====================================================== --}}
                {{-- 2. AREA UPLOAD FILE (MODERN) --}}
                {{-- ====================================================== --}}
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">3. Upload File</label>
                        <div class="upload-area" id="dropZone">
                            <input type="file" id="import_file" name="import_file" class="d-none"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                required>
                            <div id="uploadText">
                                {{-- [ICON UPDATE] --}}
                                <i class="icon-base ri ri-file-excel-2-line file-icon"></i>
                                <h5>Klik atau Seret File ke Sini</h5>
                                <p class="text-muted text-sm">Support CSV, XLSX, XLS (Max 10MB)</p>
                            </div>
                            <div id="fileInfo" class="d-none">
                                {{-- [ICON UPDATE] --}}
                                <i class="icon-base ri ri-file-check-line file-icon text-success"></i>
                                <h5 id="fileName" class="text-dark">filename.csv</h5>
                                <p class="text-muted text-sm">Klik untuk ganti file</p>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="col-12 progress-wrapper" id="progressContainer">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold" id="progressStatus">Mengupload file...</span>
                            <span class="fw-bold" id="progressPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" id="progressBar"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted mt-1 d-block" id="progressDetail">Mohon tunggu, jangan tutup halaman
                            ini.</small>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-12 d-flex gap-3 pt-3">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            {{-- [ICON UPDATE] --}}
                            <i class="icon-base ri ri-upload-2-line me-2"></i> Mulai Proses Import
                        </button>
                        <a href="{{ route('masterdata.parking-locations.index') }}"
                            class="btn btn-label-secondary">Batal</a>
                    </div>
                </div>
            </form>
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

            // 4. Handle Submit dengan Axios & Progress Bar
            const form = document.getElementById('importForm');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const progressStatus = document.getElementById('progressStatus');
            const progressDetail = document.getElementById('progressDetail');
            const btnSubmit = document.getElementById('btnSubmit');
            const errorAlert = document.getElementById('errorAlert');
            const errorAlertMessage = document.getElementById('errorAlertMessage');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(this);

                // Reset UI
                errorAlert.classList.add('d-none'); // Sembunyikan alert error lama
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                // [PERUBAHAN] Hapus kelas animasi di awal
                progressBar.classList.remove('bg-success', 'bg-danger', 'progress-bar-striped',
                    'progress-bar-animated');
                progressBar.classList.add('bg-primary');
                progressPercent.textContent = '0%';
                progressStatus.textContent = 'Mengupload file...';
                progressDetail.textContent = 'Mohon tunggu, jangan tutup halaman ini.';
                btnSubmit.disabled = true;

                axios.post("{{ route('masterdata.parking-locations.importStore') }}", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function(progressEvent) {
                            const percentCompleted = Math.round((progressEvent.loaded * 100) /
                                progressEvent.total);
                            progressBar.style.width = percentCompleted + '%';
                            progressPercent.textContent = percentCompleted + '%';

                            if (percentCompleted === 100) {
                                // [PERUBAHAN] Ini yang Anda minta
                                // Saat upload 100%, ganti teks dan buat animasi bolak-balik
                                progressBar.classList.add('progress-bar-striped',
                                    'progress-bar-animated');
                                progressStatus.textContent =
                                    '{{ 'Proses tiap sheet di input kedalam database' }}';
                                progressDetail.textContent =
                                    'Menyimpan data ke database... Ini mungkin perlu beberapa saat.';
                            }
                        }
                    })
                    .then(function(response) {
                        // Sukses (Pakai Swal2)
                        progressBar.style.width = '100%';
                        progressBar.classList.remove('bg-primary', 'progress-bar-striped',
                            'progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        progressStatus.textContent = 'Selesai!';

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.data.message, // Ambil pesan dari JSON
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href =
                                "{{ route('masterdata.parking-locations.index') }}";
                        });
                    })
                    .catch(function(error) {
                        // [PERUBAHAN] Error (Pakai Alert Biasa)
                        progressContainer.style.display = 'none'; // Sembunyikan progress bar
                        btnSubmit.disabled = false; // Aktifkan tombol lagi

                        let errorMessage = 'Terjadi kesalahan pada server.';
                        if (error.response && error.response.data) {
                            errorMessage = error.response.data.message || errorMessage;

                            // Handle error validasi Laravel
                            if (error.response.data.errors) {
                                let errors = error.response.data.errors;
                                let errorList = '<ul>';
                                for (const key in errors) {
                                    errors[key].forEach(msg => {
                                        // Bersihkan pesan error
                                        let cleanMsg = msg.replace(/^file(\.\d+)?\s*/, '');
                                        errorList += `<li>${cleanMsg}</li>`;
                                    });
                                }
                                errorList += '</ul>';
                                errorMessage = errorList;
                            }
                        }

                        // Tampilkan di alert biasa
                        errorAlertMessage.innerHTML = errorMessage;
                        errorAlert.classList.remove('d-none');
                        // Scroll ke atas agar user lihat errornya
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
