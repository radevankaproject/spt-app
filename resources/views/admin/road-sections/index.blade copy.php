@extends('layouts.app')

@section('title', 'Manajemen Ruas Jalan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Ruas Jalan</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Ruas Jalan</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Ruas Jalan --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Ruas Jalan</h5>
                <p class="text-muted mb-0">Total {{ $roadSections->total() }} ruas jalan terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                <form action="{{ route('masterdata.road-sections.index') }}" method="GET"
                    class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama ruas jalan..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="icon-base ri ri-search-line"></i></button>
                    </div>
                </form>
                <a href="{{ route('masterdata.road-sections.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah Ruas Jalan
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                {{-- Notifikasi akan ditangani oleh SweetAlert2 --}}
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Nama Ruas Jalan</th>
                            {{-- ✅ PERUBAHAN 1: Tambahkan Kolom Zona --}}
                            <th scope="col">Zona</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($roadSections as $roadSection)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $roadSection->name }}</span>
                                </td>
                                <td>
                                    {{-- Tampilkan data zona dengan badge --}}
                                    <span class="badge rounded-pill bg-label-info">{{ $roadSection->zone }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        {{-- ✅ PERUBAHAN 2: Tombol Hapus Dihilangkan --}}
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.road-sections.edit', $roadSection->id) }}"
                                            data-bs-toggle="tooltip" title="Edit Ruas Jalan">
                                            <i class="icon-base ri ri-pencil-line icon-22px"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Sesuaikan colspan menjadi 3 --}}
                                <td colspan="3" class="text-center">Tidak ada data ruas jalan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $roadSections->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Notifikasi Sukses setelah Create/Update
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif
        });
    </script>
@endpush
