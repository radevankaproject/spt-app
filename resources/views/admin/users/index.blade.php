@extends('layouts.contentNavbarLayout')

@section('title', 'Manajemen User')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
    {{-- ============================================= --}}
    {{-- HERO HEADER --}}
    {{-- ============================================= --}}
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="backdrop-filter: blur(5px);">
                        Total: {{ $users->total() }} User
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-users me-2"></i>Manajemen User Admin & Staff</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola hak akses pengguna, staff, dan koordinator lapangan.</p>
            </div>
        </div>
        <i class="ti tabler-users position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    {{-- Daftar User --}}
    <div class="glass-card overflow-hidden anim-2">
        <div class="card-header p-4 d-flex flex-wrap justify-content-between align-items-center gap-4 border-bottom">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Pengguna Aktif</h5>
                <p class="text-muted mb-0">Menampilkan {{ $users->count() }} dari total {{ $users->total() }} user.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex align-items-center">
                    <div class="input-group input-group-merge shadow-sm" style="border-radius: 50rem; overflow: hidden; height: 38px;">
                        <input type="search" name="search" class="form-control border-0 px-3 bg-white" placeholder="Cari nama/email..." value="{{ request('search') }}">
                        <span class="input-group-text border-0 bg-white pe-3" style="cursor: pointer;" onclick="this.closest('form').submit()">
                            <i class="ti tabler-search text-primary"></i>
                        </span>
                    </div>
                </form>
                @if(Auth::user()->role !== 'leader')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill shadow-sm btn-action">
                    <i class="icon-base ti tabler-plus me-1"></i> Tambah User
                </a>
                @endif
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">User Info</th>
                        <th class="text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Email</th>
                        <th class="text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Role</th>
                        <th class="text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Status</th>
                        <th class="text-center pe-4 text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                        <tr class="premium-row">
                            <td class="ps-4">
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper me-3">
                                        <div class="avatar avatar-md bg-transparent">
                                            @if ($user->img)
                                                <img src="{{ asset('storage/' . $user->img) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; background: transparent;">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $user->name }}</span>
                                        <small class="text-muted"><i class="ti tabler-at me-1" style="font-size: 0.8rem;"></i>{{ $user->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box-sm bg-label-secondary me-2 rounded d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti tabler-mail text-secondary" style="font-size: 1rem;"></i>
                                    </div>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $role = $user->role;
                                    $colorClass = $role == 'admin' ? 'bg-danger' : ($role == 'leader' ? 'bg-warning' : 'bg-info');
                                @endphp
                                <span class="badge rounded-pill shadow-sm {{ $colorClass }} px-3 py-2 fw-bold text-white">
                                    <i class="ti tabler-shield-check me-1"></i> {{ ucfirst($role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-label-success px-3 py-2 fw-bold">
                                    <span class="badge-dot bg-success me-1"></span> Aktif
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a class="btn btn-sm btn-icon btn-label-info rounded-circle btn-action" href="{{ route('admin.users.show', $user->id) }}" data-bs-toggle="tooltip" title="Lihat Profil">
                                        <i class="ti tabler-eye icon-20px"></i>
                                    </a>
                                    @if(Auth::user()->role !== 'leader')
                                    <a class="btn btn-sm btn-icon btn-label-primary rounded-circle btn-action" href="{{ route('admin.users.edit', $user->id) }}" data-bs-toggle="tooltip" title="Edit User">
                                        <i class="ti tabler-pencil icon-20px"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="form-delete d-inline-block m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger rounded-circle btn-action delete-btn" data-bs-toggle="tooltip" title="Hapus User">
                                            <i class="ti tabler-trash icon-20px"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="icon-glass bg-label-secondary mb-3">
                                        <i class="ti tabler-users text-secondary opacity-50" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Data User</h6>
                                    <p class="text-muted small mb-0">Belum ada user yang ditambahkan atau sesuai pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="card-footer border-top bg-transparent p-4">
            <div class="d-flex justify-content-center">
                {{ $users->appends(['search' => request('search')])->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            @endif

            const deleteBtns = document.querySelectorAll('.delete-btn');
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4 shadow-lg',
                            confirmButton: 'btn btn-danger rounded-pill px-4 me-2',
                            cancelButton: 'btn btn-outline-secondary rounded-pill px-4'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });
        });
    </script>
@endsection