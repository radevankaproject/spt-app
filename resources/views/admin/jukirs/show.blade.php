@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Juru Parkir: ' . $jukir->nama_jukir)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
    <div>
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Data Jukir /</span> Detail Profil</h4>
        <p class="text-muted mb-0">Memantau detail, KTA, dan performa Juru Parkir.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jukirs.index') }}" class="btn btn-outline-secondary">
            <i class="ti tabler-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.jukirs.print-kta', $jukir->id) }}" target="_blank" class="btn btn-primary shadow-sm">
            <i class="ti tabler-printer me-1"></i> Cetak KTA
        </a>
    </div>
</div>

<div class="row">
    <!-- KOLOM KIRI: PROFIL & STATISTIK -->
    <div class="col-xl-4 col-lg-5 col-md-5 mb-4 order-1 order-md-0">
        <div class="card mb-4 border-0 shadow-sm {{ $jukir->is_blacklisted ? 'border-danger' : '' }}">
            <div class="card-body pt-5 text-center" style="{{ $jukir->is_blacklisted ? 'opacity: 0.85;' : '' }}">
                <div class="user-avatar-section mb-4 position-relative mx-auto" style="width: fit-content;">
                    @if($jukir->image)
                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto Profil" class="img-fluid rounded-circle shadow-sm border border-3 {{ $jukir->is_blacklisted ? 'border-danger' : 'border-primary' }}" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="avatar avatar-xl rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold shadow-sm border border-3 {{ $jukir->is_blacklisted ? 'border-danger bg-label-danger' : 'border-primary bg-label-primary' }}" style="width: 120px; height: 120px; font-size: 2.5rem;">
                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                        </div>
                    @endif
                    @if($jukir->is_blacklisted)
                        <span class="position-absolute bottom-0 end-0 badge bg-danger p-2 shadow" style="border-radius: 50%; transform: translate(25%, 25%);"><i class="ti tabler-ban fs-5"></i></span>
                    @endif
                </div>
                
                <h5 class="mb-1 fw-bold">{{ $jukir->nama_jukir }}</h5>
                <p class="text-muted mb-2">ID: <span class="fw-bold text-primary">{{ $jukir->id_jukir ?? '-' }}</span></p>
                
                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                    @if($jukir->is_blacklisted)
                        <span class="badge bg-danger rounded-pill shadow-sm"><i class="ti tabler-ban me-1"></i> BLACKLISTED</span>
                    @elseif($jukir->is_active)
                        <span class="badge bg-label-success rounded-pill px-3"><i class="ti tabler-check me-1"></i> Aktif</span>
                    @else
                        <span class="badge bg-label-warning rounded-pill px-3"><i class="ti tabler-alert-circle me-1"></i> Nonaktif</span>
                    @endif
                    
                    @if($jukir->kta_type)
                        <span class="badge bg-label-info rounded-pill px-3 text-uppercase"><i class="ti tabler-id-badge me-1"></i> {{ $jukir->kta_type }}</span>
                    @endif
                </div>

                <!-- STATISTIK KINERJA -->
                <div class="bg-lighter rounded-3 p-3 mb-4 text-start">
                    <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-chart-bar me-1"></i> Ringkasan Kinerja</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status KTA</span>
                        <span class="fw-bold {{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'text-danger' : 'text-success' }}">
                            {{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'Kedaluwarsa' : 'Aktif' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Pelanggaran</span>
                        <span class="fw-bold {{ $jukir->violations->count() >= 3 ? 'text-danger' : 'text-dark' }}">{{ $jukir->violations->count() }} / 5</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="text-muted">Titik Lokasi</span>
                        <span class="fw-bold text-dark text-truncate" style="max-width: 150px;" title="{{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}">
                            {{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}
                        </span>
                    </div>
                </div>

                <h6 class="pb-2 border-bottom text-start mb-3">Informasi Personal</h6>
                <ul class="list-unstyled mb-4 text-start small">
                    <li class="mb-2 d-flex align-items-center"><i class="ti tabler-id text-muted me-2"></i> <span class="fw-medium me-1">NIK:</span> <span>{{ $jukir->no_ktp ?? '-' }}</span></li>
                    <li class="mb-2 d-flex align-items-center"><i class="ti tabler-phone text-muted me-2"></i> <span class="fw-medium me-1">Kontak:</span> <span>{{ $jukir->phone_number ?? '-' }}</span></li>
                    <li class="mb-2 d-flex align-items-center"><i class="ti tabler-calendar-event text-muted me-2"></i> <span class="fw-medium me-1">KTA Terbit:</span> <span>{{ $jukir->kta_start_date ? \Carbon\Carbon::parse($jukir->kta_start_date)->format('d/m/Y') : '-' }}</span></li>
                    <li class="d-flex align-items-center"><i class="ti tabler-calendar-off text-muted me-2"></i> <span class="fw-medium me-1">KTA Expire:</span> <span class="{{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'text-danger fw-bold' : '' }}">{{ $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->format('d/m/Y') : '-' }}</span></li>
                </ul>
            </div>
        </div>

        @if($jukir->image_ktp)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="pb-2 border-bottom mb-3"><i class="ti tabler-id-badge-2 me-1"></i> Dokumen KTP</h6>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ktpModal">
                        <img src="{{ Storage::url($jukir->image_ktp) }}" alt="Foto KTP" class="img-fluid rounded-3 shadow-sm" style="cursor: zoom-in;">
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- KOLOM KANAN: KTA & TAB AKTIVITAS -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-kta" aria-controls="tab-kta" aria-selected="true">
                        <i class="ti tabler-id-badge me-1"></i> ID Card (KTA)
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-riwayat" aria-controls="tab-riwayat" aria-selected="false">
                        <i class="ti tabler-history me-1"></i> Riwayat Aktivitas
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-pelanggaran" aria-controls="tab-pelanggaran" aria-selected="false">
                        <i class="ti tabler-alert-triangle me-1"></i> Pelanggaran 
                        @if($jukir->violations->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $jukir->violations->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0 border-0 shadow-none bg-transparent">
            
            <!-- TAB: KTA -->
            <div class="tab-pane fade show active" id="tab-kta">
                <div class="row g-4">
                    <!-- KTA Depan -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
                            <div class="card-header bg-transparent border-bottom text-center pb-3">
                                <h6 class="mb-0 fw-bold text-dark">KTA Bagian Depan</h6>
                            </div>
                            <div class="card-body p-4 text-center">
                                <!-- Aspect Ratio Box for KTA -->
                                <div style="position: relative; width: 100%; padding-top: 158.77%; overflow: hidden; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                                    <iframe id="frame-front" 
                                            src="{{ route('admin.jukirs.print-kta', ['jukir' => $jukir->id, 'preview' => 'front']) }}" 
                                            style="position: absolute; top: 0; left: 0; width: 638px; height: 1013px; border: 0; transform-origin: 0 0;" 
                                            onload="this.style.transform = 'scale(' + (this.parentElement.clientWidth / 638) + ')';">
                                    </iframe>
                                </div>
                                <button type="button" onclick="document.getElementById('frame-front').contentWindow.downloadCard('kta-front', 'KTA-Front-{{ $jukir->id_jukir }}')" class="btn btn-primary w-100 mt-4 shadow-sm">
                                    <i class="ti tabler-download me-1"></i> Download Depan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- KTA Belakang -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
                            <div class="card-header bg-transparent border-bottom text-center pb-3">
                                <h6 class="mb-0 fw-bold text-dark">KTA Bagian Belakang</h6>
                            </div>
                            <div class="card-body p-4 text-center">
                                <!-- Aspect Ratio Box for KTA -->
                                <div style="position: relative; width: 100%; padding-top: 158.77%; overflow: hidden; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                                    <iframe id="frame-back" 
                                            src="{{ route('admin.jukirs.print-kta', ['jukir' => $jukir->id, 'preview' => 'back']) }}" 
                                            style="position: absolute; top: 0; left: 0; width: 638px; height: 1013px; border: 0; transform-origin: 0 0;" 
                                            onload="this.style.transform = 'scale(' + (this.parentElement.clientWidth / 638) + ')';">
                                    </iframe>
                                </div>
                                <button type="button" onclick="document.getElementById('frame-back').contentWindow.downloadCard('kta-back', 'KTA-Back-{{ $jukir->id_jukir }}')" class="btn btn-success w-100 mt-4 shadow-sm">
                                    <i class="ti tabler-download me-1"></i> Download Belakang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: RIWAYAT -->
            <div class="tab-pane fade" id="tab-riwayat">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header border-bottom bg-white">
                        <h5 class="card-title mb-0 fw-bold"><i class="ti tabler-history text-primary me-2"></i> Riwayat Perubahan & Lokasi</h5>
                    </div>
                    <div class="card-body pt-4">
                        <ul class="timeline pb-0 mb-0">
                            @forelse($jukir->histories as $history)
                                <li class="timeline-item timeline-item-transparent {{ $loop->last ? 'border-transparent' : '' }}">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0 fw-bold">{{ $history->action }}</h6>
                                            <small class="text-muted"><i class="ti tabler-clock me-1"></i> {{ $history->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-2">{{ $history->description }}</p>
                                        
                                        @if($history->action == 'Update' && $history->old_values && $history->new_values)
                                            <div class="mb-2 bg-lighter rounded p-3">
                                                <a class="collapse-toggle text-primary fw-bold fs-6 mb-2 d-inline-block" data-bs-toggle="collapse" href="#detailPerubahan{{ $history->id }}">
                                                    <i class="ti tabler-chevron-down me-1"></i> Lihat Detail Perubahan
                                                </a>
                                                <div class="collapse" id="detailPerubahan{{ $history->id }}">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered bg-white mt-2 mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Nilai Lama</th>
                                                                    <th>Nilai Baru</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($history->new_values as $key => $newValue)
                                                                    @if(isset($history->old_values[$key]) && $history->old_values[$key] !== $newValue)
                                                                        <tr>
                                                                            <td class="fw-medium text-capitalize">{{ str_replace('_', ' ', $key) }}</td>
                                                                            <td class="text-danger"><del>{{ $history->old_values[$key] }}</del></td>
                                                                            <td class="text-success fw-bold">{{ $newValue }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-secondary"><i class="ti tabler-user"></i></span>
                                            </div>
                                            <small class="text-muted">Oleh: <span class="fw-bold text-dark">{{ $history->user->name ?? 'Sistem' }}</span></small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <div class="text-center py-5 bg-lighter rounded">
                                    <i class="ti tabler-history text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat aktivitas tercatat.</p>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- TAB: PELANGGARAN -->
            <div class="tab-pane fade" id="tab-pelanggaran">
                @if(in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                    <!-- Form Tambah Pelanggaran -->
                    <div class="card mb-4 border-0 shadow-sm border-top border-warning border-3">
                        <div class="card-header border-bottom bg-white">
                            <h5 class="card-title mb-0 fw-bold"><i class="ti tabler-alert-triangle text-warning me-2"></i> Catat Pelanggaran Baru</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fw-bold" role="alert">
                                    <i class="ti tabler-alert-triangle me-1"></i> {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fw-bold" role="alert">
                                    <i class="ti tabler-check me-1"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form action="{{ route('admin.jukir-violations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="jukir_id" value="{{ $jukir->id }}">
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Tanggal Kejadian <span class="text-danger">*</span></label>
                                        <input type="date" name="violation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold">Keterangan Pelanggaran <span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan bentuk pelanggaran secara detail..." required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-warning shadow-sm" {{ $jukir->is_blacklisted ? 'disabled' : '' }}>
                                        <i class="ti tabler-plus me-1"></i> Simpan Catatan Pelanggaran
                                    </button>
                                </div>
                            </form>
                            @if($jukir->is_blacklisted)
                                <div class="mt-3 alert alert-danger mb-0 p-2 text-center fw-bold"><i class="ti tabler-info-circle me-1"></i> Jukir sudah di-blacklist. Tidak dapat menambah pelanggaran baru.</div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- List Pelanggaran -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white">
                        <h5 class="card-title mb-0 fw-bold">Daftar Pelanggaran</h5>
                        <span class="badge bg-label-danger rounded-pill px-3 py-2">Total: {{ $jukir->violations->count() }} / 5</span>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Tanggal</th>
                                    <th>Keterangan Pelanggaran</th>
                                    <th width="15%" class="text-center">Pencatat</th>
                                    @if(in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                                        <th width="10%" class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($jukir->violations as $index => $violation)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-medium text-dark"><i class="ti tabler-calendar-event text-muted me-1"></i> {{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') }}</span>
                                        </td>
                                        <td style="white-space: normal;">{{ $violation->description }}</td>
                                        <td class="text-center"><span class="badge bg-label-secondary"><i class="ti tabler-user me-1"></i> {{ $violation->user->name ?? 'Sistem' }}</span></td>
                                        @if(in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                                            <td class="text-center">
                                                <form action="{{ route('admin.jukir-violations.destroy', $violation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus catatan pelanggaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus Pelanggaran">
                                                        <i class="ti tabler-trash icon-22px"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']) ? 5 : 4 }}" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ti tabler-shield-check text-success mb-2" style="font-size: 2.5rem;"></i>
                                                <h6 class="mb-0 text-dark">Bersih dari Pelanggaran</h6>
                                                <small>Belum ada catatan pelanggaran untuk jukir ini.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@if($jukir->image_ktp)
    {{-- Modal KTP --}}
    <div class="modal fade" id="ktpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white">Dokumen KTP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="{{ Storage::url($jukir->image_ktp) }}" class="img-fluid w-100" alt="Foto KTP">
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
