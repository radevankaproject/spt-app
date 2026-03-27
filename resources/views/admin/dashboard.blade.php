@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        /* Custom Scrollbar untuk tabel */
        .perfect-scrollbar-table {
            position: relative;
            max-height: 380px;
        }
        /* Hero Search Card styling */
        .hero-search-card {
            background: linear-gradient(135deg, #696cff 0%, #3b3e99 100%);
            color: white;
            border: none;
            overflow: hidden;
        }
        .hero-search-card .input-group {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        /* Efek Hover Tabel Premium */
        .premium-table tbody tr {
            transition: all 0.2s ease;
        }
        .premium-table tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.05);
            transform: scale(1.01);
        }
    </style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-admin-dashboard')
@endsection

@section('content')

    {{-- ✅ 1. HERO SEARCH SECTION --}}
    <div class="card mb-4 hero-search-card shadow-lg">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h3 class="text-white fw-bold mb-2">Selamat Datang di Pusat Kendali Admin! 🚀</h3>
                    <p class="text-white-50 mb-4">Akses cepat data Perjanjian Kerja Sama (PKS) berdasarkan nomor registrasi.</p>

                    <form action="{{ route('admin.agreements.find') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-lg rounded-pill overflow-hidden bg-white">
                            <span class="input-group-text bg-white border-0 text-primary ps-4">
                                {{-- ✅ ICON FIXED (tambah class ri) --}}
                                <i class="ri icon-base ri-search-line ri-22px"></i>
                            </span>
                            <input type="search" name="agreement_number" class="form-control border-0 fs-6 shadow-none"
                                placeholder="Ketik Nomor PKS di sini..." required>
                            <button class="btn btn-primary px-4 fw-bold" type="submit">Cari PKS</button>
                        </div>
                        @if (session('error'))
                            <div class="text-danger bg-white rounded-pill d-inline-block px-3 py-1 fw-bold small mt-3 shadow-sm">
                                {{-- ✅ ICON FIXED --}}
                                <i class="ri ri-error-warning-line me-1"></i> {{ session('error') }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. TOP METRICS CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Pimpinan --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    @php
                        $leaderName = $currentLeader->user->name ?? 'Belum Ada';
                        $leaderAvatar = ($currentLeader && $currentLeader->user && $currentLeader->user->img && file_exists(public_path($currentLeader->user->img)))
                            ? asset($currentLeader->user->img)
                            : "https://ui-avatars.com/api/?name=" . urlencode($leaderName) . "&background=random&color=fff&bold=true";
                    @endphp
                    <img src="{{ $leaderAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3" width="56" height="56" style="object-fit: cover;">
                    <div>
                        <p class="text-muted mb-0 text-sm fw-medium">Pihak Pertama (Pimpinan)</p>
                        <h5 class="mb-0 fw-bold text-dark">{{ $leaderName }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Rekening --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-card-icon bg-label-info me-3">
                        {{-- ✅ ICON FIXED --}}
                        <i class="ri icon-base ri-bank-card-line ri-24px"></i>
                    </div>
                    @if ($activeBankAccount)
                        <div>
                            <p class="text-muted mb-0 text-sm fw-medium">Rekening BLUD Aktif ({{ $activeBankAccount->bank_name }})</p>
                            <h5 class="mb-0 fw-bold text-info">{{ $activeBankAccount->account_number }}</h5>
                        </div>
                    @else
                        <div>
                            <p class="text-muted mb-0 text-sm fw-medium">Rekening BLUD</p>
                            <h5 class="mb-0 fw-bold text-danger">Belum disetting</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Total Setoran --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-label-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-card-icon bg-success text-white me-3 shadow-sm">
                        {{-- ✅ ICON FIXED --}}
                        <i class="ri icon-base ri-money-dollar-circle-line ri-24px"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-sm fw-bold text-success">TOTAL SETORAN (TAHUN INI)</p>
                        <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($currentYearValidatedDeposit, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 3. MAIN CHARTS ROW --}}
    <div class="row g-4 mb-4">
        {{-- Mixed Chart --}}
        <div class="col-xl-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center pb-3">
                    <h5 class="card-title fw-bold mb-0">
                        {{-- ✅ ICON FIXED --}}
                        <i class="ri icon-base ri-bar-chart-grouped-line text-primary me-2 ri-20px"></i> Grafik Validasi Setoran
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">Tahun {{ now()->year }}</span>
                </div>
                <div class="card-body pt-4">
                    <div id="deposit-mixed-chart"></div>
                </div>
            </div>
        </div>

        {{-- Bar Chart (Top 10 Lokasi) --}}
        <div class="col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent pb-3">
                    <h5 class="card-title fw-bold mb-0">
                        {{-- ✅ ICON FIXED --}}
                        <i class="ri icon-base ri-road-map-line text-warning me-2 ri-20px"></i> Top 10 Ruas Jalan
                    </h5>
                    <small class="text-muted">Berdasarkan kepadatan titik lokasi</small>
                </div>
                <div class="card-body pt-4">
                    <div id="locations-per-road-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 4. POLAR CHARTS (ZONA) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-center pb-0">
                    <h6 class="fw-bold mb-0">Distribusi Ruas Jalan (Per Zona)</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="road-section-zone-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-center pb-0">
                    <h6 class="fw-bold mb-0">Distribusi Titik Lokasi (Per Zona)</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="parking-location-zone-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 5. TABLES LIST ROW --}}
    <div class="row g-4">

        {{-- Tabel 1: Setoran Terbaru --}}
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0">Setoran Terbaru</h6>
                    <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Detail</a>
                </div>
                <div class="table-responsive text-nowrap perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentDeposits as $deposit)
                                @php
                                    $coordName = $deposit->agreement->fieldCoordinator->user->name ?? 'N/A';
                                    $coordAvatar = "https://ui-avatars.com/api/?name=" . urlencode($coordName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr>
                                    <td class="ps-0 pe-2 py-2" width="40px">
                                        <img src="{{ $coordAvatar }}" alt="Avatar" class="rounded-circle shadow-sm">
                                    </td>
                                    <td class="py-2">
                                        <h6 class="mb-0 text-sm fw-bold">{{ Str::limit($coordName, 15) }}</h6>
                                        <small class="text-muted">{{ $deposit->deposit_date->format('d M Y') }}</small>
                                    </td>
                                    <td class="text-end py-2 pe-0">
                                        <span class="badge bg-label-success fw-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel 2: Lokasi Terbaru --}}
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0">Lokasi Parkir Baru</h6>
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Detail</a>
                </div>
                <div class="table-responsive text-nowrap perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentParkingLocations as $location)
                                <tr>
                                    <td class="ps-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                {{-- ✅ ICON FIXED --}}
                                                <span class="avatar-initial rounded-circle bg-label-info"><i class="ri icon-base ri-map-pin-line"></i></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm fw-bold">{{ Str::limit($location->name, 20) }}</h6>
                                                <small class="text-muted">{{ $location->roadSection->name ?? 'Tanpa Ruas' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-center py-4 text-muted">Belum ada lokasi parkir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel 3: Korlap Terbaru --}}
        <div class="col-lg-4 col-md-12">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0">Korlap Lapangan</h6>
                    <a href="{{ route('admin.field-coordinators.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Detail</a>
                </div>
                <div class="table-responsive text-nowrap perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentCoordinators as $coordinator)
                                @php
                                    $cName = $coordinator->user->name ?? 'N/A';
                                    $cAvatar = ($coordinator->user && $coordinator->user->img && file_exists(public_path($coordinator->user->img)))
                                        ? asset($coordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr>
                                    <td class="ps-0 pe-2 py-2" width="40px">
                                        <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td class="py-2">
                                        <h6 class="mb-0 text-sm fw-bold">{{ Str::limit($cName, 15) }}</h6>
                                        {{-- ✅ ICON FIXED --}}
                                        <small class="text-muted"><i class="ri ri-phone-line align-bottom"></i> {{ $coordinator->phone_number ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">Belum ada koordinator.</td></tr>
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
            const primaryColor = config.colors.primary;
            const infoColor = config.colors.info;
            const warningColor = config.colors.warning;
            const successColor = config.colors.success;
            const dangerColor = config.colors.danger;
            const bodyColor = config.colors.bodyColor;
            const borderColor = config.colors.borderColor;

            // 1. Mixed Chart (Deposit vs Target Asli)
            const mixedChartEl = document.querySelector("#deposit-mixed-chart");
            if (mixedChartEl) {
                const mixedChartOptions = {
                    chart: {
                        height: 380,
                        type: 'line',
                        stacked: false,
                        toolbar: { show: false },
                        fontFamily: 'Work Sans, sans-serif'
                    },
                    stroke: { width: [0, 3], curve: 'smooth' },
                    plotOptions: {
                        bar: { columnWidth: '45%', borderRadius: 4, startingShape: 'rounded' }
                    },
                    series: [
                        { name: 'Total Setoran', type: 'bar', data: @json($mainChartData) },
                        { name: 'Target Proyeksi', type: 'line', data: @json($targetChartData ?? []) }
                    ],
                    xaxis: { categories: @json($mainChartLabels), axisBorder: {show: false}, axisTicks: {show: false} },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                if (!val) return "Rp 0";
                                return `Rp ${(val / 1000000).toFixed(1)} Jt`;
                            },
                            style: { colors: bodyColor }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [0, 1],
                        formatter: function (val) {
                            if(!val || val === 0) return '';
                            return `${(val / 1000000).toFixed(1)} Jt`;
                        },
                        style: { fontSize: '11px', fontWeight: 600 },
                        background: { enabled: true, foreColor: '#fff', padding: 4, borderRadius: 2, borderWidth: 0 }
                    },
                    grid: { borderColor: borderColor, strokeDashArray: 4, padding: { top: 20 } },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function(val) {
                                if (val === undefined || val === null) return "Rp 0";
                                return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    colors: [primaryColor, warningColor],
                    legend: { position: 'top', horizontalAlign: 'left' }
                };
                new ApexCharts(mixedChartEl, mixedChartOptions).render();
            }

            // 2. Bar Chart (Top 10 Ruas Jalan)
            const locationsPerRoadEl = document.querySelector("#locations-per-road-chart");
            if (locationsPerRoadEl) {
                const locationsPerRoadOptions = {
                    chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Work Sans, sans-serif' },
                    plotOptions: {
                        bar: { horizontal: true, barHeight: '50%', borderRadius: 4 }
                    },
                    dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: function (val, opt) { return val + " Titik"; }, offsetX: 0 },
                    series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                    xaxis: { categories: @json($barChartData['labels']), labels: {show: false}, axisBorder: {show: false}, axisTicks: {show: false} },
                    grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                    colors: [infoColor]
                };
                new ApexCharts(locationsPerRoadEl, locationsPerRoadOptions).render();
            }

            // 3. Polar Area: Ruas Jalan Zona
            const roadSectionZoneEl = document.querySelector("#road-section-zone-chart");
            if (roadSectionZoneEl) {
                const roadSectionZoneOptions = {
                    series: @json($zoneChartData['roadSections']),
                    chart: { height: 300, type: 'polarArea', fontFamily: 'Work Sans, sans-serif' },
                    labels: @json($zoneChartData['labels']),
                    stroke: { colors: ['#fff'] },
                    fill: { opacity: 0.8 },
                    colors: [primaryColor, successColor, infoColor, warningColor, dangerColor],
                    legend: { position: 'bottom' }
                };
                new ApexCharts(roadSectionZoneEl, roadSectionZoneOptions).render();
            }

            // 4. Polar Area: Lokasi Zona
            const parkingLocationZoneEl = document.querySelector("#parking-location-zone-chart");
            if (parkingLocationZoneEl) {
                const parkingLocationZoneOptions = {
                    series: @json($zoneChartData['parkingLocations']),
                    chart: { height: 300, type: 'polarArea', fontFamily: 'Work Sans, sans-serif' },
                    labels: @json($zoneChartData['labels']),
                    stroke: { colors: ['#fff'] },
                    fill: { opacity: 0.8 },
                    colors: [warningColor, primaryColor, dangerColor, infoColor, successColor],
                    legend: { position: 'bottom' }
                };
                new ApexCharts(parkingLocationZoneEl, parkingLocationZoneOptions).render();
            }

            // 5. Inisialisasi Perfect Scrollbar
            const scrollableTables = document.querySelectorAll('.perfect-scrollbar-table');
            if (scrollableTables.length) {
                scrollableTables.forEach(el => {
                    new PerfectScrollbar(el, { wheelPropagation: false, suppressScrollX: true });
                });
            }
        });
    </script>
@endpush
