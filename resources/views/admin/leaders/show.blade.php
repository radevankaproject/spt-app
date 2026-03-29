@extends('layouts.app')

@section('title', 'Profil Pimpinan: ' . ($leader->user->name ?? 'N/A'))

@section('skeleton')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('layouts.partials._skeleton-field-coordinator-show')
    </div>
@endsection

@push('styles')
    <style>
        .timeline-card { transition: all 0.3s ease; }
        .timeline-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }

        /* ✅ CUSTOM SCROLLBAR ELEGAN */
        .custom-scroll { max-height: 500px; overflow-y: auto; overflow-x: hidden; padding-right: 5px; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(67, 89, 113, 0.2); border-radius: 10px; }
        .custom-scroll:hover::-webkit-scrollbar-thumb { background: rgba(67, 89, 113, 0.4); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ✅ HEADER & FILTER TAHUN --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Rapor & Portofolio Pimpinan</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.leaders.index') }}">Leaders</a></li>
                        <li class="breadcrumb-item active">Detail Profil</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <ul class="nav nav-pills flex-nowrap overflow-auto hide-scrollbar m-0" style="white-space: nowrap;">
                    @foreach($availableYears as $year)
                        <li class="nav-item me-2">
                            <a class="nav-link py-2 {{ $year == $selectedYear ? 'active shadow-sm' : 'bg-white border' }}"
                               href="{{ route('admin.leaders.show', ['leader' => $leader->id, 'year' => $year]) }}">
                               <i class="ri ri-calendar-line me-1"></i> Tahun {{ $year }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.leaders.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="ri ri-arrow-left-line"></i>
                </a>
            </div>
        </div>

        @php
            $uName = $leader->user->name ?? 'N/A';
            $uAvatar = ($leader->user && $leader->user->img)
                ? asset('storage/' . $leader->user->img)
                : "https://ui-avatars.com/api/?name=".urlencode($uName)."&background=auto&color=fff&rounded=true&size=120";
            $isActive = $leader->user ? $leader->user->is_active : false;
        @endphp

        <div class="row">
            {{-- KOLOM KIRI: PROFIL & STATISTIK --}}
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body pt-5 text-center">
                        <div class="user-avatar-section mb-4">
                            <div class="position-relative d-inline-block">
                                <img class="img-fluid rounded-circle shadow-sm {{ !$isActive ? 'opacity-50' : '' }}" style="object-fit: cover; width: 120px; height: 120px;"
                                    src="{{ $uAvatar }}" alt="Avatar" />
                                @if($isActive)
                                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle" data-bs-toggle="tooltip" title="Aktif Menjabat">
                                        <span class="visually-hidden">Aktif</span>
                                    </span>
                                @else
                                    <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-white rounded-circle" data-bs-toggle="tooltip" title="Purna Tugas">
                                        <span class="visually-hidden">Purna Tugas</span>
                                    </span>
                                @endif
                            </div>
                            <h5 class="mt-3 mb-1 fw-bold {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $uName }}</h5>

                            @if($isActive)
                                <span class="badge bg-label-primary rounded-pill px-3 py-2 mt-1"><i class="ri ri-vip-crown-line me-1"></i> Pimpinan Aktif</span>
                            @else
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 mt-1"><i class="ri ri-history-line me-1"></i> Purna Tugas</span>
                            @endif
                        </div>

                        {{-- STATISTIK KONTRAK (Berdasarkan Tahun) --}}
                        <div class="bg-lighter rounded-3 p-3 mb-4 text-start border {{ !$isActive ? 'border-danger border-opacity-25 bg-danger bg-opacity-10' : 'border-primary border-opacity-10' }}">
                            <h6 class="fw-bold mb-3 {{ !$isActive ? 'text-danger' : 'text-primary' }}"><i class="ri ri-bar-chart-box-line me-1"></i> Statistik Pengesahan ({{ $selectedYear }})</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total PKS Ditandatangani</span>
                                <span class="fw-bold text-dark">{{ $totalAgreementsCount }} Dokumen</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="text-muted">PKS Aktif Berjalan</span>
                                <span class="fw-bold text-success">{{ $activeAgreementsCount }} PKS</span>
                            </div>
                        </div>

                        <h6 class="pb-2 border-bottom text-start mb-3">Informasi Personal</h6>
                        <ul class="list-unstyled mb-4 text-start small">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-fingerprint-line text-muted me-2 ri-20px"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Nomor Induk Pegawai (NIP)</span>
                                    <span class="fw-medium text-dark">{{ formatNip($leader->employee_number) }}</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-mail-line text-muted me-2 ri-20px"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Email (Username)</span>
                                    <span class="fw-medium text-dark">{{ $leader->user->email ?? '-' }} ({{ $leader->user->username ?? '-' }})</span>
                                </div>
                            </li>

                            {{-- ✅ TAMBAHAN: STATUS JABATAN SAAT INI (TETAP/PLT/PLH) --}}
                            @php
                                $statusLabel = 'Pimpinan Definitif (Tetap)';
                                if($leader->status_jabatan == 'plt') $statusLabel = 'Pelaksana Tugas (Plt)';
                                if($leader->status_jabatan == 'plh') $statusLabel = 'Pelaksana Harian (Plh)';
                            @endphp
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-briefcase-4-line text-muted me-2 ri-20px"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Status Jabatan</span>
                                    <span class="fw-medium text-primary">{{ $statusLabel }}</span>
                                </div>
                            </li>

                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-calendar-check-line text-muted me-2 ri-20px"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Mulai Menjabat</span>
                                    <span class="fw-medium text-dark">{{ $leader->start_date ? \Carbon\Carbon::parse($leader->start_date)->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="ri ri-calendar-close-line text-muted me-2 ri-20px"></i>
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Akhir Menjabat</span>
                                    <span class="fw-medium {{ $leader->end_date ? 'text-danger' : 'text-success' }}">
                                        {{ $leader->end_date ? \Carbon\Carbon::parse($leader->end_date)->translatedFormat('d F Y') : 'Sekarang (Masih Menjabat)' }}
                                    </span>
                                </div>
                            </li>
                        </ul>

                        {{-- ✅ RIWAYAT MASA JABATAN (MESIN WAKTU) --}}
                        @if($leader->histories->isNotEmpty())
                            <h6 class="pb-2 border-bottom text-start mb-3 mt-5"><i class="ri ri-history-line me-1"></i> Jejak Riwayat Jabatan</h6>
                            <ul class="timeline mb-0 text-start ps-3" style="border-left: 2px solid #e1e4e8; list-style-type: none;">
                                @foreach($leader->histories as $history)
                                    <li class="mb-3 position-relative" style="padding-left: 15px;">
                                        {{-- Lingkaran Timeline --}}
                                        <span class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -24px; top: 5px; border: 2px solid #fff;"></span>

                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                @if($history->status_jabatan == 'tetap')
                                                    Pimpinan Definitif (Tetap)
                                                @elseif($history->status_jabatan == 'plt')
                                                    Pelaksana Tugas (Plt)
                                                @else
                                                    Pelaksana Harian (Plh)
                                                @endif
                                            </span>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($history->start_date)->translatedFormat('d M Y') }}
                                                <i class="ri ri-arrow-right-line align-middle mx-1"></i>
                                                {{ $history->end_date ? \Carbon\Carbon::parse($history->end_date)->translatedFormat('d M Y') : 'Sekarang' }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PORTOFOLIO PKS --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- 1. PKS SEDANG BERJALAN (AKTIF) --}}
                <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="ri ri-file-paper-2-line text-primary me-2"></i> PKS Aktif (Tahun {{ $selectedYear }})</h5>

                {{-- ✅ BUNGKUS DENGAN CUSTOM SCROLL --}}
                <div class="custom-scroll mb-5 pe-2">
                    @forelse ($activeAgreements as $pks)
                        @php
                            $cName = $pks->fieldCoordinator->user->name ?? 'N/A';
                            $cAvatar = ($pks->fieldCoordinator->user && $pks->fieldCoordinator->user->img)
                                ? asset('storage/' . $pks->fieldCoordinator->user->img)
                                : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                        @endphp
                        <div class="card mb-3 border-primary border-opacity-25 shadow-sm timeline-card">
                            <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-primary"><i class="ri ri-file-list-3-line"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-primary">{{ $pks->agreement_number }}</h6>
                                        <small class="text-muted">Masa Berlaku: <span class="fw-medium text-dark">{{ $pks->start_date->translatedFormat('d M Y') }} - {{ $pks->end_date->translatedFormat('d M Y') }}</span></small>
                                    </div>
                                </div>
                                <span class="badge bg-{{ $pks->status == 'active' ? 'success' : 'warning' }} rounded-pill px-3 shadow-sm">
                                    {{ ucwords(str_replace('_', ' ', $pks->status)) }}
                                </span>
                            </div>
                            <div class="card-body pt-3 pb-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="text-muted me-3 small">Bermitra dengan:</span>
                                        <img src="{{ $cAvatar }}" alt="Korlap" class="rounded-circle shadow-sm me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        <span class="fw-medium text-dark">{{ $cName }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="btn btn-sm btn-primary rounded-pill"><i class="ri ri-eye-line me-1"></i> Detail PKS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm bg-lighter">
                            <div class="card-body text-center py-5">
                                <div class="avatar avatar-xl mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="ri ri-folder-forbid-line ri-2x"></i></span>
                                </div>
                                <h6 class="fw-bold text-dark">Tidak ada Kontrak Aktif</h6>
                                <p class="text-muted mb-0">Belum ada dokumen PKS aktif di tahun {{ $selectedYear }}.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- 2. ARSIP PKS (KEDALUWARSA/DIPUTUS) --}}
                <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="ri ri-archive-drawer-line text-secondary me-2"></i> Riwayat Pengesahan Selesai ({{ $selectedYear }})</h5>
                <div class="card border-0 shadow-sm mb-4">
                    {{-- ✅ TABEL DIBUNGKUS CUSTOM SCROLL --}}
                    <div class="table-responsive text-nowrap custom-scroll" style="max-height: 400px;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th>No. Dokumen PKS</th>
                                    <th>Mitra Korlap</th>
                                    <th>Masa Berlaku</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($historyAgreements as $history)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $history->agreement_number }}</span></td>
                                        <td><span class="fw-medium">{{ Str::limit($history->fieldCoordinator->user->name ?? 'N/A', 20) }}</span></td>
                                        <td>
                                            <small class="d-block text-muted">Mulai: {{ $history->start_date->format('d/m/Y') }}</small>
                                            <small class="d-block text-danger">Akhir: {{ $history->end_date->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $history->status == 'expired' ? 'danger' : 'dark' }} rounded-pill shadow-sm">
                                                {{ ucwords(str_replace('_', ' ', $history->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('masterdata.agreements.show', $history->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Lihat Histori PKS">
                                                <i class="ri ri-arrow-right-circle-line icon-20px"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            Belum ada riwayat kontrak yang selesai di tahun {{ $selectedYear }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
