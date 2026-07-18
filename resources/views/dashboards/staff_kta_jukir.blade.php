@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Staff KTA Jukir')

@section('page-style')
<style>
    .premium-table tbody tr { transition: all 0.2s ease; }
    .premium-table tbody tr:hover { background-color: rgba(99, 102, 241, 0.05); }
</style>
@endsection

@section('content')

@php
    $staffName = Auth::user()->name ?? 'Staff KTA Jukir';
    $staffNip = Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-';
    $userAvatar = Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode($staffName).'&background=fff&color=6366f1';

    $hour = date('H');
    if ($hour >= 5 && $hour < 11) { $greeting = 'Selamat Pagi'; }
    elseif ($hour >= 11 && $hour < 15) { $greeting = 'Selamat Siang'; }
    elseif ($hour >= 15 && $hour < 18) { $greeting = 'Selamat Sore'; }
    else { $greeting = 'Selamat Malam'; }
@endphp

{{-- 1. HERO CARD --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="fintech-card shadow-lg text-white p-4 p-lg-5 animate__animated animate__fadeInLeft d-flex align-items-center position-relative">
            <i class="ti tabler-id-badge position-absolute text-white opacity-10" style="font-size: 220px; right: -20px; bottom: -40px; transform: rotate(-10deg);"></i>
            <div class="row w-100 align-items-center position-relative z-1">
                <div class="col-md-9 text-md-start text-center mb-4 mb-md-0">
                    <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ explode(' ', $staffName)[0] }}! <span class="waving-hand">👋</span></h2>
                    <div class="d-inline-flex flex-wrap gap-3 justify-content-center justify-content-md-start mb-4">
                        <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                           <i class="ti tabler-id me-1"></i> NIP: <strong>{{ $staffNip }}</strong>
                        </div>
                    </div>
                    <p class="mb-0 opacity-75 fs-6" style="max-width: 600px;">
                        Pusat pengelolaan data Juru Parkir, pencetakan Kartu Tanda Anggota (KTA), dan pencatatan riwayat pelanggaran Jukir di lapangan.
                    </p>
                </div>
                <div class="col-md-3 text-center text-md-end">
                    <div class="position-relative d-inline-block">
                        <div class="position-absolute w-100 h-100 rounded-circle bg-white opacity-25" style="top: 10px; left: -10px; filter: blur(20px);"></div>
                        <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 140px; height: 140px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. QUICK STATS --}}
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-4 col-12">
        <a href="{{ route('admin.jukirs.index') }}" class="text-decoration-none d-block h-100">
            <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast">
                <div class="text-center">
                    <div class="stat-glow-icon bg-white text-primary mx-auto mb-2"><i class="ti tabler-users ti-md"></i></div>
                    <div class="fw-bolder text-primary" style="font-size: 1.5rem;">{{ $totalJukir }}</div>
                    <div class="text-muted small fw-bold text-uppercase mt-1">Total Jukir</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast" style="animation-delay: 0.1s;">
            <div class="text-center">
                <div class="stat-glow-icon bg-white text-success mx-auto mb-2"><i class="ti tabler-user-check ti-md"></i></div>
                <div class="fw-bolder text-success" style="font-size: 1.5rem;">{{ $activeJukir }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1">Jukir Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-6">
        <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast" style="animation-delay: 0.2s;">
            <div class="text-center">
                <div class="stat-glow-icon bg-white text-danger mx-auto mb-2"><i class="ti tabler-user-off ti-md"></i></div>
                <div class="fw-bolder text-danger" style="font-size: 1.5rem;">{{ $blacklistedJukir }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1">Blacklisted</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pelanggaran Terbaru -->
    <div class="col-md-6 mb-4">
        <div class="glass-card h-100 animate__animated animate__fadeInUp border-top border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-alert-triangle text-warning me-2"></i> Pelanggaran Terbaru</h6>
                <a href="{{ route('admin.jukirs.index') }}" class="btn btn-xs btn-outline-primary">Data Jukir</a>
            </div>
            <div class="table-responsive">
                <table class="table premium-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2">Jukir</th>
                            <th class="py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentViolations as $violation)
                        <tr>
                            <td class="py-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ $violation->jukir->image_url }}" class="rounded-circle" style="object-fit: cover;">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.jukirs.show', $violation->jukir->id) }}" class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $violation->jukir->nama_jukir }}</a>
                                        <small class="text-muted">{{ $violation->jukir->id_jukir ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2">
                                <span class="d-block text-truncate" style="max-width: 150px; font-size: 0.8rem;" title="{{ $violation->description }}">{{ $violation->description }}</span>
                                <small class="text-warning fw-bold"><i class="ti tabler-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ti tabler-shield-check mb-2 text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mb-0">Tidak ada pelanggaran</h6>
                                    <small>Belum ada catatan pelanggaran terbaru.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Aktivitas -->
    <div class="col-md-6 mb-4">
        <div class="glass-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-history text-info me-2"></i> Riwayat Aktivitas Jukir</h6>
            </div>
            <div class="card-body p-4 pt-3">
                <ul class="timeline pb-0 mb-0">
                    @forelse($recentHistories as $history)
                    <li class="timeline-item timeline-item-transparent {{ $loop->last ? 'border-transparent' : '' }}">
                        <span class="timeline-point timeline-point-info"></span>
                        <div class="timeline-event pb-3">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0 fw-bold text-dark">{{ $history->action }}</h6>
                                <small class="text-muted"><i class="ti tabler-clock me-1"></i>{{ $history->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-muted" style="font-size: 0.85rem;">{{ $history->description }}</p>
                            <div class="d-flex align-items-center bg-lighter rounded p-2">
                                <div class="avatar avatar-xs me-2">
                                    <img src="{{ $history->jukir->image_url }}" class="rounded-circle" style="object-fit: cover;">
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $history->jukir->nama_jukir }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Oleh: {{ $history->user->name ?? 'Sistem' }}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-4 list-unstyled">
                        <div class="text-muted">
                            <i class="ti tabler-history mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0">Tidak ada riwayat</h6>
                            <small>Belum ada riwayat aktivitas terbaru.</small>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
