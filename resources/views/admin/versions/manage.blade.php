@extends('layouts.app')

@section('title', 'Manajemen Versi Aplikasi')

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
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="changelog" name="changelog" placeholder="Masukkan catatan perubahan di sini..."
                                        style="height: 200px;" required>{{ old('changelog') }}</textarea>
                                    <label for="changelog">Catatan Perubahan (Changelog)</label>
                                </div>
                                <div class="form-text">
                                    Gunakan format Markdown sederhana. Awali setiap poin dengan tanda hubung (`- `).
                                </div>
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
                            <ul class="list-unstyled mt-2 mb-0 ps-3">
                                @foreach (explode("\n", $version->changelog) as $item)
                                    @if (trim($item))
                                        <li>{{ trim(str_replace('- ', '', $item)) }}</li>
                                    @endif
                                @endforeach
                            </ul>
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
