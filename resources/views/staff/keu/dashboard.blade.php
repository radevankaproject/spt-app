@extends('layouts.app')
@section('title', 'Dashboard Staff Keuangan')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
    .hero-card-keu { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    .quick-stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; }
    .quick-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
    .quick-stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
    .quick-stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table tbody tr { transition: all 0.2s ease; }
    .premium-table tbody tr:hover { background-color: rgba(16, 185, 129, 0.05); }
</style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-staff-keu-dashboard')
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

{{-- ✅ 1. HERO CARD --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm hero-card-keu text-white h-100" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5 d-flex align-items-center position-relative overflow-hidden">
                <div class="row w-100 align-items-center position-relative z-1">
                    <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
                        <span class="badge bg-white text-success rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                            <i class="ri ri-calendar-todo-line me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                        <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ explode(' ', $staffName)[0] }}! 👋</h2>
                        <div class="badge border border-white text-white rounded-pill px-3 py-2 mb-3">
                           <i class="ri ri-profile-line me-1 align-middle"></i> NIP: {{ $staffNip }}
                        </div>
                        <p class="mb-0 opacity-75 fs-6" style="max-width: 500px;">
                            Pusat pantauan data penerimaan setoran pendapatan parkir. Berikut adalah statistik kinerja keuangan terkini.
                        </p>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <div class="d-inline-block position-relative rounded-circle p-1" style="background: linear-gradient(135deg, #f6d365 0%, #ffb142 100%);">
                            <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle shadow-lg border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
                        </div>
                    </div>
                </div>
                <i class="ri ri-wallet-3-line position-absolute text-white" style="font-size: 220px; right: -20px; bottom: -40px; opacity: 0.1; transform: rotate(-10deg);"></i>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="col-xl-4 col-lg-5">
        <div class="row g-4 h-100">
            <div class="col-12 h-50 pb-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 quick-stat-card">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h6 class="text-sm fw-medium text-muted mb-2"><i class="ri ri-calendar-check-line text-success me-1"></i> SETORAN BULAN INI</h6>
                        <h3 class="fw-bold text-success mb-0">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 h-50 pt-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 quick-stat-card">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h6 class="text-sm fw-medium text-muted mb-2"><i class="ri ri-bar-chart-box-line text-primary me-1"></i> TOTAL TAHUN {{ now()->year }}</h6>
                        <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 2. QUICK STATS (4 kartu) --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-primary mx-auto mb-2"><i class="ri ri-file-text-line ri-20px"></i></div>
                <div class="quick-stat-value text-primary">{{ $totalActiveAgreements }}</div>
                <div class="quick-stat-label text-muted mt-1">PKS Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-success mx-auto mb-2"><i class="ri ri-checkbox-circle-line ri-20px"></i></div>
                <div class="quick-stat-value text-success">{{ $paidCount }}</div>
                <div class="quick-stat-label text-muted mt-1">Sudah Setor</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-danger mx-auto mb-2"><i class="ri ri-close-circle-line ri-20px"></i></div>
                <div class="quick-stat-value text-danger">{{ $unpaidCount }}</div>
                <div class="quick-stat-label text-muted mt-1">Belum Setor</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-warning mx-auto mb-2"><i class="ri ri-time-line ri-20px"></i></div>
                <div class="quick-stat-value text-warning">{{ $pendingValidationsCount }}</div>
                <div class="quick-stat-label text-muted mt-1">Pending</div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 3. CHART SETORAN --}}
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0"><i class="ri icon-base ri-line-chart-line text-primary me-2"></i> Grafik Setoran Tervalidasi ({{ now()->year }})</h5>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold">Tahun {{ now()->year }}</span>
            </div>
            <div class="card-body">
                <div id="deposit-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 4. TABEL SUDAH / BELUM SETOR --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-success">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-success mb-0"><i class="ri ri-checkbox-circle-fill me-2"></i> Sudah Setor (Bulan Ini)</h5>
                <span class="badge bg-success rounded-pill">{{ $paidCount }}</span>
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-sm premium-table">
                    <thead class="table-light">
                        <tr><th>No. PKS</th><th>Koordinator Lapangan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($paidAgreements as $pks)
                        <tr>
                            <td class="fw-medium text-primary">{{$pks->agreement_number}}</td>
                            <td>{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center p-4 text-muted">Belum ada data setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-danger">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-danger mb-0"><i class="ri ri-close-circle-fill me-2"></i> Belum Setor (Bulan Ini)</h5>
                <span class="badge bg-danger rounded-pill">{{ $unpaidCount }}</span>
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-sm premium-table">
                    <thead class="table-light">
                        <tr><th>No. PKS</th><th>Koordinator Lapangan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($unpaidAgreements as $pks)
                        <tr>
                            <td class="fw-medium text-danger">{{$pks->agreement_number}}</td>
                            <td>{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center p-4 text-success fw-medium"><i class="ri ri-check-double-line me-1"></i> Luar Biasa! Semua PKS sudah menyetor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendors-js')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartEl = document.querySelector("#deposit-chart");
        if (chartEl) {
            new ApexCharts(chartEl, {
                chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                series: [{ name: 'Total Setoran', data: @json($depositChartData) }],
                xaxis: { categories: @json($depositChartLabels), labels: { style: { colors: '#64748b' } } },
                yaxis: { labels: { style: { colors: '#64748b' }, formatter: (val) => `Rp ${(val / 1000000).toFixed(1)} Jt` } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                tooltip: { theme: 'light', y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) } },
                colors: ['#10b981']
            }).render();
        }
    });
</script>
@endpush