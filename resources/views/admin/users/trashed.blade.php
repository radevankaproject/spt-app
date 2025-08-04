@extends('layouts.app')

@section('title', 'Arsip User')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Arsip User</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Arsip</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar User yang Dihapus</h5>
                <p class="text-muted mb-0">Total {{ $users->total() }} user di dalam arsip.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-2"></i>Kembali ke Daftar User
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
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
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
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-secondary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <small class="text-muted">{{ '@' . $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge rounded-pill bg-label-secondary">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                            title="Aktifkan Kembali">
                                            <i class="icon-base ri ri-history-line me-1"></i>Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data user di dalam arsip.</td>
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
