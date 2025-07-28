@extends('layouts.app')

@section('title', 'Profil UPT Perparkiran')

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Profil UPT Perparkiran</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Profil UPT</li>
                </ol>
            </nav>
        </div>
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

    <div class="card">
        <form action="{{ route('admin.upt-profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-6">
                    {{-- Tampilan Logo --}}
                    @if ($profile->logo && file_exists(public_path($profile->logo)))
                        <img src="{{ asset($profile->logo) }}?v={{ time() }}" alt="logo-upt"
                            class="d-block w-px-120 h-px-120 rounded-3" id="uploadedLogo" />
                    @else
                        <img src="{{ asset('assets/img/illustrations/image-light.png') }}" alt="logo-upt"
                            class="d-block w-px-120 h-px-120 rounded-3" id="uploadedLogo" />
                    @endif

                    <div class="button-wrapper">
                        <label for="upload" class="btn btn-primary me-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload Logo Baru</span>
                            <i class="icon-base ri-upload-2-line d-sm-none"></i>
                            <input type="file" id="upload" name="logo" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                        <p class="text-muted mt-3 mb-0">Hanya JPG atau PNG. Ukuran maks 512KB.</p>
                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <div class="row g-6">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="app_name" name="app_name"
                                placeholder="Nama Website" value="{{ old('app_name', $profile->app_name) }}" required />
                            <label for="app_name">Nama Website</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama UPT"
                                value="{{ old('name', $profile->name) }}" required />
                            <label for="name">Nama UPT</label>
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
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                value="{{ old('email', $profile->email) }}" />
                            <label for="email">Alamat Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="url" class="form-control" id="website" name="website"
                                placeholder="https://contoh.com" value="{{ old('website', $profile->website) }}" />
                            <label for="website">Website</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" placeholder="Alamat lengkap UPT" style="height: 100px;">{{ old('address', $profile->address) }}</textarea>
                            <label for="address">Alamat</label>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Script untuk menampilkan preview gambar logo yang di-upload
        document.addEventListener('DOMContentLoaded', function() {
            const uploadInput = document.getElementById('upload');
            const uploadedLogo = document.getElementById('uploadedLogo');

            if (uploadInput && uploadedLogo) {
                uploadInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            uploadedLogo.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
