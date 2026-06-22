@extends('layouts.contentNavbarLayout')

@section('title', 'Admin Dashboard')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        .perfect-scrollbar-table { position: relative; max-height: 380px; overflow: hidden; }
        .avatar-fit { width: 48px; height: 48px; object-fit: cover; }
    </style>
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
        $treasurerStart = $currentTreasurer ? \Carbon\Carbon::parse($currentTreasurer->start_date)->translatedFormat('d M Y') : '-';
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
    <div class="row mb-4">
        <div class="col-12">
            <div class="fintech-card shadow-lg text-white p-4 p-lg-5 animate__animated animate__fadeInDown">
                <i class="ti tabler-crown position-absolute gold-blink" style="font-size: 8rem; right: -1%; top: -10%; transform: rotate(15deg);"></i>
                <div class="row align-items-center position-relative z-1">
                    <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm mb-3">
                            <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                        <h2 class="text-white fw-bold mb-2" style="font-size: 2.5rem; letter-spacing: -1px;">{{ $greeting }}, {{ $adminName }}! <span class="waving-hand">👋</span></h2>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-3">
                            <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="ti tabler-id me-1"></i> NIP: <strong>{{ $adminNip ? formatNip($adminNip) : '-' }}</strong>
                            </div>
                        </div>
                        <p class="fs-5 opacity-75 mb-0" style="max-width: 600px; font-weight: 300;">
                            Selamat datang kembali di Pusat Kendali Administrator. Berikut adalah ringkasan kinerja dan data pengelolaan perparkiran terkini.
                        </p>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <div class="position-relative d-inline-block">
                            <div class="position-absolute w-100 h-100 rounded-circle bg-white opacity-25" style="top: 10px; left: -10px; filter: blur(20px);"></div>
                            <img src="{{ $adminAvatar }}" alt="Admin Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 150px; height: 150px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle p-2" title="Online" style="margin-bottom: 10px; margin-right: 10px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. QUICK STATS BAR (6 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-green text-center p-3 h-100 animate__animated animate__zoomIn animate__fast">
                <div class="stat-glow-icon bg-white text-primary mx-auto mb-3"><i class="ti tabler-file-text ti-md"></i></div>
                <h3 class="fw-bolder text-primary mb-1">{{ $totalAgreements }}</h3>
                <span class="text-muted small fw-bold text-uppercase">PKS Aktif</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-purple text-center p-3 h-100 animate__animated animate__bounceIn animate__fast" style="animation-delay: 0.1s;">
                <div class="stat-glow-icon bg-white text-info mx-auto mb-3"><i class="ti tabler-map-pin ti-md"></i></div>
                <h3 class="fw-bolder text-info mb-1">{{ $totalParkingLocations }}</h3>
                <span class="text-muted small fw-bold text-uppercase">Titik Parkir</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <a href="{{ route('masterdata.road-sections.index') }}" class="text-decoration-none d-block h-100">
                <div class="fintech-card fintech-card-blue text-center p-3 h-100 animate__animated animate__fadeInUp animate__fast" style="animation-delay: 0.2s;">
                    <div class="stat-glow-icon bg-white text-dark mx-auto mb-3"><i class="ti tabler-road ti-md"></i></div>
                    <h3 class="fw-bolder text-dark mb-1">{{ $totalRoadSections }}</h3>
                    <span class="text-muted small fw-bold text-uppercase">Ruas Jalan</span>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-orange text-center p-3 h-100 animate__animated animate__flipInX animate__fast" style="animation-delay: 0.3s;">
                <div class="stat-glow-icon bg-white text-secondary mx-auto mb-3"><i class="ti tabler-user-pin ti-md"></i></div>
                <h3 class="fw-bolder text-secondary mb-1">{{ $totalFieldCoordinators }}</h3>
                <span class="text-muted small fw-bold text-uppercase">Korlap</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card text-center p-3 h-100 animate__animated animate__slideInUp animate__fast" style="animation-delay: 0.4s;">
                <div class="stat-glow-icon bg-white text-warning mx-auto mb-3"><i class="ti tabler-clock ti-md"></i></div>
                <h3 class="fw-bolder text-warning mb-1">{{ $pendingValidationsCount }}</h3>
                <span class="text-muted small fw-bold text-uppercase">Pending</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-green text-center p-3 h-100 animate__animated animate__lightSpeedInRight animate__fast" style="animation-delay: 0.5s;">
                <div class="stat-glow-icon bg-success text-white mx-auto mb-3 shadow-sm"><i class="ti tabler-currency-dollar ti-md"></i></div>
                <h4 class="fw-bolder text-success mb-1" style="font-size: 1.1rem;">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</h4>
                <span class="text-success small fw-bold text-uppercase">Setor Bln Ini</span>
            </div>
        </div>
    </div>

    {{-- ✅ 3. PEJABAT & REKENING (3 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="glass-card h-100 p-3 animate__animated animate__fadeInLeft animate__delay-1s">
                <a href="{{ route('admin.leaders.show', $currentLeader->id) }}">
                <div class="d-flex align-items-center">
                    <img src="{{ $leaderAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit border border-2 border-primary">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $leaderName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $leaderJabatan }}</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $leaderNip ? formatNip($leaderNip) : '-' }}</p>
                        <p class="text-primary fw-bold mb-0 mt-1" style="font-size: 0.70rem;"><i class="ti tabler-calendar-check me-1"></i>Mulai: {{ $leaderStart }}</p>
                    </div>
                </div>
                </a>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="glass-card h-100 p-3 animate__animated animate__zoomIn animate__delay-1s">
                <a href="{{ route('admin.treasurers.show', $currentTreasurer->id) }}">
                <div class="d-flex align-items-center">
                    <img src="{{ $treasurerAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3 avatar-fit border border-2 border-warning">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $treasurerName }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Bendahara Penerimaan</p>
                        <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $treasurerNip ? formatNip($treasurerNip) : '-' }}</p>
                        <p class="text-primary fw-bold mb-0 mt-1" style="font-size: 0.70rem;"><i class="ti tabler-calendar-check me-1"></i>Mulai: {{ $treasurerStart }}</p>
                    </div>
                </div>
                </a>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="glass-card h-100 p-3 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="d-flex align-items-center">
                    <div class="stat-glow-icon bg-white text-info me-3"><i class="ri icon-base ti tabler-credit-card ti-lg"></i></div>
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
            <div class="glass-card h-100 p-0 animate__animated animate__zoomInUp animate__delay-1s">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="ri icon-base ti tabler-chart-bar text-primary me-2 ti-md"></i> Grafik Validasi Setoran vs Target
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-pill">Tahun {{ now()->year }}</span>
                </div>
                <div class="p-4"><div id="deposit-mixed-chart"></div></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card h-100 p-0 animate__animated animate__jackInTheBox animate__delay-1s">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri icon-base ti tabler-road text-warning me-2 ti-md"></i> Top 10 Ruas Jalan</h5>
                    <small class="text-muted">Berdasarkan kepadatan titik lokasi</small>
                </div>
                <div class="p-4"><div id="locations-per-road-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 5. POLAR CHARTS (ZONA) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="glass-card h-100 p-0 animate__animated animate__rotateInDownLeft animate__delay-1s">
                <div class="p-4 text-center pb-0"><h6 class="fw-bold mb-0 text-dark">Distribusi Ruas Jalan (Per Zona)</h6></div>
                <div class="p-4 d-flex flex-column justify-content-center"><div id="road-section-zone-chart"></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="glass-card h-100 p-0 animate__animated animate__rotateInUpRight animate__delay-1s">
                <div class="p-4 text-center pb-0"><h6 class="fw-bold mb-0 text-dark">Distribusi Titik Lokasi (Per Zona)</h6></div>
                <div class="p-4 d-flex flex-column justify-content-center"><div id="parking-location-zone-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 6. TABLES LIST ROW --}}
    <div class="row g-4">
        {{-- Tabel 1: Setoran Terbaru --}}
        <div class="col-lg-4 col-md-6">
            <div class="glass-card h-100 p-0 animate__animated animate__bounceInUp animate__delay-1s">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-primary bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-primary">Setoran Terbaru</h6>
                    <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-xs btn-primary rounded-pill shadow-sm">Detail</a>
                </div>
                <div class="perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentDeposits as $deposit)
                                @php
                                    $coordName = $deposit->agreement->fieldCoordinator->user->name ?? 'N/A';
                                    $coordAvatar = ($deposit->agreement->fieldCoordinator->user && $deposit->agreement->fieldCoordinator->user->img)
                                        ? asset('storage/' . $deposit->agreement->fieldCoordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($coordName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.deposit-transactions.show', $deposit->id) }}'">
                                    <td class="ps-2 pe-2 py-2" width="50px"><img src="{{ $coordAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit" style="width: 40px; height: 40px;"></td>
                                    <td class="py-2 text-wrap">
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $coordName }}</h6>
                                        <small class="text-muted">{{ $deposit->deposit_date->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td class="text-end py-2 pe-2 align-middle"><span class="badge bg-label-success fw-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span></td>
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
            <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s" style="animation-delay: 1.2s;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-warning bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-warning">PKS Segera Berakhir</h6>
                    <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-xs btn-warning rounded-pill shadow-sm">Detail</a>
                </div>
                <div class="perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($expiringAgreements as $pks)
                                @php
                                    $daysLeft = now()->diffInDays($pks->end_date, false);
                                    $badgeClass = $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning';
                                @endphp
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.agreements.show', $pks->id) }}'">
                                    <td class="ps-2 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle {{ $badgeClass }}"><i class="ri icon-base ti tabler-file-alert"></i></span>
                                            </div>
                                            <div class="text-wrap">
                                                <h6 class="mb-0 text-sm fw-bold text-dark">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $pks->agreement_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end py-2 pe-2 align-middle">
                                        <span class="badge {{ $badgeClass }} fw-bold">{{ (int)$daysLeft }} hari lagi</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-success"><i class="ti tabler-checks me-1"></i> Semua PKS aman, tidak ada yang segera berakhir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel 3: Korlap Terbaru --}}
        <div class="col-lg-4 col-md-12">
            <div class="glass-card h-100 p-0 animate__animated animate__backInUp animate__delay-1s" style="animation-delay: 1.4s;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-info bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-info">Koordinator Lapangan</h6>
                    <a href="{{ route('admin.field-coordinators.index') }}" class="btn btn-xs btn-info text-white rounded-pill shadow-sm">Detail</a>
                </div>
                <div class="perfect-scrollbar-table p-2">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse ($recentCoordinators as $coordinator)
                                @php
                                    $cName = $coordinator->user->name ?? 'N/A';
                                    $cAvatar = ($coordinator->user && $coordinator->user->img)
                                        ? asset('storage/' . $coordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                                @endphp
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('admin.field-coordinators.show', $coordinator->id) }}'">
                                    <td class="ps-2 pe-2 py-2" width="50px"><img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm avatar-fit" style="width: 40px; height: 40px;"></td>
                                    <td class="py-2 text-wrap">
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $cName }}</h6>
                                        <small class="text-muted"><i class="ti tabler-phone align-bottom"></i> {{ $coordinator->phone_number ?? '-' }}</small>
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

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}" defer></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
@endsection

@section('page-script')
    <script type="module">
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
                    chart: { 
                        type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif',
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
                    plotOptions: { bar: { horizontal: true, barHeight: '50%', borderRadius: 4 } },
                    dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: (val) => val + " Titik", offsetX: 0 },
                    series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                    xaxis: { categories: @json($barChartData['labels']), labels: {show: false}, axisBorder: {show: false}, axisTicks: {show: false} },
                    grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                    colors: [infoColor],
                    states: { hover: { filter: { type: 'darken', value: 0.9 } }, active: { filter: { type: 'darken', value: 0.8 } } }
                }).render();
                // Add pointer cursor
                document.querySelector("#locations-per-road-chart").style.cursor = "pointer";
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
@endsection
