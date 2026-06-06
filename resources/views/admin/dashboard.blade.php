@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        /* Custom Scrollbar untuk tabel */
        .perfect-scrollbar-table {
            position: relative;
            max-height: 380px;
            overflow: hidden;
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
        /* Menjaga Avatar Tetap Bulat Sempurna */
        .avatar-fit {
            width: 48px;
            height: 48px;
            object-fit: cover;
        }
    </style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-admin-dashboard')
@endsection

@section('content')

    {{-- ✅ LOGIKA DATA PIMPINAN & BENDAHARA --}}
    @php
        // 1. Data Pimpinan
        $leaderName = $currentLeader->user->name ?? 'Belum Ada';
        $leaderAvatar = ($currentLeader && $currentLeader->user && $currentLeader->user->img)
            ? asset('storage/' . $currentLeader->user->img)
            : "https://ui-avatars.com/api/?name=" . urlencode($leaderName) . "&background=696cff&color=fff&bold=true";

        $leaderJabatan = 'Kepala UPT Perparkiran';
        if($currentLeader && $currentLeader->status_jabatan == 'plt') $leaderJabatan = 'Plt. Kepala UPT';
        if($currentLeader && $currentLeader->status_jabatan == 'plh') $leaderJabatan = 'Plh. Kepala UPT';

        $leaderNip = $currentLeader ? formatNip($currentLeader->employee_number) : '-';
        $leaderStart = $currentLeader ? \Carbon\Carbon::parse($currentLeader->start_date)->translatedFormat('d M Y') : '-';

        // 2. Data Bendahara Aktif
        $currentTreasurer = \App\Models\Treasurer::with('user')->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->first();

        $treasurerName = $currentTreasurer->user->name ?? 'Belum Ada';
        $treasurerAvatar = ($currentTreasurer && $currentTreasurer->user && $currentTreasurer->user->img)
            ? asset('storage/' . $currentTreasurer->user->img)
            : "https://ui-avatars.com/api/?name=" . urlencode($treasurerName) . "&background=ffab00&color=fff&bold=true";

        $treasurerNip = $currentTreasurer ? formatNip($currentTreasurer->employee_number) : '-';
    @endphp

    @php
        $adminName = Auth::user()->name ?? 'Administrator';
        $adminNip = Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-';
        $adminAvatar = (Auth::user() && Auth::user()->img)
            ? asset('storage/' . Auth::user()->img)
            : "https://ui-avatars.com/api/?name=" . urlencode($adminName) . "&background=fff&color=696cff&bold=true";
            
        $hour = date('H');
        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }
    @endphp

    <div class="card mb-4 hero-search-card shadow-lg border-0" style="background: linear-gradient(135deg, #696cff 0%, #3b3e99 100%);">
        <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
            <!-- Dekorasi background -->
            <i class="ri ri-vip-crown-line position-absolute text-white opacity-10" style="font-size: 8rem; right: -1%; top: -10%; transform: rotate(15deg);"></i>
            
            <div class="row align-items-center position-relative z-1">
                <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm mb-3">
                        <i class="ri ri-calendar-todo-line me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <h2 class="text-white fw-bold mb-2" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ $adminName }}! 👋</h2>
                    <div class="badge border border-white text-white rounded-pill px-3 py-2 mb-3">
                        <i class="ri ri-profile-line me-1 align-middle"></i> NIP: {{ $adminNip }}
                    </div>
                    <p class="text-white-75 mb-0 fs-6" style="max-width: 500px;">
                        Selamat datang kembali di Pusat Kendali Administrator. Berikut adalah ringkasan kinerja dan data pengelolaan perparkiran (SPKP) terkini.
                    </p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="d-inline-block position-relative rounded-circle p-1" style="background: linear-gradient(135deg, #f6d365 0%, #ffb142 100%);">
                        <img src="{{ $adminAvatar }}" alt="Admin Avatar" class="rounded-circle shadow-lg border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 5px;" title="Online"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. TOP METRICS CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Pimpinan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $leaderAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit">
                    <div>
                        {{-- Nama ditampilkan utuh (wrap text jika panjang) --}}
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $leaderName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $leaderJabatan }}</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $leaderNip }}</p>
                        <p class="text-primary fw-bold mb-0 mt-1" style="font-size: 0.70rem;"><i class="ri ri ri-calendar-check-line me-1"></i>Mulai: {{ $leaderStart }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Bendahara --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $treasurerAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit">
                    <div>
                        {{-- Nama ditampilkan utuh --}}
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $treasurerName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Bendahara Penerimaan</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $treasurerNip }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Rekening --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-info">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-card-icon bg-label-info me-3">
                        <i class="ri icon-base ri-bank-card-line ri-24px"></i>
                    </div>
                    @if ($activeBankAccount)
                        <div>
                            <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">BANK ({{ $activeBankAccount->bank_name }})</p>
                            <h6 class="mb-0 fw-bold text-primary">{{ $activeBankAccount->account_name}}</h6>
                            <h6 class="mb-0 fw-bold text-info"><small>{{ $activeBankAccount->account_number }}</small></h6>
                        </div>
                    @else
                        <div>
                            <p class="text-muted mb-0 text-sm fw-medium">Rekening BLUD</p>
                            <h6 class="mb-0 fw-bold text-danger">Belum disetting</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 4: Total Setoran --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 bg-label-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-card-icon bg-success text-white me-3 shadow-sm">
                        <i class="ri icon-base ri-money-dollar-circle-line ri-24px"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold text-success" style="font-size: 0.75rem;">TOTAL SETORAN (SAH)</p>
                        <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($currentYearValidatedDeposit, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 3. MAIN CHARTS ROW --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center pb-3">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="ri icon-base ri ri-bar-chart-grouped-line text-primary me-2 ri-20px"></i> Grafik Validasi Setoran vs Target
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">Tahun {{ now()->year }}</span>
                </div>
                <div class="card-body pt-4">
                    <div id="deposit-mixed-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent pb-3">
                    <h5 class="card-title fw-bold mb-0">
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
                                    $coordAvatar = ($deposit->agreement->fieldCoordinator->user && $deposit->agreement->fieldCoordinator->user->img)
                                        ? asset('storage/' . $deposit->agreement->fieldCoordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($coordName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr>
                                    <td class="ps-0 pe-2 py-2" width="40px">
                                        <img src="{{ $coordAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit">
                                    </td>
                                    <td class="py-2 text-wrap"> {{-- Tambah text-wrap agar nama tidak kepotong horizontal --}}
                                        <h6 class="mb-0 text-sm fw-bold">{{ $coordName }}</h6>
                                        <small class="text-muted">{{ $deposit->deposit_date->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td class="text-end py-2 pe-0 align-middle">
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
                                                <span class="avatar-initial rounded-circle bg-label-info"><i class="ri icon-base ri-map-pin-line"></i></span>
                                            </div>
                                            <div class="text-wrap">
                                                {{-- Lokasi kadang panjang, text-wrap memastikan turun ke bawah rapi --}}
                                                <h6 class="mb-0 text-sm fw-bold">{{ $location->name }}</h6>
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
                    <h6 class="card-title fw-bold mb-0">Koordinator Lapangan</h6>
                    <a href="{{ route('admin.field-coordinators.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Detail</a>
                </div>
                <div class="table-responsive text-nowrap perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentCoordinators as $coordinator)
                                @php
                                    $cName = $coordinator->user->name ?? 'N/A';
                                    $cAvatar = ($coordinator->user && $coordinator->user->img)
                                        ? asset('storage/' . $coordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr>
                                    <td class="ps-0 pe-2 py-2" width="40px">
                                        <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit">
                                    </td>
                                    <td class="py-2 text-wrap">
                                        <h6 class="mb-0 text-sm fw-bold">{{ $cName }}</h6>
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
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
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
                        enabled: false
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
                    dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: function (val) { return val + " Titik"; }, offsetX: 0 },
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
