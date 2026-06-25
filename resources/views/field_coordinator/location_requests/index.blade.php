@extends('layouts.contentNavbarLayout')

@section('title', 'Riwayat Pengajuan Titik Parkir')



@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <style>
        .table-premium { border-collapse: separate; border-spacing: 0; min-width: 900px; }
        .table-premium th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb !important; color: #6b7280; padding-bottom: 1rem; }
        .table-premium td { vertical-align: middle; border-bottom: 1px solid #f3f4f6; transition: all 0.2s ease; }
        .table-premium tbody tr { transition: all 0.2s ease; }
        .table-premium tbody tr:hover td { background-color: #f8fafc; }
        .table-premium tbody tr:hover td:first-child { border-left-color: #696cff; }
        
        .badge-modern { padding: 0.5em 0.8em; font-weight: 600; border-radius: 50rem; display: inline-flex; align-items: center; gap: 0.35rem; }
        
        .btn-action { transition: all 0.2s ease; border-radius: 0.5rem; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        .table-responsive::-webkit-scrollbar { height: 8px; }
        .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .search-bar { max-width: 250px; width: 100%; transition: all 0.3s ease; }
        .search-bar:focus-within { max-width: 300px; box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1); }
        
        /* Custom Filter Dropdown */
        .filter-dropdown { min-width: 300px; padding: 1.5rem; border-radius: 1rem; border: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flatpickrDate = document.querySelectorAll('.flatpickr-date');
        if (flatpickrDate) {
            flatpickrDate.forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d'
                });
            });
        }
    });
</script>
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
                        <i class="ti tabler-calendar me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-map-pin me-2"></i>Pengajuan Titik Parkir</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Pantau status penambahan atau pencabutan titik kelolaan Anda.</p>
            </div>
        </div>
        <i class="ti tabler-map-pin position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

{{-- PENGUMUMAN AUTO-CLEANUP 60 HARI --}}
<div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 d-flex align-items-start rounded-4 p-4 mb-4" role="alert">
    <div class="avatar avatar-md bg-warning rounded-circle me-3 d-flex flex-shrink-0 align-items-center justify-content-center text-white shadow-sm">
        <i class="ti tabler-database ti tabler-xl"></i>
    </div>
    <div>
        <h6 class="alert-heading fw-bold mb-1 text-dark">Informasi Penyimpanan File</h6>
        <p class="mb-0 text-dark" style="font-size: 0.85rem;">
            Demi menjaga performa sistem, file lampiran (Foto & Proposal) pada pengajuan yang telah berstatus <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2">Disetujui</span> atau <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-2">Ditolak</span> akan dihapus secara otomatis setelah <strong>60 hari</strong>. Jejak rekam teks pengajuan Anda akan tetap tersimpan permanen.
        </p>
    </div>
    <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="glass-card anim-2 border-0 overflow-hidden mb-4">
    {{-- HEADER TABEL & FILTER --}}
    <div class="card-header p-4 border-bottom pb-3 bg-transparent">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0 fw-bold">Riwayat Pengajuan Anda</h5>
            <a href="{{ route('field_coordinator.location-requests.create') }}" class="btn btn-primary rounded-pill btn-action shadow-sm px-4"><i class="ti tabler-plus me-1"></i> Buat Pengajuan Baru</a>
            
            <form action="{{ route('field_coordinator.location-requests.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                
                {{-- TOMBOL FILTER TANGGAL (DROPDOWN) --}}
                <div class="dropdown">
                    <button class="btn {{ request('start_date') || request('end_date') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="ti tabler-filter me-1"></i> 
                        {{ request('start_date') ? 'Tgl Difilter' : 'Filter Tgl' }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-end filter-dropdown" aria-labelledby="filterDropdown">
                        <h6 class="fw-bold mb-3">Rentang Waktu</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="text" name="start_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('start_date') }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="text" name="end_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('end_date') }}">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill flex-grow-1" onclick="resetDateFilter()">Reset</button>
                            <button type="submit" class="btn btn-primary rounded-pill btn-action flex-grow-1">Terapkan</button>
                        </div>
                    </div>
                </div>

                {{-- SEARCH BAR --}}
                <div class="search-bar">
                    <div class="input-group input-group-merge rounded-pill shadow-sm border">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 px-2" placeholder="Cari nama, jalan..." value="{{ request('search') }}">
                        @if(request('search') || request('start_date') || request('end_date'))
                            <a href="{{ route('field_coordinator.location-requests.index') }}" class="input-group-text bg-transparent border-0 text-danger" data-bs-toggle="tooltip" title="Hapus Semua Filter">
                                <i class="ti tabler-circle-x"></i>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary rounded-pill m-1 px-3 d-none d-md-block">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body p-0 bg-white">
        <div class="table-responsive text-nowrap pb-2">
            <table class="table table-premium mb-0">
                <thead class="bg-white">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Jenis Pengajuan</th>
                        <th>Detail Titik Usulan</th>
                        <th>Alasan</th>
                        <th class="text-center">Status</th>
                        <th>Tanggapan Dinas</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td class="ps-4" style="border-left: 4px solid transparent;">
                                <span class="fw-bold text-dark d-block">{{ $request->created_at->translatedFormat('d M Y') }}</span>
                                <small class="text-muted">{{ $request->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                @if($request->request_type == 'add')
                                    <span class="badge bg-label-success badge-modern"><i class="ti tabler-circle-plus"></i> Penambahan</span>
                                @else
                                    <span class="badge bg-label-danger badge-modern"><i class="ti tabler-trash"></i> Pencabutan</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3 bg-lighter rounded d-flex align-items-center justify-content-center text-primary">
                                        <i class="ti tabler-user"></i>
                                    </div>
                                    <div>
                                        @if($request->request_type == 'add')
                                            <span class="fw-bold text-dark d-block text-truncate" style="max-width: 200px;" title="{{ $request->name }}">{{ $request->name }}</span>
                                            <small class="text-muted"><i class="ti tabler-road align-middle"></i> Jl. {{ Str::limit($request->road_section_name, 15) }}</small>
                                        @else
                                            <span class="fw-bold text-dark d-block text-truncate" style="max-width: 200px;" title="{{ $request->parkingLocation->name ?? 'Titik Tidak Diketahui' }}">{{ $request->parkingLocation->name ?? 'Titik Tidak Diketahui' }}</span>
                                            <small class="text-muted"><i class="ti tabler-road align-middle"></i> ID Ruas: {{ $request->parkingLocation->roadSection->name ?? '-' }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate text-muted" style="max-width: 150px;" title="{{ $request->reason }}">
                                    {{ $request->reason }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($request->status == 'pending')
                                    <span class="badge bg-label-warning badge-modern"><i class="ti tabler-clock"></i> Pending</span>
                                @elseif($request->status == 'surveyed')
                                    <span class="badge bg-label-info badge-modern"><i class="ti tabler-clipboard"></i> Disurvey</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-label-success badge-modern"><i class="ti tabler-checks"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-label-danger badge-modern"><i class="ti tabler-circle-x"></i> Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($request->admin_note)
                                    <div class="d-flex align-items-start">
                                        <i class="ti tabler-message text-muted me-2 mt-1"></i>
                                        <span class="d-inline-block text-truncate text-dark small fw-medium" style="max-width: 150px;" title="{{ $request->admin_note }}">
                                            {{ $request->admin_note }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">- Menunggu -</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                {{-- LOGIKA AKSI --}}
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Tombol Detail Show (Read-Only) SELALU MUNCUL --}}
                                    <a href="{{ route('field_coordinator.location-requests.show', $request->id) }}"
                                       class="btn btn-sm btn-icon btn-label-info btn-action"
                                       data-bs-toggle="tooltip" title="Lihat Detail Progress">
                                        <i class="ti tabler-eye"></i>
                                    </a>

                                    @if($request->status == 'pending')
                                        <a href="{{ route('field_coordinator.location-requests.edit', $request->id) }}"
                                           class="btn btn-sm btn-icon btn-label-primary btn-action"
                                           data-bs-toggle="tooltip" title="Edit Pengajuan">
                                            <i class="ti tabler-pencil"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-label-danger btn-action"
                                                data-bs-toggle="tooltip" title="Batalkan Pengajuan"
                                                onclick="openDeleteModal('{{ route('field_coordinator.location-requests.destroy', $request->id) }}')">
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state-container d-flex flex-column align-items-center justify-content-center">
                                    <div class="avatar avatar-xl bg-lighter rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                        <i class="ti tabler-zoom-in ti-xl text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Data Tidak Ditemukan</h6>
                                    @if(request('search') || request('start_date') || request('end_date'))
                                        <p class="text-muted small mb-3">Pencarian filter Anda tidak membuahkan hasil.</p>
                                        <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                            <i class="ti tabler-refresh me-1"></i> Reset Semua Filter
                                        </a>
                                    @else
                                        <p class="text-muted small mb-3">Anda belum pernah mengajukan penambahan atau pencabutan titik.</p>
                                        <a href="{{ route('field_coordinator.location-requests.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="ti tabler-plus me-1"></i> Buat Pengajuan Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS PREMIUM --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
                    <i class="ti tabler-trash ti-xl"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Batalkan Pengajuan?</h5>
                <p class="text-muted small mb-4">Pengajuan yang dibatalkan akan dihapus permanen dari sistem dan tidak dapat dikembalikan.</p>
                
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary fw-bold flex-grow-1 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger fw-bold flex-grow-1 rounded-pill shadow-sm">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flatpickrDate = document.querySelectorAll('.flatpickr-date');
        if (flatpickrDate) {
            flatpickrDate.forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d'
                });
            });
        }
    });
</script>
@endsection

@section('page-script')
<script type="module">
        document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    function openDeleteModal(deleteUrl) {
        document.getElementById('deleteForm').action = deleteUrl;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    }

    // Fungsi reset filter tanggal
    function resetDateFilter() {
        document.querySelector('input[name="start_date"]').value = '';
        document.querySelector('input[name="end_date"]').value = '';
        // Otomatis submit form setelah di-reset
        document.querySelector('.search-bar form, form.d-flex').submit();
    }
</script>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flatpickrDate = document.querySelectorAll('.flatpickr-date');
        if (flatpickrDate) {
            flatpickrDate.forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d'
                });
            });
        }
    });
</script>
@endsection