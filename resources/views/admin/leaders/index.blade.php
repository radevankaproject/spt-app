@extends('layouts.app')

@section('title', 'Manajemen Leader')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Leader</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Leaders</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Leader --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Leader</h5>
                <p class="text-muted mb-0">Total {{ $leaders->total() }} leader terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                {{-- Form Pencarian --}}
                <form action="{{ route('admin.leaders.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="search" name="search" class="form-control" placeholder="Cari..."
                        value="{{ request('search') }}">
                </form>
                {{-- Tombol Tambah --}}
                <a href="{{ route('admin.leaders.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah Leader
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
                            <th>Nama Leader</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($leaders as $leader)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper me-4">
                                            <div class="avatar avatar-sm">
                                                @if ($leader->user && $leader->user->img)
                                                    <img src="{{ asset('storage/' . $leader->user->img) }}" alt="Avatar"
                                                        class="rounded-circle">
                                                @else
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($leader->user->name ?? 'L', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $leader->user->name ?? 'N/A' }}</span>
                                            <small
                                                class="text-muted">{{ '@' . ($leader->user->username ?? 'N/A') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $leader->user->email ?? 'N/A' }}</td>
                                <td><span class="fw-medium">{{ $leader->employee_number }}</span></td>
                                <td><span class="badge bg-label-success">Aktif</span></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('admin.leaders.edit', $leader->id) }}" data-bs-toggle="tooltip"
                                            title="Edit Leader">
                                            <i class="icon-base ri ri-pencil-line icon-22px"></i>
                                        </a>
                                        <form action="{{ route('admin.leaders.destroy', $leader->id) }}" method="POST"
                                            class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                title="Hapus Leader">
                                                <i class="icon-base ri ri-delete-bin-line icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data leader.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leaders->appends(['search' => request('search')])->links() }}
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
                        text: "Data leader yang dihapus akan memengaruhi data terkait lainnya!",
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
