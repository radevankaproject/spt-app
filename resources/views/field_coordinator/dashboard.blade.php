@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Mitra')



@section('page-style')
<style>
    .avatar-fit {
        width: 48px;
        height: 48px;
        object-fit: cover;
    }

    .card {
        border: none;
        box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15);
    }

    .pdf-container {
        height: 75vh;
        width: 100%;
        border: none;
        border-radius: 8px;
    }

    .btn-copy {
        transition: all 0.2s ease;
    }

    .btn-copy:active {
        transform: scale(0.9);
    }

    .box-estimasi {
        transition: all 0.3s ease;
        border: 1px solid rgba(105, 108, 255, 0.2);
    }

    .box-estimasi:hover {
        border-color: rgba(105, 108, 255, 0.5);
        background-color: #f8f9fa !important;
    }
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

{{-- HERO SECTION --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm"
            style="background: linear-gradient(135deg, #696cff, #8485ff);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="text-white fw-bold mb-2">Halo, {{ Auth::user()->name }}! 👋</h4>
                    <p class="mb-0 text-white-50">Selamat datang di Panel Mitra Pengelolaan Perparkiran Kota Pekanbaru.
                    </p>
                </div>
                <div class="avatar avatar-xl d-none d-md-flex">
                    <img src="{{ Auth::user()->img ? asset('storage/'.Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=fff&color=696cff&bold=true' }}"
                        alt="Avatar" class="rounded-circle shadow" style="object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted fw-bold text-uppercase mb-3"><i class="ti tabler-government me-1"></i> Pejabat Dinas Saat Ini
</h6>
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-start border-4 border-primary">
            <div class="card-body d-flex align-items-center py-3">
                <img src="{{ $leaderAvatar }}" alt="Avatar Pimpinan" class="rounded-circle shadow-sm me-3 avatar-fit">
                <div>
                    <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $leaderName }}</h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $leaderJabatan }}</p>
                    <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $leaderNip }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-start border-4 border-warning">
            <div class="card-body d-flex align-items-center py-3">
                <img src="{{ $treasurerAvatar }}" alt="Avatar Bendahara"
                    class="rounded-circle shadow-sm me-3 avatar-fit">
                <div>
                    <h6 class="mb-0 fw-bold text-dark text-wrap">{{ $treasurerName }}</h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Bendahara Penerimaan (Tujuan Setoran)</p>
                    <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">NIP. {{ $treasurerNip }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted fw-bold text-uppercase mb-3"><i class="ti tabler-layout-dashboard me-1"></i> Ringkasan Kontrak</h6>
<div class="row g-4 mb-4">

    {{-- 1. STATUS PKS --}}
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-success shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <div
                            class="avatar avatar-sm me-2 bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ti tabler-file-description"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">Kontrak PKS</h6>
                    </div>
                    @if($activeAgreement)
                    <button type="button" class="btn btn-xs btn-label-success rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#modalPks">
                        <i class="ti tabler-eye me-1"></i> Lihat Dokumen
                    </button>
                    @endif
                </div>
                @if($activeAgreement)
                <h5 class="fw-bold text-success mb-1">{{ $activeAgreement->agreement_number }}</h5>
                <small class="text-muted d-block">Berlaku s/d: {{ $activeAgreement->end_date->translatedFormat('d M Y')
                    }}</small>
                @else
                <h5 class="fw-bold text-danger mb-0">Tidak Ada PKS Aktif</h5>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. SETORAN HARIAN & PREDIKSI --}}
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-warning shadow-sm">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-2">
                    <div
                        class="avatar avatar-sm me-2 bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ti tabler-currency-dollar"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Target Setoran Harian</h6>
                </div>
                <h4 class="fw-bold text-dark mb-1">Rp {{ number_format($dailyDeposit, 0, ',', '.') }}</h4>

                {{-- ✅ LOGIKA TAMPILAN PRABAYAR (UPDATE) --}}
                @if($isContractLunas)
                <div class="mt-2 mb-3 box-estimasi bg-label-success p-3 rounded-3 border-dashed">
                    <div class="d-flex align-items-center mb-2 border-bottom border-success border-opacity-25 pb-2">
                        <i class="ti tabler-circle-check-filled text-success ti tabler-lg me-1"></i>
                        <span class="text-success fw-bold" style="font-size: 0.8rem;">BULAN INI ({{
                            strtoupper($currentMonthName) }}) LUNAS</span>
                    </div>
                    <p class="text-dark mb-0 fw-medium mt-2" style="font-size: 0.75rem; line-height: 1.5;">
                        Bulan ini merupakan akhir dari PKS. Silakan memperpanjang kontrak Anda dengan menghubungi
                        @if($uptPhoneWa)
                        <a href="https://wa.me/{{ $uptPhoneWa }}" target="_blank"
                            class="fw-bold text-primary text-decoration-underline" data-bs-toggle="tooltip"
                            title="Chat WA Admin">Admin</a>
                        @else
                        <strong>Admin</strong>
                        @endif
                        atau langsung datang ke kantor <strong>{{ $uptName }}</strong>.
                    </p>
                </div>
                @elseif($hasPaidCurrentMonth)
                <div class="mt-2 mb-3 box-estimasi bg-label-primary p-2 rounded-3">
                    <div
                        class="d-flex align-items-center justify-content-between mb-1 border-bottom border-primary border-opacity-25 pb-1">
                        <div>
                            <i class="ti tabler-circle-check-filled text-success me-1"></i>
                            <span class="text-success fw-bold" style="font-size: 0.7rem;">BULAN INI ({{
                                strtoupper($currentMonthName) }}) LUNAS</span>
                        </div>
                        <span class="badge bg-primary px-2" style="font-size: 0.6rem;">PREDIKSI</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-dark fw-bold" style="font-size: 0.7rem;">Tagihan {{ $nextMonthName }} :</span>
                        <span class="fw-bold text-primary" style="font-size: 0.85rem;">Rp {{
                            number_format($nextMonthTotal, 0, ',', '.') }}</span>
                    </div>
                    {{-- RUMUS KALKULASI --}}
                    <div class="text-end mt-1">
                        <span class="text-muted fst-italic" style="font-size: 0.65rem;">*(Rp {{
                            number_format($dailyDeposit, 0, ',', '.') }} x {{ $daysInNextMonth }} hari)*</span>
                    </div>
                </div>
                @else
                <small class="text-danger d-block mb-3 fw-bold"><i class="ti tabler-alert me-1"></i> Prabayar bulan {{
                    $currentMonthName }} belum dibayar!</small>
                @endif

                {{-- Kotak Rekening BLUD --}}
                <div class="mt-auto bg-lighter p-2 rounded-3 border-dashed position-relative">
                    <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem;">TUJUAN TRANSFER
                        (BLUD):</small>
                    @if($activeBankAccount)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-primary d-block" style="font-size: 0.85rem;">{{
                                $activeBankAccount->bank_name }}</span>
                            <span class="fw-bold text-dark font-monospace fs-6">{{ $activeBankAccount->account_number
                                }}</span>
                        </div>
                        {{-- Tombol Copy Rekening --}}
                        <button type="button"
                            class="btn btn-sm btn-icon btn-outline-secondary rounded-circle btn-copy bg-white shadow-sm"
                            onclick="copyRekening('{{ $activeBankAccount->account_number }}', this)"
                            data-bs-toggle="tooltip" title="Salin Rekening">
                            <i class="ti tabler-file-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1 text-truncate" style="font-size: 0.7rem;"
                        title="{{ $activeBankAccount->account_name }}">a.n {{ $activeBankAccount->account_name
                        }}</small>
                    @else
                    <span class="badge bg-label-danger mt-1">Rekening belum diatur Dinas.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TOTAL TITIK --}}
    <div class="col-md-4">
        <div class="card h-100 border-start border-4 border-info shadow-sm">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-2">
                    <div
                        class="avatar avatar-sm me-2 bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ti tabler-user-pin"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Titik Parkir Dikelola</h6>
                </div>
                <h3 class="fw-bold text-info mb-3">{{ $totalLocations }} <small class="fs-6 text-muted">Titik</small>
                </h3>
                <div class="mt-auto text-end">
                    <a href="{{ route('field_coordinator.location-requests.create') }}"
                        class="btn btn-sm btn-outline-info rounded-pill w-100"><i class="ti tabler-plus me-1"></i>
                        Ajukan Perubahan Titik</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DATA --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0"><i class="ti tabler-map-pin text-primary me-1"></i> Titik Parkir
                    Anda</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Lokasi</th>
                            <th>Ruas Jalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLocations as $loc)
                        <tr>
                            <td class="fw-medium">{{ Str::limit($loc->name, 25) }}</td>
                            <td><span class="badge bg-label-secondary">{{ Str::limit($loc->roadSection->name ?? '-', 20)
                                    }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">
                                <i class="ti tabler-map-pin-plus ti-xl mb-2 text-muted opacity-50"></i><br>
                                Belum ada titik parkir.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0"><i class="ti tabler-history text-warning me-1"></i> Status
                    Pengajuan Terakhir</h6>
                <a href="{{ route('field_coordinator.location-requests.index') }}"
                    class="btn btn-xs btn-outline-secondary rounded-pill">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipe</th>
                            <th>Detail Titik</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                        <tr>
                            <td>
                                @if($req->request_type == 'add') <span class="badge bg-label-success"><i
                                        class="ti tabler-plus me-1"></i> Penambahan</span>
                                @else <span class="badge bg-label-danger"><i class="ti tabler-subtract me-1"></i>
                                    Pencabutan</span> @endif
                            </td>
                            <td>
                                <span class="fw-medium text-wrap d-block" style="max-width: 150px;">
                                    {{ $req->request_type == 'add' ? $req->name : ($req->parkingLocation->name ?? '-')
                                    }}
                                </span>
                            </td>
                            <td>
                                @if($req->status == 'pending') <span class="badge bg-label-warning">Pending</span>
                                @elseif($req->status == 'surveyed') <span class="badge bg-label-info">Surveyed</span>
                                @elseif($req->status == 'approved') <span
                                    class="badge bg-label-success">Disetujui</span>
                                @else <span class="badge bg-label-danger">Ditolak</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="ti tabler-file-text ti-xl mb-2 text-muted opacity-50"></i><br>
                                Belum ada riwayat pengajuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW DOKUMEN PKS --}}
@if($activeAgreement)
<div class="modal fade" id="modalPks" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="ti tabler-file-description me-2 text-success"></i>Dokumen PKS Aktif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="{{ route('masterdata.agreements.pdf', $activeAgreement->id) }}#toolbar=0"
                    class="pdf-container"></iframe>
            </div>
            <div class="modal-footer border-top bg-white justify-content-between">
                <p class="text-muted small mb-0"><i class="ti tabler-info-circle me-1"></i> Dokumen ini di-generate
                    otomatis oleh sistem.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('masterdata.agreements.pdf', $activeAgreement->id) }}" target="_blank"
                        class="btn btn-success fw-bold rounded-pill">
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
        setTimeout(() => { icon.className = 'ti tabler-file-copy'; }, 2000);
    }
</script>
@endsection