@extends('layouts.app')

@section('title', 'Manajemen Perjanjian Kerjasama')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-index')
@endsection

@push('styles')
    {{-- CSS untuk SweetAlert2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Perjanjian Kerjasama</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">PKS</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Perjanjian --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Perjanjian</h5>
                <p class="text-muted mb-0">Total {{ $agreements->total() }} perjanjian terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                {{-- Form Pencarian --}}
                <form action="{{ route('masterdata.agreements.index') }}" method="GET" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari No PKS/Korlap..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="icon-base ri ri-search-line"></i></button>
                    </div>
                </form>
                {{-- Tombol Tambah --}}
                <a href="{{ route('masterdata.agreements.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah PKS
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nomor PKS</th>
                            <th>Koordinator Lapangan</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($agreements as $agreement)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $agreement->agreement_number }}</span>
                                    <small class="d-block text-muted">Pimpinan:
                                        {{ $agreement->leader->user->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar-wrapper me-3">
                                            <div class="avatar avatar-sm">
                                                @if ($agreement->fieldCoordinator->user->img)
                                                    <img src="{{ asset('storage/' . $agreement->fieldCoordinator->user->img) }}"
                                                        alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-warning">{{ strtoupper(substr($agreement->fieldCoordinator->user->name ?? 'K', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span>{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span
                                            class="d-block">{{ $agreement->start_date->translatedFormat('d M Y') }}</span>
                                        <small class="text-muted">s/d
                                            {{ $agreement->end_date->translatedFormat('d M Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-label-secondary'; // Default
                                        if ($agreement->status == 'active') {
                                            $statusClass = 'bg-label-success';
                                        }
                                        if ($agreement->status == 'expired') {
                                            $statusClass = 'bg-label-danger';
                                        }
                                        if ($agreement->status == 'terminated') {
                                            $statusClass = 'bg-label-dark';
                                        }
                                        if ($agreement->status == 'pending_renewal') {
                                            $statusClass = 'bg-label-warning';
                                        }
                                    @endphp
                                    <span
                                        class="badge rounded-pill {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $agreement->status)) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.agreements.show', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="icon-base ri ri-eye-line"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.agreements.edit', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="icon-base ri ri-pencil-line"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.agreements.pdf', $agreement->id) }}" target="_blank"
                                            data-bs-toggle="tooltip" title="Cetak PKS">
                                            <i class="icon-base ri ri-printer-line"></i>
                                        </a>
                                        <form action="{{ route('masterdata.agreements.destroy', $agreement->id) }}"
                                            method="POST" class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                title="Hapus">
                                                <i class="icon-base ri ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data perjanjian ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $agreements->appends(['search' => request('search')])->links() }}
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
                    timer: 2500
                });
            @endif

            // Konfirmasi Hapus
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data perjanjian yang dihapus akan membebaskan lokasi parkir terkait.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6f6b7d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
