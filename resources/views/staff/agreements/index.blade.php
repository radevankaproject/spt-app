@extends('layouts.app')

@section('title', 'Manajemen Perjanjian Kerjasama')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #696cff;
            border-bottom: 3px solid #696cff;
        }
        /* Highlight baris masa tenggang */
        .row-grace-period {
            background-color: rgba(255, 171, 0, 0.05) !important;
        }
    </style>
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

    {{-- Tabs Navigasi --}}
    <ul class="nav nav-tabs mb-4 border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'all' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'all', 'search' => request('search')]) }}">
               Semua PKS <span class="badge bg-{{ $tab == 'all' ? 'primary' : 'label-secondary' }} ms-1 rounded-pill">{{ $countAll }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'active' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'active', 'search' => request('search')]) }}">
               Aktif <span class="badge bg-{{ $tab == 'active' ? 'success' : 'label-secondary' }} ms-1 rounded-pill">{{ $countActive }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'inactive' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'inactive', 'search' => request('search')]) }}">
               Tidak Aktif <span class="badge bg-{{ $tab == 'inactive' ? 'danger' : 'label-secondary' }} ms-1 rounded-pill">{{ $countInactive }}</span>
            </a>
        </li>
    </ul>

    {{-- Daftar Perjanjian --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3">
            <div class="card-title mb-0">
                <h5 class="mb-1">
                    @if($tab == 'active') Daftar PKS Aktif
                    @elseif($tab == 'inactive') Daftar PKS Tidak Aktif (Kedaluwarsa/Diputus)
                    @else Daftar Semua PKS
                    @endif
                </h5>
                <p class="text-muted mb-0">Total {{ $agreements->total() }} data ditampilkan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                {{-- Form Pencarian --}}
                <form action="{{ route('masterdata.agreements.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari No PKS/Korlap..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="icon-base ri ri-search-line"></i></button>
                    </div>
                </form>
                {{-- Tombol Tambah --}}
                <a href="{{ route('masterdata.agreements.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-1"></i> Tambah PKS
                </a>
            </div>
        </div>
        <div class="card-body pt-3">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                    <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="20%">Nomor PKS</th>
                            <th width="25%">Koordinator Lapangan</th>
                            <th width="25%">Masa Berlaku</th>
                            <th width="15%">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($agreements as $agreement)
                            @php
                                // ✅ LOGIKA MASA TENGGANG (10 Hari)
                                $isGracePeriod = false;
                                $daysRemaining = null;
                                if ($agreement->status === 'active') {
                                    $daysRemaining = (int) now()->diffInDays($agreement->end_date, false);
                                    if ($daysRemaining >= 0 && $daysRemaining <= 10) {
                                        $isGracePeriod = true;
                                    }
                                }

                                // Setup Avatar
                                $cName = $agreement->fieldCoordinator->user->name ?? 'N/A';
                                $cAvatar = ($agreement->fieldCoordinator->user && $agreement->fieldCoordinator->user->img)
                                    ? asset($agreement->fieldCoordinator->user->img)
                                    : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=32&rounded=true&bold=true";
                            @endphp

                            <tr class="{{ $isGracePeriod ? 'row-grace-period border-start border-4 border-warning' : '' }}">
                                <td>
                                    <span class="fw-bold text-dark">{{ $agreement->agreement_number }}</span>
                                    <small class="d-block text-muted">Pimpinan: {{ Str::limit($agreement->leader->user->name ?? 'N/A', 15) }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle" style="object-fit: cover;">
                                        </div>
                                        <span class="fw-medium text-dark">{{ $cName }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="d-block text-dark">{{ $agreement->start_date->translatedFormat('d M Y') }}</span>
                                            <small class="text-muted">s/d <span class="{{ $isGracePeriod ? 'text-danger fw-bold' : '' }}">{{ $agreement->end_date->translatedFormat('d M Y') }}</span></small>
                                        </div>
                                        {{-- ✅ INDIKATOR MASA TENGGANG --}}
                                        @if($isGracePeriod)
                                            <span class="badge bg-label-warning bg-opacity-20 text-warning ms-3 rounded-pill" data-bs-toggle="tooltip" title="Tersisa {{ $daysRemaining }} hari lagi!">
                                                <i class="ri ri-alert-line me-1"></i> Tenggang
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-label-secondary';
                                        if ($agreement->status == 'active') $statusClass = 'bg-label-success';
                                        if ($agreement->status == 'expired') $statusClass = 'bg-label-danger';
                                        if ($agreement->status == 'terminated') $statusClass = 'bg-label-dark';
                                        if ($agreement->status == 'pending_renewal') $statusClass = 'bg-label-warning';
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} fw-bold">
                                        {{ strtoupper(str_replace('_', ' ', $agreement->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill"
                                            href="{{ route('masterdata.agreements.show', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="icon-base ri ri-eye-line ri-20px"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            href="{{ route('masterdata.agreements.edit', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Edit PKS">
                                            <i class="icon-base ri ri-pencil-line ri-20px"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                            href="{{ route('masterdata.agreements.pdf', $agreement->id) }}" target="_blank"
                                            data-bs-toggle="tooltip" title="Cetak Dokumen PKS">
                                            <i class="icon-base ri ri-printer-line ri-20px"></i>
                                        </a>
                                        <form action="{{ route('masterdata.agreements.destroy', $agreement->id) }}"
                                            method="POST" class="form-delete d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip"
                                                title="Hapus PKS">
                                                <i class="icon-base ri ri-delete-bin-line ri-20px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}" width="120" class="mb-3 opacity-50" alt="No Data">
                                    <p class="text-muted">Tidak ada data perjanjian ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{-- Memastikan query search dan tab dibawa saat pindah halaman --}}
                {{ $agreements->appends(['search' => request('search'), 'tab' => $tab])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Aktifkan Tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // ✅ HANYA Modal Konfirmasi Hapus
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data perjanjian yang dihapus akan otomatis melepaskan lokasi parkir terkait (kembali menjadi Tersedia).",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-outline-secondary waves-effect'
                        },
                        buttonsStyling: false,
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
