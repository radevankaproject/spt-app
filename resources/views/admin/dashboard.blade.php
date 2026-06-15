@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        .perfect-scrollbar-table { position: relative; max-height: 380px; overflow: hidden; }
        .hero-search-card { background: linear-gradient(135deg, #696cff 0%, #3b3e99 100%); color: white; border: none; overflow: hidden; }
        .stat-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .premium-table tbody tr { transition: all 0.2s ease; }
        .premium-table tbody tr:hover { background-color: rgba(105, 108, 255, 0.05); transform: scale(1.01); }
        .avatar-fit { width: 48px; height: 48px; object-fit: cover; }
        .quick-stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; }
        .quick-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
        .quick-stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
        .quick-stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-admin-dashboard')
@endsection

@section('content')

    {{-- ✅ LOGIKA DATA PIMPINAN & BENDAHARA --}}
    @php
        $leaderName = $currentLeader->user->name ?? 'Belum Ada';
        $leaderAvatar = ($currentLeader && $currentLeader->user && $currentLeader->user->img)
            ? asset('storage/' . $currentLeader->user->img)
            : "https://ui-avatars.com/api/?name=" . urlencode($leaderName) . "&background=696cff&color=fff&bold=true";
        $leaderJabatan = 'Kepala UPT Perparkiran';
        if($currentLeader && $currentLeader->status_jabatan == 'plt') $leaderJabatan = 'Plt. Kepala UPT';
        if($currentLeader && $currentLeader->status_jabatan == 'plh') $leaderJabatan = 'Plh. Kepala UPT';
        $leaderNip = $currentLeader ? formatNip($currentLeader->employee_number) : '-';
        $leaderStart = $currentLeader ? \Carbon\Carbon::parse($currentLeader->start_date)->translatedFormat('d M Y') : '-';

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
        if ($hour >= 5 && $hour < 11) { $greeting = 'Selamat Pagi'; }
        elseif ($hour >= 11 && $hour < 15) { $greeting = 'Selamat Siang'; }
        elseif ($hour >= 15 && $hour < 18) { $greeting = 'Selamat Sore'; }
        else { $greeting = 'Selamat Malam'; }
    @endphp

    {{-- ✅ 1. HERO CARD --}}
    <div class="card mb-4 hero-search-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
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
                        Selamat datang kembali di Pusat Kendali Administrator. Berikut adalah ringkasan kinerja dan data pengelolaan perparkiran terkini.
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

    {{-- ✅ 2. QUICK STATS BAR (6 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-primary mx-auto mb-2"><i class="ri ri-file-text-line ri-20px"></i></div>
                    <div class="quick-stat-value text-primary">{{ $totalAgreements }}</div>
                    <div class="quick-stat-label text-muted mt-1">PKS Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-info mx-auto mb-2"><i class="ri ri-map-pin-line ri-20px"></i></div>
                    <div class="quick-stat-value text-info">{{ $totalParkingLocations }}</div>
                    <div class="quick-stat-label text-muted mt-1">Titik Parkir</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-dark mx-auto mb-2"><i class="ri ri-road-map-line ri-20px"></i></div>
                    <div class="quick-stat-value">{{ $totalRoadSections }}</div>
                    <div class="quick-stat-label text-muted mt-1">Ruas Jalan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-secondary mx-auto mb-2"><i class="ri ri-user-location-line ri-20px"></i></div>
                    <div class="quick-stat-value">{{ $totalFieldCoordinators }}</div>
                    <div class="quick-stat-label text-muted mt-1">Korlap</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-warning mx-auto mb-2"><i class="ri ri-time-line ri-20px"></i></div>
                    <div class="quick-stat-value text-warning">{{ $pendingValidationsCount }}</div>
                    <div class="quick-stat-label text-muted mt-1">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100 bg-label-success">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-success text-white mx-auto mb-2 shadow-sm"><i class="ri ri-money-dollar-circle-line ri-20px"></i></div>
                    <div class="quick-stat-value text-success" style="font-size: 1.1rem;">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</div>
                    <div class="quick-stat-label text-success mt-1">Setor Bln Ini</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 3. PEJABAT & REKENING (3 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $leaderAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $leaderName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $leaderJabatan }}</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $leaderNip }}</p>
                        <p class="text-primary fw-bold mb-0 mt-1" style="font-size: 0.70rem;"><i class="ri ri ri-calendar-check-line me-1"></i>Mulai: {{ $leaderStart }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $treasurerAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $treasurerName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Bendahara Penerimaan</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $treasurerNip }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-info">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-card-icon bg-label-info me-3"><i class="ri icon-base ri-bank-card-line ri-24px"></i></div>
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
    </div>

    {{-- ✅ 4. MAIN CHARTS ROW --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center pb-3">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="ri icon-base ri ri-bar-chart-grouped-line text-primary me-2 ri-20px"></i> Grafik Validasi Setoran vs Target
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">Tahun {{ now()->year }}</span>
                </div>
                <div class="card-body pt-4"><div id="deposit-mixed-chart"></div></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent pb-3">
                    <h5 class="card-title fw-bold mb-0"><i class="ri icon-base ri-road-map-line text-warning me-2 ri-20px"></i> Top 10 Ruas Jalan</h5>
                    <small class="text-muted">Berdasarkan kepadatan titik lokasi</small>
                </div>
                <div class="card-body pt-4"><div id="locations-per-road-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 5. POLAR CHARTS (ZONA) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-center pb-0"><h6 class="fw-bold mb-0">Distribusi Ruas Jalan (Per Zona)</h6></div>
                <div class="card-body d-flex flex-column justify-content-center"><div id="road-section-zone-chart"></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-center pb-0"><h6 class="fw-bold mb-0">Distribusi Titik Lokasi (Per Zona)</h6></div>
                <div class="card-body d-flex flex-column justify-content-center"><div id="parking-location-zone-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 6. TABLES LIST ROW --}}
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
                                    <td class="ps-0 pe-2 py-2" width="40px"><img src="{{ $coordAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit"></td>
                                    <td class="py-2 text-wrap">
                                        <h6 class="mb-0 text-sm fw-bold">{{ $coordName }}</h6>
                                        <small class="text-muted">{{ $deposit->deposit_date->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td class="text-end py-2 pe-0 align-middle"><span class="badge bg-label-success fw-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel 2: PKS Segera Berakhir --}}
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold mb-0"><i class="ri ri-error-warning-line text-warning me-1"></i> PKS Segera Berakhir</h6>
                    <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-xs btn-outline-warning rounded-pill">Detail</a>
                </div>
                <div class="table-responsive text-nowrap perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($expiringAgreements as $pks)
                                @php
                                    $daysLeft = now()->diffInDays($pks->end_date, false);
                                    $badgeClass = $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning';
                                @endphp
                                <tr>
                                    <td class="ps-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle {{ $badgeClass }}"><i class="ri icon-base ri-timer-line"></i></span>
                                            </div>
                                            <div class="text-wrap">
                                                <h6 class="mb-0 text-sm fw-bold">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $pks->agreement_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end py-2 pe-0 align-middle">
                                        <span class="badge {{ $badgeClass }} fw-bold">{{ (int)$daysLeft }} hari lagi</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-success"><i class="ri ri-check-double-line me-1"></i> Semua PKS aman, tidak ada yang segera berakhir.</td></tr>
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
                                    <td class="ps-0 pe-2 py-2" width="40px"><img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit"></td>
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

            // 1. Mixed Chart (Deposit vs Target)
            const mixedChartEl = document.querySelector("#deposit-mixed-chart");
            if (mixedChartEl) {
                new ApexCharts(mixedChartEl, {
                    chart: { height: 380, type: 'line', stacked: false, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                    stroke: { width: [0, 3], curve: 'smooth' },
                    plotOptions: { bar: { columnWidth: '45%', borderRadius: 4, startingShape: 'rounded' } },
                    series: [
                        { name: 'Total Setoran', type: 'bar', data: @json($mainChartData) },
                        { name: 'Target Proyeksi', type: 'line', data: @json($targetChartData ?? []) }
                    ],
                    xaxis: { categories: @json($mainChartLabels), axisBorder: {show: false}, axisTicks: {show: false} },
                    yaxis: { labels: { formatter: (val) => val ? `Rp ${(val / 1000000).toFixed(1)} Jt` : "Rp 0", style: { colors: bodyColor } } },
                    dataLabels: { enabled: false },
                    grid: { borderColor: borderColor, strokeDashArray: 4, padding: { top: 20 } },
                    tooltip: { shared: true, intersect: false, y: { formatter: (val) => val !== undefined && val !== null ? "Rp " + new Intl.NumberFormat('id-ID').format(val) : "Rp 0" } },
                    colors: [primaryColor, warningColor],
                    legend: { position: 'top', horizontalAlign: 'left' }
                }).render();
            }

            // 2. Bar Chart (Top 10 Ruas Jalan)
            const locationsPerRoadEl = document.querySelector("#locations-per-road-chart");
            if (locationsPerRoadEl) {
                new ApexCharts(locationsPerRoadEl, {
                    chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                    plotOptions: { bar: { horizontal: true, barHeight: '50%', borderRadius: 4 } },
                    dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: (val) => val + " Titik", offsetX: 0 },
                    series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                    xaxis: { categories: @json($barChartData['labels']), labels: {show: false}, axisBorder: {show: false}, axisTicks: {show: false} },
                    grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                    colors: [infoColor]
                }).render();
            }

            // 3. Polar Area: Ruas Jalan Zona
            const roadSectionZoneEl = document.querySelector("#road-section-zone-chart");
            if (roadSectionZoneEl) {
                new ApexCharts(roadSectionZoneEl, {
                    series: @json($zoneChartData['roadSections']),
                    chart: { height: 300, type: 'polarArea', fontFamily: 'Outfit, sans-serif' },
                    labels: @json($zoneChartData['labels']),
                    stroke: { colors: ['#fff'] }, fill: { opacity: 0.8 },
                    colors: [primaryColor, successColor, infoColor, warningColor, dangerColor],
                    legend: { position: 'bottom' }
                }).render();
            }

            // 4. Polar Area: Lokasi Zona
            const parkingLocationZoneEl = document.querySelector("#parking-location-zone-chart");
            if (parkingLocationZoneEl) {
                new ApexCharts(parkingLocationZoneEl, {
                    series: @json($zoneChartData['parkingLocations']),
                    chart: { height: 300, type: 'polarArea', fontFamily: 'Outfit, sans-serif' },
                    labels: @json($zoneChartData['labels']),
                    stroke: { colors: ['#fff'] }, fill: { opacity: 0.8 },
                    colors: [warningColor, primaryColor, dangerColor, infoColor, successColor],
                    legend: { position: 'bottom' }
                }).render();
            }

            // 5. Perfect Scrollbar
            document.querySelectorAll('.perfect-scrollbar-table').forEach(el => {
                new PerfectScrollbar(el, { wheelPropagation: false, suppressScrollX: true });
            });
        });
    </script>
@endpush
