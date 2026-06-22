@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Bendahara')

@section('page-style')
<style>
    .hover-link { transition: all 0.2s ease; text-decoration: none; }
    .hover-link:hover { color: #696cff !important; transform: translateX(3px); display: inline-block; }
    
    /* Fintech Card Styles */
    .fintech-card {
        background: linear-gradient(135deg, #2b4162 0%, #fa9c7a 100%);
        border-radius: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(43, 65, 98, 0.2);
    }
    .fintech-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .fintech-card-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 15px 35px rgba(17, 153, 142, 0.2);
    }
    .fintech-card-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
    }
    
    .credit-card-ui {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px;
    }
    
    .stat-glow-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
</style>
@endsection



@php
    $treasurerName = explode(' ', Auth::user()->name)[0];
    $userAvatar = Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode($treasurerName).'&background=fff&color=696cff';
@endphp

@section('content')
{{-- ✅ 1. HERO SECTION (FINTECH STYLE) --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="fintech-card h-100 p-4 p-md-5 animate__animated animate__fadeInLeft d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold shadow-sm mb-3" style="font-size:0.8rem;">
                        <i class="ti tabler-calendar-event me-1 text-primary"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <h2 class="text-white fw-bold mb-1">Halo, {{ $treasurerName }}! <span class="waving-hand">👋</span></h2>
                    <p class="text-white opacity-75 mb-0">NIP. {{ Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-' }}</p>
                </div>
                <div class="position-relative">
                    <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 80px; height: 80px; object-fit: cover;">
                </div>
            </div>
            
            <div class="row align-items-end">
                <div class="col-md-7 mb-3 mb-md-0">
                    <p class="text-white opacity-75 mb-2 fw-medium">Total Pendapatan Disahkan (Tahun Ini)</p>
                    <h1 class="text-white fw-bolder mb-0" style="font-size: 2.8rem; text-shadow: 0 4px 10px rgba(0,0,0,0.1);">Rp {{ number_format($totalValidatedThisYear, 0, ',', '.') }}</h1>
                </div>
                <div class="col-md-5">
                    <div class="credit-card-ui d-flex align-items-center">
                        <div class="stat-glow-icon bg-white text-primary me-3">
                            <i class="ti tabler-building-bank"></i>
                        </div>
                        <div>
                            <span class="d-block text-white opacity-75 small text-uppercase fw-bold mb-1">Rekening Aktif BLUD</span>
                            <span class="fw-bold fs-6 text-white text-wrap">{{ $activeBankAccount->bank_name ?? 'N/A' }} <br/> {{ $activeBankAccount->account_number ?? 'Belum diatur' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Donut Chart --}}
    <div class="col-xl-4">
        <div class="glass-card h-100 p-0 animate__animated animate__zoomIn animate__delay-1s d-flex flex-column border-0">
            <div class="p-4 border-bottom bg-transparent text-center">
                <h6 class="fw-bold text-dark mb-0">Rasio Validasi ({{ now()->translatedFormat('F Y') }})</h6>
                <small class="text-muted">Nominal Sah vs Pending</small>
            </div>
            <div class="p-4 d-flex flex-column justify-content-center align-items-center flex-grow-1">
                <div id="validationDonutChart" style="min-height: 220px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ 2. QUICK STATS (Fintech Vibrant Cards) --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="fintech-card fintech-card-purple p-4 h-100 animate__animated animate__slideInUp animate__fast">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-glow-icon bg-white text-primary">
                    <i class="ti tabler-checks"></i>
                </div>
                <span class="badge bg-white text-primary rounded-pill px-2 py-1 fw-bold shadow-sm">Bulan Ini</span>
            </div>
            <p class="text-white opacity-75 mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size:0.8rem;">Kinerja Validasi</p>
            <h3 class="fw-bolder text-white mb-0">Rp {{ number_format($validatedThisMonth, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100 border-0 shadow-sm animate__animated animate__slideInUp animate__fast" style="animation-delay: 0.1s;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-glow-icon bg-label-danger text-danger">
                    <i class="ti tabler-wallet"></i>
                </div>
                <span class="badge bg-label-danger rounded-pill px-2 py-1 fw-bold">Perlu Tindakan</span>
            </div>
            <p class="text-muted mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size:0.8rem;">Nominal Pending</p>
            <h3 class="fw-bolder text-dark mb-0">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100 border-0 shadow-sm animate__animated animate__slideInUp animate__fast" style="animation-delay: 0.2s;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-glow-icon bg-label-warning text-warning">
                    <i class="ti tabler-clock"></i>
                </div>
                <span class="badge bg-label-warning rounded-pill px-2 py-1 fw-bold">Antrean</span>
            </div>
            <p class="text-muted mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size:0.8rem;">Trx Tunggu Validasi</p>
            <h3 class="fw-bolder text-dark mb-0">{{ $pendingValidationsCount }} <span class="fs-6 text-muted fw-normal">Trx</span></h3>
        </div>
    </div>
</div>

{{-- ✅ 3. DATA TABLES --}}
<div class="row g-4">
    {{-- Antrean Validasi --}}
    <div class="col-xl-7 col-lg-7">
        <div class="glass-card border-0 h-100 p-0 animate__animated animate__fadeInLeft animate__delay-1s" style="animation-delay: 0.3s;">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-alert-triangle-filled me-2 text-warning ti-lg"></i>Antrean Validasi Terbaru</h6>
                <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-sm btn-primary rounded-pill shadow-sm px-3">Lihat Semua</a>
            </div>
            <div class="p-2" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-borderless premium-table mb-0">
                    <tbody>
                        @forelse($recentPendingDeposits as $deposit)
                        @php
                            $refCode = $deposit->referral_code ?? 'TRX-'.str_pad($deposit->id, 5, '0', STR_PAD_LEFT);
                            $detailRoute = Route::has('masterdata.deposit-transactions.show') ? route('masterdata.deposit-transactions.show', $deposit->id) : '#';
                        @endphp
                        <tr class="premium-list-item">
                            <td class="py-3 ps-3">
                                <a href="{{ $detailRoute }}" class="fw-bold text-primary d-block hover-link mb-1">{{ $refCode }}</a>
                                <span class="text-muted small"><i class="ti tabler-calendar align-bottom me-1"></i>{{ \Carbon\Carbon::parse($deposit->deposit_date)->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($deposit->agreement->fieldCoordinator->user->name ?? 'N/A') }}&background=random&color=fff" class="rounded-circle shadow-sm">
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ Str::limit($deposit->agreement->fieldCoordinator->user->name ?? 'N/A', 15) }}</span>
                                        <a href="{{ route('masterdata.agreements.show', $deposit->agreement_id) }}" class="text-muted small hover-link">{{ $deposit->agreement->agreement_number ?? '-' }}</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end py-3 pe-3 align-middle">
                                <span class="fw-bold text-dark d-block mb-1">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                                <a href="{{ $detailRoute }}" class="badge bg-warning rounded-pill text-white text-decoration-none shadow-sm px-2 py-1"><i class="ti tabler-zoom-check me-1"></i>Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="avatar avatar-xl bg-label-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti tabler-checks ti-lg"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Kerja Bagus!</h6>
                                <p class="text-muted small mb-0">Tidak ada antrean setoran yang menunggu validasi saat ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Riwayat Validasi --}}
    <div class="col-xl-5 col-lg-5">
        <div class="glass-card border-0 h-100 p-0 animate__animated animate__fadeInRight animate__delay-1s" style="animation-delay: 0.4s;">
            <div class="p-4 border-bottom bg-transparent">
                <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-history me-2 text-info ti-lg"></i>Riwayat Validasi Terkini</h6>
            </div>
            <div class="p-2" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-borderless premium-table mb-0">
                    <tbody>
                        @forelse($recentValidatedDeposits as $val)
                        @php
                            $refCode = $val->referral_code ?? 'TRX-'.str_pad($val->id, 5, '0', STR_PAD_LEFT);
                            $detailRoute = Route::has('masterdata.deposit-transactions.show') ? route('masterdata.deposit-transactions.show', $val->id) : '#';
                        @endphp
                        <tr class="premium-list-item">
                            <td class="py-3 ps-3" width="50px">
                                <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti tabler-check"></i>
                                </div>
                            </td>
                            <td class="py-3">
                                <a href="{{ $detailRoute }}" class="mb-0 fw-bold text-dark d-block hover-link" style="font-size: 0.9rem;">{{ Str::limit($val->agreement->fieldCoordinator->user->name ?? 'N/A', 18) }}</a>
                                <small class="text-muted">{{ $refCode }}</small>
                            </td>
                            <td class="text-end py-3 pe-3 align-middle">
                                <h6 class="mb-0 fw-bold text-success">+ Rp {{ number_format($val->amount, 0, ',', '.') }}</h6>
                                <small class="text-muted d-flex align-items-center justify-content-end" style="font-size: 0.65rem;">
                                    <i class="ti tabler-clock me-1"></i> {{ \Carbon\Carbon::parse($val->validation_date)->diffForHumans() }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 border-0">
                                <i class="ti tabler-file-text ti-xl text-muted opacity-50 mb-2 d-block"></i>
                                <span class="text-muted small">Anda belum memvalidasi transaksi apapun.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="module">
        document.addEventListener("DOMContentLoaded", function() {
        const pendingAmount = {{ $pendingAmount ?? 0 }};
        const validatedAmount = {{ $validatedThisMonth ?? 0 }};

        const seriesData = (pendingAmount === 0 && validatedAmount === 0) ? [0, 1] : [pendingAmount, validatedAmount];
        const chartColors = (pendingAmount === 0 && validatedAmount === 0) ? ['#e7e7eb', '#e7e7eb'] : ['#ffab00', '#71dd37'];

        var options = {
            series: seriesData,
            labels: ['Tertunda (Pending)', 'Sah (Bulan Ini)'],
            chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif', animations: { enabled: true, easing: 'easeinout', speed: 800, animateGradually: { enabled: true, delay: 150 }, dynamicAnimation: { enabled: true, speed: 350 } } },
            colors: chartColors,
            plotOptions: { pie: { donut: { size: '78%', labels: { show: true, name: { fontSize: '0.85rem', color: '#a1acb8' }, value: { fontSize: '1.2rem', fontWeight: 700, color: '#566a7f', formatter: (val) => { if(val === 1 && pendingAmount === 0 && validatedAmount === 0) return "Rp 0"; return "Rp " + new Intl.NumberFormat('id-ID').format(val); } }, total: { show: true, label: 'Total Diproses', color: '#a1acb8', formatter: (w) => { const total = pendingAmount + validatedAmount; return "Rp " + new Intl.NumberFormat('id-ID').format(total); } } } } } },
            dataLabels: { enabled: false },
            stroke: { show: true, colors: ['#fff'], width: 3 },
            legend: { show: true, position: 'bottom', markers: { offsetX: -2 }, itemMargin: { horizontal: 10, vertical: 0 } },
            tooltip: { y: { formatter: (value) => { if(value === 1 && pendingAmount === 0 && validatedAmount === 0) return "Rp 0"; return "Rp " + new Intl.NumberFormat('id-ID').format(value); } } }
        };

        var chart = new ApexCharts(document.querySelector("#validationDonutChart"), options);
        chart.render();
    });
</script>
@endsection
