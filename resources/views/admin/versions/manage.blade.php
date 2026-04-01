@extends('layouts.app')

@section('title', 'Manajemen Versi Aplikasi')

@section('skeleton')
    @include('layouts.partials._skeleton-versions-manage')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Versi Aplikasi</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Manajemen Versi</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-6">
        <!-- Kolom Kiri: Form Input -->
        <div class="col-lg-5">
            @if(Auth::user()->role !== 'leader')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Versi Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.app-versions.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="version" name="version"
                                        placeholder="Contoh: v1.0.1" value="{{ old('version') }}" required />
                                    <label for="version">Nomor Versi</label>
                                </div>
                                @error('version')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" id="release_date" name="release_date"
                                        value="{{ old('release_date', date('Y-m-d')) }}" required />
                                    <label for="release_date">Tanggal Rilis</label>
                                </div>
                                @error('release_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label class="fw-bold text-dark">Catatan Perubahan (Changelog)</label>
                                </div>
                                <div class="border rounded">
                                    <div id="editor-container" style="height: 200px;">{!! old('changelog') !!}</div>
                                </div>
                                <input type="hidden" id="changelog" name="changelog" required>
                                <div class="form-text">Sebutkan penambahan fitur atau perbaikan *bug*.</div>
                                @error('changelog')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="pt-6 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Versi</button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                     <i class="ri ri-lock-line ri-3x text-muted mb-3"></i>
                     <h6>Akses Dibatasi</h6>
                     <p class="text-muted mb-0">Anda hanya dapat melihat riwayat versi aplikasi.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Daftar Versi -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">5 Versi Terakhir</h5>
                </div>
                <div class="card-body">
                    @forelse ($versions as $version)
                        <div class="mb-4 pb-4 @if (!$loop->last) border-bottom @endif">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1"><strong>Versi {{ $version->version }}</strong></h6>
                                <small class="text-muted">{{ $version->release_date->translatedFormat('d F Y') }}</small>
                            </div>
                            <div class="mt-3 text-dark" style="font-size: 0.95rem;">
                                {!! $version->changelog !!}
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada data versi yang ditambahkan.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $versions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 200px; }
    .ql-editor ul {
       padding-left: 1.5rem !important;
       list-style-type: disc !important;
    }
    .ql-editor ol {
       padding-left: 1.5rem !important;
       list-style-type: decimal !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        var form = document.querySelector('form');
        if (form) {
            form.onsubmit = function() {
                var changelogInput = document.querySelector('input[name=changelog]');
                changelogInput.value = quill.root.innerHTML;
            };
        }
    });
</script>
@endpush
