@extends('layouts.contentNavbarLayout')

@section('title', 'Manajemen Rekening BLUD')



@section('page-style')
    {{-- CSS untuk SweetAlert2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
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
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-building-bank me-2"></i>Rekening Bank BLUD</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Manajemen data rekening bank.</p>
            </div>
        </div>
        <i class="ti tabler-building-bank position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    {{-- Daftar Rekening --}}
    <div class="glass-card anim-2 border-0 overflow-hidden mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 p-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Rekening BLUD</h5>
                <p class="text-muted mb-0">Total {{ $accounts->total() }} rekening terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center">
                @if(Auth::user()->role !== 'leader')
                <a href="{{ route('admin.blud-bank-accounts.create') }}" class="btn btn-primary rounded-pill btn-action">
                    <i class="icon-base ti tabler-plus me-2"></i>Tambah Rekening
                </a>
                @endif
            </div>
        </div>
        <div class="card-body p-4">
            @if (session('success'))
                {{-- Notifikasi akan ditangani oleh SweetAlert2 --}}
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Bank</th>
                            <th>Nomor Rekening</th>
                            <th>Atas Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($accounts as $account)
                            <tr>
                                <td><span class="fw-medium">{{ $account->bank_name }}</span></td>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ $account->account_name }}</td>
                                <td>{{ $account->start_date->format('d M Y') }}</td>
                                <td>
                                    @if ($account->is_active)
                                        <span class="badge rounded-pill bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @if(Auth::user()->role !== 'leader')
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('admin.blud-bank-accounts.edit', $account->id) }}"
                                            data-bs-toggle="tooltip" title="Edit Rekening">
                                            <i class="icon-base ti tabler-pencil icon-22px"></i>
                                        </a>
                                        {{-- ✅ PERUBAHAN: Tombol hapus hanya muncul jika rekening tidak aktif --}}
                                        @if (!$account->is_active)
                                            <form action="{{ route('admin.blud-bank-accounts.destroy', $account->id) }}"
                                                method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                    title="Hapus Rekening">
                                                    <i class="icon-base ti tabler-trash icon-22px"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data rekening.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script type="module">
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
                        text: "Data rekening yang dihapus tidak dapat dikembalikan!",
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
                    })
                });
            });
        });
    </script>
@endsection
