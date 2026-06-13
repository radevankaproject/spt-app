@extends('layouts.app')

@section('title', 'Portofolio Koordinator: ' . $fieldCoordinator->user->name)

@section('skeleton')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('layouts.partials._skeleton-field-coordinator-show')
    </div>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ✅ FILTER TAHUN (Nav Pills Premium) --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Rapor & Portofolio Koordinator</h4>
                <p class="text-muted mb-0">Memantau kinerja kontrak berdasarkan tahun anggaran.</p>
            </div>
            <div>
                <ul class="nav nav-pills flex-nowrap overflow-auto hide-scrollbar" style="white-space: nowrap;">
                    @foreach($availableYears as $year)
                        <li class="nav-item me-2">
                            <a class="nav-link {{ $year == $selectedYear ? 'active shadow-sm' : 'bg-white border' }}"
                               href="{{ route('admin.field-coordinators.show', ['field_coordinator' => $fieldCoordinator->id, 'year' => $year]) }}">
                               <i class="ri ri-calendar-line me-1"></i> Tahun {{ $year }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            {{-- KOLOM KIRI: PROFIL & STATISTIK --}}
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body pt-5 text-center">
                        <div class="user-avatar-section mb-4">
                            @if ($fieldCoordinator->user && $fieldCoordinator->user->img)
                                <img class="img-fluid rounded-circle shadow-sm" style="object-fit: cover;"
                                    src="{{ asset('storage/'.$fieldCoordinator->user->img) }}" height="120"
                                    width="120" alt="Avatar" />
                            @else
                                <div class="avatar avatar-xl mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-warning" style="font-size: 2rem;">
                                        {{ strtoupper(substr($fieldCoordinator->user->name ?? 'K', 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                            <h5 class="mt-3 mb-1 fw-bold">{{ $fieldCoordinator->user->name }}</h5>
                            <span class="badge bg-label-warning rounded-pill">Koordinator Lapangan</span>
                        </div>

                        {{-- ✅ STATISTIK BERDASARKAN TAHUN --}}
                        <div class="bg-lighter rounded-3 p-3 mb-4 text-start">
                            <h6 class="fw-bold text-primary mb-3"><i class="ri ri-bar-chart-box-line me-1"></i> Statistik Tahun {{ $selectedYear }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Kontrak (PKS)</span>
                                <span class="fw-bold text-dark">{{ $totalAgreementsCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Titik Lokasi Dikelola</span>
                                <span class="fw-bold text-dark">{{ $activeParkingLocationsCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="text-muted">Total Setoran Masuk</span>
                                <span class="fw-bold text-success">Rp {{ number_format($totalValidatedDeposit, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <h6 class="pb-2 border-bottom text-start mb-3">Informasi Personal</h6>
                        <ul class="list-unstyled mb-4 text-start small">
                            <li class="mb-2 d-flex align-items-center"><i class="ri ri-mail-line text-muted me-2"></i> <span>{{ $fieldCoordinator->user->email }}</span></li>
                            <li class="mb-2 d-flex align-items-center"><i class="ri ri-phone-line text-muted me-2"></i> <span>{{ $fieldCoordinator->phone_number }}</span></li>
                            <li class="mb-2 d-flex align-items-center"><i class="ri ri-id-card-line text-muted me-2"></i> <span>{{ $fieldCoordinator->id_card_number }}</span></li>
                            <li class="d-flex align-items-start"><i class="ri ri-map-pin-line text-muted me-2 mt-1"></i> <span>{{ $fieldCoordinator->address }}</span></li>
                        </ul>
                        <div class="d-grid gap-2 mt-4">
                            {{-- Edit buttons moved to index --}}
                        </div>
                    </div>
                </div>

                {{-- KARTU KTP --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="pb-2 border-bottom mb-3"><i class="ri ri-pass-valid-line me-1"></i> Dokumen KTP</h6>
                        @if ($fieldCoordinator->id_card_img)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ktpModal">
                                <img src="{{ asset('storage/'.$fieldCoordinator->id_card_img) }}" alt="Foto KTP"
                                    class="img-fluid rounded-3 shadow-sm" style="cursor: zoom-in;">
                            </a>
                        @else
                            <div class="text-center py-4 bg-lighter rounded-3">
                                <i class="icon-base ri ri-image-line icon-22px"></i>
                                <small class="text-muted">Belum ada KTP</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PORTOFOLIO KONTRAK --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- 1. PKS SEDANG BERJALAN (AKTIF) --}}
                <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="ri ri-play-circle-line text-primary me-2"></i> Kontrak Berjalan (Tahun {{ $selectedYear }})</h5>
                @forelse ($activeAgreements as $pks)
                    <div class="card mb-4 border-primary border-opacity-25 shadow-sm">
                        <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="mb-0 fw-bold text-primary">{{ $pks->agreement_number }}</h5>
                                <small class="text-muted">{{ $pks->start_date->translatedFormat('d M Y') }} - {{ $pks->end_date->translatedFormat('d M Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $pks->status == 'active' ? 'success' : 'warning' }} rounded-pill px-3">
                                {{ ucwords(str_replace('_', ' ', $pks->status)) }}
                            </span>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle bg-label-info"><i class="ri ri-map-pin-user-line"></i></span></div>
                                        <div>
                                            <p class="mb-0 fw-medium text-dark">{{ $pks->activeParkingLocations->count() }} Lokasi</p>
                                            <small class="text-muted">Dikelola saat ini</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle bg-label-success"><i class="ri ri-money-dollar-circle-line"></i></span></div>
                                        <div>
                                            <p class="mb-0 fw-medium text-dark">Rp {{ number_format($pks->total_deposit ?? 0, 0, ',', '.') }}</p>
                                            <small class="text-muted">Setoran Masuk</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top text-end">
                                @if($pks->signed_document_path)
                                <a href="{{ Storage::url($pks->signed_document_path) }}" target="_blank" class="btn btn-sm btn-outline-success me-2"><i class="ri ri-file-check-line me-1"></i> File Scan Asli</a>
                                @endif
                                <a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="btn btn-sm btn-primary shadow-sm"><i class="ri ri-eye-line me-1"></i> Buka Detail PKS</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card mb-5 border-0 shadow-sm bg-lighter">
                        <div class="card-body text-center py-5">
                            <i class="ri ri-folder-forbid-line" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">Tidak ada Kontrak Aktif</h6>
                            <p class="text-muted mb-0">Koordinator ini tidak memiliki PKS yang sedang berjalan di tahun {{ $selectedYear }}.</p>
                        </div>
                    </div>
                @endforelse

                {{-- 2. ARSIP PKS (KEDALUWARSA/DIPUTUS) --}}
                <h5 class="fw-bold mb-3 mt-5 d-flex align-items-center"><i class="ri ri-archive-drawer-line text-secondary me-2"></i> Riwayat Kontrak Selesai</h5>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Dokumen PKS</th>
                                    <th>Masa Berlaku</th>
                                    <th>Total Setoran</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($historyAgreements as $history)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $history->agreement_number }}</span></td>
                                        <td>
                                            <small class="d-block text-muted">Mulai: {{ $history->start_date->format('d/m/Y') }}</small>
                                            <small class="d-block text-danger">Akhir: {{ $history->end_date->format('d/m/Y') }}</small>
                                        </td>
                                        <td><span class="fw-medium text-success">Rp {{ number_format($history->total_deposit ?? 0, 0, ',', '.') }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $history->status == 'expired' ? 'danger' : 'dark' }} rounded-pill">
                                                {{ ucwords(str_replace('_', ' ', $history->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($history->signed_document_path)
                                            <a href="{{ Storage::url($history->signed_document_path) }}" target="_blank" class="btn btn-sm btn-icon btn-text-success rounded-pill me-1" data-bs-toggle="tooltip" title="File Scan Asli">
                                                <i class="ri ri-file-check-line icon-22px"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('masterdata.agreements.show', $history->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Lihat Histori">
                                                <i class="ri ri-arrow-right-circle-line icon-22px"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada riwayat kontrak yang selesai di tahun ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal KTP --}}
        <div class="modal fade" id="ktpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white">Dokumen KTP</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img src="{{ $fieldCoordinator->id_card_img ? asset('storage/'.$fieldCoordinator->id_card_img) : '' }}"
                            class="img-fluid w-100" alt="Foto KTP">
                    </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide scrollbar class untuk nav-pills tahun agar rapi di mobile
        const style = document.createElement('style');
        style.innerHTML = `
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        `;
        document.head.appendChild(style);

        // Aktifkan Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
