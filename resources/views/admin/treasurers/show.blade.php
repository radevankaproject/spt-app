@extends('layouts.app')

@section('title', 'Profil Bendahara: ' . ($treasurer->user->name ?? 'N/A'))

@section('skeleton')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('layouts.partials._skeleton-field-coordinator-show')
    </div>
@endsection

@push('styles')
<style>
    .hover-link { transition: all 0.2s ease; text-decoration: none; }
    .hover-link:hover { color: #696cff !important; transform: translateX(3px); display: inline-block;}
</style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Detail Profil Bendahara</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.treasurers.index') }}">Bendahara</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.treasurers.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill"><i class="ri ri-arrow-left-line me-1"></i> Kembali</a>
            </div>
        </div>

        @php
            $uName = $treasurer->user->name ?? 'N/A';
            $uAvatar = ($treasurer->user && $treasurer->user->img) ? asset('storage/' . $treasurer->user->img) : "https://ui-avatars.com/api/?name=".urlencode($uName)."&background=auto&color=fff&rounded=true&size=120";
            $isActive = $treasurer->user ? $treasurer->user->is_active : false;
        @endphp

        <div class="row g-4">
            {{-- ========================================= --}}
            {{-- KOLOM KIRI: PROFIL & BIODATA BENDAHARA    --}}
            {{-- ========================================= --}}
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-4 border-0 shadow-sm rounded-4">
                    <div class="card-body pt-5 text-center">
                        <div class="user-avatar-section mb-4">
                            <div class="position-relative d-inline-block">
                                <img class="img-fluid rounded-circle shadow-sm {{ !$isActive ? 'opacity-50' : '' }}" style="object-fit: cover; width: 120px; height: 120px; border: 4px solid #fff;" src="{{ $uAvatar }}" alt="Avatar" />
                            </div>
                            <h5 class="mt-3 mb-1 fw-bold {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $uName }}</h5>
                            @if($isActive) 
                                <span class="badge bg-label-primary rounded-pill px-3 py-2 mt-1"><i class="ri ri-vip-crown-line me-1"></i> Bendahara Aktif</span>
                            @else 
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 mt-1"><i class="ri ri-history-line me-1"></i> Purna Tugas</span> 
                            @endif
                        </div>

                        <h6 class="pb-2 border-bottom text-start mb-3 mt-4 text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Informasi Personal</h6>
                        <ul class="list-unstyled mb-4 text-start small">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-fingerprint-line text-primary me-3 ri-24px p-2 bg-lighter rounded"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Nomor Induk Pegawai (NIP)</span>
                                    <span class="fw-bold text-dark">{{ formatNip($treasurer->employee_number) }}</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-mail-line text-primary me-3 ri-24px p-2 bg-lighter rounded"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Email (Username)</span>
                                    <span class="fw-bold text-dark">{{ $treasurer->user->email ?? '-' }}<br>({{ $treasurer->user->username ?? '-' }})</span>
                                </div>
                            </li>
                            @php
                                $statusLabel = 'Bendahara Definitif (Tetap)';
                                if($treasurer->status_jabatan == 'plt') $statusLabel = 'Pelaksana Tugas (Plt)';
                                if($treasurer->status_jabatan == 'plh') $statusLabel = 'Pelaksana Harian (Plh)';
                            @endphp
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-briefcase-4-line text-primary me-3 ri-24px p-2 bg-lighter rounded"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Status Jabatan</span>
                                    <span class="fw-bold text-primary">{{ $statusLabel }}</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-calendar-check-line text-primary me-3 ri-24px p-2 bg-lighter rounded"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Mulai Menjabat</span>
                                    <span class="fw-bold text-dark">{{ $treasurer->start_date ? \Carbon\Carbon::parse($treasurer->start_date)->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                            </li>
                        </ul>

                        @if($treasurer->histories->isNotEmpty())
                            <h6 class="pb-2 border-bottom text-start mb-3 mt-5 text-muted fw-bold text-uppercase" style="font-size: 0.75rem;"><i class="ri ri-history-line me-1"></i> Riwayat Jabatan</h6>
                            <ul class="timeline mb-0 text-start ps-3" style="border-left: 2px solid #e1e4e8; list-style-type: none;">
                                @foreach($treasurer->histories as $history)
                                    <li class="mb-3 position-relative" style="padding-left: 15px;">
                                        <span class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -24px; top: 5px; border: 2px solid #fff;"></span>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                @if($history->status_jabatan == 'tetap') Bendahara Definitif (Tetap)
                                                @elseif($history->status_jabatan == 'plt') Pelaksana Tugas (Plt)
                                                @else Pelaksana Harian (Plh) @endif
                                            </span>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($history->start_date)->translatedFormat('d M Y') }} <i class="ri ri-arrow-right-line align-middle mx-1"></i> {{ $history->end_date ? \Carbon\Carbon::parse($history->end_date)->translatedFormat('d M Y') : 'Sekarang' }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- KOLOM KANAN: JEJAK AUDIT & TRANSAKSI      --}}
            {{-- ========================================= --}}
            <div class="col-xl-8 col-lg-7 col-md-7">
                
                {{-- Card Statistik --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 fw-medium opacity-75">Total Uang Negara yang Divalidasi</p>
                            <h3 class="mb-0 text-white fw-bold">Rp {{ number_format($totalValidatedAmount, 0, ',', '.') }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white rounded p-2">
                            <i class="ri icon-base ri-safe-2-line icon-22px text-primary"></i>
                        </div>
                    </div>
                </div>

                {{-- Tabel Histori Transaksi --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-bottom py-3 bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold"><i class="ri ri-file-list-3-line me-2 text-primary"></i>Jejak Audit Validasi Setoran</h6>
                        <span class="badge bg-label-secondary rounded-pill">Total: {{ $deposits->total() }} Transaksi</span>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-bold text-dark">Kode Ref & Tanggal</th>
                                        <th class="fw-bold text-dark">No. Kontrak PKS</th>
                                        <th class="fw-bold text-dark text-end">Nominal</th>
                                        <th class="fw-bold text-dark text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deposits as $deposit)
                                        <tr>
                                            <td>
                                                {{-- ✅ LINK REFERRAL CODE & TANGGAL --}}
                                                @php
                                                    // Ambil referal_code, kalau kosong fallback bikin TRX-0001
                                                    $refCode = $deposit->referal_code ?? 'TRX-'.str_pad($deposit->id, 5, '0', STR_PAD_LEFT);
                                                    
                                                    // ✅ Tambahkan prefix 'masterdata.' sesuai URL antum
                                                    $detailRoute = Route::has('masterdata.deposit-transactions.show') ? route('masterdata.deposit-transactions.show', $deposit->id) : '#';
                                                @endphp
                                                
                                                <a href="{{ $detailRoute }}" class="fw-bold text-primary d-inline-block mb-1 hover-link" data-bs-toggle="tooltip" title="Lihat Detail Setoran">
                                                    <i class="ri ri-file-paper-2-line align-bottom me-1"></i>{{ $refCode }}
                                                </a>
                                                <span class="d-block text-dark small"><i class="ri ri-calendar-line align-bottom me-1"></i>{{ \Carbon\Carbon::parse($deposit->deposit_date)->translatedFormat('d M Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">Divalidasi: {{ $deposit->validation_date ? \Carbon\Carbon::parse($deposit->validation_date)->translatedFormat('d M, H:i') : '-' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('masterdata.agreements.show', $deposit->agreement_id) }}" class="fw-bold text-dark d-inline-block hover-link">
                                                    {{ $deposit->agreement->agreement_number ?? 'N/A' }}
                                                </a>
                                                <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="{{ $deposit->agreement->fieldCoordinator->user->name ?? 'Korlap' }}">
                                                    Korlap: <span class="fw-medium text-dark">{{ $deposit->agreement->fieldCoordinator->user->name ?? 'N/A' }}</span>
                                                </small>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if($deposit->is_validated)
                                                    <span class="badge bg-label-success rounded-pill px-3 py-1"><i class="ri ri-check-double-line me-1"></i> Sah</span>
                                                @else
                                                    <span class="badge bg-label-warning rounded-pill px-3 py-1"><i class="ri ri-time-line me-1"></i> Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="ri ri-file-search-line ri-3x text-muted opacity-50 d-block mb-2"></i>
                                                <h6 class="fw-bold text-dark mb-1">Belum ada jejak transaksi</h6>
                                                <span class="text-muted small">Bendahara ini belum melakukan validasi setoran apapun dari Korlap.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($deposits->hasPages())
                            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-lighter">
                                <small class="text-muted fw-medium">Menampilkan {{ $deposits->firstItem() }} - {{ $deposits->lastItem() }} dari {{ $deposits->total() }} transaksi</small>
                                <div>
                                    {{ $deposits->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush