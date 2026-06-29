@extends('layouts.contentNavbarLayout')

@section('title', 'Data Jukir')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />

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

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th scope="col" class="text-uppercase fw-bold text-primary" width="5%">No</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Foto</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Nama Jukir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Titik Parkir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Kontak / KTP</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($jukirs as $index => $jukir)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($jukir->image)
                                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto Jukir" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="avatar avatar-sm rounded-circle bg-label-secondary d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $jukir->nama_jukir }}</span>
                                    @if(!$jukir->is_active)
                                        <span class="badge bg-label-danger ms-1" style="font-size: 0.65rem;">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $jukir->parkingLocation->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted"><i class="ti tabler-phone"></i> {{ $jukir->phone_number ?? '-' }}</span>
                                        <span class="small text-muted"><i class="ti tabler-id"></i> {{ $jukir->no_ktp ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            onclick="openEditModal({{ $jukir->id }}, '{{ addslashes($jukir->nama_jukir) }}', '{{ $jukir->parking_location_id }}', '{{ addslashes($jukir->no_ktp) }}', '{{ addslashes($jukir->phone_number) }}', {{ $jukir->is_active ? 'true' : 'false' }}, '{{ $jukir->image ? Storage::url($jukir->image) : '' }}')"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="ri icon-base ti tabler-pencil icon-22px"></i>
                                        </button>

                                        <form
                                            action="{{ route('admin.jukirs.destroy', $jukir->id) }}"
                                            method="POST" class="d-inline" id="deleteForm{{ $jukir->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-text-danger rounded-pill"
                                                onclick="confirmDelete({{ $jukir->id }}, '{{ addslashes($jukir->nama_jukir) }}')"
                                                data-bs-toggle="tooltip" title="Hapus">
                                                <i class="ri icon-base ti tabler-trash icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-center py-5">
                                        <div class="icon-glass bg-label-secondary mx-auto mb-3">
                                            <i class="ti tabler-user-off fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Tidak Ada Data</h5>
                                        <p class="text-muted mb-0">Belum ada data Jukir yang terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            <label class="form-label fw-bold">Titik Parkir <span class="text-danger">*</span></label>
                            <select name="parking_location_id" class="form-select select2-parking" required>
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
                            <label class="form-label fw-bold">Titik Parkir <span class="text-danger">*</span></label>
                            <select name="parking_location_id" id="edit_parking_location_id" class="form-select select2-parking" required>
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
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js", "resources/assets/vendor/libs/select2/select2.js"])
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
        // ------------------------------------------------

        // Update Info Box Logic
        $('.select2-parking').on('change', function() {
            let option = $(this).find('option:selected');
            let modalId = $(this).closest('.modal').attr('id');
            if (option.val()) {
                $('#' + modalId + ' .info-korlap').text(option.data('korlap') || '-');
                $('#' + modalId + ' .info-zona').text(option.data('zona') || '-');
                $('#' + modalId + ' .info-alamat').text(option.data('alamat') || '-');
                $('#' + modalId + ' .parking-info-box').slideDown();
            } else {
                $('#' + modalId + ' .parking-info-box').slideUp();
            }
        });
        
        window.openEditModal = function(id, name, parkingLocationId, noKtp, phoneNumber, isActive, imageUrl) {
            document.getElementById('editForm').action = '/admin/jukirs/' + id;
            document.getElementById('edit_nama_jukir').value = name;
            $('#edit_parking_location_id').val(parkingLocationId).trigger('change');
            document.getElementById('edit_no_ktp').value = noKtp;
            document.getElementById('edit_phone_number').value = phoneNumber;
            document.getElementById('edit_is_active').checked = isActive;
            
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
