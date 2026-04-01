@extends('layouts.app')

@section('title', 'Daftar Pengajuan Titik')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="ri ri-survey-line me-2 text-primary"></i>Persetujuan Titik Parkir</h4>
</div>

{{-- NOTIFIKASI SUCCESS / ERROR --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="ri ri-checkbox-circle-line me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="ri ri-error-warning-line me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ✅ CARD FILTER PINTAR --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('masterdata.location-requests.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                {{-- Smart Search --}}
                <div class="col-md-8 col-lg-9">
                    <div class="input-group input-group-merge shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="ri ri-search-line text-primary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari Nama Titik, Ruas Jalan, Nama Korlap, atau No PKS...">
                    </div>
                </div>
                
                {{-- Tombol Aksi Filter --}}
                <div class="col-md-4 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="ri ri-search-2-line me-1"></i> Cari</button>
                    <button class="btn btn-outline-secondary w-100 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter" aria-expanded="false" aria-controls="advancedFilter">
                        <i class="ri ri-filter-3-line me-1"></i> Filter
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
                            <input type="date" name="start_date" class="form-control shadow-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control shadow-sm" value="{{ request('end_date') }}">
                        </div>
                        
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('masterdata.location-requests.index') }}" class="btn btn-sm btn-danger rounded-pill shadow-sm"><i class="ri ri-refresh-line me-1"></i> Reset Filter</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ TABEL DATA --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom pb-3 bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-bold">Daftar Pengajuan dari Koordinator</h6>
        <span class="badge bg-label-primary rounded-pill">Total: {{ $requests->total() }} Data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-dark">Tanggal</th>
                        <th class="fw-bold text-dark">Koordinator (Mitra)</th>
                        <th class="fw-bold text-dark">Tipe Pengajuan</th>
                        <th class="fw-bold text-dark">Detail Lokasi</th>
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
                                    <span class="badge bg-label-success rounded-pill"><i class="ri ri-add-circle-fill me-1"></i> Penambahan</span>
                                @else
                                    <span class="badge bg-label-danger rounded-pill"><i class="ri ri-delete-bin-fill me-1"></i> Pencabutan</span>
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
                                    <span class="badge bg-warning text-white shadow-sm"><i class="ri ri-time-line me-1"></i> Review</span>
                                @elseif($request->status == 'surveyed')
                                    <span class="badge bg-info text-white shadow-sm"><i class="ri ri-clipboard-line me-1"></i> Disurvey</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success text-white shadow-sm"><i class="ri ri-check-double-line me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-danger text-white shadow-sm"><i class="ri ri-close-circle-line me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('masterdata.location-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm">
                                    <i class="ri ri-eye-line me-1"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ri ri-search-eye-line ri-3x mb-2 text-muted opacity-50"></i><br>
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