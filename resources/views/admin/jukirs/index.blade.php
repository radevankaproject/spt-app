@extends('layouts.contentNavbarLayout')

@section('title', 'Data Jukir')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <style>
        .flatpickr-calendar {
            z-index: 1090 !important;
        }
    </style>

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-users me-2"></i>Data Juru Parkir (Jukir)</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola data jukir yang terdaftar di masing-masing titik lokasi parkir.</p>
            </div>
        </div>
        <i class="ti tabler-users position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <div class="glass-card anim-2 border-0 overflow-hidden mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3 p-4">
            <div>
                <h5 class="mb-1">Daftar Jukir Terdaftar</h5>
                <p class="text-muted mb-0">Total {{ count($jukirs) }} jukir.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <button type="button" class="btn btn-primary rounded-pill btn-action" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="ri icon-base ti tabler-plus me-1"></i> Tambah Jukir
                </button>
            </div>
        </div>

        <div class="card-body pt-3 p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fw-bold" role="alert">
                    <i class="ti tabler-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                @forelse ($jukirs as $jukir)
                    <div class="col-md-4 col-xl-3">
                        <div class="card h-100 position-relative {{ $jukir->is_blacklisted ? 'border-danger' : 'border-0 shadow-sm' }}" style="transition: all 0.3s ease;">
                            @if($jukir->is_blacklisted)
                                <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 d-flex align-items-center justify-content-center bg-white" style="opacity: 0.6; z-index: 2; border-radius: inherit; backdrop-filter: blur(2px);">
                                </div>
                                <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 3;">
                                    <span class="badge bg-danger shadow fs-6 py-2 px-3 border border-white"><i class="ti tabler-ban me-1"></i> BLACKLISTED</span>
                                </div>
                            @endif

                            <div class="card-body text-center d-flex flex-column" style="{{ $jukir->is_blacklisted ? 'filter: blur(2px);' : '' }}">
                                <div class="mx-auto mb-3 position-relative">
                                    @if($jukir->image)
                                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto Jukir" class="rounded-circle border border-2 border-primary" width="80" height="80" style="object-fit: cover; padding: 2px;">
                                    @else
                                        <div class="avatar avatar-xl rounded-circle bg-label-primary mx-auto d-flex align-items-center justify-content-center fw-bold text-primary border border-2 border-primary p-1" style="width: 80px; height: 80px;">
                                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                                        </div>
                                    @endif
                                    @if(!$jukir->is_blacklisted)
                                        <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $jukir->is_active ? 'success' : 'warning' }} border border-white rounded-circle" title="{{ $jukir->is_active ? 'Aktif' : 'Nonaktif' }}"></span>
                                    @endif
                                </div>
                                <h5 class="mb-1 text-dark fw-bold">{{ $jukir->nama_jukir }}</h5>
                                <p class="text-muted mb-3" style="font-size: 0.85rem;"><i class="ti tabler-id-badge text-muted me-1"></i> {{ $jukir->id_jukir ?? '-' }}</p>
                                
                                <div class="d-flex align-items-center justify-content-center mb-2 bg-label-secondary rounded p-2">
                                    <i class="ti tabler-map-pin text-primary me-2"></i>
                                    <span class="fw-medium text-dark text-truncate" style="max-width: 150px;" title="{{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}">
                                        {{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <i class="ti tabler-phone text-muted me-2"></i>
                                    <span class="text-muted small">{{ $jukir->phone_number ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-center gap-2 mt-auto pt-3 border-top position-relative" style="z-index: {{ $jukir->is_blacklisted ? '0' : '4' }};">
                                    <a href="{{ route('admin.jukirs.show', $jukir->id) }}" class="btn btn-sm btn-info rounded-pill" data-bs-toggle="tooltip" title="Detail & Riwayat">
                                        <i class="ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.jukirs.print-kta', $jukir->id) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill" data-bs-toggle="tooltip" title="Cetak KTA">
                                        <i class="ti tabler-file-type-pdf"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon"
                                        onclick="openEditModal({{ $jukir->id }}, '{{ $jukir->id_jukir }}', '{{ addslashes($jukir->nama_jukir) }}', '{{ $jukir->parking_location_id }}', '{{ addslashes($jukir->no_ktp) }}', '{{ addslashes($jukir->phone_number) }}', {{ $jukir->is_active ? 'true' : 'false' }}, '{{ $jukir->image ? Storage::url($jukir->image) : '' }}', '{{ $jukir->image_ktp ? Storage::url($jukir->image_ktp) : '' }}', '{{ $jukir->kta_type }}', '{{ $jukir->kta_start_date }}')"
                                        data-bs-toggle="tooltip" title="Edit">
                                        <i class="ti tabler-pencil"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="icon-glass bg-label-secondary mx-auto mb-3">
                            <i class="ti tabler-user-off fs-1 text-muted"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Tidak Ada Data</h5>
                        <p class="text-muted mb-0">Belum ada data Jukir yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Juru Parkir Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.jukirs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titik Parkir</label>
                            <select name="parking_location_id" class="form-select select2-parking">
                                <option value="">Pilih Titik Parkir</option>
                                @foreach($parkingLocations as $pl)
                                    @php
                                        $activeAgreement = $pl->agreements->first();
                                        $korlap = $activeAgreement && $activeAgreement->leader ? $activeAgreement->leader->name : '-';
                                        $zona = $pl->roadSection->zone ?? '-';
                                        $alamat = $pl->roadSection->name ?? '-';
                                    @endphp
                                    <option value="{{ $pl->id }}" data-korlap="{{ $korlap }}" data-zona="{{ $zona }}" data-alamat="{{ $alamat }}">{{ $pl->name }}</option>
                                @endforeach
                            </select>
                            <div class="parking-info-box mt-2 p-3 bg-light rounded border border-primary border-dashed" style="display: none; font-size: 0.85rem;">
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-user me-1"></i> Korlap</span><span class="fw-bold text-dark info-korlap">-</span></div>
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-map-pin me-1"></i> Zona</span><span class="fw-bold text-dark info-zona">-</span></div>
                                <div class="d-flex"><span class="text-muted w-px-100"><i class="ti tabler-road me-1"></i> Ruas Jalan</span><span class="fw-bold text-dark info-alamat">-</span></div>
                            </div>
                        </div>

                        <!-- KTA Fields (Hidden by default) -->
                        <div id="kta_section_create" class="row d-none bg-label-primary p-3 rounded mb-3 mx-0">
                            <h6 class="fw-bold text-primary mb-2 px-0"><i class="ti tabler-id-badge"></i> Informasi KTA</h6>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Jenis KTA</label>
                                <select name="kta_type" id="create_kta_type" class="form-select">
                                    <option value="">Pilih Jenis (Opsional)</option>
                                    <option value="baru">Baru</option>
                                    <option value="perpanjangan">Perpanjangan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Tanggal Mulai KTA</label>
                                <input type="date" name="kta_start_date" id="create_kta_start_date" class="form-control">
                                <small class="text-muted">Masa berlaku +3 bulan</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Jukir</label>
                            <input type="text" class="form-control text-muted" value="{{ $nextIdJukir }}" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Jukir <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jukir" class="form-control" placeholder="Nama lengkap jukir" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. KTP</label>
                            <input type="text" name="no_ktp" class="form-control" placeholder="Nomor KTP (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Handphone</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="Nomor HP (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_preview_create" src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle border border-2 border-primary" width="60" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_progress_container_create" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_progress_bar_create" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image" id="image_input_create" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis crop 1:1 (lingkaran) & kompres dibawah 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto KTP</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_ktp_preview_create" src="" class="rounded border border-2 border-primary" width="90" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_ktp_progress_container_create" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_ktp_progress_bar_create" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image_ktp" id="image_ktp_input_create" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis kompres dibawah 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_create" value="1" checked>
                            <label class="form-check-label fw-bold" for="is_active_create">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-label-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Data Juru Parkir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titik Parkir</label>
                            <select name="parking_location_id" id="edit_parking_location_id" class="form-select select2-parking">
                                <option value="">Pilih Titik Parkir</option>
                                @foreach($parkingLocations as $pl)
                                    @php
                                        $activeAgreement = $pl->agreements->first();
                                        $korlap = $activeAgreement && $activeAgreement->leader ? $activeAgreement->leader->name : '-';
                                        $zona = $pl->roadSection->zone ?? '-';
                                        $alamat = $pl->roadSection->name ?? '-';
                                    @endphp
                                    <option value="{{ $pl->id }}" data-korlap="{{ $korlap }}" data-zona="{{ $zona }}" data-alamat="{{ $alamat }}">{{ $pl->name }}</option>
                                @endforeach
                            </select>
                            <div class="parking-info-box mt-2 p-3 bg-light rounded border border-primary border-dashed" style="display: none; font-size: 0.85rem;">
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-user me-1"></i> Korlap</span><span class="fw-bold text-dark info-korlap">-</span></div>
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-map-pin me-1"></i> Zona</span><span class="fw-bold text-dark info-zona">-</span></div>
                                <div class="d-flex"><span class="text-muted w-px-100"><i class="ti tabler-road me-1"></i> Ruas Jalan</span><span class="fw-bold text-dark info-alamat">-</span></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Jukir</label>
                            <input type="text" id="edit_id_jukir" class="form-control text-muted" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Jukir <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jukir" id="edit_nama_jukir" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. KTP</label>
                            <input type="text" name="no_ktp" id="edit_no_ktp" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Handphone</label>
                            <input type="text" name="phone_number" id="edit_phone_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Profil (Opsional)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_preview_edit" src="" class="rounded-circle border border-2 border-primary" width="60" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_progress_container_edit" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_progress_bar_edit" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image" id="image_input_edit" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Biarkan kosong jika tak diubah. Otomatis crop 1:1 & kompres &lt; 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto KTP (Opsional)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_ktp_preview_edit" src="" class="rounded border border-2 border-primary" width="90" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_ktp_progress_container_edit" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_ktp_progress_bar_edit" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image_ktp" id="image_ktp_input_edit" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Biarkan kosong jika tak diubah. Otomatis kompres &lt; 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div id="kta_section_edit" class="row d-none bg-label-primary p-3 rounded mb-3 mx-0">
                            <h6 class="fw-bold text-primary mb-2 px-0"><i class="ti tabler-id-badge"></i> Informasi KTA</h6>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Jenis KTA</label>
                                <select name="kta_type" id="edit_kta_type" class="form-select">
                                    <option value="">Pilih Jenis (Opsional)</option>
                                    <option value="baru">Baru</option>
                                    <option value="perpanjangan">Perpanjangan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Tanggal Mulai KTA</label>
                                <input type="date" name="kta_start_date" id="edit_kta_start_date" class="form-control">
                                <small class="text-muted">Masa berlaku +3 bulan</small>
                            </div>
                        </div>
                        <div class="mb-3 form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label fw-bold" for="edit_is_active">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-label-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js", "resources/assets/vendor/libs/select2/select2.js", "resources/assets/vendor/libs/flatpickr/flatpickr.js"])
    <script type="module">
        $(document).ready(function() {
            $('#createModal .select2-parking').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Cari Titik Parkir...",
                allowClear: true
            });
            
            $('#editModal .select2-parking').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Cari Titik Parkir...",
                allowClear: true
            });

            // Inisialisasi Flatpickr
            const createDate = document.getElementById('create_kta_start_date');
            const editDate = document.getElementById('edit_kta_start_date');

            if (createDate) {
                flatpickr(createDate, {
                    dateFormat: "Y-m-d",
                });
            }

            if (editDate) {
                flatpickr(editDate, {
                    dateFormat: "Y-m-d",
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // --- Image Compression & 1:1 Circle Crop Logic ---
        async function processImage(inputEl, previewEl, containerEl, barEl) {
            const file = inputEl.files[0];
            if (!file) {
                previewEl.style.display = 'none';
                return;
            }

            containerEl.style.display = 'block';
            previewEl.style.display = 'block';
            previewEl.style.opacity = '0.3';
            barEl.style.width = '10%';

            const reader = new FileReader();
            reader.onload = function(e) {
                barEl.style.width = '30%';
                const img = new Image();
                img.onload = function() {
                    barEl.style.width = '50%';
                    
                    // 1:1 Crop calculation (center)
                    const size = Math.min(img.width, img.height);
                    const startX = (img.width - size) / 2;
                    const startY = (img.height - size) / 2;

                    const canvas = document.createElement('canvas');
                    canvas.width = size;
                    canvas.height = size;
                    const ctx = canvas.getContext('2d');
                    
                    // Draw cropped square
                    ctx.drawImage(img, startX, startY, size, size, 0, 0, size, size);

                    let quality = 0.9;
                    let dataUrl = canvas.toDataURL('image/jpeg', quality);
                    
                    // Target ~50KB max -> base64 size approx 68,000 chars
                    const maxBase64Size = 68000;
                    barEl.style.width = '70%';
                    
                    // Compress quality
                    let loop = 0;
                    while(dataUrl.length > maxBase64Size && quality > 0.1 && loop < 10) {
                        quality -= 0.1;
                        dataUrl = canvas.toDataURL('image/jpeg', quality);
                        loop++;
                    }
                    
                    // Scale down resolution if still too big
                    let scale = 1.0;
                    while(dataUrl.length > maxBase64Size && scale > 0.2 && loop < 20) {
                        scale -= 0.2;
                        const scaledCanvas = document.createElement('canvas');
                        scaledCanvas.width = size * scale;
                        scaledCanvas.height = size * scale;
                        const scaledCtx = scaledCanvas.getContext('2d');
                        scaledCtx.drawImage(canvas, 0, 0, scaledCanvas.width, scaledCanvas.height);
                        dataUrl = scaledCanvas.toDataURL('image/jpeg', 0.5);
                        loop++;
                    }

                    barEl.style.width = '90%';

                    // Convert to File
                    fetch(dataUrl)
                        .then(res => res.blob())
                        .then(blob => {
                            const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
                            
                            // Re-assign to input
                            const dt = new DataTransfer();
                            dt.items.add(compressedFile);
                            inputEl.files = dt.files;

                            // Update Preview
                            previewEl.src = dataUrl;
                            previewEl.style.opacity = '1';
                            
                            barEl.style.width = '100%';
                            setTimeout(() => {
                                containerEl.style.display = 'none';
                                barEl.style.width = '0%';
                            }, 500);
                        });
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        document.getElementById('image_input_create').addEventListener('change', function() {
            processImage(this, document.getElementById('image_preview_create'), document.getElementById('image_progress_container_create'), document.getElementById('image_progress_bar_create'));
        });

        document.getElementById('image_input_edit').addEventListener('change', function() {
            processImage(this, document.getElementById('image_preview_edit'), document.getElementById('image_progress_container_edit'), document.getElementById('image_progress_bar_edit'));
        });

        async function processImageKtp(inputEl, previewEl, containerEl, barEl) {
            const file = inputEl.files[0];
            if (!file) {
                previewEl.style.display = 'none';
                return;
            }

            containerEl.style.display = 'block';
            previewEl.style.display = 'block';
            previewEl.style.opacity = '0.3';
            barEl.style.width = '10%';

            const reader = new FileReader();
            reader.onload = function(e) {
                barEl.style.width = '30%';
                const img = new Image();
                img.onload = function() {
                    barEl.style.width = '50%';
                    
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    
                    ctx.drawImage(img, 0, 0, img.width, img.height);

                    let quality = 0.9;
                    let dataUrl = canvas.toDataURL('image/jpeg', quality);
                    
                    const maxBase64Size = 68000;
                    barEl.style.width = '70%';
                    
                    let loop = 0;
                    while(dataUrl.length > maxBase64Size && quality > 0.1 && loop < 10) {
                        quality -= 0.1;
                        dataUrl = canvas.toDataURL('image/jpeg', quality);
                        loop++;
                    }
                    
                    let scale = 1.0;
                    while(dataUrl.length > maxBase64Size && scale > 0.2 && loop < 20) {
                        scale -= 0.2;
                        const scaledCanvas = document.createElement('canvas');
                        scaledCanvas.width = img.width * scale;
                        scaledCanvas.height = img.height * scale;
                        const scaledCtx = scaledCanvas.getContext('2d');
                        scaledCtx.drawImage(canvas, 0, 0, scaledCanvas.width, scaledCanvas.height);
                        dataUrl = scaledCanvas.toDataURL('image/jpeg', 0.5);
                        loop++;
                    }

                    barEl.style.width = '90%';

                    fetch(dataUrl)
                        .then(res => res.blob())
                        .then(blob => {
                            const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
                            
                            const dt = new DataTransfer();
                            dt.items.add(compressedFile);
                            inputEl.files = dt.files;

                            previewEl.src = dataUrl;
                            previewEl.style.opacity = '1';
                            
                            barEl.style.width = '100%';
                            setTimeout(() => {
                                containerEl.style.display = 'none';
                                barEl.style.width = '0%';
                            }, 500);
                        });
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        document.getElementById('image_ktp_input_create').addEventListener('change', function() {
            processImageKtp(this, document.getElementById('image_ktp_preview_create'), document.getElementById('image_ktp_progress_container_create'), document.getElementById('image_ktp_progress_bar_create'));
        });

        document.getElementById('image_ktp_input_edit').addEventListener('change', function() {
            processImageKtp(this, document.getElementById('image_ktp_preview_edit'), document.getElementById('image_ktp_progress_container_edit'), document.getElementById('image_ktp_progress_bar_edit'));
        });
        // ------------------------------------------------

        // Update Info Box Logic
        $('.select2-parking').on('change', function() {
            let val = $(this).val();
            if (val) {
                let option = $(this).find('option:selected');
                let infoBox = $(this).siblings('.parking-info-box');
                infoBox.find('.info-korlap').text(option.data('korlap'));
                infoBox.find('.info-zona').text(option.data('zona'));
                infoBox.find('.info-alamat').text(option.data('alamat'));
                infoBox.slideDown();
                
                // Show KTA section
                if ($(this).attr('id') === 'edit_parking_location_id') {
                    $('#kta_section_edit').removeClass('d-none');
                } else {
                    $('#kta_section_create').removeClass('d-none');
                }
            } else {
                $(this).siblings('.parking-info-box').slideUp();
                
                // Hide and reset KTA section
                if ($(this).attr('id') === 'edit_parking_location_id') {
                    $('#kta_section_edit').addClass('d-none');
                    $('#edit_kta_type').val('');
                    $('#edit_kta_start_date').val('');
                    if (document.getElementById('edit_kta_start_date')._flatpickr) document.getElementById('edit_kta_start_date')._flatpickr.clear();
                } else {
                    $('#kta_section_create').addClass('d-none');
                    $('#create_kta_type').val('');
                    $('#create_kta_start_date').val('');
                    if (document.getElementById('create_kta_start_date')._flatpickr) document.getElementById('create_kta_start_date')._flatpickr.clear();
                }
            }
        });
        
        // Handle KTA fields visibility on edit modal open
        window.openEditModal = function(id, idJukir, nama, parkingLocationId, noKtp, phoneNumber, isActive, imageUrl, imageKtpUrl, ktaType, ktaStartDate) {
            let form = document.getElementById('editForm');
            form.action = `/admin/jukirs/${id}`;

            document.getElementById('edit_id_jukir').value = idJukir;
            document.getElementById('edit_nama_jukir').value = nama;
            $('#edit_parking_location_id').val(parkingLocationId).trigger('change');
            document.getElementById('edit_no_ktp').value = noKtp;
            document.getElementById('edit_phone_number').value = phoneNumber;
            document.getElementById('edit_is_active').checked = isActive;
            
            if(ktaType) {
                document.getElementById('edit_kta_type').value = ktaType;
            } else {
                document.getElementById('edit_kta_type').value = "";
            }
            
            if(ktaStartDate) {
                document.getElementById('edit_kta_start_date').value = ktaStartDate.substring(0, 10);
                if (document.getElementById('edit_kta_start_date')._flatpickr) {
                    document.getElementById('edit_kta_start_date')._flatpickr.setDate(ktaStartDate.substring(0, 10));
                }
            } else {
                document.getElementById('edit_kta_start_date').value = "";
                if (document.getElementById('edit_kta_start_date')._flatpickr) {
                    document.getElementById('edit_kta_start_date')._flatpickr.clear();
                }
            }
            
            // Toggle visibility immediately based on passed parkingLocationId
            if (parkingLocationId) {
                $('#kta_section_edit').removeClass('d-none');
            } else {
                $('#kta_section_edit').addClass('d-none');
            }
            
            // Preview image reset or set
            let preview = document.getElementById('image_preview_edit');
            if(imageUrl) {
                preview.src = imageUrl;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
            document.getElementById('image_input_edit').value = '';

            let previewKtp = document.getElementById('image_ktp_preview_edit');
            if(imageKtpUrl) {
                previewKtp.src = imageKtpUrl;
                previewKtp.style.display = 'block';
            } else {
                previewKtp.src = '';
                previewKtp.style.display = 'none';
            }
            document.getElementById('image_ktp_input_edit').value = '';
            
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        };

        window.confirmDelete = function(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: `Anda akan menghapus data jukir <strong>${name}</strong>.<br>Data ini akan terhapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>
@endsection
