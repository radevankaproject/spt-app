@extends('layouts.app')

@section('title', 'Manajemen Koordinator Lapangan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Koordinator Lapangan</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Koordinator Lapangan</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Koordinator --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Koordinator Lapangan</h5>
                <p class="text-muted mb-0">Total {{ $fieldCoordinators->total() }} korlap terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                {{-- Form Pencarian --}}
                <form action="{{ route('admin.field-coordinators.index') }}" method="GET"
                    class="d-flex align-items-center">
                    <input type="search" name="search" class="form-control" placeholder="Cari nama/NIK..."
                        value="{{ request('search') }}">
                </form>
                {{-- <a href="{{ route('admin.field-coordinators.trashed') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ri ri-archive-line me-2"></i>Lihat Arsip
                </a> --}}
                {{-- Tombol Tambah --}}
                <a href="{{ route('admin.field-coordinators.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah Korlap
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Koordinator</th>
                            <th>Kontak</th>
                            <th>NIK</th>
                            <th>Perjanjian Aktif</th> {{-- ✅ Kolom Baru --}}
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($fieldCoordinators as $coordinator)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper me-4">
                                            <div class="avatar avatar-sm">
                                                @if ($coordinator->user && $coordinator->user->img && file_exists(public_path($coordinator->user->img)))
                                                    <img src="{{ asset('storage/' . $coordinator->user->img) }}"
                                                        alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-warning">{{ strtoupper(substr($coordinator->user->name ?? 'K', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $coordinator->user->name ?? 'N/A' }}</span>
                                            <small
                                                class="text-muted">{{ '@' . ($coordinator->user->username ?? 'N/A') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="d-block">{{ $coordinator->user->email ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $coordinator->phone_number ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td><span class="fw-medium">{{ $coordinator->id_card_number }}</span></td>
                                <td>
                                    {{-- ✅ Logika untuk menampilkan Info PKS --}}
                                    @if ($coordinator->agreements->isNotEmpty())
                                        @php $activeAgreement = $coordinator->agreements->first(); @endphp
                                        <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}"
                                            class="badge bg-label-primary">
                                            {{ $activeAgreement->agreement_number }}
                                        </a>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Ada PKS</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('admin.field-coordinators.show', $coordinator->id) }}"
                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="icon-base ri ri-eye-line icon-22px"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('admin.field-coordinators.edit', $coordinator->id) }}"
                                            data-bs-toggle="tooltip" title="Edit Koordinator">
                                            <i class="icon-base ri ri-pencil-line icon-22px"></i>
                                        </a>
                                        <form action="{{ route('admin.field-coordinators.destroy', $coordinator->id) }}"
                                            method="POST" class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                title="Hapus Koordinator">
                                                <i class="icon-base ri ri-delete-bin-line icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data koordinator.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $fieldCoordinators->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Notifikasi Sukses
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Konfirmasi Hapus
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data koordinator yang dihapus akan memengaruhi data PKS terkait!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });
        });
    </script>
@endpush
