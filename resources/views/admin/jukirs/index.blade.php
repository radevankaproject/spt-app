@extends('layouts.contentNavbarLayout')

@section('title', 'Data Jukir')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <style>
        .flatpickr-calendar {
            z-index: 1090 !important;
        }
        .jukir-card-item .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12) !important;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake-animation {
            animation: shake 0.3s ease-in-out;
        }
    </style>

@section('content')
    {{-- Page Hero --}}
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-users me-2"></i>Data Juru Parkir (Jukir)</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola data jukir yang terdaftar di masing-masing titik lokasi parkir.</p>
            </div>
        </div>
        <i class="ti tabler-users position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    {{-- Main Card --}}
    <div class="glass-card anim-2 border-0 overflow-hidden mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3 p-4">
            <div>
                <h5 class="mb-1">Daftar Jukir Terdaftar</h5>
                <p class="text-muted mb-0">Total {{ count($jukirs) }} jukir.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <a href="{{ route('admin.jukirs.importCreate') }}" class="btn btn-outline-primary rounded-pill btn-action">
                    <i class="ri icon-base ti tabler-file-import me-1"></i> Import Bulk
                </a>
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

            {{-- Search Box --}}
            <form action="{{ route('admin.jukirs.index') }}" method="GET" class="row mb-4">
                <div class="col-12 col-md-6 mx-auto">
                    <div class="input-group input-group-merge shadow-sm" style="border-radius: 50px; overflow: hidden; background: white;">
                        <span class="input-group-text border-0 bg-transparent text-primary ps-4"><i class="ti tabler-search fs-4"></i></span>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control border-0 bg-transparent py-3" placeholder="Cari ID, nama jukir, atau lokasi parkir..." aria-label="Search">
                        @if(request('search'))
                            <a href="{{ route('admin.jukirs.index') }}" class="input-group-text border-0 bg-transparent text-danger pe-4"><i class="ti tabler-x fs-4"></i></a>
                        @endif
                        <button type="submit" class="d-none"></button>
                    </div>
                </div>
            </form>

            {{-- Jukir Cards --}}
            <div class="row g-4" id="jukir-cards-container">
                @forelse ($jukirs as $jukir)
                    @php
                        $activeAgreementCard = $jukir->parkingLocation ? $jukir->parkingLocation->agreements->first() : null;
                        $korlapCard = $activeAgreementCard && $activeAgreementCard->fieldCoordinator && $activeAgreementCard->fieldCoordinator->user ? $activeAgreementCard->fieldCoordinator->user->name : '-';
                        $ruasJalanCard = $jukir->parkingLocation && $jukir->parkingLocation->roadSection ? $jukir->parkingLocation->roadSection->name : '-';
                    @endphp
                    <div class="col-md-4 col-xl-3 jukir-card-item"
                         data-id="{{ strtolower($jukir->id_jukir ?? '') }}"
                         data-nama="{{ strtolower($jukir->nama_jukir) }}"
                         data-lokasi="{{ strtolower($jukir->parkingLocation->name ?? '') }}">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            {{-- Banner & Status --}}
                            <div class="bg-primary position-relative" style="height: 90px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                                @if($jukir->is_blacklisted)
                                    <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); z-index: 2;">
                                        <span class="badge bg-danger fs-6 py-1 px-3 border border-white shadow"><i class="ti tabler-ban me-1"></i> BLACKLISTED</span>
                                    </div>
                                @else
                                    <div class="position-absolute top-0 end-0 p-3">
                                        <span class="badge bg-{{ $jukir->is_active ? 'success' : 'warning' }} rounded-pill border border-white shadow-sm">
                                            {{ $jukir->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body text-center pt-0 position-relative" style="{{ $jukir->is_blacklisted ? 'filter: grayscale(100%);' : '' }}">
                                {{-- Avatar --}}
                                <div class="mx-auto mb-3" style="margin-top: -45px; position: relative; z-index: 5;">
                                    @if($jukir->image)
                                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto" class="rounded-circle border border-4 border-white shadow-sm" width="90" height="90" style="object-fit: cover; background: #fff;">
                                    @else
                                        <div class="rounded-circle bg-white text-primary border border-4 border-white shadow-sm mx-auto d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 90px; height: 90px;">
                                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <h5 class="mb-1 fw-bold text-dark">{{ $jukir->nama_jukir }}</h5>
                                <p class="text-primary mb-3 fw-medium" style="font-size: 0.85rem; letter-spacing: 0.5px;">{{ $jukir->id_jukir ?? '-' }}</p>

                                <div class="bg-light rounded-3 p-3 mb-3 text-start border border-dashed border-secondary" style="font-size: 0.85rem;">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="bg-white p-1 rounded shadow-sm text-primary me-2 flex-shrink-0"><i class="ti tabler-map-pin"></i></div>
                                        <div class="overflow-hidden">
                                            <span class="d-block text-muted small fw-bold mb-0">Lokasi Parkir</span>
                                            <span class="text-dark fw-medium text-truncate d-block">{{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="bg-white p-1 rounded shadow-sm text-info me-2 flex-shrink-0"><i class="ti tabler-road"></i></div>
                                        <div class="overflow-hidden">
                                            <span class="d-block text-muted small fw-bold mb-0">Ruas Jalan</span>
                                            <span class="text-dark fw-medium text-truncate d-block">{{ $ruasJalanCard }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="bg-white p-1 rounded shadow-sm text-warning me-2 flex-shrink-0"><i class="ti tabler-user-star"></i></div>
                                        <div class="overflow-hidden">
                                            <span class="d-block text-muted small fw-bold mb-0">Korlap</span>
                                            <span class="text-dark fw-medium text-truncate d-block">{{ $korlapCard }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white p-1 rounded shadow-sm text-success me-2 flex-shrink-0"><i class="ti tabler-phone"></i></div>
                                        <div class="overflow-hidden">
                                            <span class="d-block text-muted small fw-bold mb-0">No. Handphone</span>
                                            <span class="text-dark fw-medium">{{ $jukir->phone_number ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-center gap-2 mt-auto" style="position: relative; z-index: 10;">
                                    <a href="{{ route('admin.jukirs.show', $jukir->id) }}" class="btn btn-sm btn-label-info rounded-pill px-3 flex-grow-1" data-bs-toggle="tooltip" title="Detail Profil">
                                        <i class="ti tabler-eye me-1"></i> Detail
                                    </a>
                                    <a href="{{ route('admin.jukirs.print-kta', $jukir->id) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="tooltip" title="Cetak KTA">
                                        <i class="ti tabler-printer"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3"
                                        onclick="openEditModal({{ $jukir->id }}, '{{ $jukir->id_jukir }}', '{{ addslashes($jukir->nama_jukir) }}', '{{ $jukir->tanggal_lahir }}', '{{ addslashes($jukir->alamat) }}', '{{ $jukir->parking_location_id }}', '{{ addslashes($jukir->no_ktp) }}', '{{ addslashes($jukir->phone_number) }}', {{ $jukir->is_active ? 'true' : 'false' }}, '{{ $jukir->image ? Storage::url($jukir->image) : '' }}', '{{ $jukir->image_ktp ? Storage::url($jukir->image_ktp) : '' }}', '{{ $jukir->kta_type }}', '{{ $jukir->kta_start_date }}')"
                                        data-bs-toggle="tooltip" title="Edit Data">
                                        <i class="ti tabler-pencil"></i>
                                    </button>
                                    <form id="deleteForm{{ $jukir->id }}" action="{{ route('admin.jukirs.destroy', $jukir->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding: 0;"
                                        onclick="confirmDelete({{ $jukir->id }}, '{{ addslashes($jukir->nama_jukir) }}')"
                                        data-bs-toggle="tooltip" title="Hapus Jukir">
                                        <i class="ti tabler-trash" style="font-size: 15px;"></i>
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

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $jukirs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- ==================== CREATE MODAL ==================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Juru Parkir Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createForm" action="{{ route('admin.jukirs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            {{-- Kolom Data Pribadi --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-user"></i> Data Pribadi</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Jukir</label>
                                    <input type="text" name="id_jukir" id="create_id_jukir" class="form-control fw-bold text-primary" value="{{ $nextIdJukir }}" required>
                                    <div id="create_id_jukir_feedback" class="invalid-feedback">ID Jukir ini sudah terdaftar! Gunakan ID lain.</div>
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
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="create_tanggal_lahir" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap (Opsional)"></textarea>
                                </div>

                                <hr>

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
                                            <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis crop 1:1 & kompres &lt; 50KB.</small>
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
                                            <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis kompres &lt; 50KB.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Data Penugasan --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-briefcase"></i> Data Penugasan</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pilih Ruas Jalan</label>
                                    <select id="create_road_section_id" class="form-select select2-road">
                                        <option value="">Pilih Ruas Jalan</option>
                                        @foreach($roadSections as $rs)
                                            <option value="{{ $rs->id }}">{{ $rs->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Titik Parkir <span class="text-danger">*</span></label>
                                    <select name="parking_location_id" id="create_parking_location_id" class="form-select select2-parking" required disabled>
                                        <option value="">Pilih Titik Parkir</option>
                                        @foreach($parkingLocations as $pl)
                                            @php
                                                $activeAgreement = $pl->agreements->first();
                                                $korlap = $activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user ? $activeAgreement->fieldCoordinator->user->name : '-';
                                                $zona = $pl->roadSection->zone ?? '-';
                                                $alamatPl = $pl->roadSection->name ?? '-';
                                            @endphp
                                            <option value="{{ $pl->id }}" data-road-section-id="{{ $pl->road_section_id }}" data-korlap="{{ $korlap }}" data-zona="{{ $zona }}" data-alamat="{{ $alamatPl }}">{{ $pl->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="parking-info-box mt-2 p-3 bg-light rounded border border-primary border-dashed" style="display: none; font-size: 0.85rem;">
                                        <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-user me-1"></i> Korlap</span><span class="fw-bold text-dark info-korlap">-</span></div>
                                        <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-map-pin me-1"></i> Zona</span><span class="fw-bold text-dark info-zona">-</span></div>
                                        <div class="d-flex"><span class="text-muted w-px-100"><i class="ti tabler-road me-1"></i> Ruas Jalan</span><span class="fw-bold text-dark info-alamat">-</span></div>
                                    </div>
                                </div>

                                {{-- KTA Fields (Hidden by default) --}}
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

                                <hr>

                                <div class="mb-3 form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active_create" value="1" checked>
                                    <label class="form-check-label fw-bold" for="is_active_create">Status Aktif</label>
                                </div>
                            </div>
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

    {{-- ==================== EDIT MODAL ==================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Data Juru Parkir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            {{-- Kolom Data Pribadi --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-user"></i> Data Pribadi</h6>

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
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat</label>
                                    <textarea name="alamat" id="edit_alamat" class="form-control" rows="2" placeholder="Alamat lengkap (Opsional)"></textarea>
                                </div>

                                <hr>

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
                                            <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Biarkan kosong jika tak diubah.</small>
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
                                            <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Biarkan kosong jika tak diubah.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Data Penugasan --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-briefcase"></i> Data Penugasan</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pilih Ruas Jalan</label>
                                    <select id="edit_road_section_id" class="form-select select2-road">
                                        <option value="">Pilih Ruas Jalan</option>
                                        @foreach($roadSections as $rs)
                                            <option value="{{ $rs->id }}">{{ $rs->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Titik Parkir <span class="text-danger">*</span></label>
                                    <select name="parking_location_id" id="edit_parking_location_id" class="form-select select2-parking" required disabled>
                                        <option value="">Pilih Titik Parkir</option>
                                        @foreach($parkingLocations as $pl)
                                            @php
                                                $activeAgreement = $pl->agreements->first();
                                                $korlap = $activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user ? $activeAgreement->fieldCoordinator->user->name : '-';
                                                $zona = $pl->roadSection->zone ?? '-';
                                                $alamatPl = $pl->roadSection->name ?? '-';
                                            @endphp
                                            <option value="{{ $pl->id }}" data-road-section-id="{{ $pl->road_section_id }}" data-korlap="{{ $korlap }}" data-zona="{{ $zona }}" data-alamat="{{ $alamatPl }}">{{ $pl->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="parking-info-box mt-2 p-3 bg-light rounded border border-primary border-dashed" style="display: none; font-size: 0.85rem;">
                                        <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-user me-1"></i> Korlap</span><span class="fw-bold text-dark info-korlap">-</span></div>
                                        <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-map-pin me-1"></i> Zona</span><span class="fw-bold text-dark info-zona">-</span></div>
                                        <div class="d-flex"><span class="text-muted w-px-100"><i class="ti tabler-road me-1"></i> Ruas Jalan</span><span class="fw-bold text-dark info-alamat">-</span></div>
                                    </div>
                                </div>

                                {{-- KTA Fields (Hidden by default) --}}
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

                                <hr>

                                <div class="mb-3 form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                    <label class="form-check-label fw-bold" for="edit_is_active">Status Aktif</label>
                                </div>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    <script type="module">
        $(document).ready(function() {
            // ==================== ID JUKIR VALIDATION ====================
            const existingIdJukirs = @json($jukirs->pluck('id_jukir')->filter()->values());
            const createIdInput = document.getElementById('create_id_jukir');
            const createForm = document.getElementById('createForm');

            createIdInput.addEventListener('input', function() {
                const val = this.value.trim();
                if (existingIdJukirs.includes(val)) {
                    this.classList.add('is-invalid', 'shake-animation');
                    setTimeout(() => {
                        this.classList.remove('shake-animation');
                    }, 300);
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            createForm.addEventListener('submit', function(e) {
                if (createIdInput.classList.contains('is-invalid')) {
                    e.preventDefault();
                    createIdInput.classList.add('shake-animation');
                    setTimeout(() => {
                        createIdInput.classList.remove('shake-animation');
                    }, 300);
                    return;
                }
                
                if (pendingCompressions > 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'Mohon Tunggu',
                        text: 'Sedang memproses kompresi gambar...',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Coba submit ulang setelah beberapa saat
                    let checkInterval = setInterval(() => {
                        if (pendingCompressions === 0) {
                            clearInterval(checkInterval);
                            createForm.submit();
                        }
                    }, 500);
                }
            });

            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    if (pendingCompressions > 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'info',
                            title: 'Mohon Tunggu',
                            text: 'Sedang memproses kompresi gambar...',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        let checkInterval = setInterval(() => {
                            if (pendingCompressions === 0) {
                                clearInterval(checkInterval);
                                editForm.submit();
                            }
                        }, 500);
                    }
                });
            }

            // ==================== SEARCH FILTER ====================
            // Pencarian sekarang dilakukan di sisi server via Form Submit (Enter).

            // ==================== SELECT2 INIT ====================
            $('#createModal .select2-parking').select2({
                dropdownParent: $('#createModal'),
                placeholder: 'Pilih Titik Parkir'
            });
            $('#createModal .select2-road').select2({
                dropdownParent: $('#createModal'),
                placeholder: 'Pilih Ruas Jalan'
            });
            $('#editModal .select2-parking').select2({
                dropdownParent: $('#editModal'),
                placeholder: 'Pilih Titik Parkir'
            });
            $('#editModal .select2-road').select2({
                dropdownParent: $('#editModal'),
                placeholder: 'Pilih Ruas Jalan'
            });

            // ==================== CASCADING DROPDOWNS ====================
            // Create Modal: Road Section -> Parking Location
            const createAllParkingOptions = $('#create_parking_location_id option').clone();
            $('#create_road_section_id').on('change', function() {
                let roadSectionId = $(this).val();
                let parkingSelect = $('#create_parking_location_id');
                parkingSelect.empty();
                parkingSelect.append('<option value="">Pilih Titik Parkir</option>');
                if (roadSectionId) {
                    createAllParkingOptions.each(function() {
                        if ($(this).val() == '' || $(this).data('road-section-id') == roadSectionId) {
                            parkingSelect.append($(this).clone());
                        }
                    });
                    parkingSelect.prop('disabled', false);
                } else {
                    parkingSelect.prop('disabled', true);
                }
                parkingSelect.trigger('change');
            });

            // Edit Modal: Road Section -> Parking Location
            window.editAllParkingOptions = $('#edit_parking_location_id option').clone();
            $('#edit_road_section_id').on('change', function(e, isInit) {
                let roadSectionId = $(this).val();
                let parkingSelect = $('#edit_parking_location_id');
                let currentValue = parkingSelect.val();
                parkingSelect.empty();
                parkingSelect.append('<option value="">Pilih Titik Parkir</option>');
                if (roadSectionId) {
                    window.editAllParkingOptions.each(function() {
                        if ($(this).val() == '' || $(this).data('road-section-id') == roadSectionId) {
                            parkingSelect.append($(this).clone());
                        }
                    });
                    parkingSelect.prop('disabled', false);
                } else {
                    parkingSelect.prop('disabled', true);
                }
                if (isInit) {
                    parkingSelect.val(currentValue);
                } else {
                    parkingSelect.val('');
                }
                parkingSelect.trigger('change');
            });

            // ==================== FLATPICKR INIT ====================
            const createDate = document.getElementById('create_kta_start_date');
            const editDate = document.getElementById('edit_kta_start_date');
            if (createDate) flatpickr(createDate, { dateFormat: "Y-m-d" });
            if (editDate) flatpickr(editDate, { dateFormat: "Y-m-d" });

            const createDob = document.getElementById('create_tanggal_lahir');
            const editDob = document.getElementById('edit_tanggal_lahir');
            if (createDob) flatpickr(createDob, { dateFormat: "Y-m-d" });
            if (editDob) flatpickr(editDob, { dateFormat: "Y-m-d" });
        });

        // ==================== TOOLTIPS ====================
        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // ==================== IMAGE COMPRESSION (Profile: 1:1 Crop) ====================
        let pendingCompressions = 0;

        async function processImage(inputEl, previewEl, containerEl, barEl) {
            let file = inputEl.files[0];
            if (!file) { previewEl.style.display = 'none'; return; }

            pendingCompressions++;
            containerEl.style.display = 'block';
            previewEl.style.display = 'block';
            previewEl.style.opacity = '0.3';
            barEl.style.width = '10%';
            
            try {
                // 1. Center Crop 1:1
                file = await new Promise((resolve) => {
                    const img = new Image();
                    img.src = URL.createObjectURL(file);
                    img.onload = () => {
                        const size = Math.min(img.width, img.height);
                        const canvas = document.createElement('canvas');
                        canvas.width = size;
                        canvas.height = size;
                        const ctx = canvas.getContext('2d');
                        const startX = (img.width - size) / 2;
                        const startY = (img.height - size) / 2;
                        ctx.drawImage(img, startX, startY, size, size, 0, 0, size, size);
                        canvas.toBlob((blob) => resolve(new File([blob], file.name, { type: file.type })), file.type, 1.0);
                    };
                });

                // 2. Compress
                const options = {
                    maxSizeMB: 50 / 1024, // 50KB
                    maxWidthOrHeight: 800,
                    useWebWorker: true,
                    onProgress: function (progress) {
                        const currentProgress = Math.max(20, progress);
                        barEl.style.width = currentProgress + '%';
                    }
                };

                const compressedFile = await imageCompression(file, options);
                const dt = new DataTransfer();
                dt.items.add(new File([compressedFile], file.name, { type: compressedFile.type }));
                inputEl.files = dt.files;
                
                previewEl.src = URL.createObjectURL(compressedFile);
                previewEl.style.opacity = '1';
                barEl.style.width = '100%';
                
                setTimeout(() => { containerEl.style.display = 'none'; barEl.style.width = '0%'; }, 1500);
            } catch (error) {
                console.error('Compression error:', error);
                containerEl.style.display = 'none';
                inputEl.value = '';
            } finally {
                pendingCompressions--;
            }
        }

        document.getElementById('image_input_create').addEventListener('change', function() {
            processImage(this, document.getElementById('image_preview_create'), document.getElementById('image_progress_container_create'), document.getElementById('image_progress_bar_create'));
        });
        document.getElementById('image_input_edit').addEventListener('change', function() {
            processImage(this, document.getElementById('image_preview_edit'), document.getElementById('image_progress_container_edit'), document.getElementById('image_progress_bar_edit'));
        });

        // ==================== IMAGE COMPRESSION (KTP: No Crop) ====================
        async function processImageKtp(inputEl, previewEl, containerEl, barEl) {
            let file = inputEl.files[0];
            if (!file) { previewEl.style.display = 'none'; return; }

            pendingCompressions++;
            containerEl.style.display = 'block';
            previewEl.style.display = 'block';
            previewEl.style.opacity = '0.3';
            barEl.style.width = '10%';

            try {
                const options = {
                    maxSizeMB: 50 / 1024, // 50KB
                    maxWidthOrHeight: 1200,
                    useWebWorker: true,
                    onProgress: function (progress) {
                        const currentProgress = Math.max(10, progress);
                        barEl.style.width = currentProgress + '%';
                    }
                };

                const compressedFile = await imageCompression(file, options);
                const dt = new DataTransfer();
                dt.items.add(new File([compressedFile], file.name, { type: compressedFile.type }));
                inputEl.files = dt.files;
                
                previewEl.src = URL.createObjectURL(compressedFile);
                previewEl.style.opacity = '1';
                barEl.style.width = '100%';
                
                setTimeout(() => { containerEl.style.display = 'none'; barEl.style.width = '0%'; }, 1500);
            } catch (error) {
                console.error('KTP Compression error:', error);
                containerEl.style.display = 'none';
                inputEl.value = '';
            } finally {
                pendingCompressions--;
            }
        }

        document.getElementById('image_ktp_input_create').addEventListener('change', function() {
            processImageKtp(this, document.getElementById('image_ktp_preview_create'), document.getElementById('image_ktp_progress_container_create'), document.getElementById('image_ktp_progress_bar_create'));
        });
        document.getElementById('image_ktp_input_edit').addEventListener('change', function() {
            processImageKtp(this, document.getElementById('image_ktp_preview_edit'), document.getElementById('image_ktp_progress_container_edit'), document.getElementById('image_ktp_progress_bar_edit'));
        });

        // ==================== PARKING INFO BOX & KTA TOGGLE ====================
        $('.select2-parking').on('change', function() {
            let val = $(this).val();
            if (val) {
                let option = $(this).find('option:selected');
                let infoBox = $(this).siblings('.parking-info-box');
                infoBox.find('.info-korlap').text(option.data('korlap'));
                infoBox.find('.info-zona').text(option.data('zona'));
                infoBox.find('.info-alamat').text(option.data('alamat'));
                infoBox.slideDown();

                if ($(this).attr('id') === 'edit_parking_location_id') {
                    $('#kta_section_edit').removeClass('d-none');
                } else {
                    $('#kta_section_create').removeClass('d-none');
                }
            } else {
                $(this).siblings('.parking-info-box').slideUp();

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

        // ==================== OPEN EDIT MODAL ====================
        window.openEditModal = function(id, idJukir, nama, tanggalLahir, alamat, parkingLocationId, noKtp, phoneNumber, isActive, imageUrl, imageKtpUrl, ktaType, ktaStartDate) {
            let form = document.getElementById('editForm');
            form.action = `/admin/jukirs/${id}`;

            document.getElementById('edit_id_jukir').value = idJukir;
            document.getElementById('edit_nama_jukir').value = nama;
            
            document.getElementById('edit_tanggal_lahir').value = tanggalLahir;
            if (document.getElementById('edit_tanggal_lahir')._flatpickr) {
                if (tanggalLahir) {
                    document.getElementById('edit_tanggal_lahir')._flatpickr.setDate(tanggalLahir);
                } else {
                    document.getElementById('edit_tanggal_lahir')._flatpickr.clear();
                }
            }

            document.getElementById('edit_alamat').value = alamat;
            document.getElementById('edit_no_ktp').value = noKtp;
            document.getElementById('edit_phone_number').value = phoneNumber;
            document.getElementById('edit_is_active').checked = isActive;

            // Populate road section and parking location
            if (parkingLocationId) {
                let option = window.editAllParkingOptions.filter(`[value="${parkingLocationId}"]`);
                if (option.length) {
                    let rsId = option.data('road-section-id');
                    $('#edit_road_section_id').val(rsId).trigger('change', [true]);
                    // Need a small delay for the cascading dropdown to populate
                    setTimeout(function() {
                        $('#edit_parking_location_id').val(parkingLocationId).trigger('change');
                    }, 100);
                }
            } else {
                $('#edit_road_section_id').val('').trigger('change', [true]);
                $('#edit_parking_location_id').val('').trigger('change');
            }

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

            // Toggle KTA visibility
            if (parkingLocationId) {
                $('#kta_section_edit').removeClass('d-none');
            } else {
                $('#kta_section_edit').addClass('d-none');
            }

            // Preview images
            let preview = document.getElementById('image_preview_edit');
            if(imageUrl) { preview.src = imageUrl; preview.style.display = 'block'; }
            else { preview.src = ''; preview.style.display = 'none'; }
            document.getElementById('image_input_edit').value = '';

            let previewKtp = document.getElementById('image_ktp_preview_edit');
            if(imageKtpUrl) { previewKtp.src = imageKtpUrl; previewKtp.style.display = 'block'; }
            else { previewKtp.src = ''; previewKtp.style.display = 'none'; }
            document.getElementById('image_ktp_input_edit').value = '';

            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        };

        // ==================== DELETE CONFIRMATION ====================
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
