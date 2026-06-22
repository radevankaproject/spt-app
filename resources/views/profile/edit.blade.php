@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Profil')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <style>
        .profile-hero-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .profile-hero-edit::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .profile-avatar-edit-wrap {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.4);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .profile-avatar-edit-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-avatar-edit-initial {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 2px;
        }
        .readonly-field {
            background-color: #f8f9fa !important;
            opacity: 1 !important;
            cursor: not-allowed;
        }
        .form-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }
    </style>
@endsection



@section('content')
    @php
        $nameParts = explode(' ', trim($user->name));
        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#696cff"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#fff" font-family="sans-serif" font-size="40" font-weight="bold">' . $initials . '</text></svg>';
        $defaultAvatar = 'data:image/svg+xml;base64,' . base64_encode($svg);

        $roleLabels = [
            'admin' => ['text' => 'Administrator Sistem', 'color' => 'bg-label-danger', 'icon' => 'ti tabler-user-shield'],
            'leader' => ['text' => 'Kepala UPT / Pimpinan', 'color' => 'bg-label-primary', 'icon' => 'ti tabler-crown'],
            'staff_pks' => ['text' => 'Staff Administrasi PKS', 'color' => 'bg-label-info', 'icon' => 'ti tabler-file-text'],
            'staff_keu' => ['text' => 'Staff Keuangan', 'color' => 'bg-label-warning', 'icon' => 'ti tabler-currency-dollar'],
            'treasurer' => ['text' => 'Bendahara Penerimaan', 'color' => 'bg-label-warning', 'icon' => 'ti tabler-wallet'],
            'field_coordinator' => ['text' => 'Koordinator Lapangan (Mitra)', 'color' => 'bg-label-success', 'icon' => 'ti tabler-user-pin'],
        ];
        $currentRole = $roleLabels[$user->role] ?? ['text' => strtoupper($user->role), 'color' => 'bg-label-secondary', 'icon' => 'ti tabler-user'];
    @endphp

    {{-- Breadcrumb --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Akun /</span> Edit Profil</h4>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('profile.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="ri icon-base ti tabler-arrow-left me-1"></i> Kembali ke Profil
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('status') || session('error'))
        <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} d-flex align-items-center border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="ri icon-base {{ session('error') ? 'ti tabler-circle-x' : 'ti tabler-check' }} me-2 ti-md"></i>
            @if (session('status') === 'profile-updated')
                Profil berhasil diperbarui.
            @elseif (session('status') === 'password-updated')
                Kata sandi berhasil diperbarui.
            @elseif (session('status') === 'profile-image-deleted')
                Foto profil berhasil dihapus.
            @endif
            @if (session('error'))
                {{ session('error') }}
            @endif
        </div>
    @endif

    {{-- Hero Card with Avatar Upload --}}
    <div class="profile-hero-edit mb-4 shadow-lg">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 position-relative" style="z-index:1;">
            <div class="profile-avatar-edit-wrap position-relative">
                <img src="{{ $user->img ? asset('storage/' . $user->img) : $defaultAvatar }}"
                     alt="{{ $user->name }}" id="uploadedAvatar">
                
                {{-- Progress Bar Overlay --}}
                <div id="avatarProgressOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(0,0,0,0.6); display: none !important; backdrop-filter: blur(2px);">
                    <div class="spinner-border text-white mb-1" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 0.2em;"></div>
                    <span id="avatarProgressText" class="text-white small fw-bold" style="font-size: 0.75rem;">0%</span>
                </div>
            </div>
            <div class="text-center text-sm-start">
                <h4 class="fw-bold mb-1 text-white">{{ $user->name }}</h4>
                <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                    <i class="ri icon-base {{ $currentRole['icon'] }} me-1"></i> {{ $currentRole['text'] }}
                </span>
                <div class="d-flex flex-wrap gap-2 mt-2 justify-content-center justify-content-sm-start">
                    <label for="upload" class="btn btn-sm btn-light rounded-pill shadow-sm px-3 mb-0" style="cursor:pointer;">
                        <i class="ri icon-base ti tabler-camera me-1"></i> <span class="d-none d-sm-inline">Ganti Foto</span><span class="d-inline d-sm-none">Foto</span>
                        <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" name="img" form="basicInfoForm" />
                    </label>
                    <button type="button" id="resetButton" class="btn btn-sm btn-outline-light rounded-pill px-3 mb-0">
                        <i class="ri icon-base ti tabler-refresh me-1"></i> Reset
                    </button>
                    @if ($user->img)
                        <button type="button" id="deleteImageButton" class="btn btn-sm btn-outline-light rounded-pill px-3 mb-0">
                            <i class="ri icon-base ti tabler-trash me-1"></i> Hapus
                        </button>
                    @endif
                </div>
                <p class="text-white opacity-50 small mt-2 mb-0">JPG atau PNG. Maksimal 10MB.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Form Profil --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card form-card shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ri icon-base ti tabler-user-edit me-2 text-primary"></i>Informasi Dasar</h6>
                </div>
                <div class="card-body pt-4">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    {{-- Reuse form from hero (same action) --}}
                    <form method="post" action="{{ route('profile.update.custom') }}" enctype="multipart/form-data" id="basicInfoForm">
                        @csrf
                        @method('patch')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('name', 'updateProfile') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama Lengkap" autofocus />
                                    <label for="name">Nama Lengkap</label>
                                </div>
                                @error('name', 'updateProfile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('username', 'updateProfile') is-invalid @enderror" type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Username" />
                                    <label for="username">Username</label>
                                </div>
                                @error('username', 'updateProfile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('email', 'updateProfile') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" />
                                    <label for="email">Alamat Email</label>
                                </div>
                                @error('email', 'updateProfile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            @if(!in_array($user->role, ['leader', 'treasurer', 'staff_keu', 'staff_pks', 'field_coordinator']))
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('employee_number', 'updateProfile') is-invalid @enderror" type="text" id="employee_number" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}" placeholder="Kosongkan jika tidak ada" />
                                    <label for="employee_number">NIP / Employee Number (Opsional)</label>
                                </div>
                                @error('employee_number', 'updateProfile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="ri icon-base ti tabler-device-floppy me-1"></i> Simpan Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: Role Specific + Password --}}
        <div class="col-xl-4 col-lg-5">
            
            {{-- Role-Specific Info --}}
            @if($user->role !== 'admin')
                <div class="card form-card shadow-sm rounded-4 mb-4 border-start border-4 border-info">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-info"><i class="ri icon-base ti tabler-id me-2"></i>Informasi Spesifik</h6>
                    </div>
                    <div class="card-body pt-4">
                        
                        @if($user->role === 'field_coordinator' && $user->fieldCoordinator)
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Nomor Induk Kependudukan (NIK)</label>
                                <input type="text" class="form-control readonly-field form-control-sm" value="{{ $user->fieldCoordinator->id_card_number ?? '-' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Nomor WhatsApp / HP</label>
                                <input type="text" class="form-control readonly-field form-control-sm" value="{{ $user->fieldCoordinator->phone_number ?? '-' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Alamat Rumah</label>
                                <textarea class="form-control readonly-field form-control-sm" rows="2" readonly>{{ $user->fieldCoordinator->address ?? '-' }}</textarea>
                            </div>
                            <div class="alert alert-warning mb-0 py-2 small">
                                <i class="ri icon-base ti tabler-info-circle"></i> Hubungi Admin Dinas jika ingin mengubah data spesifik ini.
                            </div>
                        
                        @elseif(in_array($user->role, ['leader', 'treasurer', 'staff_keu', 'staff_pks']))
                            @php
                                $nipValue = '-';
                                if ($user->role === 'leader' && $user->leader) {
                                    $nipValue = $user->leader->employee_number ?? '-';
                                } else {
                                    $nipValue = $user->employee_number ?? '-';
                                }
                            @endphp
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Nomor Induk Pegawai (NIP)</label>
                                <input type="text" class="form-control readonly-field form-control-sm" value="{{ $nipValue !== '-' ? formatNip($nipValue) : '-' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Nomor WhatsApp / HP</label>
                                <input type="text" class="form-control readonly-field form-control-sm" value="{{ $user->phone_number ?? '-' }}" readonly>
                            </div>
                            @if($user->role === 'leader' && $user->leader)
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Status Jabatan</label>
                                <input type="text" class="form-control readonly-field form-control-sm text-uppercase" value="{{ $user->leader->status_jabatan ?? '-' }}" readonly>
                            </div>
                            @endif
                            <div class="alert alert-warning mb-0 py-2 small text-center">
                                <i class="ri icon-base ti tabler-info-circle mb-1 d-block" style="font-size: 1.5rem;"></i> 
                                Hubungi Admin jika ada perubahan NIP / Jabatan.
                                @if(!empty($uptProfile->phone_number_admin))
                                    @php
                                        $waAdmin = preg_replace('/^0/', '62', $uptProfile->phone_number_admin);
                                    @endphp
                                    <a href="https://wa.me/{{ $waAdmin }}" target="_blank" class="btn btn-success btn-sm w-100 mt-2 rounded-pill">
                                        <i class="ri icon-base ti tabler-whatsapp me-1"></i> Chat Admin
                                    </a>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- Card Ubah Password --}}
            <div class="card form-card shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ri icon-base ti tabler-lock-password me-2 text-danger"></i>Ubah Kata Sandi</h6>
                </div>
                <div class="card-body pt-4">
                    <form method="post" action="{{ route('password.update.custom') }}" id="passwordForm">
                        @csrf
                        @method('put')

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="current_password">Kata Sandi Saat Ini</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm @error('current_password', 'updatePassword') is-invalid @enderror" type="password" name="current_password" id="current_password" required />
                                <span class="input-group-text cursor-pointer"><i class="ri icon-base ti tabler-eye-off"></i></span>
                            </div>
                        </div>

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="password">Kata Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm @error('password', 'updatePassword') is-invalid @enderror" type="password" id="password" name="password" required />
                                <span class="input-group-text cursor-pointer"><i class="ri icon-base ti tabler-eye-off"></i></span>
                            </div>
                        </div>

                        {{-- Password Strength --}}
                        <div id="password-strength-meter" class="mb-3">
                            <div class="progress" style="height: 6px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <ul id="password-rules-list" class="list-unstyled mt-2 mb-0" style="font-size: 0.7rem;">
                                <li id="rule-length" class="text-danger"><i class="ri icon-base ti tabler-circle-x me-1"></i>Min. 8 karakter</li>
                                <li id="rule-mixed-case" class="text-danger"><i class="ri icon-base ti tabler-circle-x me-1"></i>Huruf besar & kecil</li>
                                <li id="rule-numbers" class="text-danger"><i class="ri icon-base ti tabler-circle-x me-1"></i>Min. satu angka</li>
                                <li id="rule-symbols" class="text-danger"><i class="ri icon-base ti tabler-circle-x me-1"></i>Min. satu simbol</li>
                            </ul>
                        </div>

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm" type="password" name="password_confirmation" id="password_confirmation" required />
                                <span class="input-group-text cursor-pointer"><i class="ri icon-base ti tabler-eye-off"></i></span>
                            </div>
                            <div id="password-match-message" class="small mt-1"></div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 rounded-pill shadow-sm mt-2" id="submitPasswordButton">Perbarui Sandi</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FITUR UPLOAD FOTO PROFIL (Kompresi)
            const accountUserImage = document.getElementById('uploadedAvatar');
            const fileInput = document.getElementById('upload');
            const resetButton = document.getElementById('resetButton');
            const deleteImageButton = document.getElementById('deleteImageButton');

            if (accountUserImage && fileInput) {
                const initialAvatarSrc = accountUserImage.src;
                const overlay = document.getElementById('avatarProgressOverlay');
                const progressText = document.getElementById('avatarProgressText');

                fileInput.addEventListener('change', async function(event) {
                    const imageFile = event.target.files[0];
                    if (!imageFile) { accountUserImage.src = initialAvatarSrc; return; }
                    try {
                        // Tampilkan overlay progress
                        overlay.style.setProperty('display', 'flex', 'important');
                        
                        const compressedFile = await imageCompression(imageFile, { 
                            maxSizeMB: 0.5, 
                            maxWidthOrHeight: 1024, 
                            useWebWorker: true,
                            onProgress: function(progress) {
                                progressText.textContent = progress + '%';
                            }
                        });
                        
                        // Sembunyikan overlay setelah selesai
                        overlay.style.setProperty('display', 'none', 'important');
                        progressText.textContent = '0%';
                        
                        accountUserImage.src = URL.createObjectURL(compressedFile);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        fileInput.files = dataTransfer.files;
                    } catch (error) {
                        overlay.style.setProperty('display', 'none', 'important');
                        Swal.fire({ title: 'Gagal Kompres!', text: 'Terjadi kesalahan.', icon: 'error' });
                        fileInput.value = ''; accountUserImage.src = initialAvatarSrc;
                    }
                });

                if (resetButton) resetButton.onclick = () => { fileInput.value = ''; accountUserImage.src = initialAvatarSrc; };

                if (deleteImageButton) {
                    deleteImageButton.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Hapus Foto?', text: "Foto profil akan dihapus permanen!", icon: 'warning',
                            showCancelButton: true, confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
                            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' }, buttonsStyling: false
                        }).then((result) => {
                            if (result.value) {
                                fetch("{{ route('profile.delete-image') }}", {
                                    method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                                }).then(r => r.json()).then(d => { if(d.success) window.location.reload(); });
                            }
                        });
                    });
                }
            }

            // FITUR UBAH KATA SANDI
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                const newPass = document.getElementById('password');
                const confPass = document.getElementById('password_confirmation');
                const strengthBar = document.getElementById('password-strength-bar');
                const matchMsg = document.getElementById('password-match-message');

                const updateRuleUI = (id, valid) => {
                    const el = document.getElementById(id);
                    if(!el) return;
                    el.className = valid ? 'text-success' : 'text-danger';
                    el.querySelector('i').className = valid ? 'ri icon-base ti tabler-check me-1' : 'ri icon-base ti tabler-circle-x me-1';
                };

                newPass.addEventListener('input', () => {
                    const p = newPass.value;
                    const len = p.length >= 8;
                    const mixed = /[a-z]/.test(p) && /[A-Z]/.test(p);
                    const num = /[0-9]/.test(p);
                    const sym = /[^A-Za-z0-9]/.test(p);

                    updateRuleUI('rule-length', len);
                    updateRuleUI('rule-mixed-case', mixed);
                    updateRuleUI('rule-numbers', num);
                    updateRuleUI('rule-symbols', sym);

                    let score = [len, mixed, num, sym].filter(Boolean).length;
                    const classes = ['bg-danger', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
                    
                    strengthBar.style.width = (score * 25) + '%';
                    strengthBar.className = `progress-bar ${score > 0 ? classes[score] : ''}`;
                    checkMatch();
                });

                const checkMatch = () => {
                    if(!confPass.value) { matchMsg.textContent = ''; return; }
                    if(newPass.value === confPass.value) {
                        matchMsg.textContent = '✅ Kata sandi cocok!'; matchMsg.className = 'small mt-1 text-success';
                    } else {
                        matchMsg.textContent = '❌ Kata sandi tidak cocok.'; matchMsg.className = 'small mt-1 text-danger';
                    }
                };
                confPass.addEventListener('input', checkMatch);

                // SweetAlert Error Backend
                const passwordErrors = @json($errors->updatePassword->all() ?? []);
                if (passwordErrors.length > 0) {
                    Swal.fire({ title: 'Gagal Mengubah Password', html: `<ul class="text-start ps-3 mb-0">${passwordErrors.map(e => `<li>${e}</li>`).join('')}</ul>`, icon: 'error' });
                }
            }
        });
    </script>
@endsection