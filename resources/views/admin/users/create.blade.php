@extends('layouts.contentNavbarLayout')

@section('title', 'Tambah User Baru')



@section('page-style')
    {{-- CSS untuk SweetAlert2 jika belum ada di layout utama --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Tambah User Baru</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Menampilkan Pesan Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
            <ul class="mt-2 mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-6">
            <!-- Kolom Kiri: Detail User -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detail Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Masukkan Nama Lengkap" value="{{ old('name') }}" required />
                                    <label for="name">Nama Lengkap</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Username" value="{{ old('username') }}" required />
                                        <label for="username">Username</label>
                                    </div>
                                    <button class="btn btn-outline-primary" type="button"
                                        id="generate-username">Generate</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="contoh@email.com" value="{{ old('email') }}" required />
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        placeholder="Contoh: 08123456789" value="{{ old('phone_number') }}" maxlength="14" />
                                    <label for="phone_number">No. Handphone (Opsional)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <label for="password">Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="icon-base ti tabler-eye-off"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="password_confirmation">Ulangi Password</label>
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password_confirmation" class="form-control"
                                                name="password_confirmation"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required />
                                            <label for="password_confirmation">Ulangi Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="icon-base ti tabler-eye-off"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Role</label>
                                <div class="d-flex pt-2">
                                    <div class="btn-group w-100" role="group" aria-label="Pilihan Role">

                                        <input type="radio" class="btn-check" name="role" id="roleAdmin" value="admin"
                                            {{ old('role') == 'admin' ? 'checked' : '' }} autocomplete="off">
                                        <label class="btn btn-outline-primary" for="roleAdmin">Admin</label>

                                        <input type="radio" class="btn-check" name="role" id="roleStaffPks"
                                            value="staff_pks"
                                            {{ old('role', 'staff_pks') == 'staff_pks' ? 'checked' : '' }}
                                            autocomplete="off">
                                        <label class="btn btn-outline-primary" for="roleStaffPks">Staff PKS</label>

                                        <input type="radio" class="btn-check" name="role" id="roleStaffKeu"
                                            value="staff_keu" {{ old('role') == 'staff_keu' ? 'checked' : '' }}
                                            autocomplete="off">
                                        <label class="btn btn-outline-primary" for="roleStaffKeu">Staff Keuangan</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Foto Profil</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user-avatar"
                                class="d-block w-px-120 h-px-120 rounded-circle mb-4" id="uploadedAvatar" />

                            <div class="d-flex justify-content-center gap-3 mb-3">
                                <label for="img-upload" class="btn btn-primary" tabindex="0">
                                    <span class="d-none d-sm-block">Pilih Foto</span>
                                    <i class="icon-base ti tabler-upload d-sm-none"></i>
                                    <input type="file" id="img-upload" name="img" class="account-file-input"
                                        hidden accept="image/png, image/jpeg" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset">
                                    <i class="icon-base ti tabler-refresh d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                            </div>

                            <div id="file-error" class="text-danger text-sm text-center"></div>
                            <p class="text-muted mb-0 text-center">Hanya JPG/PNG. Akan dikompres di bawah 300KB.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="col-12 text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    {{-- Library kompresi gambar --}}
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>

    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            // --- Logika untuk generate dan validasi username ---
            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');
            const generateBtn = document.getElementById('generate-username');

            if (generateBtn && nameInput && usernameInput) {
                generateBtn.addEventListener('click', function() {
                    let username = nameInput.value.trim().toLowerCase().replace(/\s+/g, '_').replace(
                        /[^a-z0-9_]/g, '');
                    usernameInput.value = username;
                });
                usernameInput.addEventListener('input', e => {
                    e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '').replace(/[^a-z0-9_]/g,
                        '');
                });
            }

            // --- Logika untuk batasan spasi password ---
            document.querySelectorAll('input[type="password"]').forEach(input => {
                if (input) {
                    input.addEventListener('input', e => {
                        e.target.value = e.target.value.replace(/\s/g, '');
                    });
                }
            });

            // --- Logika untuk upload & kompresi gambar ---
            const fileInput = document.getElementById('img-upload');
            const uploadedAvatar = document.getElementById('uploadedAvatar');
            const resetButton = document.querySelector('.account-image-reset');
            const fileErrorDiv = document.getElementById('file-error');

            if (fileInput && uploadedAvatar && resetButton) {
                const defaultAvatar = uploadedAvatar.src;

                fileInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) return;

                    fileErrorDiv.textContent = '';

                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        fileErrorDiv.textContent = 'Hanya file JPG atau PNG.';
                        fileInput.value = '';
                        return;
                    }

                    console.log(`Original size: ${(imageFile.size / 1024).toFixed(2)} KB`);
                    const options = {
                        maxSizeMB: 0.3,
                        maxWidthOrHeight: 1024,
                        useWebWorker: true,
                        fileType: imageFile.type
                    }
                    try {
                        const compressedFile = await imageCompression(imageFile, options);
                        console.log(`Compressed size: ${(compressedFile.size / 1024).toFixed(2)} KB`);

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                            type: compressedFile.type
                        }));
                        fileInput.files = dataTransfer.files;

                        uploadedAvatar.src = URL.createObjectURL(compressedFile);
                    } catch (error) {
                        console.error("Image compression error:", error);
                        fileErrorDiv.textContent = "Gagal mengkompres gambar.";
                        fileInput.value = '';
                    }
                });

                resetButton.addEventListener('click', () => {
                    uploadedAvatar.src = defaultAvatar;
                    fileInput.value = '';
                    fileErrorDiv.textContent = '';
                });
            }
        });
    </script>
@endsection
