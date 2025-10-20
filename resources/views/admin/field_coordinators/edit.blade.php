@extends('layouts.app')

@section('title', 'Edit Koordinator: ' . $fieldCoordinator->user->name)

@section('skeleton')
    @include('layouts.partials._skeleton-field-coordinators-form')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Koordinator: {{ $fieldCoordinator->user->name }}</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.field-coordinators.index') }}">Korlap</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

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

    <form action="{{ route('admin.field-coordinators.update', $fieldCoordinator->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row g-6">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Pribadi Koordinator</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Field NAMA (dari tabel User) --}}
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Masukkan Nama Lengkap"
                                        value="{{ old('name', $fieldCoordinator->user->name) }}" required />
                                    <label for="name">Nama Lengkap</label>
                                </div>
                            </div>
                            {{-- Field lainnya (dari tabel FieldCoordinator) --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="id_card_number" name="id_card_number"
                                        placeholder="Masukkan 16 digit Nomor KTP"
                                        value="{{ old('id_card_number', $fieldCoordinator->id_card_number) }}"
                                        maxlength="16" required />
                                    <label for="id_card_number">Nomor KTP</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        placeholder="Contoh: 08123456789"
                                        value="{{ old('phone_number', $fieldCoordinator->phone_number) }}" required />
                                    <label for="phone_number">Nomor Telepon/HP</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="address" name="address" placeholder="Masukkan alamat lengkap sesuai KTP"
                                        style="height: 100px;">{{ old('address', $fieldCoordinator->address) }}</textarea>
                                    <label for="address">Alamat Lengkap</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="position" name="position"
                                        value="{{ old('position', $fieldCoordinator->position) }}" required />
                                    <label for="position">Posisi / Jabatan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Foto Profil (Opsional)</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $fieldCoordinator->user && $fieldCoordinator->user->img ? asset('storage/' . $fieldCoordinator->user->img) : asset('assets/img/avatars/1.png') }}"
                            alt="user-avatar" class="d-block w-px-120 h-px-120 rounded-circle mx-auto mb-4"
                            id="avatar-preview" />
                        <label for="img-upload" class="btn btn-primary">
                            <i class="icon-base ri-upload-2-line me-2"></i>Ubah Foto
                            <input type="file" id="img-upload" name="img" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                        <div id="img-error" class="mt-2 text-danger text-sm"></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Foto KTP (Wajib)</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $fieldCoordinator->id_card_img ? asset('storage/' . $fieldCoordinator->id_card_img) : asset('assets/img/illustrations/image-light.png') }}"
                            alt="id-card-placeholder" class="d-block rounded-3 mx-auto mb-4" id="idcard-preview"
                            style="max-height: 120px;" />
                        <label for="idcard-upload" class="btn btn-primary">
                            <i class="icon-base ri-upload-2-line me-2"></i>Ubah Foto KTP
                            <input type="file" id="idcard-upload" name="id_card_img" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                        <div id="idcard-error" class="mt-2 text-danger text-sm"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end mt-4">
                <a href="{{ route('admin.field-coordinators.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Logika untuk validasi input ---
            const idCardInput = document.getElementById('id_card_number');
            const phoneInput = document.getElementById('phone_number');
            if (idCardInput) {
                idCardInput.addEventListener('input', e => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 16);
                });
            }
            if (phoneInput) {
                phoneInput.addEventListener('input', e => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 15);
                });
            }

            // --- Logika untuk upload & kompresi gambar ---
            function setupImageUploader(inputId, previewId, errorId, defaultSrc) {
                const fileInput = document.getElementById(inputId);
                const imagePreview = document.getElementById(previewId);
                const errorDiv = document.getElementById(errorId);
                if (!fileInput) return;
                fileInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) {
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya file JPG atau PNG.';
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    const options = {
                        maxSizeMB: 0.3,
                        maxWidthOrHeight: 1024,
                        useWebWorker: true
                    };
                    try {
                        const compressedFile = await imageCompression(imageFile, options);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                            type: compressedFile.type
                        }));
                        fileInput.files = dataTransfer.files;
                        imagePreview.src = URL.createObjectURL(compressedFile);
                    } catch (error) {
                        errorDiv.textContent = "Gagal memproses gambar.";
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                    }
                });
            }
            setupImageUploader('img-upload', 'avatar-preview', 'img-error',
                "{{ $fieldCoordinator->user && $fieldCoordinator->user->img && file_exists(public_path($fieldCoordinator->user->img)) ? asset($fieldCoordinator->user->img) : asset('assets/img/avatars/1.png') }}"
            );
            setupImageUploader('idcard-upload', 'idcard-preview', 'idcard-error',
                "{{ $fieldCoordinator->id_card_img && file_exists(public_path($fieldCoordinator->id_card_img)) ? asset($fieldCoordinator->id_card_img) : asset('assets/img/illustrations/image-light.png') }}"
            );
        });
    </script>
@endpush
