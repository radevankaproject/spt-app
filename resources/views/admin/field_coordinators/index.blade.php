@extends('layouts.app')

@section('title', 'Manajemen Koordinator Lapangan')

@section('skeleton')
    @include('layouts.partials._skeleton-users-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .progress-container { display: none; height: 10px; margin-top: 10px; border-radius: 10px; }
        .progress-bar-custom { transition: width 0.2s ease; font-size: 10px; line-height: 10px; }
        .cursor-pointer { cursor: pointer; }
        .nav-tabs .nav-link.active { font-weight: 600; color: #696cff; border-bottom: 3px solid #696cff; }
    </style>
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Koordinator Lapangan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Koordinator Lapangan</li>
            </ol>
        </nav>
    </div>

    {{-- Error Handling (Validation) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>Oops! Terjadi kesalahan:</strong>
            <ul class="mb-0 ps-3 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Tabs Navigasi is_active --}}
    @php $currentTab = $tab ?? 'active'; @endphp
    <ul class="nav nav-tabs mb-4 border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'all' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.field-coordinators.index', ['tab' => 'all', 'search' => request('search')]) }}">
               Semua Korlap <span class="badge bg-{{ $currentTab == 'all' ? 'primary' : 'label-secondary' }} ms-1 rounded-pill">{{ $countAll ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'active' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.field-coordinators.index', ['tab' => 'active', 'search' => request('search')]) }}">
               Aktif <span class="badge bg-{{ $currentTab == 'active' ? 'success' : 'label-secondary' }} ms-1 rounded-pill">{{ $countActive ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'inactive' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.field-coordinators.index', ['tab' => 'inactive', 'search' => request('search')]) }}">
               Tidak Aktif <span class="badge bg-{{ $currentTab == 'inactive' ? 'danger' : 'label-secondary' }} ms-1 rounded-pill">{{ $countInactive ?? 0 }}</span>
            </a>
        </li>
    </ul>

    {{-- Daftar Koordinator --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3">
            <div class="card-title mb-0">
                <h5 class="mb-1">
                    @if($currentTab == 'active') Daftar Koordinator Aktif
                    @elseif($currentTab == 'inactive') Daftar Koordinator Nonaktif
                    @else Daftar Semua Koordinator
                    @endif
                </h5>
                <p class="text-muted mb-0">Total {{ $fieldCoordinators->total() }} data ditampilkan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                {{-- Form Pencarian --}}
                <form action="{{ route('admin.field-coordinators.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="tab" value="{{ $currentTab }}">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama/NIK..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="ri icon-base ri-search-line"></i></button>
                    </div>
                </form>
                @if(Auth::user()->role !== 'leader')
                <button type="button" class="btn btn-primary" id="btn-add-korlap" data-bs-toggle="modal" data-bs-target="#korlapModal">
                    <i class="ri icon-base ri-add-line me-1"></i> Tambah Korlap
                </button>
                @endif
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Koordinator</th>
                            <th>Kontak</th>
                            <th>NIK</th>
                            <th>Perjanjian Aktif</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($fieldCoordinators as $coordinator)
                            @php
                                $uName = $coordinator->user->name ?? 'N/A';
                                $uAvatar = ($coordinator->user && $coordinator->user->img)
                                    ? asset('storage/' . $coordinator->user->img)
                                    : "https://ui-avatars.com/api/?name=".urlencode($uName)."&background=auto&color=fff&rounded=true&size=38";
                                $ktpImg = $coordinator->id_card_img ? asset('storage/'.$coordinator->id_card_img) : asset('assets/img/ktp.png');
                                $isActive = $coordinator->user ? $coordinator->user->is_active : false;
                            @endphp
                            <tr class="{{ !$isActive ? 'bg-lighter' : '' }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle {{ !$isActive ? 'opacity-50' : '' }}" style="object-fit: cover;">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div>
                                                <span class="fw-medium {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $uName }}</span>
                                                @if(!$isActive)
                                                    <span class="badge bg-label-danger ms-1" style="font-size: 0.65rem;">Nonaktif</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ '@' . ($coordinator->user->username ?? 'N/A') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $coordinator->user->email ?? 'N/A' }}</span>
                                    <small class="text-muted">{{ $coordinator->phone_number ?? 'N/A' }}</small>
                                </td>
                                <td><span class="fw-medium {{ !$isActive ? 'text-muted' : '' }}">{{ $coordinator->id_card_number }}</span></td>
                                <td>
                                    @if ($coordinator->agreements->isNotEmpty())
                                        @php $activeAgreement = $coordinator->agreements->first(); @endphp
                                        <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}" class="badge bg-label-primary shadow-sm">
                                            {{ $activeAgreement->agreement_number }}
                                        </a>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Ada PKS</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill" href="{{ route('admin.field-coordinators.show', $coordinator->id) }}" data-bs-toggle="tooltip" title="Lihat Profil">
                                            <i class="ri icon-base ri-eye-line icon-20px"></i>
                                        </a>

                                        @if(Auth::user()->role !== 'leader')
                                        <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill btn-edit-korlap"
                                            data-id="{{ $coordinator->id }}"
                                            data-name="{{ $uName }}"
                                            data-ktp="{{ $coordinator->id_card_number }}"
                                            data-phone="{{ $coordinator->phone_number }}"
                                            data-address="{{ $coordinator->address }}"
                                            data-position="{{ $coordinator->position }}"
                                            data-avatar="{{ $uAvatar }}"
                                            data-ktpimg="{{ $ktpImg }}"
                                            data-bs-toggle="modal" data-bs-target="#korlapModal"
                                            data-bs-toggle="tooltip" title="Edit Koordinator">
                                            <i class="ri icon-base ri-pencil-line icon-20px"></i>
                                        </button>
                                        @endif

                                        @if(Auth::user()->role === 'admin')
                                        <button type="button" class="btn btn-sm btn-icon btn-text-warning rounded-pill btn-edit-login"
                                            data-id="{{ $coordinator->id }}"
                                            data-username="{{ $coordinator->user->username ?? '' }}"
                                            data-email="{{ $coordinator->user->email ?? '' }}"
                                            data-phone="{{ $coordinator->phone_number ?? '' }}"
                                            data-bs-toggle="modal" data-bs-target="#editLoginModal"
                                            data-bs-toggle="tooltip" title="Edit Data Login">
                                            <i class="ri icon-base ri-key-line icon-20px"></i>
                                        </button>
                                        @endif

                                        {{-- ✅ LOGIKA ACTION SUPER CERDAS (SESUAI REQUEST) --}}
                                        @if(Auth::user()->role !== 'leader')
                                        @if($isActive)
                                            {{-- 1. POSISI DI TAB AKTIF --}}
                                            @if($coordinator->agreements->isEmpty())
                                                {{-- Jika tidak punya PKS Aktif, baru boleh dinonaktifkan --}}
                                                <form action="{{ route('admin.field-coordinators.toggle-status', $coordinator->id) }}" method="POST" class="d-inline form-toggle">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-icon btn-text-warning rounded-pill btn-toggle" data-bs-toggle="tooltip" title="Nonaktifkan Akses">
                                                        <i class="ri icon-base ri-information-off-line icon-20px"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            {{-- Hapus (Delete) diharamkan tampil di Tab Aktif --}}

                                        @else
                                            {{-- 2. POSISI DI TAB TIDAK AKTIF --}}
                                            {{-- Tombol Aktifkan Kembali --}}
                                            <form action="{{ route('admin.field-coordinators.toggle-status', $coordinator->id) }}" method="POST" class="d-inline form-toggle">
                                                @csrf @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-icon btn-text-success rounded-pill btn-toggle" data-bs-toggle="tooltip" title="Aktifkan Kembali">
                                                    <i class="ri icon-base ri-refresh-line icon-20px"></i>
                                                </button>
                                            </form>

                                            {{-- Tombol Hapus (Dilindungi oleh Controller Smart Delete) --}}
                                            <form action="{{ route('admin.field-coordinators.destroy', $coordinator->id) }}" method="POST" class="form-delete d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus Permanen">
                                                    <i class="ri icon-base ri-delete-bin-line icon-20px"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Tidak ada data koordinator ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{-- ✅ Paginate dengan membawa variabel Search dan Tab --}}
                {{ $fieldCoordinators->appends(['search' => request('search'), 'tab' => $currentTab])->links() }}
            </div>
        </div>
    </div>

    {{-- ✅ MODAL SINGLE CRUD (CREATE & EDIT) --}}
    <div class="modal fade" id="korlapModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Tambah Koordinator Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formKorlap" method="POST" action="{{ route('admin.field-coordinators.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="method-container"></div>

                    <div class="modal-body px-4 py-4">
                        <div class="row g-5">
                            {{-- KOLOM KIRI: Data Text --}}
                            <div class="col-lg-8">
                                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ri ri-user-settings-line me-1"></i> Informasi Pribadi</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" required />
                                            <label for="name">Nama Lengkap Sesuai KTP</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" placeholder="16 Digit" maxlength="16" required />
                                            <label for="id_card_number">Nomor KTP (NIK)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="08..." required />
                                            <label for="phone_number">Nomor Telepon/HP</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="address" name="address" placeholder="Alamat lengkap" style="height: 100px;" required></textarea>
                                            <label for="address">Alamat Lengkap</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="position" name="position" value="Mitra Kerjasama Pengelolaan Perparkiran" required />
                                            <label for="position">Posisi / Jabatan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: Upload Foto Cerdas --}}
                            <div class="col-lg-4 border-start">
                                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ri ri-image-add-line me-1"></i> Dokumen Foto</h6>

                                {{-- Avatar Upload --}}
                                <div class="text-center mb-4">
                                    <small class="d-block text-muted mb-2">Foto Profil (Otomatis Kompres > 50KB)</small>
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" id="avatar-preview" class="rounded-circle shadow-sm mb-2" style="width:100px; height:100px; object-fit:cover;">
                                    <div class="d-block mt-2">
                                        <label for="img-upload" class="btn btn-sm btn-outline-primary rounded-pill cursor-pointer">
                                            <i class="ri ri-upload-2-line me-1"></i> Pilih Foto
                                            <input type="file" id="img-upload" name="img" hidden accept="image/png, image/jpeg" />
                                        </label>
                                    </div>
                                    <div class="progress progress-container w-75 mx-auto" id="avatar-progress-wrap">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary progress-bar-custom" id="avatar-progress" role="progressbar" style="width: 0%">0%</div>
                                    </div>
                                    <small id="img-error" class="text-danger d-block mt-1"></small>
                                </div>

                                {{-- KTP Upload --}}
                                <div class="text-center">
                                    <small class="d-block text-muted mb-2">Foto KTP (Otomatis Kompres > 80KB)</small>
                                    <img src="{{ asset('assets/img/ktp.png') }}" id="idcard-preview" class="rounded-3 shadow-sm mb-2" style="height:100px; object-fit:cover; max-width: 100%;">
                                    <div class="d-block mt-2">
                                        <label for="idcard-upload" class="btn btn-sm btn-outline-primary rounded-pill cursor-pointer">
                                            <i class="ri ri-upload-2-line me-1"></i> Pilih KTP
                                            <input type="file" id="idcard-upload" name="id_card_img" hidden accept="image/png, image/jpeg" />
                                        </label>
                                    </div>
                                    <div class="progress progress-container w-75 mx-auto" id="ktp-progress-wrap">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success progress-bar-custom" id="ktp-progress" role="progressbar" style="width: 0%">0%</div>
                                    </div>
                                    <small id="idcard-error" class="text-danger d-block mt-1"></small>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitModal">
                            <i class="ri ri-save-3-line me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Data Login (Khusus Admin) --}}
    @if(Auth::user()->role === 'admin')
    <div class="modal fade" id="editLoginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="ri ri-shield-keyhole-line me-2"></i>Edit Data Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLoginModal" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="alert alert-warning small mb-4">
                            <i class="ri ri-error-warning-line me-1"></i> Data di bawah ini digunakan oleh koordinator untuk masuk ke dalam sistem aplikasi.
                        </div>

                        <div class="form-floating form-floating-outline mb-3">
                            <input type="text" class="form-control" id="login_username" name="username" required />
                            <label for="login_username">Username</label>
                        </div>

                        <div class="form-floating form-floating-outline mb-3">
                            <input type="email" class="form-control" id="login_email" name="email" required />
                            <label for="login_email">Alamat Email</label>
                        </div>

                        <div class="form-floating form-floating-outline mb-3">
                            <input type="text" class="form-control" id="login_phone" name="phone_number" required />
                            <label for="login_phone">Nomor Telepon/HP</label>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="fw-bold mb-3">Ubah Password (Opsional)</h6>
                        <p class="small text-muted mb-3">Kosongkan jika tidak ingin mereset password.</p>

                        <div class="form-password-toggle mb-3">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <label for="password">Password Baru</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="ri icon-base ri-eye-off-line"></i></span>
                            </div>
                        </div>

                        <div class="form-password-toggle mb-3">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="ri icon-base ri-eye-off-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="ri ri-save-3-line me-1"></i> Simpan Akses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- 1. FILTER INPUT ANGKA ---
            const idCardInput = document.getElementById('id_card_number');
            const phoneInput = document.getElementById('phone_number');
            if(idCardInput) idCardInput.addEventListener('input', e => e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 16));
            if(phoneInput) phoneInput.addEventListener('input', e => e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 15));

            // --- 2. LOGIKA MODAL SINGLE CRUD ---
            const modalTitle = document.getElementById('modalTitle');
            const formKorlap = document.getElementById('formKorlap');
            const methodContainer = document.getElementById('method-container');
            const btnSubmitModal = document.getElementById('btnSubmitModal');

            const defaultAvatar = "{{ asset('assets/img/avatars/1.png') }}";
            const defaultKtp = "{{ asset('assets/img/ktp.png') }}";

            // --- 2a. LOGIKA MODAL TAMBAH KORLAP ---
            const btnAddKorlap = document.getElementById('btn-add-korlap');
            if(btnAddKorlap) {
                btnAddKorlap.addEventListener('click', function() {
                    modalTitle.innerText = 'Tambah Koordinator Baru';
                    formKorlap.action = "{{ route('admin.field-coordinators.store') }}";
                    methodContainer.innerHTML = '';
                    btnSubmitModal.innerHTML = '<i class="ri-save-3-line me-1"></i> Simpan Data';
                    
                    document.getElementById('name').value = '';
                    document.getElementById('id_card_number').value = '';
                    document.getElementById('phone_number').value = '';
                    document.getElementById('address').value = '';
                    document.getElementById('position').value = 'Mitra Kerjasama Pengelolaan Perparkiran';

                    document.getElementById('avatar-preview').src = defaultAvatar;
                    document.getElementById('idcard-preview').src = defaultKtp;

                    document.getElementById('img-upload').required = false;
                    document.getElementById('idcard-upload').required = false;
                });
            }

            // --- 2b. LOGIKA MODAL EDIT KORLAP ---
            document.querySelectorAll('.btn-edit-korlap').forEach(btn => {
                btn.addEventListener('click', function() {
                    modalTitle.innerText = 'Edit Koordinator: ' + this.dataset.name;
                    formKorlap.action = `/admin/field-coordinators/${this.dataset.id}`;
                    methodContainer.innerHTML = '@method("PATCH")';
                    btnSubmitModal.innerHTML = '<i class="ri-save-3-line me-1"></i> Simpan Perubahan';

                    document.getElementById('name').value = this.dataset.name;
                    document.getElementById('id_card_number').value = this.dataset.ktp;
                    document.getElementById('phone_number').value = this.dataset.phone;
                    document.getElementById('address').value = this.dataset.address;
                    document.getElementById('position').value = this.dataset.position;

                    document.getElementById('avatar-preview').src = this.dataset.avatar;
                    document.getElementById('idcard-preview').src = this.dataset.ktpimg;

                    document.getElementById('img-upload').required = false;
                    document.getElementById('idcard-upload').required = false;
                });
            });

            // --- 2b. LOGIKA MODAL EDIT LOGIN ---
            document.querySelectorAll('.btn-edit-login').forEach(btn => {
                btn.addEventListener('click', function() {
                    const formLogin = document.getElementById('formLoginModal');
                    if(formLogin) {
                        formLogin.action = `/admin/field-coordinators/${this.dataset.id}/update-login`;
                        document.getElementById('login_username').value = this.dataset.username;
                        document.getElementById('login_email').value = this.dataset.email;
                        document.getElementById('login_phone').value = this.dataset.phone;
                        document.getElementById('password').value = '';
                        document.getElementById('password_confirmation').value = '';
                    }
                });
            });

            // --- 3. LOGIKA CROP 1:1 & KOMPRESI GAMBAR ---
            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            async function handleImageUpload(inputId, previewId, errorId, defaultSrc, maxKbSize, progressWrapId, progressBarId, isSquareCrop = false) {
                const fileInput = document.getElementById(inputId);
                const imagePreview = document.getElementById(previewId);
                const errorDiv = document.getElementById(errorId);
                const progressWrap = document.getElementById(progressWrapId);
                const progressBar = document.getElementById(progressBarId);

                fileInput.addEventListener('change', async (e) => {
                    let imageFile = e.target.files[0];
                    if (!imageFile) {
                        imagePreview.src = defaultSrc;
                        progressWrap.style.display = 'none';
                        return;
                    }

                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = '❌ Hanya file JPG atau PNG.';
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                        progressWrap.style.display = 'none';
                        return;
                    }

                    const originalSizeStr = formatBytes(imageFile.size);
                    progressWrap.style.display = 'block';
                    progressBar.style.width = '10%';
                    progressBar.innerText = 'Memproses...';
                    btnSubmitModal.disabled = true;

                    try {
                        if (isSquareCrop) {
                            imageFile = await new Promise((resolve) => {
                                const img = new Image();
                                img.src = URL.createObjectURL(imageFile);
                                img.onload = () => {
                                    const size = Math.min(img.width, img.height);
                                    const canvas = document.createElement('canvas');
                                    canvas.width = size;
                                    canvas.height = size;
                                    const ctx = canvas.getContext('2d');
                                    const startX = (img.width - size) / 2;
                                    const startY = (img.height - size) / 2;
                                    ctx.drawImage(img, startX, startY, size, size, 0, 0, size, size);
                                    canvas.toBlob((blob) => {
                                        resolve(new File([blob], imageFile.name, { type: imageFile.type }));
                                    }, imageFile.type, 1.0);
                                };
                            });
                        }

                        const fileSizeKB = imageFile.size / 1024;
                        if(fileSizeKB <= maxKbSize) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(imageFile);
                            fileInput.files = dataTransfer.files;
                            imagePreview.src = URL.createObjectURL(imageFile);

                            const finalSizeStr = formatBytes(imageFile.size);
                            progressBar.style.width = '100%';
                            progressBar.innerText = `Selesai! (${finalSizeStr})`;
                            setTimeout(() => { progressWrap.style.display = 'none'; }, 2500);
                            return;
                        }

                        const options = {
                            maxSizeMB: maxKbSize / 1024,
                            maxWidthOrHeight: 1200,
                            useWebWorker: true,
                            onProgress: function (progress) {
                                const currentProgress = Math.max(10, progress);
                                progressBar.style.width = currentProgress + '%';
                                progressBar.innerText = currentProgress + '%';
                            }
                        };

                        const compressedFile = await imageCompression(imageFile, options);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        fileInput.files = dataTransfer.files;

                        imagePreview.src = URL.createObjectURL(compressedFile);

                        const compressedSizeStr = formatBytes(compressedFile.size);
                        progressBar.innerText = `Selesai! (${originalSizeStr} ➔ ${compressedSizeStr})`;
                        setTimeout(() => { progressWrap.style.display = 'none'; }, 3000);

                    } catch (error) {
                        errorDiv.textContent = "Gagal memproses gambar.";
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                        progressWrap.style.display = 'none';
                    } finally {
                        btnSubmitModal.disabled = false;
                    }
                });
            }

            handleImageUpload('img-upload', 'avatar-preview', 'img-error', defaultAvatar, 50, 'avatar-progress-wrap', 'avatar-progress', true);
            handleImageUpload('idcard-upload', 'idcard-preview', 'idcard-error', defaultKtp, 80, 'ktp-progress-wrap', 'ktp-progress', false);

            // --- 4. SWEETALERT HAPUS & TOGGLE ---
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data koordinator (yang belum ada riwayat) ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-outline-secondary waves-effect'
                        },
                        buttonsStyling: false,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    })
                });
            });

            document.querySelectorAll('.btn-toggle').forEach(btn => {
                btn.addEventListener('click', function(event) {
                    const form = this.closest('form');
                    const isDeactivating = this.classList.contains('btn-text-warning');

                    Swal.fire({
                        title: isDeactivating ? 'Nonaktifkan Koordinator?' : 'Aktifkan Kembali?',
                        text: isDeactivating
                            ? "Akses login koordinator ini akan dicabut."
                            : "Koordinator akan diaktifkan dan dapat login kembali.",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: isDeactivating ? 'btn btn-warning me-3 waves-effect waves-light' : 'btn btn-success me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-outline-secondary waves-effect'
                        },
                        buttonsStyling: false,
                        confirmButtonText: isDeactivating ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    })
                });
            });

            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(t => new bootstrap.Tooltip(t));
        });
    </script>
@endpush
