@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Data Jukir')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <a href="{{ route('admin.jukirs.index') }}" class="btn btn-sm btn-light text-primary rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Kembali
                </a>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-user-edit me-2"></i>Edit Data Jukir</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Perbarui data identitas dan status aktif Juru Parkir.</p>
            </div>
        </div>
        <i class="ti tabler-user-edit position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <div class="glass-card anim-2 border-0 overflow-hidden mb-4 p-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                <i class="ti tabler-alert-triangle me-1"></i> Periksa kembali isian Anda
                <ul class="mb-0 mt-2 text-sm fw-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.jukirs.update', $jukir->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-12 text-center mb-3">
                    <div class="d-flex flex-column align-items-center">
                        @if($jukir->image)
                            <img src="{{ Storage::url($jukir->image) }}" alt="Foto Jukir" class="rounded-circle mb-3 border border-3 border-primary shadow-sm" width="120" height="120" style="object-fit: cover;">
                        @else
                            <div class="avatar rounded-circle bg-label-secondary d-flex align-items-center justify-content-center fw-bold text-secondary mb-3 shadow-sm" style="width: 120px; height: 120px; font-size: 2.5rem;">
                                {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                            </div>
                        @endif
                        <label for="imageUpload" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="ti tabler-upload me-1"></i> Ganti Foto
                        </label>
                        <input type="file" id="imageUpload" name="image" accept="image/*" class="d-none">
                        <small class="text-muted mt-2">Maks. 2MB (JPG/PNG)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" name="nama_jukir" value="{{ old('nama_jukir', $jukir->nama_jukir) }}" placeholder="Nama Jukir" required />
                        <label>Nama Jukir <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" value="{{ $jukir->parkingLocation->name ?? '-' }}" disabled readonly />
                        <label>Titik Parkir</label>
                    </div>
                    <small class="text-muted">Titik parkir tidak dapat diubah dari menu ini.</small>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" name="no_ktp" value="{{ old('no_ktp', $jukir->no_ktp) }}" placeholder="Nomor KTP" />
                        <label>Nomor KTP</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $jukir->phone_number) }}" placeholder="Nomor HP/WA" />
                        <label>Nomor HP / WhatsApp</label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $jukir->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">Status Aktif</label>
                    </div>
                    <small class="text-muted">Jika nonaktif, jukir tidak akan muncul di daftar pilihan titik parkir.</small>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('admin.jukirs.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill btn-action">
                    <i class="ti tabler-device-floppy me-1"></i> Update Data
                </button>
            </div>
        </form>
    </div>
@endsection
