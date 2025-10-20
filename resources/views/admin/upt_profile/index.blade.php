@extends('layouts.app')

@section('title', 'Profil UPT Perparkiran')

{{-- ✅ Panggil Skeleton Loader --}}
@section('skeleton')
    @include('layouts.partials._skeleton-upt-profile')
@endsection

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

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
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

    <form action="{{ route('admin.upt-profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-6">

            {{-- ✅ KOLOM KIRI: FORMULIR UTAMA --}}
            <div class="col-lg-8">
                <div class="card">
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
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Nomor Telepon" value="{{ old('phone', $profile->phone) }}" />
                                    <label for="phone">Nomor Telepon</label>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                    <textarea class="form-control" id="address" name="address" placeholder="Alamat lengkap UPT" style="height: 120px;">{{ old('address', $profile->address) }}</textarea>
                                    <label for="address">Alamat</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </div>

            {{-- ✅ KOLOM KANAN: UPLOAD LOGO --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Logo Instansi</h5>
                    </div>
                    <div class="card-body text-center">
                         <div class="d-flex flex-column align-items-center">
                             @if ($profile->logo && Storage::disk('public')->exists($profile->logo))
                                <img src="{{ asset('storage/' . $profile->logo) }}?v={{ time() }}" alt="logo-upt"
                                    class="d-block w-px-150 h-px-150 rounded-3 mb-4 object-fit-contain" id="uploadedLogo" />
                            @else
                                <img src="{{ asset('assets/img/illustrations/image-light.png') }}" alt="logo-upt"
                                    class="d-block w-px-150 h-px-150 rounded-3 mb-4" id="uploadedLogo" />
                            @endif

                             <div class="d-flex justify-content-center gap-3 mb-3">
                                <label for="logo-upload" class="btn btn-primary" tabindex="0">
                                    <span class="d-none d-sm-block">Pilih Logo</span>
                                    <i class="icon-base ri-upload-2-line d-sm-none"></i>
                                    <input type="file" id="logo-upload" name="logo" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset">
                                    <span class="d-none d-sm-block">Reset</span>
                                     <i class="icon-base ri-refresh-line d-sm-none"></i>
                                </button>
                            </div>
                            <div id="logo-error" class="text-danger text-sm text-center"></div>
                            <p class="text-muted mb-0">Hanya JPG/PNG. Maks 512KB.</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    {{-- Library kompresi gambar --}}
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadInput = document.getElementById('logo-upload');
            const uploadedLogo = document.getElementById('uploadedLogo');
            const resetButton = document.querySelector('.account-image-reset');
            const errorDiv = document.getElementById('logo-error');
            const defaultLogo = uploadedLogo.src;

            if (uploadInput && uploadedLogo && resetButton) {
                uploadInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) return;

                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya file JPG atau PNG.';
                        uploadInput.value = '';
                        return;
                    }

                    const options = {
                        maxSizeMB: 0.5, // Maks 512KB
                        maxWidthOrHeight: 800,
                        useWebWorker: true,
                    }
                    try {
                        const compressedFile = await imageCompression(imageFile, options);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        uploadInput.files = dataTransfer.files;
                        uploadedLogo.src = URL.createObjectURL(compressedFile);
                    } catch (error) {
                        errorDiv.textContent = "Gagal mengkompres gambar.";
                        uploadInput.value = '';
                    }
                });

                resetButton.addEventListener('click', () => {
                    uploadedLogo.src = defaultLogo;
                    uploadInput.value = '';
                    errorDiv.textContent = '';
                });
            }
        });
    </script>
@endpush
