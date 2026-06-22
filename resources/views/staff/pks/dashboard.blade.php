@extends('layouts.contentNavbarLayout')
@section('title', 'Dashboard Staff PKS')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
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
        <div class="fintech-card shadow-lg text-white p-4 p-lg-5 animate__animated animate__fadeInLeft h-100 d-flex align-items-center position-relative">
            <i class="ti tabler-file-description position-absolute text-white opacity-10" style="font-size: 220px; right: -20px; bottom: -40px; transform: rotate(-10deg);"></i>
            <div class="row w-100 align-items-center position-relative z-1">
                <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
                    <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ explode(' ', $staffName)[0] }}! <span class="waving-hand">👋</span></h2>
                    <div class="d-inline-flex flex-wrap gap-3 justify-content-center justify-content-md-start mb-4">
                        <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                           <i class="ti tabler-id me-1"></i> NIP: <strong>{{ $staffNip ? formatNip($staffNip) : '-' }}</strong>
                        </div>
                    </div>
                    <p class="mb-0 opacity-75 fs-6" style="max-width: 500px;">
                        Pusat pengelolaan data Perjanjian Kerjasama (PKS) dan Titik Lokasi Parkir. Tetap pantau masa berlaku PKS agar selalu terbarukan.
                    </p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="position-relative d-inline-block">
                        <div class="position-absolute w-100 h-100 rounded-circle bg-white opacity-25" style="top: 10px; left: -10px; filter: blur(20px);"></div>
                        <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 140px; height: 140px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="glass-card h-100 p-0 border-start border-4 border-primary animate__animated animate__fadeInRight" style="animation-delay: 0.2s;">
            <div class="p-3 d-flex align-items-center h-100">
                <img src="{{ $currentLeader && $currentLeader->user->img ? asset('storage/'.$currentLeader->user->img) : asset('assets/img/avatars/1.png') }}"
                    alt="Pimpinan" class="rounded-circle shadow-sm me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">{{ $currentLeader->user->name ?? 'Belum Ada' }}</h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Pimpinan UPT (NIP: {{ $currentLeader->employee_number ? formatNip($currentLeader->employee_number) : '-' }})</p>
                    <p class="text-primary fw-medium mb-0 mt-1" style="font-size: 0.70rem;"><i class="ti tabler-calendar-check me-1"></i>Mulai: {{ $currentLeader ? $currentLeader->start_date->translatedFormat('d M Y') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 2. QUICK STATS (4 kartu) --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast">
            <div class="text-center">
                <div class="stat-glow-icon bg-white text-primary mx-auto mb-2"><i class="ti tabler-file-text ti-md"></i></div>
                <div class="fw-bolder text-primary" style="font-size: 1.5rem;">{{ $totalAgreements }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1">PKS Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast" style="animation-delay: 0.1s;">
            <div class="text-center">
                <div class="stat-glow-icon bg-white text-info mx-auto mb-2"><i class="ti tabler-map-pin ti-md"></i></div>
                <div class="fw-bolder text-info" style="font-size: 1.5rem;">{{ $totalParkingLocations }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1">Titik Parkir</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <a href="{{ route('masterdata.road-sections.index') }}" class="text-decoration-none d-block h-100">
            <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast" style="animation-delay: 0.2s;">
                <div class="text-center">
                    <div class="stat-glow-icon bg-white text-dark mx-auto mb-2"><i class="ti tabler-road ti-md"></i></div>
                    <div class="fw-bolder text-dark" style="font-size: 1.5rem;">{{ $totalRoadSections }}</div>
                    <div class="text-muted small fw-bold text-uppercase mt-1">Ruas Jalan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="glass-card p-3 h-100 animate__animated animate__jackInTheBox animate__fast" style="animation-delay: 0.3s;">
            <div class="text-center">
                <div class="stat-glow-icon bg-white text-secondary mx-auto mb-2"><i class="ti tabler-user-pin ti-md"></i></div>
                <div class="fw-bolder text-secondary" style="font-size: 1.5rem;">{{ $totalFieldCoordinators }}</div>
                <div class="text-muted small fw-bold text-uppercase mt-1">Korlap</div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 3. CHART + LOKASI TERBARU --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s d-flex flex-column">
            <div class="p-4 border-bottom bg-transparent text-center">
                <h5 class="fw-bold text-dark mb-0"><i class="ri icon-base ti tabler-chart-bar text-primary me-2"></i> Top 10 Ruas Jalan (by Titik Lokasi)</h5>
            </div>
            <div class="p-4 flex-grow-1">
                <div id="locations-per-road-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s" style="animation-delay: 1.2s;">
            <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-primary"><i class="ti tabler-map-pin-2 me-2"></i>Lokasi Terbaru</h6>
                @if ($totalParkingLocations > 10)
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-xs btn-primary rounded-pill shadow-sm">Lihat Semua</a>
                @endif
            </div>
            <div class="p-2" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-borderless premium-table mb-0">
                    <thead class="bg-lighter">
                        <tr>
                            <th>Lokasi</th>
                            <th class="text-end">Zona</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentParkingLocations as $loc)
                        <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.parking-locations.show', $loc->id) }}'">
                            <td class="py-2 fw-bold text-dark">{{ Str::limit($loc->name, 30) }}</td>
                            <td class="text-end py-2"><span class="badge bg-label-secondary">{{ $loc->roadSection->zone ?? '-' }}</span></td>
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
        <div class="glass-card h-100 p-0 animate__animated animate__slideInUp animate__delay-1s" style="animation-delay: 1.4s;">
            <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-info"><i class="ti tabler-file-description me-2"></i>PKS Terbaru</h6>
                @if ($totalAgreements > 10)
                    <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-xs btn-info rounded-pill shadow-sm text-white">Lihat Semua</a>
                @endif
            </div>
            <div class="p-2" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-borderless premium-table mb-0">
                    <thead class="bg-lighter">
                        <tr><th>No. PKS</th><th>Korlap</th><th class="text-center">Titik</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentAgreements as $pks)
                        <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.agreements.show', $pks->id) }}'">
                            <td class="py-2 fw-bold text-info">{{ $pks->agreement_number }}</td>
                            <td class="py-2 text-dark fw-medium">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                            <td class="py-2 text-center"><span class="badge bg-label-primary rounded-pill">{{ $pks->active_parking_locations_count }}</span></td>
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
        <div class="glass-card h-100 p-0 animate__animated animate__slideInUp animate__delay-1s" style="animation-delay: 1.6s;">
            <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0 text-warning"><i class="ti tabler-alert-triangle me-2"></i>PKS Segera Berakhir</h6>
            </div>
            <div class="p-2" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-borderless premium-table mb-0">
                    <thead class="bg-lighter">
                        <tr><th>No. PKS</th><th>Korlap</th><th class="text-end">Sisa</th></tr>
                    </thead>
                    <tbody>
                        @forelse($expiringAgreements as $pks)
                            @php $daysLeft = now()->diffInDays($pks->end_date, false); @endphp
                        <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.agreements.show', $pks->id) }}'">
                            <td class="py-2 fw-bold text-warning">{{ $pks->agreement_number }}</td>
                            <td class="py-2 text-dark fw-medium">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                            <td class="py-2 text-end"><span class="badge {{ $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning' }}">{{ (int)$daysLeft }} hari</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-success p-3"><i class="ti tabler-checks me-1 ti-md"></i>Semua PKS aman.</td></tr>
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
                chart: { 
                    type: 'bar', height: 380, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif',
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const ids = @json($barChartData['ids'] ?? []);
                            const id = ids[config.dataPointIndex];
                            if (id) {
                                let url = "{{ route('masterdata.road-sections.show', ':id') }}";
                                window.location.href = url.replace(':id', id);
                            }
                        }
                    }
                },
                plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 6, distributed: true } },
                series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                xaxis: { categories: @json($barChartData['labels']), labels: { style: { colors: '#64748b' } } },
                yaxis: { labels: { style: { colors: '#334155', fontWeight: 500 } } },
                dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: (val) => val, offsetX: 0, dropShadow: { enabled: true } },
                tooltip: { theme: 'light' },
                legend: { show: false },
                colors: ['#6366f1', '#8b5cf6', '#d946ef', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#14b8a6'],
                states: { hover: { filter: { type: 'darken', value: 0.9 } }, active: { filter: { type: 'darken', value: 0.8 } } }
            }).render();
            // Add pointer cursor
            document.querySelector("#locations-per-road-chart").style.cursor = "pointer";
        }
    });
</script>
@endsection
