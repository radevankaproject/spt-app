@extends('layouts.app')

@section('title', 'Manajemen Pimpinan (Leader)')

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
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Pimpinan (Leader)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Leaders</li>
            </ol>
        </nav>
    </div>

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

    {{-- Tabs Navigasi is_active --}}
    @php $currentTab = $tab ?? 'active'; @endphp
    <ul class="nav nav-tabs mb-4 border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'all' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.leaders.index', ['tab' => 'all', 'search' => request('search')]) }}">
               Semua Leader <span class="badge bg-{{ $currentTab == 'all' ? 'primary' : 'label-secondary' }} ms-1 rounded-pill">{{ $countAll ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'active' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.leaders.index', ['tab' => 'active', 'search' => request('search')]) }}">
               Aktif Menjabat <span class="badge bg-{{ $currentTab == 'active' ? 'success' : 'label-secondary' }} ms-1 rounded-pill">{{ $countActive ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $currentTab == 'inactive' ? 'active shadow-sm bg-white' : '' }}"
               href="{{ route('admin.leaders.index', ['tab' => 'inactive', 'search' => request('search')]) }}">
               Purna Tugas <span class="badge bg-{{ $currentTab == 'inactive' ? 'danger' : 'label-secondary' }} ms-1 rounded-pill">{{ $countInactive ?? 0 }}</span>
            </a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3">
            <div class="card-title mb-0">
                <h5 class="mb-1">
                    @if($currentTab == 'active') Daftar Pimpinan Aktif
                    @elseif($currentTab == 'inactive') Daftar Pimpinan Purna Tugas (Nonaktif)
                    @else Daftar Semua Pimpinan
                    @endif
                </h5>
                <p class="text-muted mb-0">Total {{ $leaders->total() }} data ditampilkan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <form action="{{ route('admin.leaders.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="tab" value="{{ $currentTab }}">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari Nama/NIP..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="ri ri-search-line icon-20px"></i></button>
                    </div>
                </form>
                @if(Auth::user()->role !== 'leader')
                {{-- Tombol Tambah --}}
                <button type="button" class="btn btn-primary" id="btn-add-leader" data-bs-toggle="modal" data-bs-target="#leaderModal">
                    <i class="ri ri-add-line me-1"></i> Tambah Pimpinan
                </button>
                @endif
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Pimpinan</th>
                            <th>Kontak (Email)</th>
                            <th>NIP</th>
                            <th>Masa Jabatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($leaders as $leader)
                            @php
                                $uName = $leader->user->name ?? 'N/A';
                                $uAvatar = ($leader->user && $leader->user->img)
                                    ? asset('storage/' . $leader->user->img)
                                    : "https://ui-avatars.com/api/?name=".urlencode($uName)."&background=auto&color=fff&rounded=true&size=38";
                                $isActive = $leader->user ? $leader->user->is_active : false;

                                $startDate = $leader->start_date ? \Carbon\Carbon::parse($leader->start_date)->format('d/m/Y') : '-';
                                $endDate = $leader->end_date ? \Carbon\Carbon::parse($leader->end_date)->format('d/m/Y') : 'Sekarang';
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
                                                @if($isActive)
                                                    <span class="badge bg-label-success ms-1" style="font-size: 0.65rem;">Aktif</span>
                                                @else
                                                    <span class="badge bg-label-danger ms-1" style="font-size: 0.65rem;">Nonaktif</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ '@' . ($leader->user->username ?? 'N/A') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $leader->user->email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{-- ✅ PEMANGGILAN HELPER PHP UNTUK NIP --}}
                                    <span class="fw-medium {{ !$isActive ? 'text-muted' : '' }}">{{ formatNip($leader->employee_number) }}</span>
                                </td>
                                <td>
                                    <small class="d-block text-muted">Mulai: {{ $startDate }}</small>
                                    <small class="d-block {{ $leader->end_date ? 'text-danger' : 'text-success' }}">Akhir: {{ $endDate }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill" href="{{ route('admin.leaders.show', $leader->id) }}" data-bs-toggle="tooltip" title="Lihat Profil">
                                            <i class="ri icon-base ri-eye-line icon-20px"></i>
                                        </a>
                                        @if(Auth::user()->role !== 'leader')
                                        {{-- Tombol Edit --}}
                                        <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill btn-edit-leader"
                                            data-id="{{ $leader->id }}"
                                            data-name="{{ $uName }}"
                                            data-username="{{ $leader->user->username }}"
                                            data-email="{{ $leader->user->email }}"
                                            data-nip="{{ $leader->employee_number }}"
                                            data-startdate="{{ $leader->start_date ? \Carbon\Carbon::parse($leader->start_date)->format('Y-m-d') : '' }}"
                                            data-enddate="{{ $leader->end_date ? \Carbon\Carbon::parse($leader->end_date)->format('Y-m-d') : '' }}"
                                            data-avatar="{{ $uAvatar }}"
                                            data-bs-toggle="modal" data-bs-target="#leaderModal"
                                            data-bs-toggle="tooltip" title="Edit Pimpinan">
                                            <i class="ri ri-pencil-line icon-20px"></i>
                                        </button>
                                        @endif

                                        @if(Auth::user()->role !== 'leader')
                                        {{-- SMART ACTION: Toggle Aktif/Nonaktif & Hapus Permanen --}}
                                        @if($isActive)
                                            <button type="button" class="btn btn-sm btn-icon btn-text-warning rounded-pill btn-nonaktif"
                                                data-id="{{ $leader->id }}" data-name="{{ $uName }}" data-bs-toggle="modal" data-bs-target="#modalNonaktif" data-bs-toggle="tooltip" title="Purna Tugas (Nonaktif)">
                                                                                                    <i class="ri ri-information-off-line icon-20px"></i>

                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-icon btn-text-success rounded-pill btn-aktif"
                                                data-id="{{ $leader->id }}" data-name="{{ $uName }}" data-bs-toggle="modal" data-bs-target="#modalAktif" data-bs-toggle="tooltip" title="Jabat Kembali (Aktif)">
                                                <i class="ri ri-refresh-line icon-20px"></i>
                                            </button>

                                            @if($leader->agreements->isEmpty())
                                                <form action="{{ route('admin.leaders.destroy', $leader->id) }}" method="POST" class="form-delete d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus Permanen">
                                                        <i class="ri ri-delete-bin-line icon-20px"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Tidak ada data pimpinan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{ $leaders->appends(['search' => request('search'), 'tab' => $currentTab])->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL SINGLE CRUD (CREATE & EDIT) --}}
    <div class="modal fade" id="leaderModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Tambah Pimpinan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLeader" method="POST" action="{{ route('admin.leaders.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="method-container"></div>

                    <div class="modal-body px-4 py-4">
                        <div class="row g-5">
                            {{-- KOLOM KIRI --}}
                            <div class="col-lg-8">
                                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ri ri-user-settings-line me-1"></i> Informasi Akun Login</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" required />
                                            <label for="name">Nama Lengkap</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="form-floating form-floating-outline w-75">
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required />
                                                <label for="username">Username</label>
                                            </div>
                                            <button class="btn btn-outline-primary w-25 px-1" type="button" id="generate-username" data-bs-toggle="tooltip" title="Generate Otomatis"><i class="ri ri-loop-left-line"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required />
                                            <label for="email">Alamat Email</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="password" id="password" class="form-control" name="password" placeholder="********" required />
                                                    <label for="password" id="label-password">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="********" required />
                                                    <label for="password_confirmation">Ulangi Password</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ri ri-briefcase-line me-1"></i> Detail Pimpinan</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            {{-- ✅ INPUT TAMPILAN BER-SPASI (Tidak dikirim ke DB karena tidak punya atribut 'name') --}}
                                            <input type="text" class="form-control" id="employee_number_display" placeholder="Contoh: 19900815 201601 1 005" maxlength="21" required autocomplete="off" />
                                            {{-- ✅ INPUT HIDDEN UNTUK DATABASE (18 Digit Angka Murni) --}}
                                            <input type="hidden" id="employee_number" name="employee_number" />

                                            <label for="employee_number_display">NIP (Nomor Induk Pegawai)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="start_date" name="start_date" required />
                                            <label for="start_date">Mulai Menjabat</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="end_date" name="end_date" />
                                            <label for="end_date">Akhir Menjabat (Opsional)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: Upload Foto --}}
                            <div class="col-lg-4 border-start">
                                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ri ri-image-add-line me-1"></i> Foto Profil</h6>
                                <div class="text-center mb-4 pt-4">
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" id="avatar-preview" class="rounded-circle shadow-sm mb-3" style="width:140px; height:140px; object-fit:cover;">
                                    <div class="d-block mt-2">
                                        <label for="img-upload" class="btn btn-primary rounded-pill cursor-pointer">
                                            <i class="ri ri-upload-2-line me-1"></i> Pilih Foto
                                            <input type="file" id="img-upload" name="img" hidden accept="image/png, image/jpeg" />
                                        </label>
                                    </div>
                                    {{-- Progress Bar --}}
                                    <div class="progress progress-container w-100 mx-auto mt-3" id="avatar-progress-wrap">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary progress-bar-custom" id="avatar-progress" role="progressbar" style="width: 0%">0%</div>
                                    </div>
                                    <small id="img-error" class="text-danger d-block mt-2"></small>
                                    <p class="text-muted mt-3 mb-0" style="font-size: 0.8rem;">Otomatis Center-Crop 1:1<br>Kompresi Cerdas Maks. 50KB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitModal">
                            <i class="ri ri-save-3-line me-1"></i> Simpan Pimpinan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL NONAKTIFKAN (PURNA TUGAS) --}}
    <div class="modal fade" id="modalNonaktif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-warning"><i class="ri-power-line me-1"></i> Purna Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNonaktif" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body px-4 py-4">
                        <p class="text-dark mb-3">Tentukan tanggal berakhirnya masa jabatan untuk: <strong id="namaNonaktif"></strong></p>
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="end_date" name="end_date" required />
                            <label for="end_date">Tanggal Akhir Menjabat</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Nonaktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL AKTIFKAN (JABAT KEMBALI) --}}
    <div class="modal fade" id="modalAktif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success bg-opacity-10 border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-success"><i class="ri ri-refresh-line me-1"></i> Jabat Kembali</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAktif" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body px-4 py-4">
                        <p class="text-dark mb-3">Tentukan status dan tanggal mulai menjabat untuk: <strong id="namaAktif"></strong></p>

                        <div class="form-floating form-floating-outline mb-3">
                            <select class="form-select" id="status_jabatan" name="status_jabatan" required>
                                <option value="tetap">Pimpinan Definitif (Tetap)</option>
                                <option value="plt">Pelaksana Tugas (Plt)</option>
                                <option value="plh">Pelaksana Harian (Plh)</option>
                            </select>
                            <label for="status_jabatan">Status Jabatan Baru</label>
                        </div>

                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="start_date" name="start_date" required />
                            <label for="start_date">Tanggal Mulai Menjabat</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white">Aktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- 1. FILTER INPUT & GENERATE USERNAME ---
            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');

            if(document.getElementById('generate-username')){
                document.getElementById('generate-username').addEventListener('click', () => {
                    usernameInput.value = nameInput.value.trim().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
                });
            }

            if(usernameInput){
                usernameInput.addEventListener('input', e => e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '').replace(/[^a-z0-9_]/g, ''));
            }

            document.querySelectorAll('input[type="password"]').forEach(input => {
                input.addEventListener('input', e => e.target.value = e.target.value.replace(/\s/g, ''));
            });

            // ✅ LOGIKA FORMAT NIP DI JAVASCRIPT
            function formatNipJS(value) {
                if(!value) return { raw: '', formatted: '' };
                let raw = value.replace(/[^0-9]/g, '').substring(0, 18);
                let formatted = '';
                if (raw.length > 0) formatted += raw.substring(0, 8);
                if (raw.length > 8) formatted += ' ' + raw.substring(8, 14);
                if (raw.length > 14) formatted += ' ' + raw.substring(14, 15);
                if (raw.length > 15) formatted += ' ' + raw.substring(15, 18);
                return { raw, formatted };
            }

            const nipDisplay = document.getElementById('employee_number_display');
            const nipHidden = document.getElementById('employee_number');

            if (nipDisplay) {
                nipDisplay.addEventListener('input', function(e) {
                    let result = formatNipJS(this.value);
                    this.value = result.formatted;
                    nipHidden.value = result.raw;
                });
            }

            // --- 2. LOGIKA MODAL SINGLE CRUD ---
            const modalTitle = document.getElementById('modalTitle');
            const formLeader = document.getElementById('formLeader');
            const methodContainer = document.getElementById('method-container');
            const btnSubmitModal = document.getElementById('btnSubmitModal');
            const defaultAvatar = "{{ asset('assets/img/avatars/1.png') }}";

            // BUKA MODAL TAMBAH
            const btnAddLeader = document.getElementById('btn-add-leader');
            if(btnAddLeader){
                btnAddLeader.addEventListener('click', function() {
                    modalTitle.innerText = 'Tambah Pimpinan Baru';
                    formLeader.action = "{{ route('admin.leaders.store') }}";
                    methodContainer.innerHTML = '';
                    btnSubmitModal.innerHTML = '<i class="ri-save-3-line me-1"></i> Simpan Pimpinan';
                    formLeader.reset();
                    document.getElementById('avatar-preview').src = defaultAvatar;

                    nipDisplay.value = '';
                    nipHidden.value = '';

                    document.getElementById('password').required = true;
                    document.getElementById('password_confirmation').required = true;
                    document.getElementById('label-password').innerText = 'Password';
                    document.getElementById('password').placeholder = '********';
                });
            }

            // BUKA MODAL EDIT
            document.querySelectorAll('.btn-edit-leader').forEach(btn => {
                btn.addEventListener('click', function() {
                    modalTitle.innerText = 'Edit Pimpinan: ' + this.dataset.name;

                    // ✅ RUTE AMAN ANTI-ERROR METHOD NOT ALLOWED
                    let urlAction = "{{ route('admin.leaders.update', ':id') }}";
                    formLeader.action = urlAction.replace(':id', this.dataset.id);

                    methodContainer.innerHTML = '@method("PATCH")';
                    btnSubmitModal.innerHTML = '<i class="ri-save-3-line me-1"></i> Simpan Perubahan';

                    document.getElementById('name').value = this.dataset.name;
                    document.getElementById('username').value = this.dataset.username;
                    document.getElementById('email').value = this.dataset.email;
                    document.getElementById('start_date').value = this.dataset.startdate;
                    document.getElementById('end_date').value = this.dataset.enddate;

                    let nipResult = formatNipJS(this.dataset.nip);
                    nipHidden.value = nipResult.raw;
                    nipDisplay.value = nipResult.formatted;

                    document.getElementById('avatar-preview').src = this.dataset.avatar;

                    document.getElementById('password').value = '';
                    document.getElementById('password_confirmation').value = '';
                    document.getElementById('password').required = false;
                    document.getElementById('password_confirmation').required = false;
                    document.getElementById('label-password').innerText = 'Kosongkan jika tidak diubah';
                    document.getElementById('password').placeholder = '';
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

            const fileInput = document.getElementById('img-upload');
            const imagePreview = document.getElementById('avatar-preview');
            const errorDiv = document.getElementById('img-error');
            const progressWrap = document.getElementById('avatar-progress-wrap');
            const progressBar = document.getElementById('avatar-progress');

            if(fileInput){
                fileInput.addEventListener('change', async (e) => {
                    let imageFile = e.target.files[0];
                    if (!imageFile) {
                        imagePreview.src = defaultAvatar;
                        progressWrap.style.display = 'none';
                        return;
                    }

                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = '❌ Hanya file JPG atau PNG.';
                        fileInput.value = '';
                        imagePreview.src = defaultAvatar;
                        progressWrap.style.display = 'none';
                        return;
                    }

                    const originalSizeStr = formatBytes(imageFile.size);
                    progressWrap.style.display = 'block';
                    progressBar.style.width = '10%';
                    progressBar.innerText = 'Memotong...';
                    btnSubmitModal.disabled = true;

                    try {
                        // CENTER-CROP 1:1
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
                                canvas.toBlob((blob) => resolve(new File([blob], imageFile.name, { type: imageFile.type })), imageFile.type, 1.0);
                            };
                        });

                        const fileSizeKB = imageFile.size / 1024;
                        if(fileSizeKB <= 50) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(imageFile);
                            fileInput.files = dataTransfer.files;
                            imagePreview.src = URL.createObjectURL(imageFile);
                            progressBar.style.width = '100%';
                            progressBar.innerText = `Selesai! (${formatBytes(imageFile.size)})`;
                            setTimeout(() => progressWrap.style.display = 'none', 2500);
                            return;
                        }

                        // KOMPRESI KE < 50KB
                        const options = {
                            maxSizeMB: 50 / 1024,
                            maxWidthOrHeight: 800,
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
                        setTimeout(() => progressWrap.style.display = 'none', 3000);

                    } catch (error) {
                        errorDiv.textContent = "Gagal memproses gambar.";
                        fileInput.value = '';
                        progressWrap.style.display = 'none';
                    } finally {
                        btnSubmitModal.disabled = false;
                    }
                });
            }

            // --- 4. LOGIKA MODAL MESIN WAKTU (NONAKTIF / AKTIF) ---

            // Modal Purna Tugas (Nonaktif)
            document.querySelectorAll('.btn-nonaktif').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('namaNonaktif').innerText = this.dataset.name;

                    // ✅ RUTE AMAN
                    let urlAction = "{{ route('admin.leaders.toggle-status', ':id') }}";
                    document.getElementById('formNonaktif').action = urlAction.replace(':id', this.dataset.id);
                });
            });

            // Modal Jabat Kembali (Aktif)
            document.querySelectorAll('.btn-aktif').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('namaAktif').innerText = this.dataset.name;

                    // ✅ RUTE AMAN
                    let urlAction = "{{ route('admin.leaders.toggle-status', ':id') }}";
                    document.getElementById('formAktif').action = urlAction.replace(':id', this.dataset.id);
                });
            });

            // --- 5. SWEETALERT HAPUS PERMANEN ---
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Hapus Permanen?',
                        text: "Data pimpinan (yang belum mengesahkan PKS) ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    })
                });
            });

            // Aktifkan Tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(t => new bootstrap.Tooltip(t));
        });
    </script>
@endpush
