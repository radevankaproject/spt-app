@extends('layouts.contentNavbarLayout')
@section('title', 'Dashboard Staff PKS')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
    .hero-card-pks { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .stat-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    .quick-stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; }
    .quick-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
    .quick-stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
    .quick-stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table tbody tr { transition: all 0.2s ease; }
    .premium-table tbody tr:hover { background-color: rgba(99, 102, 241, 0.05); }
</style>
@endsection



@section('content')

@php
    $staffName = Auth::user()->name ?? 'Staff PKS';
    $staffNip = Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-';
    $userAvatar = Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode($staffName).'&background=fff&color=6366f1';

    $hour = date('H');
    if ($hour >= 5 && $hour < 11) { $greeting = 'Selamat Pagi'; }
    elseif ($hour >= 11 && $hour < 15) { $greeting = 'Selamat Siang'; }
    elseif ($hour >= 15 && $hour < 18) { $greeting = 'Selamat Sore'; }
    else { $greeting = 'Selamat Malam'; }
@endphp

{{-- ✅ 1. HERO CARD + PIMPINAN --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm hero-card-pks text-white h-100" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5 d-flex align-items-center position-relative overflow-hidden">
                <div class="row w-100 align-items-center position-relative z-1">
                    <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
                        <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                            <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                        <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ explode(' ', $staffName)[0] }}! 👋</h2>
                        <div class="badge border border-white text-white rounded-pill px-3 py-2 mb-3">
                           <i class="ti tabler-id me-1 align-middle"></i> NIP: {{ $staffNip }}
                        </div>
                        <p class="mb-0 opacity-75 fs-6" style="max-width: 500px;">
                            Pusat pengelolaan data Perjanjian Kerjasama (PKS) dan Titik Lokasi Parkir. Tetap pantau masa berlaku PKS agar selalu terbarukan.
                        </p>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <div class="d-inline-block position-relative rounded-circle p-1" style="background: linear-gradient(135deg, #f6d365 0%, #ffb142 100%);">
                            <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle shadow-lg border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
                        </div>
                    </div>
                </div>
                <i class="ti tabler-file-description position-absolute text-white" style="font-size: 220px; right: -20px; bottom: -40px; opacity: 0.1; transform: rotate(-10deg);"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-primary">
            <div class="card-body d-flex align-items-center p-3">
                <img src="{{ $currentLeader && $currentLeader->user->img ? asset('storage/'.$currentLeader->user->img) : asset('assets/img/avatars/1.png') }}"
                    alt="Pimpinan" class="rounded-circle shadow-sm me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">{{ $currentLeader->user->name ?? 'Belum Ada' }}</h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Pimpinan UPT (NIP: {{ $currentLeader->employee_number ?? '-' }})</p>
                    <p class="text-primary fw-medium mb-0 mt-1" style="font-size: 0.70rem;"><i class="ti tabler-calendar-check me-1"></i>Mulai: {{ $currentLeader ? $currentLeader->start_date->translatedFormat('d M Y') : '-' }}</p>
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
                <div class="stat-card-icon bg-label-primary mx-auto mb-2"><i class="ti tabler-file-text ti-md"></i></div>
                <div class="quick-stat-value text-primary">{{ $totalAgreements }}</div>
                <div class="quick-stat-label text-muted mt-1">PKS Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-info mx-auto mb-2"><i class="ti tabler-map-pin ti-md"></i></div>
                <div class="quick-stat-value text-info">{{ $totalParkingLocations }}</div>
                <div class="quick-stat-label text-muted mt-1">Titik Parkir</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-dark mx-auto mb-2"><i class="ti tabler-road ti-md"></i></div>
                <div class="quick-stat-value">{{ $totalRoadSections }}</div>
                <div class="quick-stat-label text-muted mt-1">Ruas Jalan</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card quick-stat-card shadow-sm h-100">
            <div class="card-body p-3 text-center">
                <div class="stat-card-icon bg-label-secondary mx-auto mb-2"><i class="ti tabler-user-pin ti-md"></i></div>
                <div class="quick-stat-value">{{ $totalFieldCoordinators }}</div>
                <div class="quick-stat-label text-muted mt-1">Korlap</div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 3. CHART + LOKASI TERBARU --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="card-title fw-bold m-0"><i class="ri icon-base ti tabler-chart-bar text-primary me-2"></i> Top 10 Ruas Jalan (by Titik Lokasi)</h5>
            </div>
            <div class="card-body">
                <div id="locations-per-road-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-primary"><i class="ti tabler-map-pin-2 me-2"></i>Lokasi Terbaru</h6>
                @if ($totalParkingLocations > 10)
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="badge bg-primary text-decoration-none">Lihat Semua</a>
                @endif
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-sm premium-table">
                    <thead class="table-light">
                        <tr>
                            <th>Lokasi</th>
                            <th class="text-end">Zona</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentParkingLocations as $loc)
                        <tr>
                            <td class="fw-medium text-dark">{{ Str::limit($loc->name, 30) }}</td>
                            <td class="text-end"><span class="badge bg-label-secondary">{{ $loc->roadSection->zone ?? '-' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted p-3">Belum ada data lokasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 4. TABEL PKS (TERBARU + SEGERA BERAKHIR) --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-info"><i class="ti tabler-file-description me-2"></i>PKS Terbaru</h6>
                @if ($totalAgreements > 10)
                    <a href="{{ route('masterdata.agreements.index') }}" class="badge bg-info text-decoration-none">Lihat Semua</a>
                @endif
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-hover table-sm premium-table">
                    <thead class="table-light">
                        <tr><th>No. PKS</th><th>Korlap</th><th class="text-center">Titik</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentAgreements as $pks)
                        <tr>
                            <td class="fw-medium text-info">{{ $pks->agreement_number }}</td>
                            <td>{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                            <td class="text-center"><span class="badge bg-label-primary rounded-pill">{{ $pks->active_parking_locations_count }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted p-3">Belum ada data PKS.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-warning"><i class="ti tabler-alert-triangle me-2"></i>PKS Segera Berakhir</h6>
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-hover table-sm premium-table">
                    <thead class="table-light">
                        <tr><th>No. PKS</th><th>Korlap</th><th class="text-end">Sisa</th></tr>
                    </thead>
                    <tbody>
                        @forelse($expiringAgreements as $pks)
                            @php $daysLeft = now()->diffInDays($pks->end_date, false); @endphp
                        <tr>
                            <td class="fw-medium text-warning">{{ $pks->agreement_number }}</td>
                            <td>{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                            <td class="text-end"><span class="badge {{ $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning' }}">{{ (int)$daysLeft }} hari</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-success p-3"><i class="ti tabler-checks me-1"></i>Semua PKS aman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
        const barChartEl = document.querySelector("#locations-per-road-chart");
        if (barChartEl) {
            new ApexCharts(barChartEl, {
                chart: { type: 'bar', height: 380, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 6, distributed: true } },
                series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                xaxis: { categories: @json($barChartData['labels']), labels: { style: { colors: '#64748b' } } },
                yaxis: { labels: { style: { colors: '#334155', fontWeight: 500 } } },
                dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: (val) => val, offsetX: 0, dropShadow: { enabled: true } },
                tooltip: { theme: 'light' },
                legend: { show: false },
                colors: ['#6366f1', '#8b5cf6', '#d946ef', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#14b8a6']
            }).render();
        }
    });
</script>
@endsection
