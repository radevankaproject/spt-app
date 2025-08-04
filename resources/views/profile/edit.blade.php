@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan Akun /</span> Profil</h4>

        <div class="row">
            <div class="col-md-12">
                <!-- Card Akun -->
                <div class="card mb-6">
                    <h5 class="card-header">Detail Profil</h5>
                    <!-- Form Akun -->
                    <div class="card-body">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success alert-dismissible" role="alert">
                                Informasi profil berhasil disimpan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form id="formAccountSettings" method="POST" action="{{ route('profile.update.custom') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="d-flex align-items-start align-items-sm-center gap-6">
                                <img src="{{ Auth::user()->img ? asset('storage/' . Auth::user()->img) : '<span class="avatar-initial rounded-circle bg-label-secondary">' . strtoupper(substr(Auth::user()->name, 0, 2)) . '</span>' }}"
                                    alt="user-avatar" class="d-block w-px-120 h-px-120 rounded-3" id="uploadedAvatar" />
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-3" tabindex="0">
                                        <span class="d-none d-sm-block">Upload foto baru</span>
                                        <i class="ri-upload-2-line d-block d-sm-none"></i>
                                        <input type="file" id="upload" name="img" class="account-file-input"
                                            hidden accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="button" class="btn btn-outline-secondary account-image-reset me-3">
                                        <i class="ri-refresh-line d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Reset</span>
                                    </button>
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="remove_image"
                                            id="removeImageCheckbox">
                                        <label class="form-check-label" for="removeImageCheckbox">
                                            Hapus Foto Profil
                                        </label>
                                    </div>
                                    <p class="mb-0 mt-4 text-muted">Hanya JPG atau PNG. Ukuran maks 2MB.</p>
                                </div>
                            </div>
                            <hr class="my-6">
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" id="name" name="name"
                                            value="{{ old('name', $user->name) }}" autofocus required />
                                        <label for="name">Nama Lengkap</label>
                                    </div>
                                    @error('name', 'updateProfile')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" name="username" id="username"
                                            value="{{ old('username', $user->username) }}" placeholder="john.doe"
                                            required />
                                        <label for="username">Username</label>
                                    </div>
                                    @error('username', 'updateProfile')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="email" id="email" name="email"
                                            value="{{ old('email', $user->email) }}" required />
                                        <label for="email">E-mail</label>
                                    </div>
                                    @error('email', 'updateProfile')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                <button type="reset" class="btn btn-outline-secondary"
                                    id="reset-profile-form">Batal</button>
                            </div>
                        </form>
                    </div>
                    <!-- /Form Akun -->
                </div>

                <!-- Card Ubah Password -->
                <div class="card">
                    <h5 class="card-header">Ubah Password</h5>
                    <div class="card-body">
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success alert-dismissible" role="alert">
                                Password berhasil diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form id="formChangePassword" method="POST" action="{{ route('password.update.custom') }}">
                            @csrf
                            @method('put')
                            <div class="row g-6">
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="password" name="current_password"
                                                id="current_password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required autocomplete="current-password" />
                                            <label for="current_password">Password Saat Ini</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri-eye-off-line"></i></span>
                                    </div>
                                    @error('current_password', 'updatePassword')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-6 mt-4">
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="password" id="password" name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required autocomplete="new-password" />
                                            <label for="password">Password Baru</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri-eye-off-line"></i></span>
                                    </div>
                                    @error('password', 'updatePassword')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required autocomplete="new-password" />
                                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary me-2">Simpan Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            (function() {
                // Pratinjau & Reset Avatar
                const uploadInput = document.querySelector('.account-file-input'),
                    resetButton = document.querySelector('.account-image-reset'),
                    uploadedAvatar = document.getElementById('uploadedAvatar');

                if (uploadedAvatar) {
                    const originalImage = uploadedAvatar.src;

                    uploadInput.onchange = () => {
                        if (uploadInput.files[0]) {
                            uploadedAvatar.src = window.URL.createObjectURL(uploadInput.files[0]);
                        }
                    };

                    resetButton.onclick = () => {
                        uploadInput.value = '';
                        uploadedAvatar.src = originalImage;
                        document.getElementById('removeImageCheckbox').checked = false;
                    };

                    // Reset form juga akan mereset gambar
                    const resetProfileFormBtn = document.getElementById('reset-profile-form');
                    if (resetProfileFormBtn) {
                        resetProfileFormBtn.onclick = () => {
                            setTimeout(() => { // Beri jeda agar form reset dulu
                                uploadInput.value = '';
                                uploadedAvatar.src = originalImage;
                                document.getElementById('removeImageCheckbox').checked = false;
                            }, 100);
                        };
                    }
                }
            })();
        });
    </script>
@endpush
