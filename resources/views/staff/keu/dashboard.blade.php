@extends('layouts.contentNavbarLayout')
@section('title', 'Dashboard Staff Keuangan')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
    .value-huge { font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
    
    /* Vuexy native override */
    .card { border: none !important; }
</style>
@endsection

@section('content')

@php
    $staffName = Auth::user()->name ?? 'Staff Keuangan';
    $staffNip = Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-';
    $userAvatar = Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode($staffName).'&background=fff&color=10b981';

    $hour = date('H');
    if ($hour >= 5 && $hour < 11) { $greeting = 'Selamat Pagi'; }
    elseif ($hour >= 11 && $hour < 15) { $greeting = 'Selamat Siang'; }
    elseif ($hour >= 15 && $hour < 18) { $greeting = 'Selamat Sore'; }
    else { $greeting = 'Selamat Malam'; }
@endphp

{{-- ✅ 1. HERO SECTION (Gradient Mesh) --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="fintech-card shadow-lg text-white p-4 p-lg-5 animate__animated animate__fadeInDown">
            <div class="row align-items-center position-relative z-1">
                <div class="col-md-8 text-center text-md-start">
                    <span class="badge bg-white text-success rounded-pill px-3 py-2 fw-bold shadow-sm mb-3 text-primary">
                        <i class="ti tabler-calendar-event me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <h1 class="text-white fw-bolder mb-2" style="font-size: 2.5rem; letter-spacing: -1px;">{{ $greeting }}, {{ explode(' ', $staffName)[0] }}! 🚀</h1>
                    <p class="fs-5 opacity-75 mb-4" style="max-width: 600px; font-weight: 300;">
                        Sistem mencatat performa setoran yang sangat baik bulan ini. Pantau terus grafik pendapatan dan status setoran tiap koordinator.
                    </p>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                        <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                            <i class="ti tabler-id me-1"></i> NIP: <strong>{{ $staffNip ? formatNip($staffNip) : '-' }}</strong>
                        </div>
                        <span class="btn btn-light rounded-pill fw-bold px-4 shadow-sm text-success"><a href="#statistik">Lihat Statistik <i class="ti tabler-arrow-down ms-1"></i></span></a>
                    </div>
                </div>
                <div class="col-md-4 text-center text-md-end mt-4 mt-md-0">
                    <div class="position-relative d-inline-block">
                        <div class="position-absolute w-100 h-100 rounded-circle bg-white opacity-25" style="top: 10px; left: -10px; filter: blur(20px);"></div>
                        <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 150px; height: 150px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle p-2" title="Online" style="margin-bottom: 10px; margin-right: 10px;"></span>
                    </div>
                </div>
            </div>
            <i class="ti tabler-chart-pie position-absolute text-white" style="font-size: 350px; right: -50px; top: -50px; opacity: 0.05; transform: rotate(15deg);"></i>
        </div>
    </div>
</div>

{{-- ✅ 2. GLASSMORPHISM HIGHLIGHTS --}}
<div class="row g-4 mb-4" id="statistik">
    <div class="col-xl-6 col-lg-6">
        <div class="glass-card p-4 h-100 animate__animated animate__slideInLeft animate__fast">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted fw-bold mb-1 text-uppercase tracking-wider">Total Setoran Bulan Ini</h6>
                    <div class="value-huge text-success">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</div>
                </div>
                <div class="stat-glow-icon bg-success bg-opacity-10 text-success">
                    <i class="ti tabler-wallet ti-xl"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-success rounded-pill me-2"><i class="ti tabler-trending-up"></i> Tervalidasi</span>
                <small class="text-muted">Semua transaksi sukses bulan ini</small>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="glass-card p-4 h-100 animate__animated animate__slideInRight animate__fast">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted fw-bold mb-1 text-uppercase tracking-wider">Total Pendapatan {{ now()->year }}</h6>
                    <div class="value-huge text-primary">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</div>
                </div>
                <div class="stat-glow-icon bg-primary bg-opacity-10 text-primary">
                    <i class="ti tabler-chart-bar ti-xl"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary rounded-pill me-2"><i class="ti tabler-calendar-stats"></i> Akumulasi</span>
                <small class="text-muted">Dari 1 Januari hingga saat ini</small>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 3. QUICK STATS MINI GRID --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="fintech-card fintech-card-green text-center p-3 h-100 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 0.1s;">
            <div class="stat-glow-icon bg-white text-info mx-auto mb-3"><i class="ti tabler-file-type-doc ti-md"></i></div>
            <h3 class="fw-bolder text-info mb-1">{{ $totalActiveAgreements }}</h3>
            <span class="text-muted small fw-bold text-uppercase">PKS Aktif</span>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="fintech-card fintech-card-purple text-center p-3 h-100 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 0.2s;">
            <div class="stat-glow-icon bg-white text-success mx-auto mb-3"><i class="ti tabler-circle-check ti-md"></i></div>
            <h3 class="fw-bolder text-success mb-1">{{ $paidCount }}</h3>
            <span class="text-muted small fw-bold text-uppercase">Sudah Setor</span>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="fintech-card fintech-card-blue text-center p-3 h-100 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 0.3s;">
            <div class="stat-glow-icon bg-white text-danger mx-auto mb-3"><i class="ti tabler-circle-x ti-md"></i></div>
            <h3 class="fw-bolder text-danger mb-1">{{ $unpaidCount }}</h3>
            <span class="text-muted small fw-bold text-uppercase">Belum Setor</span>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="fintech-card fintech-card-orange text-center p-3 h-100 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 0.4s;">
            <div class="stat-glow-icon bg-white text-warning mx-auto mb-3"><i class="ti tabler-clock-hour-4 ti-md"></i></div>
            <h3 class="fw-bolder text-warning mb-1">{{ $pendingValidationsCount }}</h3>
            <span class="text-muted small fw-bold text-uppercase">Menunggu Validasi</span>
        </div>
    </div>
</div>

{{-- ✅ 4. CHART & TABLES COMBO --}}
<div class="row g-4">
    {{-- CHART --}}
    <div class="col-xl-8 col-lg-12">
        <div class="glass-card p-0 h-100 animate__animated animate__fadeInUp animate__delay-1s" style="animation-delay: 0.5s;">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="ti tabler-chart-area-line text-success me-2"></i> Grafik Pendapatan Parkir</h5>
                    <small class="text-muted">Fluktuasi setoran tervalidasi per bulan</small>
                </div>
                <div class="badge bg-label-success rounded-pill px-3 py-2 fw-bold">Tahun {{ now()->year }}</div>
            </div>
            <div class="p-4">
                <div id="deposit-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- LIST PKS --}}
    <div class="col-xl-4 col-lg-12">
        <div class="row g-4 h-100">
            {{-- Sudah Setor --}}
            <div class="col-xl-12 col-md-6">
                <div class="glass-card p-0 animate__animated animate__bounceInRight animate__delay-1s" style="animation-delay: 0.6s;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-success bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                        <h6 class="fw-bold mb-0 text-success"><i class="ti tabler-shield-check me-2"></i> Sudah Setor</h6>
                        <span class="badge bg-success rounded-pill">{{ $paidCount }}</span>
                    </div>
                    <div class="p-2" style="max-height: 220px; overflow-y: auto;">
                        @forelse($paidAgreements as $pks)
                            <div class="d-flex align-items-center p-2 premium-list-item">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i class="ti tabler-check"></i></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</h6>
                                    <small class="text-muted">{{$pks->agreement_number}}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4 text-muted small">Belum ada data setoran.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Belum Setor --}}
            <div class="col-xl-12 col-md-6">
                <div class="glass-card p-0 animate__animated animate__bounceInRight animate__delay-1s" style="animation-delay: 0.8s;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-danger bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                        <h6 class="fw-bold mb-0 text-danger"><i class="ti tabler-shield-x me-2"></i> Belum Setor</h6>
                        <span class="badge bg-danger rounded-pill">{{ $unpaidCount }}</span>
                    </div>
                    <div class="p-2" style="max-height: 220px; overflow-y: auto;">
                        @forelse($unpaidAgreements as $pks)
                            <div class="d-flex align-items-center p-2 premium-list-item">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="ti tabler-x"></i></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</h6>
                                    <small class="text-muted">{{$pks->agreement_number}}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4 text-success fw-bold small"><i class="ti tabler-stars me-1"></i> Semua PKS sudah lunas!</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}" defer></script>
@endsection

@section('page-script')
<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        const chartEl = document.querySelector("#deposit-chart");
        if (chartEl) {
            new ApexCharts(chartEl, {
                chart: { 
                    type: 'area', 
                    height: 350, 
                    toolbar: { show: false }, 
                    fontFamily: 'Public Sans, sans-serif',
                    dropShadow: { enabled: true, top: 10, left: 0, blur: 5, color: '#10b981', opacity: 0.2 }
                },
                series: [{ name: 'Total Setoran', data: @json($depositChartData) }],
                xaxis: { 
                    categories: @json($depositChartLabels), 
                    labels: { style: { colors: '#64748b', fontWeight: 600 } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { 
                    labels: { style: { colors: '#64748b', fontWeight: 600 }, formatter: (val) => `Rp ${(val / 1000000).toFixed(1)} Jt` } 
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                fill: { 
                    type: 'gradient', 
                    gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.05, stops: [0, 90, 100] } 
                },
                tooltip: { 
                    theme: 'light', 
                    y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) } 
                },
                colors: ['#10b981'],
                grid: { borderColor: '#e2e8f0', strokeDashArray: 5, padding: { top: 20 } }
            }).render();
        }
    });
</script>
@endsection