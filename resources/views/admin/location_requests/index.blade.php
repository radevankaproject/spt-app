@extends('layouts.contentNavbarLayout')

@section('title', 'Daftar Pengajuan Titik')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
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
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-map-pin me-2"></i>Persetujuan Titik Parkir</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Persetujuan dan manajemen pengajuan lokasi.</p>
            </div>
        </div>
        <i class="ti tabler-map-pin position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

{{-- NOTIFIKASI SUCCESS / ERROR --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="ti tabler-circle-check me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="ti tabler-alert-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ✅ CARD FILTER PINTAR --}}
<div class="glass-card border-0 mb-4 anim-2">
    <div class="card-body p-4">
        <form action="{{ route('masterdata.location-requests.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                {{-- Smart Search --}}
                <div class="col-md-8 col-lg-9">
                    <div class="input-group input-group-merge shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="ti tabler-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari Nama Titik, Ruas Jalan, Nama Korlap, atau No PKS...">
                    </div>
                </div>
                
                {{-- Tombol Aksi Filter --}}
                <div class="col-md-4 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm rounded-pill btn-action"><i class="ti tabler-search me-1"></i> Cari</button>
                    <button class="btn btn-outline-secondary w-100 shadow-sm rounded-pill btn-action" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter" aria-expanded="false" aria-controls="advancedFilter">
                        <i class="ti tabler-filter me-1"></i> Filter
                    </button>
                </div>
            </div>

            {{-- Accordion Filter Lanjutan --}}
            <div class="collapse {{ request()->anyFilled(['status', 'type', 'start_date', 'end_date']) ? 'show' : '' }} mt-3" id="advancedFilter">
                <div class="p-4 bg-lighter rounded-4 border border-dashed">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Status Pengajuan</label>
                            <select name="status" class="form-select shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                                <option value="surveyed" {{ request('status') == 'surveyed' ? 'selected' : '' }}>Telah Disurvey</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Tipe Pengajuan</label>
                            <select name="type" class="form-select shadow-sm">
                                <option value="">Semua Tipe</option>
                                <option value="add" {{ request('type') == 'add' ? 'selected' : '' }}>Penambahan Titik</option>
                                <option value="remove" {{ request('type') == 'remove' ? 'selected' : '' }}>Pencabutan Titik</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Dari Tanggal</label>
                            <input type="text" name="start_date" class="form-control shadow-sm flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Sampai Tanggal</label>
                            <input type="text" name="end_date" class="form-control shadow-sm flatpickr-date" placeholder="YYYY-MM-DD" value="{{ request('end_date') }}">
                        </div>
                        
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('masterdata.location-requests.index') }}" class="btn btn-sm btn-danger rounded-pill shadow-sm"><i class="ti tabler-refresh me-1"></i> Reset Filter</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ TABEL DATA --}}
<div class="glass-card border-0 anim-2">
    <div class="card-header p-4 border-bottom pb-3 bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-bold">Daftar Pengajuan dari Koordinator</h6>
        <span class="badge bg-label-primary rounded-pill">Total: {{ $requests->total() }} Data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="text-uppercase fw-bold text-primary">Tanggal</th>
                        <th class="text-uppercase fw-bold text-primary">Koordinator (Mitra)</th>
                        <th class="text-uppercase fw-bold text-primary">Tipe Pengajuan</th>
                        <th class="text-uppercase fw-bold text-primary">Detail Lokasi</th>
                        <th class="fw-bold text-dark text-center">Status</th>
                        <th class="fw-bold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $request->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="fw-bold text-primary d-block">{{ Str::limit($request->agreement->fieldCoordinator->user->name ?? 'N/A', 20) }}</span>
                                <small class="text-muted">PKS: {{ $request->agreement->agreement_number }}</small>
                            </td>
                            <td>
                                @if($request->request_type == 'add')
                                    <span class="badge bg-label-success rounded-pill"><i class="ti tabler-circle-plus me-1"></i> Penambahan</span>
                                @else
                                    <span class="badge bg-label-danger rounded-pill"><i class="ti tabler-trash me-1"></i> Pencabutan</span>
                                @endif
                            </td>
                            <td>
                                @if($request->request_type == 'add')
                                    <span class="d-block fw-medium text-dark">{{ Str::limit($request->name, 25) }}</span>
                                    <small class="text-muted">Jl. {{ Str::limit($request->road_section_name, 20) }}</small>
                                @else
                                    <span class="d-block fw-medium text-dark">{{ Str::limit($request->parkingLocation->name ?? '-', 25) }}</span>
                                    <small class="text-muted">Jl. {{ Str::limit($request->parkingLocation->roadSection->name ?? '-', 20) }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning text-white shadow-sm"><i class="ti tabler-clock me-1"></i> Review</span>
                                @elseif($request->status == 'surveyed')
                                    <span class="badge bg-info text-white shadow-sm"><i class="ti tabler-clipboard me-1"></i> Disurvey</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success text-white shadow-sm"><i class="ti tabler-checks me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-danger text-white shadow-sm"><i class="ti tabler-circle-x me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('masterdata.location-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm">
                                    <i class="ti tabler-eye me-1"></i> {{ in_array($request->status, ['approved', 'rejected']) ? 'Lihat Details' : 'Proses' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ti tabler-zoom-in ti-xl mb-2 text-muted opacity-50"></i><br>
                                <span class="fw-medium">Data pengajuan tidak ditemukan.</span><br>
                                <small>Coba sesuaikan filter atau kata kunci pencarian Anda.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- ✅ PAGINATION DENGAN QUERY STRING --}}
        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-lighter">
            <small class="text-muted">Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }} data</small>
            <div>
                {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
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