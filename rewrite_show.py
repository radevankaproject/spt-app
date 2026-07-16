import sys

content = """@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Juru Parkir: ' . $jukir->nama_jukir)

@push('styles')
    <style>
        .timeline-item-transparent .timeline-event {
            background: #fff;
            padding: 1.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.15);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(165, 163, 174, 0.1);
        }
        .timeline-item-transparent .timeline-point {
            top: 1.5rem;
        }
        .timeline-item-transparent::before {
            border-left-style: dashed;
            border-left-color: #d9dee3;
        }
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a5a3ae;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #566a7f;
        }
        .profile-card {
            background: linear-gradient(180deg, rgba(105, 108, 255, 0.05) 0%, rgba(255,255,255,1) 100%);
            border-top: 4px solid #696cff;
        }
        .profile-card.blacklisted {
            background: linear-gradient(180deg, rgba(255, 62, 29, 0.05) 0%, rgba(255,255,255,1) 100%);
            border-top: 4px solid #ff3e1d;
        }
        .kta-preview-container {
            position: relative;
            width: 100%;
            padding-top: 158.77%; /* Aspect Ratio 54:86 */
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            transition: transform 0.3s ease;
        }
        .kta-preview-container:hover {
            transform: translateY(-5px);
        }
        .kta-iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 638px;
            height: 1013px;
            border: 0;
            transform-origin: 0 0;
        }
    </style>
@endpush

@php
    function translateHistoryField($key) {
        $fields = [
            'id_jukir' => 'ID Jukir',
            'nama_jukir' => 'Nama Jukir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat',
            'parking_location_id' => 'Titik Parkir',
            'no_ktp' => 'No. KTP',
            'phone_number' => 'No. Handphone',
            'image' => 'Foto Profil',
            'image_ktp' => 'Foto KTP',
            'is_active' => 'Status Aktif',
            'kta_type' => 'Tipe KTA',
            'kta_start_date' => 'Tanggal Terbit KTA',
            'kta_end_date' => 'Masa Berlaku KTA',
        ];
        return $fields[$key] ?? str_replace('_', ' ', Str::title($key));
    }

    function translateHistoryValue($key, $value, $locationNames) {
        if ($value === null || $value === '') return '-';
        
        if ($key === 'is_active') {
            return $value ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-danger">Nonaktif</span>';
        }
        if ($key === 'parking_location_id') {
            return '<span class="fw-bold text-primary">' . ($locationNames[$value] ?? 'Lokasi #' . $value) . '</span>';
        }
        if (in_array($key, ['image', 'image_ktp'])) {
            return '<span class="badge bg-label-info"><i class="ti tabler-photo me-1"></i> Gambar Diperbarui</span>';
        }
        if (in_array($key, ['tanggal_lahir', 'kta_start_date', 'kta_end_date'])) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }
        
        return htmlspecialchars($value);
    }
@endphp

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
    <div>
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Data Jukir /</span> Detail Profil</h4>
        <p class="text-muted mb-0">Memantau detail, KTA, riwayat, dan performa Juru Parkir.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jukirs.index') }}" class="btn btn-label-secondary shadow-sm">
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
        <div class="card mb-4 border-0 shadow-sm profile-card {{ $jukir->is_blacklisted ? 'blacklisted' : '' }}">
            <div class="card-body pt-5 text-center" style="{{ $jukir->is_blacklisted ? 'opacity: 0.85;' : '' }}">
                <div class="user-avatar-section mb-4 position-relative mx-auto" style="width: fit-content;">
                    @if($jukir->image)
                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto Profil" class="img-fluid rounded-circle shadow-sm border border-4 {{ $jukir->is_blacklisted ? 'border-danger' : 'border-primary' }}" width="130" height="130" style="object-fit: cover;">
                    @else
                        <div class="avatar avatar-xl rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold shadow-sm border border-4 {{ $jukir->is_blacklisted ? 'border-danger bg-label-danger' : 'border-primary bg-label-primary' }}" style="width: 130px; height: 130px; font-size: 3rem;">
                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                        </div>
                    @endif
                    @if($jukir->is_blacklisted)
                        <span class="position-absolute bottom-0 end-0 badge bg-danger p-2 shadow" style="border-radius: 50%; transform: translate(10%, 10%);" data-bs-toggle="tooltip" title="Jukir telah di-blacklist"><i class="ti tabler-ban fs-4"></i></span>
                    @elseif($jukir->is_active)
                        <span class="position-absolute bottom-0 end-0 badge bg-success p-2 shadow" style="border-radius: 50%; transform: translate(10%, 10%);" data-bs-toggle="tooltip" title="Jukir Aktif"><i class="ti tabler-check fs-4"></i></span>
                    @else
                        <span class="position-absolute bottom-0 end-0 badge bg-warning p-2 shadow" style="border-radius: 50%; transform: translate(10%, 10%);" data-bs-toggle="tooltip" title="Jukir Nonaktif"><i class="ti tabler-minus fs-4"></i></span>
                    @endif
                </div>
                
                <h4 class="mb-1 fw-bold text-dark">{{ $jukir->nama_jukir }}</h4>
                <p class="text-muted mb-2 fs-6">ID Jukir: <span class="fw-bold text-primary">{{ $jukir->id_jukir ?? '-' }}</span></p>
                
                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                    @if($jukir->is_blacklisted)
                        <span class="badge bg-danger rounded-pill shadow-sm px-3"><i class="ti tabler-ban me-1"></i> BLACKLISTED</span>
                    @endif
                    @if($jukir->kta_type)
                        <span class="badge bg-label-info rounded-pill px-3 text-uppercase fw-bold"><i class="ti tabler-id-badge me-1"></i> KTA {{ $jukir->kta_type }}</span>
                    @endif
                </div>

                <!-- INFO PENUGASAN -->
                <div class="bg-lighter rounded-3 p-3 mb-4 text-start shadow-sm border">
                    <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-map-pin me-1"></i> Lokasi Penugasan</h6>
                    
                    <div class="mb-2">
                        <div class="info-label">Titik Parkir</div>
                        <div class="info-value text-truncate" title="{{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}">
                            {{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="info-label">Ruas Jalan</div>
                        <div class="info-value">
                            {{ $jukir->parkingLocation->roadSection->name ?? '-' }}
                        </div>
                    </div>
                    
                    @php
                        $korlap = null;
                        if ($jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty()) {
                            // Find active agreement
                            $activeAgreement = $jukir->parkingLocation->agreements->where('status', 'active')->first();
                            if ($activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user) {
                                $korlap = $activeAgreement->fieldCoordinator->user->name;
                            }
                        }
                    @endphp
                    
                    <div>
                        <div class="info-label">Korlap</div>
                        <div class="info-value fw-bold text-dark">
                            {!! $korlap ? '<i class="ti tabler-user-check text-success me-1"></i> ' . $korlap : '<span class="text-muted">Tidak Ada</span>' !!}
                        </div>
                    </div>
                </div>

                <!-- STATISTIK KINERJA -->
                <div class="bg-lighter rounded-3 p-3 mb-4 text-start shadow-sm border">
                    <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-chart-bar me-1"></i> Ringkasan Kinerja</h6>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="info-value mb-0">Status KTA</span>
                        <span class="badge {{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'bg-label-danger' : 'bg-label-success' }} fw-bold">
                            {{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'Kedaluwarsa' : 'Berlaku' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="info-value mb-0">Total Pelanggaran</span>
                        <span class="badge {{ $jukir->violations->count() >= 3 ? 'bg-label-danger' : ($jukir->violations->count() > 0 ? 'bg-label-warning' : 'bg-label-dark') }} fw-bold">
                            {{ $jukir->violations->count() }} / 5
                        </span>
                    </div>
                </div>

                <h6 class="pb-2 border-bottom text-start mb-3 fw-bold"><i class="ti tabler-user me-1"></i> Informasi Personal</h6>
                <div class="text-start">
                    <div class="row mb-2">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-id me-2"></i> NIK</div>
                        <div class="col-7 info-value text-end">{{ $jukir->no_ktp ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-phone me-2"></i> Kontak</div>
                        <div class="col-7 info-value text-end">{{ $jukir->phone_number ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-cake me-2"></i> Tgl Lahir</div>
                        <div class="col-7 info-value text-end">{{ $jukir->tanggal_lahir ? \Carbon\Carbon::parse($jukir->tanggal_lahir)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-home me-2"></i> Alamat</div>
                        <div class="col-7 info-value text-end" style="word-wrap: break-word;">{{ $jukir->alamat ?? '-' }}</div>
                    </div>
                    <div class="row mb-2 mt-3 pt-3 border-top">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-calendar-event me-2"></i> KTA Terbit</div>
                        <div class="col-7 info-value text-end fw-bold">{{ $jukir->kta_start_date ? \Carbon\Carbon::parse($jukir->kta_start_date)->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 info-label text-muted d-flex align-items-center"><i class="ti tabler-calendar-off me-2"></i> KTA Expire</div>
                        <div class="col-7 info-value text-end fw-bold {{ \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() ? 'text-danger' : '' }}">{{ $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->format('d/m/Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($jukir->image_ktp)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="pb-2 border-bottom mb-3 fw-bold"><i class="ti tabler-id-badge-2 text-primary me-1"></i> Dokumen KTP</h6>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ktpModal" class="d-block text-center rounded bg-lighter p-2">
                        <img src="{{ Storage::url($jukir->image_ktp) }}" alt="Foto KTP" class="img-fluid rounded shadow-sm" style="max-height: 150px; cursor: zoom-in; object-fit: contain;">
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- KOLOM KANAN: KTA & TAB AKTIVITAS -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="nav-align-top mb-4 shadow-sm rounded-3 bg-white">
            <ul class="nav nav-pills p-2 mb-0" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active px-4 fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-kta" aria-controls="tab-kta" aria-selected="true">
                        <i class="ti tabler-id-badge me-1"></i> ID Card (KTA)
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link px-4 fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-riwayat" aria-controls="tab-riwayat" aria-selected="false">
                        <i class="ti tabler-history me-1"></i> Riwayat Aktivitas
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link px-4 fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-pelanggaran" aria-controls="tab-pelanggaran" aria-selected="false">
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
                            <div class="card-header bg-transparent border-bottom text-center pb-3 pt-4">
                                <h5 class="mb-0 fw-bold text-dark">KTA Bagian Depan</h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="kta-preview-container">
                                    <iframe id="frame-front" 
                                            src="{{ route('admin.jukirs.print-kta', ['jukir' => $jukir->id, 'preview' => 'front']) }}" 
                                            class="kta-iframe"
                                            onload="this.style.transform = 'scale(' + (this.parentElement.clientWidth / 638) + ')';">
                                    </iframe>
                                </div>
                                <button type="button" onclick="document.getElementById('frame-front').contentWindow.downloadCard('kta-front', 'KTA-Front-{{ $jukir->id_jukir }}')" class="btn btn-primary w-100 mt-4 shadow-sm fw-bold">
                                    <i class="ti tabler-download me-1"></i> Download KTA Depan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- KTA Belakang -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
                            <div class="card-header bg-transparent border-bottom text-center pb-3 pt-4">
                                <h5 class="mb-0 fw-bold text-dark">KTA Bagian Belakang</h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="kta-preview-container">
                                    <iframe id="frame-back" 
                                            src="{{ route('admin.jukirs.print-kta', ['jukir' => $jukir->id, 'preview' => 'back']) }}" 
                                            class="kta-iframe"
                                            onload="this.style.transform = 'scale(' + (this.parentElement.clientWidth / 638) + ')';">
                                    </iframe>
                                </div>
                                <button type="button" onclick="document.getElementById('frame-back').contentWindow.downloadCard('kta-back', 'KTA-Back-{{ $jukir->id_jukir }}')" class="btn btn-success w-100 mt-4 shadow-sm fw-bold">
                                    <i class="ti tabler-download me-1"></i> Download KTA Belakang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: RIWAYAT -->
            <div class="tab-pane fade" id="tab-riwayat">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header border-bottom bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="ti tabler-history text-primary me-2"></i> Riwayat Perubahan & Aktivitas</h5>
                    </div>
                    <div class="card-body pt-4 bg-lighter">
                        <ul class="timeline pb-0 mb-0">
                            @forelse($jukir->histories->sortByDesc('created_at') as $history)
                                <li class="timeline-item timeline-item-transparent {{ $loop->last ? 'border-transparent pb-0' : '' }}">
                                    @php
                                        $iconClass = 'bg-primary';
                                        $icon = 'ti-edit';
                                        if($history->action === 'Create') { $iconClass = 'bg-success'; $icon = 'ti-plus'; }
                                        elseif($history->action === 'Delete') { $iconClass = 'bg-danger'; $icon = 'ti-trash'; }
                                        elseif(str_contains(strtolower($history->description), 'pelanggaran')) { $iconClass = 'bg-warning'; $icon = 'ti-alert-triangle'; }
                                    @endphp
                                    
                                    <span class="timeline-point timeline-point-primary {{ $iconClass }} d-flex align-items-center justify-content-center">
                                        <i class="ti {{ $icon }} text-white" style="font-size: 0.8rem;"></i>
                                    </span>
                                    
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-2 pb-2 border-bottom">
                                            <h6 class="mb-0 fw-bold text-dark fs-5">
                                                {{ $history->action === 'Update' ? 'Pembaruan Data' : ($history->action === 'Create' ? 'Pendaftaran Jukir' : $history->action) }}
                                            </h6>
                                            <small class="badge bg-label-secondary"><i class="ti tabler-clock me-1"></i> {{ $history->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-3 text-muted">{{ $history->description }}</p>
                                        
                                        @if($history->action == 'Update' && $history->old_values && $history->new_values)
                                            <div class="mb-3 bg-white border rounded p-3 shadow-sm">
                                                <h6 class="fw-bold text-primary mb-2 fs-6"><i class="ti tabler-list-details me-1"></i> Detail Perubahan</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="30%">Data</th>
                                                                <th width="35%">Sebelumnya</th>
                                                                <th width="35%">Diperbarui Menjadi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="border-top-0">
                                                            @foreach($history->new_values as $key => $newValue)
                                                                @if(isset($history->old_values[$key]) && $history->old_values[$key] !== $newValue)
                                                                    <tr>
                                                                        <td class="fw-bold text-dark bg-lighter">{{ translateHistoryField($key) }}</td>
                                                                        <td class="text-danger"><del>{!! translateHistoryValue($key, $history->old_values[$key], $locationNames) !!}</del></td>
                                                                        <td class="text-success fw-bold"><i class="ti tabler-arrow-right me-1 fs-6"></i> {!! translateHistoryValue($key, $newValue, $locationNames) !!}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex align-items-center mt-1">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-dark text-white fw-bold">{{ strtoupper(substr($history->user->name ?? 'S', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-muted" style="font-size: 0.8rem;">Dicatat oleh</p>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $history->user->name ?? 'Sistem Otomatis' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <div class="text-center py-5 bg-white rounded shadow-sm border">
                                    <div class="avatar avatar-xl mx-auto mb-3 bg-label-secondary">
                                        <i class="ti tabler-history fs-2"></i>
                                    </div>
                                    <h5 class="text-dark fw-bold mb-1">Belum Ada Riwayat</h5>
                                    <p class="text-muted mb-0">Riwayat aktivitas jukir akan muncul di sini.</p>
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
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header border-bottom bg-white py-3">
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
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark">Tanggal Kejadian <span class="text-danger">*</span></label>
                                        <input type="date" name="violation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label fw-bold text-dark">Keterangan Lengkap Pelanggaran <span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan bentuk pelanggaran secara mendetail..." required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-warning shadow-sm fw-bold px-4" {{ $jukir->is_blacklisted ? 'disabled' : '' }}>
                                        <i class="ti tabler-device-floppy me-2"></i> Simpan Pelanggaran
                                    </button>
                                </div>
                            </form>
                            @if($jukir->is_blacklisted)
                                <div class="mt-3 alert alert-danger mb-0 p-3 text-center fw-bold shadow-sm"><i class="ti tabler-ban me-1 fs-5"></i> Jukir sudah di-blacklist. Tidak dapat menambah pelanggaran baru.</div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- List Pelanggaran -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="ti tabler-list text-primary me-2"></i> Daftar Pelanggaran</h5>
                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-6 shadow-sm">Total: {{ $jukir->violations->count() }} / 5</span>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center fw-bold">No</th>
                                    <th width="15%" class="fw-bold">Tanggal</th>
                                    <th class="fw-bold">Keterangan Pelanggaran</th>
                                    <th width="15%" class="text-center fw-bold">Pencatat</th>
                                    @if(in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                                        <th width="10%" class="text-center fw-bold">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($jukir->violations->sortByDesc('violation_date') as $index => $violation)
                                    <tr>
                                        <td class="text-center fw-bold text-dark">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-label-dark"><i class="ti tabler-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') }}</span>
                                        </td>
                                        <td style="white-space: normal;" class="text-dark">{{ $violation->description }}</td>
                                        <td class="text-center"><span class="badge bg-label-secondary fw-bold"><i class="ti tabler-user me-1"></i> {{ $violation->user->name ?? 'Sistem' }}</span></td>
                                        @if(in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                                            <td class="text-center">
                                                <form action="{{ route('admin.jukir-violations.destroy', $violation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus catatan pelanggaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger rounded-pill shadow-sm" data-bs-toggle="tooltip" title="Hapus Pelanggaran">
                                                        <i class="ti tabler-trash icon-22px"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']) ? 5 : 4 }}" class="text-center py-5 bg-lighter">
                                            <div class="text-muted">
                                                <div class="avatar avatar-xl mx-auto mb-3 bg-label-success">
                                                    <i class="ti tabler-shield-check fs-2"></i>
                                                </div>
                                                <h5 class="mb-1 text-dark fw-bold">Bersih dari Pelanggaran</h5>
                                                <p class="mb-0">Belum ada catatan pelanggaran untuk jukir ini.</p>
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
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white fw-bold"><i class="ti tabler-id-badge-2 me-2"></i> Dokumen KTP: {{ $jukir->nama_jukir }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0 bg-lighter">
                    <img src="{{ Storage::url($jukir->image_ktp) }}" class="img-fluid w-100" alt="Foto KTP" style="object-fit: contain; max-height: 80vh;">
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
