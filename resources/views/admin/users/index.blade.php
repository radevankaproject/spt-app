@extends('layouts.app')

@section('title', 'Manajemen User')

{{-- 1. Tambahkan CSS SweetAlert2 --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen User</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar User (Admin & Staff)</h5>
                <p class="text-muted mb-0">Total {{ $users->total() }} user ditemukan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="search" name="search" class="form-control" placeholder="Cari nama/email..."
                        value="{{ request('search') }}">
                </form>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah User
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper me-4">
                                            <div class="avatar avatar-sm">
                                                @if ($user->img)
                                                    <img src="{{ asset('storage/' . $user->img) }}" alt="Avatar"
                                                        class="rounded-circle">
                                                @else
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-secondary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <small class="text-muted">{{ '@' . $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $role = $user->role;
                                        $colorClass = $role == 'admin' ? 'bg-label-danger' : 'bg-label-info';
                                    @endphp
                                    <span class="badge rounded-pill {{ $colorClass }}">{{ ucfirst($role) }}</span>
                                </td>
                                <td><span class="badge bg-label-success">Aktif</span></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="btn btn-sm btn-icon" href="{{ route('admin.users.edit', $user->id) }}"
                                            data-bs-toggle="tooltip" title="Edit User">
                                            <i class="icon-base ri ri-pencil-line icon-22px"></i>
                                        </a>
                                        {{-- ✅ PERUBAHAN 2: Tambahkan class 'form-delete' --}}
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                title="Hapus User">
                                                <i class="icon-base ri ri-delete-bin-line icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- ✅ PERUBAHAN 3: Tambahkan JS SweetAlert2 dan logika pemicunya --}}
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

            // Konfirmasi Hapus
            const deleteForms = document.querySelectorAll('.form-delete');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Mencegah form dikirim langsung

                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Jika dikonfirmasi, kirim form
                        }
                    })
                });
            });
        });
    </script>
@endpush
