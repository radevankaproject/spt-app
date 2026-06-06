@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .role-badge { font-size: 0.85rem; padding: 0.5em 1em; }
        .readonly-field { background-color: #f8f9fa !important; opacity: 1 !important; cursor: not-allowed; }
    </style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-profile-edit')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan Akun /</span> Profil Saya</h4>

    @php
        // Label Role Bahasa Indonesia
        $roleLabels = [
            'admin' => ['text' => 'Administrator Sistem', 'color' => 'bg-label-danger'],
            'leader' => ['text' => 'Kepala UPT / Pimpinan', 'color' => 'bg-label-primary'],
            'staff_pks' => ['text' => 'Staff Administrasi PKS', 'color' => 'bg-label-info'],
            'staff_keu' => ['text' => 'Staff Keuangan', 'color' => 'bg-label-warning'],
            'bendahara' => ['text' => 'Bendahara Penerimaan', 'color' => 'bg-label-warning'],
            'field_coordinator' => ['text' => 'Koordinator Lapangan (Mitra)', 'color' => 'bg-label-success'],
        ];
        
        $currentRole = $roleLabels[$user->role] ?? ['text' => strtoupper($user->role), 'color' => 'bg-label-secondary'];
    @endphp

    <div class="row g-4">
        
        {{-- BAGIAN KIRI: FOTO & DETAIL DASAR --}}
        <div class="col-xl-8 col-lg-7">
            
            {{-- Notifikasi --}}
            @if (session('status') || session('error'))
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-3">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success d-flex align-items-center mb-0 border-0"><i class="ri ri-check-line me-2"></i> Profil berhasil diperbarui.</div>
                        @elseif (session('status') === 'password-updated')
                            <div class="alert alert-success d-flex align-items-center mb-0 border-0"><i class="ri ri-check-line me-2"></i> Kata sandi berhasil diperbarui.</div>
                        @elseif (session('status') === 'profile-image-deleted')
                            <div class="alert alert-success d-flex align-items-center mb-0 border-0"><i class="ri ri-check-line me-2"></i> Foto profil berhasil dihapus.</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center mb-0 border-0"><i class="ri ri-close-line me-2"></i> {{ session('error') }}</div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Card Utama: Profil --}}
            <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="ri ri-user-settings-line me-2 text-primary"></i>Informasi Dasar</h5>
                    <span class="badge rounded-pill {{ $currentRole['color'] }} role-badge">
                        {{ $currentRole['text'] }}
                    </span>
                </div>
                <div class="card-body pt-4">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    <form method="post" action="{{ route('profile.update.custom') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Avatar Upload --}}
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4 pb-3 border-bottom border-dashed">
                            <img src="{{ $user->img ? asset('storage/' . $user->img) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=696cff&color=fff' }}"
                                alt="Foto Profil" class="d-block w-px-100 h-px-100 rounded-circle shadow-sm"
                                style="object-fit: cover; border: 3px solid #fff;" id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-sm btn-primary me-2 mb-2 shadow-sm">
                                    <span class="d-none d-sm-block"><i class="ri ri-upload-2-line me-1"></i> Ganti Foto</span>
                                    <i class="ri-upload-2-line d-block d-sm-none"></i>
                                    <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" name="img" />
                                </label>
                                <button type="button" id="resetButton" class="btn btn-sm btn-outline-secondary mb-2">
                                    <i class="ri-refresh-line d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                @if ($user->img)
                                    <button type="button" id="deleteImageButton" class="btn btn-sm btn-outline-danger mb-2 ms-2">
                                        <i class="ri-delete-bin-line d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Hapus</span>
                                    </button>
                                @endif
                                <div class="text-muted small mt-1">JPG atau PNG. Maksimal 1MB.</div>
                            </div>
                        </div>

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
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('employee_number', 'updateProfile') is-invalid @enderror" type="text" id="employee_number" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}" placeholder="Kosongkan jika tidak ada" />
                                    <label for="employee_number">NIP / Employee Number (Opsional)</label>
                                </div>
                                @error('employee_number', 'updateProfile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="ri ri-save-3-line me-1"></i> Simpan Profil</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- BAGIAN KANAN: ROLE SPECIFIC & PASSWORD --}}
        <div class="col-xl-4 col-lg-5">
            
            {{-- ✅ FORM DINAMIS BERDASARKAN ROLE --}}
            @if($user->role !== 'admin' && $user->role !== 'staff_pks' && $user->role !== 'staff_keu')
                <div class="card mb-4 border-0 shadow-sm rounded-4 border-start border-4 border-info">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-info"><i class="ri ri-profile-line me-2"></i>Informasi Spesifik</h6>
                    </div>
                    <div class="card-body pt-4">
                        
                        {{-- JIKA KORLAP --}}
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
                                <i class="ri ri-information-line"></i> Hubungi Admin Dinas jika ingin mengubah data spesifik ini.
                            </div>
                        
                        {{-- JIKA PIMPINAN --}}
                        @elseif($user->role === 'leader' && $user->leader)
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Nomor Induk Pegawai (NIP)</label>
                                <input type="text" class="form-control readonly-field form-control-sm" value="{{ $user->leader->employee_number ?? '-' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Status Jabatan</label>
                                <input type="text" class="form-control readonly-field form-control-sm text-uppercase" value="{{ $user->leader->status_jabatan ?? '-' }}" readonly>
                            </div>
                            <div class="alert alert-warning mb-0 py-2 small">
                                <i class="ri ri-information-line"></i> Hubungi Admin jika ada perubahan NIP / Jabatan.
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- Card Ubah Password --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ri ri-lock-password-line me-2 text-danger"></i>Ubah Kata Sandi</h6>
                </div>
                <div class="card-body pt-4">
                    <form method="post" action="{{ route('password.update.custom') }}" id="passwordForm">
                        @csrf
                        @method('put')

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="current_password">Kata Sandi Saat Ini</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm @error('current_password', 'updatePassword') is-invalid @enderror" type="password" name="current_password" id="current_password" required />
                                <span class="input-group-text cursor-pointer"><i class="ri ri-eye-off-line"></i></span>
                            </div>
                        </div>

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="password">Kata Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm @error('password', 'updatePassword') is-invalid @enderror" type="password" id="password" name="password" required />
                                <span class="input-group-text cursor-pointer"><i class="ri ri-eye-off-line"></i></span>
                            </div>
                        </div>

                        {{-- INDIKATOR KEKUATAN PASSWORD --}}
                        <div id="password-strength-meter" class="mb-3">
                            <div class="progress" style="height: 6px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <ul id="password-rules-list" class="list-unstyled mt-2 mb-0" style="font-size: 0.7rem;">
                                <li id="rule-length" class="text-danger"><i class="ri-close-circle-line me-1"></i>Min. 8 karakter</li>
                                <li id="rule-mixed-case" class="text-danger"><i class="ri-close-circle-line me-1"></i>Huruf besar & kecil</li>
                                <li id="rule-numbers" class="text-danger"><i class="ri-close-circle-line me-1"></i>Min. satu angka</li>
                                <li id="rule-symbols" class="text-danger"><i class="ri-close-circle-line me-1"></i>Min. satu simbol</li>
                            </ul>
                        </div>

                        <div class="form-password-toggle mb-3">
                            <label class="text-muted small fw-bold" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control form-control-sm" type="password" name="password_confirmation" id="password_confirmation" required />
                                <span class="input-group-text cursor-pointer"><i class="ri ri-eye-off-line"></i></span>
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

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
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
                fileInput.addEventListener('change', async function(event) {
                    const imageFile = event.target.files[0];
                    if (!imageFile) { accountUserImage.src = initialAvatarSrc; return; }
                    try {
                        const compressedFile = await imageCompression(imageFile, { maxSizeMB: 0.5, maxWidthOrHeight: 1024, useWebWorker: true });
                        accountUserImage.src = URL.createObjectURL(compressedFile);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        fileInput.files = dataTransfer.files;
                    } catch (error) {
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
                    el.querySelector('i').className = valid ? 'ri-check-line me-1' : 'ri-close-circle-line me-1';
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
@endpush