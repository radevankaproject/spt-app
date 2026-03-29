@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-profile-edit')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan Akun /</span> Profil</h4>


    <div class="row">
        <div class="col-md-12">

            {{-- Card Notifikasi --}}
            @if (session('status') || session('error'))
                <div class="card mb-4">
                    <div class="card-body">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                                <i class="ri ri-check-line"></i>
                                Profil berhasil diperbarui.
                            </div>
                        @endif
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                                <i class="ri ri-check-line"></i>
                                Kata sandi berhasil diperbarui.
                            </div>
                        @endif
                        @if (session('status') === 'profile-image-deleted')
                            <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                                <i class="ri ri-check-line"></i>
                                Foto profil berhasil dihapus.
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                                <i class="ri ri-close-line"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Card Detail Profil --}}
            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>
                <div class="card-body py-6">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                    <form method="post" action="{{ route('profile.update.custom') }}" class="mt-4"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="d-flex align-items-start align-items-sm-center gap-6">
                            <img src="{{ Auth::user()->img ? asset('storage/' . Auth::user()->img) : asset('assets/img/avatars/1.png') }}"
                                alt="Foto Profil" class="d-block w-px-100 h-px-100 rounded-circle"
                                style="object-fit: cover;" id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-3 mb-3" tabindex="0">
                                    <span class="d-none d-sm-block">Upload Foto Baru</span>
                                    <i class="ri-upload-2-line d-block d-sm-none"></i>
                                    <input type="file" id="upload" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" name="img" />
                                </label>
                                <button type="button" id="resetButton"
                                    class="btn btn-label-secondary account-image-reset mb-3">
                                    <i class="ri-refresh-line d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                @if (Auth::user()->img)
                                    <button type="button" id="deleteImageButton"
                                        class="btn btn-label-danger account-image-delete mb-3 ms-2">
                                        <i class="ri-delete-bin-line d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Hapus Foto</span>
                                    </button>
                                @endif
                                <div class="text-muted small">JPG atau PNG diperbolehkan. Maks. 1MB</div>
                            </div>
                        </div>

                        <hr class="my-6" />

                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('name', 'updateProfile') is-invalid @enderror"
                                        type="text" id="name" name="name"
                                        value="{{ old('name', Auth::user()->name) }}" placeholder="Masukkan Nama Lengkap"
                                        autofocus />
                                    <label for="name">Nama Lengkap</label>
                                </div>
                                @error('name', 'updateProfile')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('username', 'updateProfile') is-invalid @enderror"
                                        type="text" id="username" name="username"
                                        value="{{ old('username', Auth::user()->username) }}"
                                        placeholder="Masukkan Username" />
                                    <label for="username">Username</label>
                                </div>
                                @error('username', 'updateProfile')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('email', 'updateProfile') is-invalid @enderror"
                                        type="email" id="email" name="email"
                                        value="{{ old('email', Auth::user()->email) }}"
                                        placeholder="john.doe@example.com" />
                                    <label for="email">Email</label>
                                </div>
                                @error('email', 'updateProfile')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
                                    <div>
                                        <p class="text-sm mt-2 text-muted"> Email Anda belum diverifikasi.
                                            <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                                                Klik di sini untuk mengirim ulang email verifikasi. </button>
                                        </p>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-success"> Tautan verifikasi baru telah
                                                dikirim ke alamat email Anda. </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="role" name="role"
                                        value="{{ old('role', Auth::user()->role) }}" placeholder="Role Pengguna"
                                        disabled />
                                    <label for="role">Role</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card Ubah Password --}}
            <div class="card">
                <h5 class="card-header">Ubah Kata Sandi</h5>
                <div class="card-body py-6">
                    <form method="post" action="{{ route('password.update.custom') }}" class="mt-4"
                        id="passwordForm">
                        @csrf
                        @method('put')

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                type="password" name="current_password" id="current_password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <label for="current_password">Kata Sandi Saat Ini</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                                @error('current_password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                type="password" id="password" name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <label for="password">Kata Sandi Baru</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ✅ INDIKATOR KEKUATAN PASSWORD --}}
                            <div class="col-12">
                                <div id="password-strength-meter" class="mt-2">
                                    <div class="progress" style="height: 8px;">
                                        <div id="password-strength-bar" class="progress-bar" role="progressbar"
                                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p id="password-strength-text" class="small mt-2"></p>

                                    {{-- ✅ TAMBAHKAN BLOK INI: DAFTAR ATURAN PASSWORD --}}
                                    <ul id="password-rules-list" class="list-unstyled mt-3 small">
                                        <li id="rule-length" class="text-danger mb-2">
                                            <i class="ri-close-circle-line me-2"></i>Minimal 8 karakter
                                        </li>
                                        <li id="rule-mixed-case" class="text-danger mb-2">
                                            <i class="ri-close-circle-line me-2"></i>Kombinasi huruf besar & kecil
                                        </li>
                                        <li id="rule-numbers" class="text-danger mb-2">
                                            <i class="ri-close-circle-line me-2"></i>Minimal satu angka (0-9)
                                        </li>
                                        <li id="rule-symbols" class="text-danger">
                                            <i class="ri-close-circle-line me-2"></i>Minimal satu simbol (!@#$%)
                                        </li>
                                    </ul>

                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                                {{-- ✅ PESAN KECOCOKAN PASSWORD --}}
                                <div id="password-match-message" class="small mt-1"></div>
                            </div>
                        </div>

                        <div class="mt-6 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="submitPasswordButton">Ubah Kata
                                Sandi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Library untuk notifikasi & kompresi gambar --}}
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ----- 1. FITUR UPLOAD FOTO PROFIL -----
            const accountUserImage = document.getElementById('uploadedAvatar');
            const fileInput = document.getElementById('upload');
            const resetButton = document.getElementById('resetButton');
            const deleteImageButton = document.getElementById('deleteImageButton');

            if (accountUserImage) {
                const initialAvatarSrc = accountUserImage.src;

                // --- Kompresi Gambar Otomatis & Preview ---
                if (fileInput) {
                    fileInput.addEventListener('change', async function(event) {
                        const imageFile = event.target.files[0];
                        if (!imageFile) {
                            accountUserImage.src = initialAvatarSrc;
                            return;
                        }
                        const options = {
                            maxSizeMB: 0.5, // Maks 500KB
                            maxWidthOrHeight: 1024,
                            useWebWorker: true,
                        };
                        try {
                            const compressedFile = await imageCompression(imageFile, options);
                            accountUserImage.src = URL.createObjectURL(compressedFile);
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                                type: compressedFile.type
                            }));
                            fileInput.files = dataTransfer.files;
                        } catch (error) {
                            Swal.fire({
                                title: 'Gagal Kompres!',
                                text: 'Terjadi kesalahan saat mengompres gambar.',
                                icon: 'error'
                            });
                            fileInput.value = '';
                            accountUserImage.src = initialAvatarSrc;
                        }
                    });
                }

                // --- Fitur Reset Gambar ---
                if (resetButton) {
                    resetButton.onclick = () => {
                        fileInput.value = '';
                        accountUserImage.src = initialAvatarSrc;
                    };
                }

                // --- Fitur Hapus Foto Profil ---
                if (deleteImageButton) {
                    deleteImageButton.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Anda yakin?',
                            text: "Foto profil akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'btn btn-danger me-3',
                                cancelButton: 'btn btn-label-secondary'
                            },
                            buttonsStyling: false
                        }).then(function(result) {
                            if (result.value) {
                                fetch("{{ route('profile.delete-image') }}", {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        },
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.reload();
                                        } else {
                                            Swal.fire({
                                                title: 'Gagal!',
                                                text: data.message ||
                                                    'Terjadi kesalahan.',
                                                icon: 'error'
                                            });
                                        }
                                    });
                            }
                        });
                    });
                }
            }


            // ----- 2. FITUR UBAH KATA SANDI -----
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                const newPasswordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('password_confirmation');
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength-text');
                const matchMessage = document.getElementById('password-match-message');

                const updateRuleUI = (ruleId, isValid) => {
                    const ruleElement = document.getElementById(ruleId);
                    if (!ruleElement) return;
                    const icon = ruleElement.querySelector('i');
                    if (isValid) {
                        ruleElement.classList.remove('text-danger');
                        ruleElement.classList.add('text-success');
                        icon.classList.remove('ri-close-circle-line');
                        icon.classList.add('ri-check-line');
                    } else {
                        ruleElement.classList.remove('text-success');
                        ruleElement.classList.add('text-danger');
                        icon.classList.remove('ri-check-line');
                        icon.classList.add('ri-close-circle-line');
                    }
                };

                function checkPasswordStrength(password) {
                    const hasLength = password.length >= 8;
                    const hasLowercase = /[a-z]/.test(password);
                    const hasUppercase = /[A-Z]/.test(password);
                    const hasMixedCase = hasLowercase && hasUppercase;
                    const hasNumber = /[0-9]/.test(password);
                    const hasSymbol = /[^A-Za-z0-9]/.test(password);

                    updateRuleUI('rule-length', hasLength);
                    updateRuleUI('rule-mixed-case', hasMixedCase);
                    updateRuleUI('rule-numbers', hasNumber);
                    updateRuleUI('rule-symbols', hasSymbol);

                    let score = [hasLength, hasMixedCase, hasNumber, hasSymbol].filter(Boolean).length;
                    if (hasLowercase && hasUppercase) score = hasMixedCase ? score : score + 1;

                    const levels = {
                        0: {
                            text: '',
                            class: '',
                            width: '0%'
                        },
                        1: {
                            text: 'Sangat Lemah',
                            class: 'bg-danger',
                            width: '20%'
                        },
                        2: {
                            text: 'Lemah',
                            class: 'bg-danger',
                            width: '40%'
                        },
                        3: {
                            text: 'Sedang',
                            class: 'bg-warning',
                            width: '60%'
                        },
                        4: {
                            text: 'Kuat',
                            class: 'bg-success',
                            width: '80%'
                        },
                        5: {
                            text: 'Sangat Kuat',
                            class: 'bg-success',
                            width: '100%'
                        }
                    };
                    const level = levels[Math.min(score, 5)];

                    strengthBar.style.width = level.width;
                    strengthBar.className = `progress-bar ${level.class}`;
                    strengthText.textContent = level.text;
                    strengthText.className = `small mt-2 ${level.class.replace('bg-', 'text-')}`;
                }

                function checkPasswordMatch() {
                    const newPass = newPasswordInput.value;
                    const confirmPass = confirmPasswordInput.value;
                    if (confirmPass.length === 0 && newPass.length === 0) {
                        matchMessage.textContent = '';
                        return;
                    }
                    if (newPass === confirmPass && newPass.length > 0) {
                        matchMessage.textContent = '✅ Kata sandi cocok!';
                        matchMessage.className = 'small mt-1 text-success';
                    } else if (confirmPass.length > 0) {
                        matchMessage.textContent = '❌ Kata sandi tidak cocok.';
                        matchMessage.className = 'small mt-1 text-danger';
                    } else {
                        matchMessage.textContent = '';
                    }
                }

                newPasswordInput.addEventListener('input', () => {
                    checkPasswordStrength(newPasswordInput.value);
                    checkPasswordMatch();
                });
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);

                // Hapus error merah saat mulai mengetik
                passwordForm.querySelectorAll('.is-invalid').forEach(input => {
                    input.addEventListener('input', () => {
                        input.classList.remove('is-invalid');
                        let errorDiv = input.closest('.form-floating-outline, .input-group-merge')
                            ?.closest('.form-password-toggle')?.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('text-danger')) {
                            errorDiv.style.display = 'none';
                        }
                    });
                });

                // Tampilkan error dari backend (FormRequest) menggunakan SweetAlert
                const passwordErrors = @json($errors->updatePassword->all());
                if (passwordErrors.length > 0) {
                    Swal.fire({
                        title: 'Oops! Gagal Mengubah Password',
                        html: `<ul class="text-start ps-3 mb-0">${passwordErrors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                        icon: 'error'
                    });
                }
            }
        });
    </script>
@endpush
