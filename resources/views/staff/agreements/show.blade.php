@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Perjanjian: ' . $agreement->agreement_number)



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
     <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/timeline.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .timeline-scrollable {
            max-height: 600px;
            position: relative;
            overflow: hidden;
            padding-right: 15px;
            padding-left: 15px;
            padding-bottom: 2rem;
        }

        .pdf-viewer-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 0.75rem;
            border: 1px solid #e7e7e8;
        }

        .pdf-viewer-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Nav Pills Custom Premium */
        .nav-pills .nav-link {
            border-radius: 50rem;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            color: #697a8d;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(105, 108, 255, 0.05);
            color: #696cff;
        }

        .cursor-pointer { cursor: pointer; }

        /* Premium Map Popup Customization */
        .custom-popup .leaflet-popup-content-wrapper {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .custom-popup .leaflet-popup-content {
            margin: 0;
            line-height: 1.5;
        }
        .custom-popup .leaflet-popup-tip {
            background: rgba(255, 255, 255, 0.98);
        }
        .custom-div-icon {
            background: transparent;
            border: none;
        }
        .custom-div-icon .marker-pin {
            background-color: #696cff;
            width: 36px;
            height: 36px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            position: absolute;
            left: -18px;
            top: -36px;
        }
        .custom-div-icon .marker-pin i {
            transform: rotate(45deg);
            font-size: 16px;
        }
    </style>
@endsection

@php
    // LOGIKA STATUS & MASA TENGGANG
    $isGracePeriod = false;
    $daysRemaining = null;
    if ($agreement->status === 'active') {
        $daysRemaining = (int) now()->diffInDays($agreement->end_date, false);
        if ($daysRemaining >= 0 && $daysRemaining <= 10) {
            $isGracePeriod = true;
        }
    }

    $statusClass = 'secondary';
    $statusText = 'Tidak Diketahui';
    if ($agreement->status == 'active') {
        $statusClass = 'success';
        $statusText = 'Aktif';
    }
    if ($agreement->status == 'expired') {
        $statusClass = 'danger';
        $statusText = 'Kedaluwarsa';
    }
    if ($agreement->status == 'terminated') {
        $statusClass = 'dark';
        $statusText = 'Diputus';
    }
    if ($agreement->status == 'pending_renewal') {
        $statusClass = 'warning';
        $statusText = 'Menunggu Perpanjangan';
    }

    // AVATAR KORLAP
    $cName = $agreement->fieldCoordinator->user->name ?? 'N/A';
    $cAvatar =
        $agreement->fieldCoordinator->user && $agreement->fieldCoordinator->user->img
            ? asset('storage/'.$agreement->fieldCoordinator->user->img)
            : 'https://ui-avatars.com/api/?name=' .
                urlencode($cName) .
                '&background=random&color=fff&size=120&rounded=true&bold=true';

    // Logika Map Lokasi
    $mapLocations = [];
    foreach ($locationsByRoadSection as $roadSectionName => $locations) {
        foreach ($locations as $loc) {
            if (!empty($loc->latitude) && !empty($loc->longitude)) {
                $mapLocations[] = [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'lat' => $loc->latitude,
                    'lng' => $loc->longitude,
                    'deposit' => number_format($loc->daily_deposit, 0, ',', '.'),
                    'road_section' => $roadSectionName
                ];
            }
        }
    }

    // Logika Format Angka Dinamis
    $formattedDeposit = '0';
    $depositSuffix = '';
    if ($totalDepositThisYear >= 1000000) {
        $formattedDeposit = number_format($totalDepositThisYear / 1000000, 1, ',', '.');
        $depositSuffix = 'Jt';
    } elseif ($totalDepositThisYear >= 1000) {
        $formattedDeposit = number_format($totalDepositThisYear / 1000, 1, ',', '.');
        $depositSuffix = 'Rb';
    } else {
        $formattedDeposit = number_format($totalDepositThisYear, 0, ',', '.');
    }
@endphp

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Detail Perjanjian Kerja Sama</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.index') }}">PKS</a></li>
                    <li class="breadcrumb-item active">{{ $agreement->agreement_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-outline-secondary">
                <i class="ti tabler-arrow-left me-1"></i> Kembali
            </a>
            @if(Auth::user()->role !== 'leader' && $agreement->status !== 'expired')
            <a href="{{ route('masterdata.agreements.edit', $agreement->id) }}" class="btn btn-primary shadow-sm">
                <i class="ti tabler-pencil me-1"></i> Edit PKS
            </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- ✅ KOLOM KIRI (Profil & Info PKS) - 4 Kolom (Dibuat Sticky) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body pt-5">
                    <div class="text-center mb-4">
                        <a href="{{ route('admin.field-coordinators.show', $agreement->field_coordinator_id) }}">
                            <img src="{{ $cAvatar }}" alt="Korlap Avatar" class="rounded-circle shadow-sm mb-3"
                                style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;" />
                        </a>
                        <a href="{{ route('admin.field-coordinators.show', $agreement->field_coordinator_id) }}">
                            <h5 class="mb-1 fw-bold text-dark">{{ $cName }}</h5>
                        </a>
                        <span class="badge bg-label-primary rounded-pill px-3 py-2">Koordinator Lapangan</span>
                    </div>

                    <div class="row text-center mb-4 g-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3 h-100">
                                <div class="avatar mx-auto mb-2"><span
                                        class="avatar-initial rounded bg-primary bg-opacity-10 text-primary"><i
                                            class="ri icon-base ti tabler-map-pin-2 ti-lg"></i></span></div>
                                <h5 class="mb-0 fw-bold">{{ $agreement->activeParkingLocations->count() }}</h5>
                                <small class="text-muted">Titik Lokasi</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 h-100">
                                {{-- ✅ FIX: Diubah ke warna Info agar tidak silau, dan angka jadi dinamis --}}
                                <div class="avatar mx-auto mb-2"><span
                                        class="avatar-initial rounded bg-info bg-opacity-10 text-info"><i
                                            class="ri icon-base ti tabler-wallet ti-lg"></i></span></div>
                                <h5 class="mb-0 fw-bold text-info">Rp {{ $formattedDeposit }} {{ $depositSuffix }}</h5>
                                <small class="text-muted">Setoran {{ now()->year }}</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="pb-2 border-bottom fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Detail
                        Kontrak</h6>
                    <ul class="list-unstyled mb-4 mt-3">
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-file-text text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">No. PKS</span>
                                <span class="fw-bold text-dark">{{ $agreement->agreement_number }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-circle-check text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span class="fw-medium text-heading">Status</span>
                                <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $statusText }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-category text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Jenis</span>
                                <span class="badge bg-label-info rounded-pill">{{ ucfirst($agreement->jenis) }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-user-star text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Pimpinan</span>
                                <span class="text-end">{{ Str::limit($agreement->leader->user->name ?? 'N/A', 15) }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="ti tabler-calendar-event text-primary me-2 ti-md mt-1"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Masa Berlaku</span>
                                <div class="text-end">
                                    <span class="d-block">{{ $agreement->start_date->translatedFormat('d M Y') }}</span>
                                    <span class="text-muted small">s/d <span
                                            class="{{ $isGracePeriod ? 'text-danger fw-bold' : '' }}">{{ $agreement->end_date->translatedFormat('d M Y') }}</span></span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-cash text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Setoran Harian</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-report-money text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Target Bulanan</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($agreement->monthly_deposit_target, 0, ',', '.') }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="ti tabler-businessplan text-primary me-2 ti-md"></i>
                            <div class="w-100 d-flex justify-content-between">
                                <span class="fw-medium text-heading">Total Selama PKS</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($agreement->total_deposit_target, 0, ',', '.') }}</span>
                            </div>
                        </li>
                    </ul>

                    @if ($isGracePeriod)
                        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
                            <i class="ti tabler-alert-octagon ti-lg me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1 fw-bold">Perhatian!</h6>
                                <p class="mb-0" style="font-size: 0.85rem;">PKS ini akan kedaluwarsa dalam
                                    <strong>{{ $daysRemaining }} hari</strong>. Segera siapkan perpanjangan.</p>
                            </div>
                        </div>
                    @endif

                    @if($agreement->status !== 'expired')
                    <div class="d-grid gap-2">
                        <a href="{{ route('masterdata.agreements.pdf', $agreement->id) }}" target="_blank"
                            class="btn btn-outline-danger">
                            <i class="ti tabler-printer me-1"></i> Cetak Dokumen PDF
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ✅ KOLOM KANAN (Tabs: Grafik, Lokasi, Riwayat, Dokumen) - 8 Kolom --}}
        <div class="col-xl-8 col-lg-7">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills mb-3 gap-2" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link {{ $agreement->status !== 'expired' ? 'active' : '' }}" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab-overview">
                            <i class="ti tabler-chart-histogram me-1"></i> Setoran & Grafik
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab-locations">
                            <i class="ti tabler-map-pin me-1"></i> Lokasi <span
                                class="badge rounded-pill bg-danger ms-1">{{ $agreement->activeParkingLocations->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab-history">
                            <i class="ti tabler-history me-1"></i> Riwayat Aktivitas
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link {{ $agreement->status === 'expired' ? 'active' : '' }}" role="tab" data-bs-toggle="tab"
                            data-bs-target="#tab-pdf">
                            <i class="ti tabler-file-zip me-1"></i> Arsip PKS
                        </button>
                    </li>
                </ul>

                <div class="tab-content bg-transparent p-0 shadow-none border-0">

                    {{-- TAB 1: OVERVIEW SETORAN & GRAFIK --}}
                    <div class="tab-pane fade {{ $agreement->status !== 'expired' ? 'show active' : '' }}" id="tab-overview" role="tabpanel">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="card-title fw-bold mb-0">Grafik Setoran Tahun {{ now()->year }}</h6>
                            </div>
                            <div class="card-body pt-4">
                                @if (count($chartData) > 0)
                                    <div id="depositChart" style="min-height: 300px;"></div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="ri icon-base ti tabler-chart-bar-off text-muted mb-2"
                                            style="font-size: 3rem;"></i>
                                        <p class="text-muted">Belum ada data setoran yang tervalidasi di tahun ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div
                                class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="card-title fw-bold mb-0">Riwayat Setoran Terbaru</h6>
                                <span class="badge bg-label-primary rounded-pill">Total: Rp
                                    {{ number_format($totalDepositThisYear, 0, ',', '.') }}</span>
                            </div>
                            <div class="table-responsive text-nowrap" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @forelse ($agreement->depositTransactions->sortByDesc('deposit_date') as $transaction)
                                            <tr>
                                                <td><i class="ri icon-base ti tabler-calendar-event text-primary me-2"></i>
                                                    {{ $transaction->deposit_date->translatedFormat('d F Y') }}</td>
                                                <td class="fw-bold text-primary">Rp
                                                    {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if ($transaction->is_validated)
                                                        <span class="badge bg-label-primary"><i
                                                                class="ri icon-base ti tabler-check me-1"></i> Valid</span>
                                                    @else
                                                        <span class="badge bg-label-warning"><i
                                                                class="ri icon-base ti tabler-clock me-1"></i> Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($transaction->is_validated)
                                                        <a href="{{ route('masterdata.deposit-transactions.pdf', $transaction->id) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-primary rounded-pill shadow-sm" data-bs-toggle="tooltip" title="Cetak Invoice">
                                                            <i class="ti tabler-printer"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-pill shadow-sm" data-bs-toggle="tooltip" title="Menunggu Validasi" disabled>
                                                            <i class="ti tabler-printer"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4"><i
                                                        class="ri icon-base ti tabler-wallet-off me-1"></i> Belum ada riwayat setoran sama
                                                    sekali.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: LOKASI PARKIR --}}
                    <div class="tab-pane fade" id="tab-locations" role="tabpanel">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="card-title fw-bold mb-0"><i class="ti tabler-map-2 text-primary me-2"></i>Peta Persebaran Lokasi Parkir</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="agreementLocationsMap" style="height: 400px; width: 100%; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; z-index: 1;"></div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">Daftar Lokasi berdasarkan Ruas Jalan</h6>
                                <div class="accordion accordion-header-primary" id="accordionParkingLocations">
                                    @forelse ($locationsByRoadSection as $roadSectionName => $locations)
                                        @php $accordionId = 'collapse-' . Str::slug($roadSectionName); @endphp
                                        <div class="accordion-item shadow-none border mb-2 rounded">
                                            <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                                                <button class="accordion-button collapsed fw-bold text-dark bg-light"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#{{ $accordionId }}">
                                                    <i class="ti tabler-route text-primary me-2"></i>
                                                    {{ $roadSectionName ?? 'Tanpa Ruas Jalan' }}
                                                    <span
                                                        class="badge bg-primary rounded-pill ms-auto me-3">{{ count($locations) }}
                                                        Titik</span>
                                                </button>
                                            </h2>
                                            <div id="{{ $accordionId }}" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionParkingLocations">
                                                <div class="accordion-body p-0 border-top">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach ($locations as $location)
                                                            <li
                                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-sm me-3">
                                                                        @if($location->image)
                                                                            <img src="{{ asset('storage/' . $location->image) }}" alt="Lokasi" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;">
                                                                        @else
                                                                            <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti tabler-map-pin"></i></span>
                                                                        @endif
                                                                    </div>
                                                                    <a href="{{ route('masterdata.parking-locations.show', $location->id) }}"
                                                                        class="fw-medium text-dark text-decoration-none">{{ $location->name }}</a>
                                                                </div>
                                                                <span class="text-primary fw-bold small">Rp
                                                                    {{ number_format($location->daily_deposit, 0, ',', '.') }}/hr</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="ri icon-base ti tabler-map-pin text-muted mb-3"
                                                style="font-size: 3rem;"></i>
                                            <p class="text-muted mb-0">Tidak ada lokasi parkir aktif yang terikat.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: TIMELINE HISTORY --}}
                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="card-title fw-bold mb-0">Riwayat Perjalanan PKS</h6>
                            </div>
                            <div class="card-body pt-4">
                                <div class="timeline-scrollable" id="historyTimeline">
                                    <ul class="timeline timeline-center mt-3 pb-4">
                                        @forelse ($agreement->histories->sortByDesc('created_at') as $history)
                                            @php
                                            $positionClass = $loop->odd ? 'timeline-item-left' : 'timeline-item-right';
                                                // Styling Event (Mengikuti icon yang sudah antum fix)
                                                $icon = 'ti tabler-file-text'; $color = 'secondary'; $eventName = 'Aktivitas Sistem';
                                                switch ($history->event_type) {
                                                    case 'agreement_created': $icon = 'ti tabler-file-add'; $color = 'primary'; $eventName = 'PKS Diterbitkan'; break;
                                                    case 'location_added': $icon = 'ti tabler-map-pin-plus'; $color = 'success'; $eventName = 'Lokasi Ditambahkan'; break;
                                                    case 'location_removed': $icon = 'ti tabler-map-pin-minus'; $color = 'danger'; $eventName = 'Lokasi Ditarik'; break;
                                                    case 'deposit_changed': $icon = 'ti tabler-currency-dollar'; $color = 'info'; $eventName = 'Perubahan Setoran'; break;
                                                    case 'status_changed': $icon = 'ti tabler-refresh'; $color = 'warning'; $eventName = 'Status Berubah'; break;
                                                    case 'agreement_renewed': $icon = 'ti tabler-refresh'; $color = 'success'; $eventName = 'PKS Diperpanjang'; break;
                                                    case 'agreement_terminated':
                                                    case 'agreement_expired': $icon = 'ti tabler-user-off'; $color = 'dark'; $eventName = 'PKS Berakhir/Diputus'; break;
                                                }

                                                // Avatar Pembuat (Ditambahkan path storage/ agar aman)
                                                $uName = $history->changer->name ?? 'Sistem Otomatis';
                                                $uAvatar = ($history->changer && $history->changer->img)
                                                    ? asset('storage/' . $history->changer->img)
                                                    : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=32&rounded=true&bold=true";

                                                // Logika Teks Panjang
                                                $notesList = array_filter(array_map('trim', explode(';', $history->notes)));
                                                $hasMultiple = count($notesList) > 1;
                                                $previewText = Str::limit($notesList[0] ?? $history->notes, 60);
                                                $collapseId = 'collapseHistoryShow_' . $history->id; // ID unik untuk accordion
                                            @endphp

                                            <li class="timeline-item {{ $positionClass }} mb-4">
                                                <span class="timeline-indicator timeline-indicator-{{ $color }} bg-white shadow-sm">
                                                    <i class="ri icon-base {{ $icon }}"></i>
                                                </span>
                                                <div class="timeline-event card p-0 border border-{{ $color }} border-opacity-25">
                                                    <div class="card-header border-bottom bg-{{ $color }} bg-opacity-10 d-flex justify-content-between align-items-center flex-wrap py-2 px-3">
                                                        <h6 class="card-title mb-0 fw-bold text-{{ $color }}">{{ $eventName }}</h6>
                                                        <div class="meta"><span class="badge bg-white text-dark shadow-sm">{{ $history->created_at->diffForHumans() }}</span></div>
                                                    </div>
                                                    <div class="card-body py-3 px-3">

                                                        {{-- ✅ LOGIKA TEKS INTERAKTIF --}}
                                                        <div class="mb-3">
                                                            @if($hasMultiple || strlen($history->notes) > 60)
                                                                <div class="d-flex flex-column align-items-start">
                                                                    <div class="d-flex justify-content-between align-items-center w-100 cursor-pointer"
                                                                         data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                                                        <span class="text-dark fw-medium">{{ $previewText }}</span>
                                                                        <span class="badge bg-label-secondary rounded-pill ms-2"><i class="ti tabler-arrow-down"></i></span>
                                                                    </div>
                                                                    <div class="collapse w-100 mt-2" id="{{ $collapseId }}">
                                                                        <div class="p-2 bg-lighter rounded-3">
                                                                            @if($hasMultiple)
                                                                                <ul class="list-unstyled mb-0 ps-1">
                                                                                    @foreach($notesList as $note)
                                                                                        <li class="d-flex align-items-start mb-1 text-muted small">
                                                                                            <i class="ti tabler-arrow-right-s-filled text-primary me-1 mt-1"></i>
                                                                                            <span class="text-wrap" style="white-space: normal;">{{ $note }}</span>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @else
                                                                                <p class="mb-0 text-muted small text-wrap" style="white-space: normal;">{{ $history->notes }}</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-dark fw-medium text-wrap" style="white-space: normal;">{{ $history->notes }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="d-flex align-items-center border-top pt-2">
                                                            <div class="avatar avatar-xs me-2">
                                                                <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle shadow-sm" style="object-fit:cover;" />
                                                            </div>
                                                            <small class="text-muted">Oleh: <span class="fw-bold text-dark">{{ $uName }}</span></small>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-event-time fw-bold text-muted" style="font-size:0.75rem;">
                                                        {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <div class="text-center py-5">
                                                <i class="ti tabler-history text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">Belum ada riwayat tercatat untuk perjanjian ini.</p>
                                            </div>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: DOKUMEN PKS --}}
                    <div class="tab-pane fade {{ $agreement->status === 'expired' ? 'show active' : '' }}" id="tab-pdf" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div
                                class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="card-title fw-bold mb-0">Preview Dokumen Perjanjian</h6>
                                <div class="d-flex gap-2">
                                    @if($agreement->signed_document_path)
                                    <a href="{{ Storage::url($agreement->signed_document_path) }}" target="_blank"
                                        class="btn btn-sm btn-success rounded-pill"><i
                                            class="ti tabler-file-check me-1"></i> Scan Tertanda Tangan</a>
                                    @endif
                                    <a href="{{ route('masterdata.agreements.pdf-history', $agreement->id) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill"><i
                                            class="ti tabler-file-dots me-1"></i> Versi Sebelumnya</a>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                @if($agreement->status === 'expired')
                                    <div class="d-flex flex-column gap-3">
                                        @forelse ($pdfHistories as $history)
                                            @php
                                                $uName = $history->generator->name ?? 'Sistem Server';
                                                $uAvatar = ($history->generator && $history->generator->img)
                                                    ? asset('storage/' . $history->generator->img)
                                                    : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=32&rounded=true&bold=true";
                                                    
                                                $notesList = array_filter(array_map('trim', explode(';', $history->notes)));
                                                $hasMultiple = count($notesList) > 1;
                                                $collapseId = 'collapseNote_' . $history->id;
                                            @endphp
                                            
                                            <div class="border rounded-4 p-3 bg-white shadow-sm position-relative overflow-hidden" style="transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                                    
                                                    {{-- Kiri: Ikon & Info Tanggal --}}
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar avatar-md rounded-circle bg-label-danger d-flex align-items-center justify-content-center flex-shrink-0">
                                                            <i class="ti tabler-file-type-doc fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1 fw-bold text-dark">Arsip PKS - {{ $history->created_at->translatedFormat('d M Y') }}</h6>
                                                            <div class="d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                                                <i class="ti tabler-clock me-1"></i> {{ $history->created_at->format('H:i') }} WIB
                                                                <span class="mx-2">•</span>
                                                                <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle me-1" width="16" height="16" style="object-fit: cover;">
                                                                Dibuat oleh {{ Str::limit($uName, 15) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Kanan: Aksi --}}
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <a href="{{ asset('storage/' . $history->file_path) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-info rounded-pill fw-medium">
                                                            <i class="ti tabler-eye me-1"></i> Lihat
                                                        </a>
                                                        <a href="{{ asset('storage/' . $history->file_path) }}" download
                                                            class="btn btn-sm btn-primary rounded-pill fw-medium shadow-sm">
                                                            <i class="ti tabler-cloud-download me-1"></i> Unduh
                                                        </a>
                                                    </div>
                                                </div>

                                                {{-- Bawah: Catatan Perubahan (Premium Box) --}}
                                                <div class="mt-3 pt-3 border-top">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <i class="ti tabler-message-3 text-primary mt-1"></i>
                                                        <div class="w-100">
                                                            @if($hasMultiple)
                                                                <div class="d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                                                    <span class="text-dark fw-medium" style="font-size: 0.9rem;">Terdapat {{ count($notesList) }} catatan perubahan. <span class="text-primary">Lihat rincian</span></span>
                                                                    <i class="ti tabler-arrow-down-s text-muted"></i>
                                                                </div>
                                                                <div class="collapse mt-2" id="{{ $collapseId }}">
                                                                    <div class="bg-lighter p-3 rounded-3">
                                                                        <ul class="list-unstyled mb-0">
                                                                            @foreach($notesList as $note)
                                                                                <li class="d-flex align-items-start mb-2 text-muted" style="font-size: 0.85rem;">
                                                                                    <i class="ti tabler-check text-success me-2 fs-6"></i>
                                                                                    <span style="line-height: 1.4;">{{ $note }}</span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.4;">{{ $history->notes }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5 border rounded-4 bg-lighter" style="border-style: dashed !important; border-width: 2px !important;">
                                                <div class="avatar avatar-xl mx-auto mb-3 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti tabler-folder-open text-muted fs-2"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">Arsip Kosong</h6>
                                                <p class="mb-0 text-muted">Belum ada dokumen PDF lama yang tersimpan.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                @else
                                    <div class="pdf-viewer-wrapper shadow-sm">
                                        <iframe src="{{ route('masterdata.agreements.pdf', $agreement->id) }}#toolbar=0"
                                            frameborder="0"></iframe>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}" defer></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Perfect Scrollbar untuk Timeline
            const timelineEl = document.getElementById('historyTimeline');
            if (timelineEl) {
                new PerfectScrollbar(timelineEl, {
                    wheelPropagation: false,
                    suppressScrollX: true
                });
            }

            // 2. Inisialisasi Grafik ApexCharts (Gaya Line/Area Premium)
            @if (count($chartData) > 0)
                const chartLabels = {!! json_encode($chartLabels) !!};
                const chartData = {!! json_encode($chartData) !!};

                const options = {
                    series: [{
                        name: 'Total Setoran',
                        data: chartData
                    }],
                    chart: {
                        type: 'area', // ✅ Diubah jadi Area (Line dengan gradien bawah)
                        height: 350,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit',
                        parentHeightOffset: 0,
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#696cff'], // Warna Primary
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth', // ✅ Garis dibuat melengkung halus (bukan kaku)
                        width: 3
                    },
                    markers: {
                        size: 5,
                        colors: ['#fff'],
                        strokeColors: '#696cff',
                        strokeWidth: 3,
                        hover: {
                            size: 7
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: chartLabels,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#a1acb8',
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#a1acb8',
                                fontSize: '13px'
                            },
                            formatter: function(val) {
                                return "Rp " + (val / 1000000).toFixed(1) + " Jt"; // Disingkat jadi Juta
                            }
                        }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val) {
                                return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    grid: {
                        borderColor: '#f0f2f8',
                        strokeDashArray: 4,
                        padding: {
                            top: -20,
                            bottom: -8,
                            left: 20,
                            right: 20
                        },
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#depositChart"), options);
                chart.render();
            @endif

            // 3. Inisialisasi Peta Lokasi (Leaflet)
            const mapLocations = {!! json_encode($mapLocations ?? []) !!};
            let mapInitialized = false;
            let map;

            const tabLocationsBtn = document.querySelector('button[data-bs-target="#tab-locations"]');
            if (tabLocationsBtn) {
                tabLocationsBtn.addEventListener('shown.bs.tab', function () {
                    if (!mapInitialized) {
                        initMap();
                        mapInitialized = true;
                    } else {
                        if (map) {
                            map.invalidateSize();
                        }
                    }
                });
            }

            function initMap() {
                // Default center (Pekanbaru)
                let centerLat = 0.5071; 
                let centerLng = 101.4478;
                
                if (mapLocations.length > 0) {
                    centerLat = mapLocations[0].lat;
                    centerLng = mapLocations[0].lng;
                }

                map = L.map('agreementLocationsMap', {
                    zoomControl: false 
                }).setView([centerLat, centerLng], 14);

                L.control.zoom({ position: 'bottomright' }).addTo(map);

                // Premium Base Map (CartoDB Positron)
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 20
                }).addTo(map);

                const bounds = [];

                const defaultIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="marker-pin"><i class="ti tabler-map-pin-filled"></i></div>`,
                    iconSize: [36, 36],
                    iconAnchor: [0, 0],
                    popupAnchor: [0, -36]
                });

                mapLocations.forEach(loc => {
                    if (loc.lat && loc.lng) {
                        bounds.push([loc.lat, loc.lng]);
                        
                        const popupContent = `
                            <div class="p-3">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="avatar avatar-sm me-3 bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti tabler-map-pin text-primary"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px; line-height: 1.2;">${loc.name}</h6>
                                </div>
                                <div class="mb-2 text-muted" style="font-size: 13px;">
                                    <i class="ti tabler-route me-2"></i> ${loc.road_section}
                                </div>
                                <div class="fw-bold text-primary mb-3" style="font-size: 14px;">
                                    <i class="ti tabler-wallet me-2"></i> Rp ${loc.deposit} <span class="text-muted fw-normal" style="font-size: 12px;">/ hari</span>
                                </div>
                            </div>
                        `;

                        L.marker([loc.lat, loc.lng], {icon: defaultIcon})
                            .addTo(map)
                            .bindPopup(popupContent, {
                                className: 'custom-popup',
                                minWidth: 260
                            });
                    }
                });

                if (bounds.length > 1) {
                    map.fitBounds(bounds, {padding: [50, 50]});
                } else if (bounds.length === 1) {
                    map.setView(bounds[0], 16);
                }
            }
        });
    </script>
@endsection
