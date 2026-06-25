@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Mitra')



@section('page-style')
<style>
    /* ===== PREMIUM DASHBOARD VARIABLES ===== */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.6);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        --primary-gradient: linear-gradient(135deg, #696cff 0%, #8592ff 100%);
        --success-gradient: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        --warning-gradient: linear-gradient(135deg, #ff9f43 0%, #ffb976 100%);
        --info-gradient: linear-gradient(135deg, #00bad1 0%, #26c6da 100%);
        --danger-gradient: linear-gradient(135deg, #ea5455 0%, #f08182 100%);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        box-shadow: var(--glass-shadow);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    }

    /* ===== HERO ===== */
    .hero-banner {
        background: var(--primary-gradient);
        border-radius: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* ===== QR CODE WITH LOGO OVERLAY ===== */
    .qr-wrapper {
        position: relative;
        display: inline-block;
        line-height: 0;
    }
    .qr-wrapper svg {
        display: block;
    }
    .qr-logo-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 28%;
        height: 28%;
        background: #fff;
        border-radius: 50%;
        padding: 3px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-logo-overlay img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }

    /* ===== STAT MINI CARD ===== */
    .stat-mini {
        border-radius: 1rem;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .stat-mini .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        flex-shrink: 0;
    }
    .stat-mini .stat-bg-icon {
        position: absolute;
        right: -10px;
        bottom: -15px;
        font-size: 5rem;
        opacity: 0.06;
        transform: rotate(-15deg);
    }

    /* ===== PEJABAT CARD ===== */
    .pejabat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: inherit !important;
    }
    .pejabat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .pejabat-avatar {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    /* ===== PREMIUM REKENING BOX ===== */
    .rekening-box {
        background: linear-gradient(135deg, rgba(105, 108, 255, 0.04) 0%, rgba(105, 108, 255, 0.02) 100%);
        border: 1px dashed rgba(105, 108, 255, 0.25);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        transition: all 0.3s ease;
    }
    .rekening-box:hover {
        border-color: rgba(105, 108, 255, 0.5);
        background: linear-gradient(135deg, rgba(105, 108, 255, 0.06) 0%, rgba(105, 108, 255, 0.03) 100%);
    }

    .btn-copy {
        transition: all 0.2s ease;
    }
    .btn-copy:active {
        transform: scale(0.9);
    }

    /* ===== TABLE PREMIUM ===== */
    .premium-table {
        border-radius: 1rem;
        overflow: hidden;
    }
    .premium-table thead th {
        background: linear-gradient(135deg, #f8f7fa 0%, #f1f0f4 100%) !important;
        border-bottom: 2px solid rgba(105, 108, 255, 0.1) !important;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #697a8d !important;
        padding: 0.875rem 1rem !important;
    }
    .premium-table tbody tr {
        transition: background 0.2s ease;
    }
    .premium-table tbody tr:hover {
        background: rgba(105, 108, 255, 0.03) !important;
    }
    .premium-table tbody td {
        padding: 0.875rem 1rem !important;
        vertical-align: middle !important;
    }

    /* ===== ALERT BOX ===== */
    .alert-premium {
        border-radius: 0.875rem;
        border: none;
        padding: 0.875rem 1rem;
        font-size: 0.8rem;
    }

    /* ===== PDF MODAL ===== */
    .pdf-container {
        height: 75vh;
        width: 100%;
        border: none;
        border-radius: 8px;
    }

    /* ===== SECTION HEADER ===== */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .section-header .section-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .section-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #697a8d;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-delay-1 { animation: fadeInUp 0.6s ease 0.1s both; }
    .anim-delay-2 { animation: fadeInUp 0.6s ease 0.2s both; }
    .anim-delay-3 { animation: fadeInUp 0.6s ease 0.3s both; }
    .anim-delay-4 { animation: fadeInUp 0.6s ease 0.4s both; }
    .anim-delay-5 { animation: fadeInUp 0.6s ease 0.5s both; }
</style>
@endsection

@section('content')

@php
$leaderName = $currentLeader->user->name ?? 'Belum Ada';
$leaderAvatar = ($currentLeader && $currentLeader->user && $currentLeader->user->img)
? asset('storage/' . $currentLeader->user->img)
: "https://ui-avatars.com/api/?name=" . urlencode($leaderName) . "&background=696cff&color=fff&bold=true";

$leaderJabatan = 'Kepala UPT Perparkiran';
if($currentLeader && $currentLeader->status_jabatan == 'plt') $leaderJabatan = 'Plt. Kepala UPT';
if($currentLeader && $currentLeader->status_jabatan == 'plh') $leaderJabatan = 'Plh. Kepala UPT';
$leaderNip = $currentLeader ? formatNip($currentLeader->employee_number) : '-';

$treasurerName = $currentTreasurer->user->name ?? 'Belum Ada';
$treasurerAvatar = ($currentTreasurer && $currentTreasurer->user && $currentTreasurer->user->img)
? asset('storage/' . $currentTreasurer->user->img)
: "https://ui-avatars.com/api/?name=" . urlencode($treasurerName) . "&background=ffab00&color=fff&bold=true";
$treasurerNip = $currentTreasurer ? formatNip($currentTreasurer->employee_number) : '-';
@endphp

{{-- ============================================= --}}
{{-- HERO BANNER --}}
{{-- ============================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="hero-banner text-white p-4 p-lg-5 shadow-lg anim-delay-1">
            <div class="row w-100 align-items-center position-relative" style="z-index: 2;">
                <div class="col-md-8 text-md-start text-center mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                            <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                    <h2 class="text-white fw-bold mb-2" style="font-size: 1.75rem;">Halo, {{ Auth::user()->name }}! <span class="waving-hand">👋</span></h2>
                    <p class="mb-0 text-white-50" style="font-size: 0.95rem; max-width: 480px;">Selamat datang di Panel Mitra Pengelolaan Perparkiran Kota Pekanbaru.</p>
                </div>
                <div class="col-md-4 text-center text-md-end d-none d-md-block">
                    <div class="position-relative d-inline-block">
                        <div class="position-absolute rounded-circle" style="width: 130px; height: 130px; top: 5px; left: 5px; background: rgba(255,255,255,0.1); filter: blur(15px);"></div>
                        <img src="{{ Auth::user()->img ? asset('storage/'.Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=fff&color=696cff&bold=true&size=120' }}"
                            alt="Avatar" class="rounded-circle position-relative" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid rgba(255,255,255,0.35); box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
                    </div>
                </div>
            </div>
            <i class="ti tabler-parking position-absolute text-white" style="font-size: 200px; right: -10px; bottom: -30px; opacity: 0.06; transform: rotate(-10deg); z-index: 1;"></i>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- PEJABAT DINAS --}}
{{-- ============================================= --}}
<div class="section-header anim-delay-2">
    <div class="section-icon bg-primary bg-opacity-10 text-primary"><i class="ti tabler-building-bank"></i></div>
    <h6>Pejabat Dinas Saat Ini</h6>
</div>
<div class="row g-3 mb-4 anim-delay-2">
    <div class="col-md-6">
        <a href="{{ route('admin.leaders.show', $currentLeader->id) }}" class="pejabat-card border-start border-3 border-primary">
            <img src="{{ $leaderAvatar }}" alt="Avatar Pimpinan" class="pejabat-avatar">
            <div class="overflow-hidden">
                <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $leaderName }}</h6>
                <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $leaderJabatan }}</small>
                <small class="text-muted fw-medium" style="font-size: 0.7rem;">NIP. {{ $leaderNip ? formatNip($leaderNip) : '-' }}</small>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <div class="pejabat-card border-start border-3 border-warning">
            <img src="{{ $treasurerAvatar }}" alt="Avatar Bendahara" class="pejabat-avatar">
            <div class="overflow-hidden">
                <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $treasurerName }}</h6>
                <small class="text-muted d-block" style="font-size: 0.72rem;">Bendahara Penerimaan (Tujuan Setoran)</small>
                <small class="text-muted fw-medium" style="font-size: 0.7rem;">NIP. {{ $treasurerNip ? formatNip($treasurerNip) : '-' }}</small>
            </div>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- RINGKASAN KONTRAK --}}
{{-- ============================================= --}}
<div class="section-header anim-delay-3">
    <div class="section-icon bg-success bg-opacity-10 text-success"><i class="ti tabler-file-description"></i></div>
    <h6>Ringkasan Kontrak</h6>
</div>

<div class="row g-4 mb-4">
    {{-- 1. STATUS PKS & QR CODE --}}
    <div class="col-lg-5 col-md-12 anim-delay-3">
        <div class="glass-card h-100 p-0">
            <div class="card-body p-4 position-relative overflow-hidden">
                {{-- Decorative --}}
                <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 0;">
                    <i class="ti tabler-writing-sign text-success" style="font-size: 120px; transform: rotate(-15deg) translate(15px, -15px);"></i>
                </div>

                <div class="d-flex align-items-center mb-3 position-relative" style="z-index: 1;">
                    <div class="stat-icon me-3" style="background: var(--success-gradient);">
                        <i class="ti tabler-file-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Kontrak PKS</h6>
                        @if($activeAgreement)
                            <span class="badge bg-label-success mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;"><i class="ti tabler-circle-check-filled me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-label-danger mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;"><i class="ti tabler-alert-circle me-1"></i>Tidak Ada PKS</span>
                        @endif
                    </div>
                </div>

                @if($activeAgreement)
                    <div class="d-flex justify-content-between align-items-end mt-3 position-relative" style="z-index: 1;">
                        <div>
                            <h4 class="fw-bold text-success mb-1" style="letter-spacing: 0.3px;">{{ $activeAgreement->agreement_number }}</h4>
                            <small class="text-muted d-block mb-1"><i class="ti tabler-calendar-event me-1"></i>Berlaku s/d: {{ $activeAgreement->end_date->translatedFormat('d M Y') }}</small>
                            <small class="text-muted d-block mb-3"><i class="ti tabler-calendar-stats me-1"></i>Ditandatangani: {{ $activeAgreement->signed_date->translatedFormat('d M Y') }}</small>
                            <button type="button" class="btn btn-sm btn-success rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalPks">
                                <i class="ti tabler-eye me-1"></i> Lihat Dokumen
                            </button>
                        </div>
                        @if($activeAgreement->verification_code)
                        <div class="text-center">
                            <div class="bg-white p-2 rounded-4 shadow-sm border border-success border-opacity-25" data-bs-toggle="tooltip" title="Kode Verifikasi: {{ $activeAgreement->verification_code }}">
                                <div class="qr-wrapper">
                                    {!! QrCode::size(100)->errorCorrection('H')->generate($activeAgreement->verification_code) !!}
                                    <div class="qr-logo-overlay">
                                        <img src="{{ asset('assets/img/logo-spt.png') }}" alt="Logo SPT">
                                    </div>
                                </div>
                            </div>
                            <small class="d-block text-muted mt-2 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">SCAN QR</small>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 position-relative" style="z-index: 1;">
                        <div class="avatar avatar-xl bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="ti tabler-alert-circle text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold text-danger mb-1">Belum Ada PKS Aktif</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Hubungi admin untuk membuat kontrak baru.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. SETORAN HARIAN & PREDIKSI --}}
    <div class="col-lg-4 col-md-7 anim-delay-4">
        <div class="glass-card h-100 p-0">
            <div class="card-body p-4 position-relative overflow-hidden h-100 d-flex flex-column">
                {{-- Decorative --}}
                <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 0;">
                    <i class="ti tabler-coins text-warning" style="font-size: 100px; transform: rotate(15deg) translate(10px, -10px);"></i>
                </div>

                <div class="d-flex align-items-center mb-3 position-relative" style="z-index: 1;">
                    <div class="stat-icon me-3" style="background: var(--warning-gradient);">
                        <i class="ti tabler-cash"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.3px;">TARGET SETORAN HARIAN</small>
                        <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($dailyDeposit, 0, ',', '.') }}</h4>
                    </div>
                </div>

                {{-- ✅ LOGIKA TAMPILAN PRABAYAR --}}
                <div class="position-relative" style="z-index: 1;">
                @if($isContractLunas)
                <div class="alert-premium d-flex align-items-start gap-2 mb-3" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.05) 100%);">
                    <i class="ti tabler-circle-check-filled text-success mt-1"></i>
                    <div>
                        <span class="text-success fw-bold d-block" style="font-size: 0.75rem;">BULAN INI ({{ strtoupper($currentMonthName) }}) LUNAS</span>
                        <p class="text-dark mb-0 mt-1" style="font-size: 0.72rem; line-height: 1.5;">Bulan ini merupakan akhir dari PKS. Silakan perpanjang kontrak melalui Admin.</p>
                    </div>
                </div>
                @elseif($hasPaidCurrentMonth)
                <div class="alert-premium mb-3" style="background: linear-gradient(135deg, rgba(105, 108, 255, 0.08) 0%, rgba(105, 108, 255, 0.03) 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid rgba(105, 108, 255, 0.15);">
                        <div class="d-flex align-items-center gap-1">
                            <i class="ti tabler-circle-check-filled text-success"></i>
                            <span class="text-success fw-bold" style="font-size: 0.72rem;">BULAN INI LUNAS</span>
                        </div>
                        <span class="badge bg-primary px-2 rounded-pill" style="font-size: 0.55rem; letter-spacing: 0.5px;">PREDIKSI</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold" style="font-size: 0.75rem;">Tagihan {{ $nextMonthName }}:</span>
                        <span class="fw-bold text-primary" style="font-size: 0.95rem;">Rp {{ number_format($nextMonthTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                <div class="alert-premium d-flex align-items-center gap-2 mb-3" style="background: linear-gradient(135deg, rgba(234, 84, 85, 0.1) 0%, rgba(234, 84, 85, 0.05) 100%);">
                    <i class="ti tabler-alert-triangle text-danger"></i>
                    <span class="text-danger fw-bold" style="font-size: 0.75rem;">Prabayar bulan {{ $currentMonthName }} belum dibayar!</span>
                </div>
                @endif
                </div>

                {{-- Kotak Rekening BLUD --}}
                <div class="mt-auto rekening-box position-relative" style="z-index: 1;">
                    <small class="d-block text-muted fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                        <i class="ti tabler-building-bank me-1"></i>TUJUAN TRANSFER (BLUD)
                    </small>
                    @if($activeBankAccount)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-primary d-block" style="font-size: 0.8rem;">{{ $activeBankAccount->bank_name }}</span>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 1rem;">{{ $activeBankAccount->account_number }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle btn-copy bg-white shadow-sm" onclick="copyRekening('{{ $activeBankAccount->account_number }}', this)" data-bs-toggle="tooltip" title="Salin No. Rekening">
                            <i class="ti tabler-copy"></i>
                        </button>
                    </div>
                    @else
                    <span class="badge bg-label-danger" style="font-size: 0.7rem;">Rekening belum diatur Dinas.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TOTAL TITIK & QUICK ACTION --}}
    <div class="col-lg-3 col-md-5 anim-delay-5">
        <div class="glass-card h-100 p-0">
            <div class="card-body p-4 position-relative overflow-hidden h-100 d-flex flex-column text-center justify-content-center">
                {{-- Decorative --}}
                <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 0;">
                    <i class="ti tabler-map-pin-bolt text-info" style="font-size: 100px; transform: rotate(-15deg) translate(10px, -10px);"></i>
                </div>

                <div class="position-relative" style="z-index: 1;">
                    <div class="avatar avatar-xl mx-auto mb-3 d-flex align-items-center justify-content-center" style="background: var(--info-gradient); border-radius: 1.25rem; box-shadow: 0 6px 20px rgba(0, 186, 209, 0.25);">
                        <i class="ti tabler-map-pin text-white" style="font-size: 1.75rem;"></i>
                    </div>

                    <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">TITIK PARKIR DIKELOLA</small>
                    <h2 class="fw-bold text-info mb-1">{{ $totalLocations }}</h2>
                    <small class="text-muted">Titik Aktif</small>

                    <div class="mt-4">
                        <a href="{{ route('field_coordinator.location-requests.create') }}" class="btn btn-info rounded-pill w-100 shadow-sm fw-bold" style="font-size: 0.8rem;">
                            <i class="ti tabler-plus me-1"></i> Ajukan Perubahan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- TABEL DATA --}}
{{-- ============================================= --}}
<div class="section-header anim-delay-5">
    <div class="section-icon bg-warning bg-opacity-10 text-warning"><i class="ti tabler-table"></i></div>
    <h6>Data Operasional</h6>
</div>

<div class="row g-4 mb-4">
    {{-- TABEL TITIK PARKIR --}}
    <div class="col-lg-6 anim-delay-5">
        <div class="glass-card h-100 p-0">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-map-pin text-primary" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="card-title fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Titik Parkir Anda</h6>
                </div>
                <span class="badge bg-label-primary rounded-pill" style="font-size: 0.6rem;">{{ $totalLocations }} Titik</span>
            </div>
            <div class="p-0" style="max-height: 380px; overflow-y: auto;">
                <table class="table table-hover mb-0 premium-table">
                    <thead>
                        <tr>
                            <th>Nama Lokasi</th>
                            <th>Ruas Jalan</th>
                            <th class="text-center" style="width: 60px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLocations as $loc)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ Str::limit($loc->name, 25) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary rounded-pill" style="font-size: 0.7rem;">{{ Str::limit($loc->roadSection->name ?? '-', 20) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('masterdata.parking-locations.show', $loc->id) }}" class="btn btn-sm btn-icon btn-text-primary rounded-pill" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="ti tabler-chevron-right fs-5"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <div class="avatar avatar-md bg-lighter rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="ti tabler-map-pin-off text-muted" style="font-size: 1.25rem;"></i>
                                </div>
                                <span class="fw-medium" style="font-size: 0.85rem;">Belum ada titik parkir.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABEL STATUS PENGAJUAN --}}
    <div class="col-lg-6 anim-delay-5">
        <div class="glass-card h-100 p-0">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <i class="ti tabler-history text-warning" style="font-size: 0.875rem;"></i>
                    </div>
                    <h6 class="card-title fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Status Pengajuan Terakhir</h6>
                </div>
                <a href="{{ route('field_coordinator.location-requests.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-3" style="font-size: 0.65rem;">Lihat Semua</a>
            </div>
            <div class="p-0" style="max-height: 380px; overflow-y: auto;">
                <table class="table table-hover mb-0 premium-table">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Detail Titik</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                        <tr>
                            <td>
                                @if($req->request_type == 'add')
                                    <span class="badge bg-label-success rounded-pill" style="font-size: 0.65rem;"><i class="ti tabler-plus me-1"></i>Penambahan</span>
                                @else
                                    <span class="badge bg-label-danger rounded-pill" style="font-size: 0.65rem;"><i class="ti tabler-minus me-1"></i>Pencabutan</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark text-wrap d-block" style="max-width: 150px; font-size: 0.85rem;">
                                    {{ $req->request_type == 'add' ? $req->name : ($req->parkingLocation->name ?? '-') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($req->status == 'pending') <span class="badge bg-label-warning rounded-pill" style="font-size: 0.65rem;">Pending</span>
                                @elseif($req->status == 'surveyed') <span class="badge bg-label-info rounded-pill" style="font-size: 0.65rem;">Surveyed</span>
                                @elseif($req->status == 'approved') <span class="badge bg-label-success rounded-pill" style="font-size: 0.65rem;">Disetujui</span>
                                @else <span class="badge bg-label-danger rounded-pill" style="font-size: 0.65rem;">Ditolak</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <div class="avatar avatar-md bg-lighter rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="ti tabler-file-x text-muted" style="font-size: 1.25rem;"></i>
                                </div>
                                <span class="fw-medium" style="font-size: 0.85rem;">Belum ada riwayat pengajuan.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- MODAL PREVIEW DOKUMEN PKS --}}
{{-- ============================================= --}}
@if($activeAgreement)
<div class="modal fade" id="modalPks" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-bottom bg-light px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="ti tabler-file-description me-2 text-success"></i>Dokumen PKS Aktif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="{{ route('masterdata.agreements.pdf', $activeAgreement->id) }}#toolbar=0"
                    class="pdf-container"></iframe>
            </div>
            <div class="modal-footer border-top bg-white justify-content-between px-4">
                <p class="text-muted small mb-0"><i class="ti tabler-info-circle me-1"></i>Dokumen ini di-generate otomatis oleh sistem.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('masterdata.agreements.pdf', $activeAgreement->id) }}" target="_blank" class="btn btn-success fw-bold rounded-pill">
                        <i class="ti tabler-external-link me-1"></i> Buka Fullscreen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('page-script')
<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // ✅ SCRIPT SALIN REKENING (ANTI GAGAL FALLBACK METHOD)
    function copyRekening(text, btn) {
        // Hilangkan strip (-) jika ada
        let cleanText = text.replace(/-/g, '');

        // Buat textarea tak kasat mata untuk proses copy (Fallback method)
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = cleanText;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);

        // Animasi icon centang hijau
        let icon = btn.querySelector('i');
        icon.className = 'ti tabler-checks text-success';
        setTimeout(() => { icon.className = 'ti tabler-copy'; }, 2000);
    }
</script>
@endsection