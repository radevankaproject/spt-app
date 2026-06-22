@extends('layouts.contentNavbarLayout')

@section('title', 'Profil UPT Perparkiran')

@section('page-style')
    {{-- CSS untuk Quill Editor --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <style>
        /* ✅ Style untuk progress bar kompresi */
        #compressionProgress {
            display: none;
            height: 10px;
        }

        /* ✅ Memaksa Quill berukuran FIT (Fix) dan tidak bertabrakan */
        .quill-editor-wrapper {
            display: block !important; /* Memutus efek flex dari parent */
            width: 100%;
        }
        .quill-editor-wrapper .ql-container {
            height: 250px !important; /* Set tinggi absolut / Fix */
            min-height: 250px !important;
            flex: none !important; /* Mencegah container memanjang/menyusut otomatis */
        }
        .quill-editor-wrapper .ql-editor {
            height: 100%;
            overflow-y: auto; /* Munculkan scrollbar hanya di dalam kotak teks */
        }
        /* Opsional: Sesuaikan border agar menyatu dengan template */
        .quill-editor-wrapper .ql-toolbar {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        .quill-editor-wrapper .ql-container {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    </style>
@endsection

{{-- ✅ Panggil Skeleton Loader --}}


@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Profil UPT Perparkiran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Profil UPT</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.upt-profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        {{-- Kita bungkus seluruh form dalam row g-6 agar paddingnya pas --}}
        <div class="row g-6">

            {{-- ✅ KOLOM KIRI: FORMULIR UTAMA (Dibungkus col-lg-8 agar menumpuk rapi, tidak bertabrakan) --}}
            <div class="col-lg-8">
                {{-- Card 1: Informasi Dasar --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Instansi & Aplikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="app_name" name="app_name"
                                        placeholder="Nama Website" value="{{ old('app_name', $profile->app_name) }}"
                                        required />
                                    <label for="app_name">Nama Aplikasi</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Nama UPT" value="{{ old('name', $profile->name) }}" required />
                                    <label for="name">Nama Instansi (UPT)</label>
                                </div>
                            </div>

                            {{-- Tambahan Input Baru: Greetings & Token Fonnte --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="login_greetings" name="login_greetings"
                                        placeholder="Sapaan di halaman login" value="{{ old('login_greetings', $profile->login_greetings) }}" />
                                    <label for="login_greetings">Login Greetings</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="api_token_fonnte" name="api_token_fonnte"
                                        placeholder="Token WhatsApp Fonnte" value="{{ old('api_token_fonnte', $profile->api_token_fonnte) }}" />
                                    <label for="api_token_fonnte">API Token Fonnte (WA)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Nomor Telepon UPT" value="{{ old('phone', $profile->phone) }}" />
                                    <label for="phone">Nomor Telepon UPT</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phone_number_admin" name="phone_number_admin"
                                        placeholder="Nomor WA Admin (Misal: 62812...)" value="{{ old('phone_number_admin', $profile->phone_number_admin) }}" />
                                    <label for="phone_number_admin">Nomor WA Admin (Untuk Bantuan)</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Email" value="{{ old('email', $profile->email) }}" />
                                    <label for="email">Alamat Email</label>
                                </div>
                            </div>
                             <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="url" class="form-control" id="website" name="website"
                                        placeholder="https://contoh.com" value="{{ old('website', $profile->website) }}" />
                                    <label for="website">Website</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="address" name="address" placeholder="Alamat lengkap UPT" style="height: 100px;">{{ old('address', $profile->address) }}</textarea>
                                    <label for="address">Alamat Lengkap</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Tentang Kami (Quill) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tentang Kami (About Us)</h5>
                    </div>
                    <div class="card-body">
                        {{-- ✅ Bungkus dengan class quill-editor-wrapper --}}
                        <div class="quill-editor-wrapper">
                            <div id="about_us_editor">
                                {!! old('about_us', $profile->about_us) !!}
                            </div>
                        </div>
                        <input type="hidden" name="about_us" id="about_us_input">
                    </div>
                </div>

                {{-- Card 3: Kebijakan Privasi (Quill) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kebijakan Privasi (Privacy Policy)</h5>
                    </div>
                    <div class="card-body">
                        {{-- ✅ Bungkus dengan class quill-editor-wrapper --}}
                        <div class="quill-editor-wrapper">
                            <div id="privacy_policy_editor">
                                {!! old('privacy_policy', $profile->privacy_policy) !!}
                            </div>
                        </div>
                        <input type="hidden" name="privacy_policy" id="privacy_policy_input">
                    </div>
                </div>
            </div>

            {{-- ✅ KOLOM KANAN: UPLOAD LOGO (Dibungkus col-lg-4, sticky) --}}
            <div class="col-lg-4">
                <div class="card position-sticky" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Logo Instansi</h5>
                    </div>
                    <div class="card-body text-center">
                         <div class="d-flex flex-column align-items-center">

                             {{-- ✅ LOGIKA BARU: Cek langsung file logo.png di folder public --}}
                             @php
                                 $encodedName = urlencode($profile->name ?? 'UPT');
                                 $uiAvatar = "https://ui-avatars.com/api/?name={$encodedName}&background=random&color=fff&size=150&bold=true&rounded=true";

                                 // Cek apakah file fisik logo.png ada di folder public/
                                 $logoPath = public_path('logo.png');
                                 $avatarSrc = file_exists($logoPath)
                                                 ? asset('logo.png') . '?v=' . time()
                                                 : $uiAvatar;
                             @endphp

                             <img src="{{ $avatarSrc }}" alt="logo-upt"
                                class="d-block w-px-150 h-px-150 rounded-circle mb-4 object-fit-contain shadow-sm border border-5 border-light bg-white" id="uploadedLogo" />

                             @if(Auth::user()->role !== 'leader')
                             <div class="d-flex justify-content-center gap-3 mb-3">
                                <label for="logo-upload" class="btn btn-primary btn-sm" tabindex="0">
                                    <span class="d-none d-sm-block">Pilih Logo</span>
                                    <i class="icon-base ti tabler-upload d-sm-none"></i>
                                    <input type="file" id="logo-upload" name="logo" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary btn-sm account-image-reset" data-default="{{ $avatarSrc }}">
                                    <span class="d-none d-sm-block">Reset</span>
                                     <i class="icon-base ti tabler-refresh d-sm-none"></i>
                                </button>
                            </div>
                            @endif

                            <div class="w-100 px-3 mb-2">
                                <div class="progress" id="compressionProgress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div id="compressionStatus" class="text-sm text-muted mt-1"></div>
                            </div>

                            <div id="logo-error" class="text-danger text-sm text-center"></div>
                            <p class="text-muted mb-0 mt-2">Hanya JPG/PNG.<br><small>Otomatis dikompres &lt; 50Kb dan menimpa Favicon.</small></p>
                         </div>
                    </div>
                    @if(Auth::user()->role !== 'leader')
                    <div class="card-footer text-center border-top">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ti tabler-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    {{-- Scripts untuk Editor & Image Compression --}}
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    {{-- Library kompresi gambar --}}
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Konfigurasi Quill Editors ---
            const quillOptions = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            };

            // Inisialisasi editor
            const aboutUsQuill = new Quill('#about_us_editor', quillOptions);
            const privacyPolicyQuill = new Quill('#privacy_policy_editor', quillOptions);

            // Handler submit form: Pindahkan konten Quill ke input hidden
            const form = document.getElementById('profileForm');
            form.onsubmit = function() {
                // Ambil HTML dari quill dan masukkan ke input hidden
                document.getElementById('about_us_input').value = aboutUsQuill.root.innerHTML;
                document.getElementById('privacy_policy_input').value = privacyPolicyQuill.root.innerHTML;
                return true; // Lanjutkan submit
            };


            // --- 2. Logika Kompresi Gambar & Preview ---
            const uploadInput = document.getElementById('logo-upload');
            const uploadedLogo = document.getElementById('uploadedLogo');
            const resetButton = document.querySelector('.account-image-reset');
            const errorDiv = document.getElementById('logo-error');
            const progressDiv = document.getElementById('compressionProgress');
            const progressBar = progressDiv.querySelector('.progress-bar');
            const statusDiv = document.getElementById('compressionStatus');

            // Helper untuk format ukuran file
            function formatBytes(bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'Kb', 'Mb'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            if (uploadInput && uploadedLogo && resetButton) {

                uploadInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) return;

                    // Reset status
                    errorDiv.textContent = '';
                    statusDiv.textContent = '';
                    progressBar.style.width = '0%';
                    progressBar.setAttribute('aria-valuenow', 0);

                    // Validasi tipe file
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya file JPG atau PNG yang diperbolehkan.';
                        uploadInput.value = ''; // Reset input
                        return;
                    }

                    const originalSize = imageFile.size;
                    const originalSizeStr = formatBytes(originalSize);

                    // Tampilkan progress bar
                    progressDiv.style.display = 'flex';
                    statusDiv.textContent = `Sedang mengkompres (${originalSizeStr})...`;

                    // ✅ Opsi Kompresi: Target di bawah 50Kb (kita set 0.048Mb agar aman)
                    const options = {
                        maxSizeMB: 0.048, // Target ~49Kb
                        maxWidthOrHeight: 600, // Kecilkan resolusi dikit biar enteng
                        useWebWorker: true,
                        // Callback progress
                        onProgress: (progress) => {
                            progressBar.style.width = `${progress}%`;
                            progressBar.setAttribute('aria-valuenow', progress);
                        }
                    }

                    try {
                        // Proses kompresi
                        const compressedFile = await imageCompression(imageFile, options);

                        const compressedSize = compressedFile.size;
                        const compressedSizeStr = formatBytes(compressedSize);

                        // Update status sukses
                        statusDiv.innerHTML = `<span class="text-success fw-bold">${originalSizeStr} → ${compressedSizeStr}</span> (Selesai)`;

                        // Buat data transfer baru untuk mengganti file di input file
                        // Agar file yang dikirim ke server adalah file yang sudah dikompresi
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        uploadInput.files = dataTransfer.files;

                        // Ganti preview gambar
                        uploadedLogo.src = URL.createObjectURL(compressedFile);

                    } catch (error) {
                        console.error(error);
                        errorDiv.textContent = "Gagal mengkompres gambar. Pastikan file valid.";
                        statusDiv.textContent = '';
                        progressDiv.style.display = 'none';
                        uploadInput.value = '';
                    }
                });

                // Handler tombol reset
                resetButton.addEventListener('click', function() {
                    // Ambil default avatar dari data attribute
                    uploadedLogo.src = this.getAttribute('data-default');
                    uploadInput.value = ''; // Reset input file
                    errorDiv.textContent = '';
                    statusDiv.textContent = '';
                    progressDiv.style.display = 'none';
                });
            }
        });
    </script>
@endsection
