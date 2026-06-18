    @extends('layouts.contentNavbarLayout')

    @section('title', 'Arsip Pimpinan')

    @section('content')
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Arsip Pimpinan</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.leaders.index') }}">Pimpinan</a></li>
                        <li class="breadcrumb-item active">Arsip</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between gap-4">
                <div class="card-title mb-0">
                    <h5 class="mb-1">Daftar Pimpinan yang Dihapus</h5>
                    <p class="text-muted mb-0">Total {{ $leaders->total() }} pimpinan di dalam arsip.</p>
                </div>
                <div class="d-flex justify-content-md-end align-items-center">
                    <a href="{{ route('admin.leaders.index') }}" class="btn btn-outline-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>Kembali ke Daftar Pimpinan
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
                                <th>Nama Pimpinan</th>
                                <th>NIP</th>
                                <th>Tanggal Mulai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($leaders as $leader)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="avatar-wrapper me-4">
                                                <div class="avatar avatar-sm">
                                                    <img src="{{ $leader->user && $leader->user->img ? asset('storage/' . $leader->user->img) : asset('assets/img/avatars/1.png') }}"
                                                        alt="Avatar" class="rounded-circle">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $leader->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $leader->employee_number }}</td>
                                    <td>{{ $leader->start_date->translatedFormat('d F Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.leaders.restore', $leader->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                title="Pulihkan Data">
                                                <i class="icon-base ti tabler-history me-1"></i>Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data pimpinan di dalam arsip.</td>
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
