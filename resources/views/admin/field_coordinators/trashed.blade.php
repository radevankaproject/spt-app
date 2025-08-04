@extends('layouts.app')

@section('title', 'Arsip Koordinator Lapangan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Arsip Koordinator Lapangan</h4>
    <div class="d-flex align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.field-coordinators.index') }}">Korlap</a></li>
                <li class="breadcrumb-item active">Arsip</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        <div class="card-title mb-0">
            <h5 class="mb-1">Daftar Korlap yang Dihapus</h5>
            <p class="text-muted mb-0">Total {{ $coordinators->total() }} korlap di dalam arsip.</p>
        </div>
        <div class="d-flex justify-content-md-end align-items-center">
            <a href="{{ route('admin.field-coordinators.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>Kembali ke Daftar Korlap
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
                        <th>Nama Korlap</th>
                        <th>NIK</th>
                        <th>No. Telepon</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($coordinators as $coordinator)
                    <tr>
                        <td>
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar-wrapper me-4">
                                    <div class="avatar avatar-sm">
                                        <img src="{{ $coordinator->user && $coordinator->user->img ? asset('storage/' . $coordinator->user->img) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $coordinator->user->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $coordinator->id_card_number }}</td>
                        <td>{{ $coordinator->phone_number }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.field-coordinators.restore', $coordinator->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Pulihkan Data">
                                    <i class="icon-base ri ri-history-line me-1"></i>Restore
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data korlap di dalam arsip.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $coordinators->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection