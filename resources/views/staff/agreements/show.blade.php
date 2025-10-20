@extends('layouts.app')

@section('title', 'Detail Perjanjian: ' . $agreement->agreement_number)

@section('skeleton')
    @include('layouts.partials._skeleton-agreement-show')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if ($agreement->fieldCoordinator->user->img)
                                    {{-- ✅ FIX: Gambar dibuat bulat (rounded-circle) dan aspect ratio dijaga --}}
                                    <img class="img-fluid rounded-circle mb-4"
                                        src="{{ asset('storage/' . $agreement->fieldCoordinator->user->img) }}"
                                        style="width: 120px; height: 120px; object-fit: cover;" alt="Korlap Avatar" />
                                @else
                                    {{-- ✅ FIX: Avatar juga dibuat bulat (rounded-circle) --}}
                                    <div class="avatar avatar-xl mb-4">
                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                            {{ strtoupper(substr($agreement->fieldCoordinator->user->name ?? 'K', 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="user-info text-center">
                                    <h5 class="mb-2">{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}</h5>
                                    @php $zone = $agreement->activeParkingLocations->first()->roadSection->zone ?? null; @endphp
                                    <span class="badge bg-label-warning rounded-pill">Koordinator Lapangan @if ($zone)
                                            {{ $zone }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ FIX: Statistik dibuat sejajar menggunakan grid system --}}
                        <div class="row text-center my-6">
                            <div class="col-6 border-end">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="avatar mb-2">
                                        <div class="avatar-initial bg-label-primary rounded-3">
                                            <i class="icon-base ri ri-map-pin-2-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-0">{{ $agreement->activeParkingLocations->count() }}</h5>
                                    <span>Titik Lokasi</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="avatar mb-2">
                                        <div class="avatar-initial bg-label-success rounded-3">
                                            <i class="icon-base ri ri-wallet-3-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-0">Rp {{ number_format($totalDepositThisYear, 0, ',', '.') }}</h5>
                                    <span>Setoran Thn Ini</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="pb-4 border-bottom mb-4">Detail Perjanjian</h5>
                        <div class="info-container">
                            {{-- ✅ FIX: Detail dirapikan dengan struktur tabel deskripsi --}}
                            <dl class="row mb-6">
                                <dt class="col-sm-5 fw-medium text-heading">No. PKS</dt>
                                <dd class="col-sm-7">{{ $agreement->agreement_number }}</dd>

                                <dt class="col-sm-5 fw-medium text-heading">Status</dt>
                                <dd class="col-sm-7">
                                    @php
                                        $statusClass = 'secondary';
                                        if ($agreement->status == 'active') {
                                            $statusClass = 'success';
                                        }
                                        if (in_array($agreement->status, ['expired', 'terminated'])) {
                                            $statusClass = 'danger';
                                        }
                                        if ($agreement->status == 'pending_renewal') {
                                            $statusClass = 'warning';
                                        }
                                    @endphp
                                    <span class="badge rounded-pill bg-label-{{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $agreement->status)) }}
                                    </span>
                                </dd>

                                <dt class="col-sm-5 fw-medium text-heading">Pimpinan</dt>
                                <dd class="col-sm-7">{{ $agreement->leader->user->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-5 fw-medium text-heading">Masa Berlaku</dt>
                                <dd class="col-sm-7">{{ $agreement->start_date->translatedFormat('d M y') }} -
                                    {{ $agreement->end_date->translatedFormat('d M y') }}</dd>
                            </dl>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('masterdata.agreements.edit', $agreement->id) }}" class="btn btn-primary"
                                    data-bs-toggle="tooltip" title="Edit Perjanjian">
                                    <i class="icon-base ri ri-pencil-line"></i><span
                                        class="d-none d-sm-inline ms-1">Edit</span>
                                </a>
                                <a href="{{ route('masterdata.agreements.pdf', $agreement->id) }}" target="_blank"
                                    class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Cetak PKS">
                                    <i class="icon-base ri ri-printer-line"></i>
                                </a>
                                <a href="{{ route('masterdata.agreements.pdf-history', $agreement->id) }}"
                                    class="btn btn-info" data-bs-toggle="tooltip" title="Histori Perjanjian">
                                    <i class="icon-base ri ri-file-copy-2-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="nav-align-top">
                    {{-- ✅ Navigasi Tab --}}
                    <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
                        <li class="nav-item">
                            {{-- ✅ FIX 1: Menambahkan total count di judul tab --}}
                            <a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab"
                                data-bs-target="#locations">
                                <i class="icon-base ri ri-map-pin-line icon-sm me-2"></i>Lokasi Parkir
                                <span
                                    class="badge rounded-pill bg-primary ms-2">{{ $agreement->activeParkingLocations->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                                data-bs-target="#deposits"><i
                                    class="icon-base ri ri-money-dollar-box-line icon-sm me-2"></i>Riwayat Setoran</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                                data-bs-target="#pdf-preview"><i
                                    class="icon-base ri ri-file-pdf-line icon-sm me-2"></i>Preview
                                PKS</a></li>
                    </ul>
                    {{-- ✅ Konten Tab --}}
                    <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="locations" role="tabpanel">
                            <div class="accordion" id="accordionParkingLocations">
                                @forelse ($locationsByRoadSection as $roadSectionName => $locations)
                                    @php
                                        // Membuat ID yang aman untuk accordion dari nama jalan
                                        $accordionId = 'collapse-' . Str::slug($roadSectionName);
                                    @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}">
                                                <div class="d-flex w-100 align-items-center">
                                                    <span
                                                        class="flex-grow-1">{{ $roadSectionName ?? 'Tanpa Ruas Jalan' }}</span>
                                                    <span
                                                        class="badge bg-secondary rounded-pill me-2">{{ count($locations) }}
                                                        Lokasi</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="{{ $accordionId }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading-{{ $accordionId }}"
                                            data-bs-parent="#accordionParkingLocations">
                                            <div class="accordion-body p-0">
                                                <div class="table-responsive text-nowrap">
                                                    <table class="table table-sm table-hover">
                                                        <tbody>
                                                            @foreach ($locations as $location)
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{ route('masterdata.parking-locations.show', $location->id) }}"
                                                                            class="d-block text-body">
                                                                            <i
                                                                                class="icon-base ri ri-map-pin-line text-primary me-2"></i>
                                                                            {{ $location->name }}
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="card">
                                        <div class="card-body text-center text-muted py-5">
                                            <i class="ri-map-pin-line ri-3x text-secondary mb-3"></i>
                                            <p class="mb-0">Tidak ada lokasi parkir aktif yang terikat pada perjanjian
                                                ini.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane fade" id="deposits" role="tabpanel">
                            <div class="card">
                                <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <tbody>
                                            @forelse ($agreement->depositTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->deposit_date->translatedFormat('d F Y') }}</td>
                                                    <td class="fw-medium">Rp
                                                        {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if ($transaction->is_validated)
                                                            <span class="badge bg-label-success">Tervalidasi</span>
                                                        @else
                                                            <span class="badge bg-label-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-4">Belum ada
                                                        riwayat
                                                        setoran.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-center">
                                    <small class="text-muted">Total Setoran Tervalidasi ({{ now()->year }}):</small>
                                    <h6 class="mb-0 fw-bold">Rp {{ number_format($totalDepositThisYear, 0, ',', '.') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pdf-preview" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <iframe src="{{ route('masterdata.agreements.pdf', $agreement->id) }}" width="100%"
                                        height="800px" style="border:none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 order-2 mt-6">
                    <div class="card">
                        <h5 class="card-header pb-4 border-bottom mb-12">Timeline Riwayat Perjanjian</h5>
                        <div class="card-body">
                            <ul class="timeline timeline-center">
                                {{-- Urutkan data dari yang terbaru ke terlama --}}
                                @forelse ($agreement->histories->sortByDesc('created_at') as $history)
                                    @php
                                        // Menentukan posisi event (kiri/kanan) secara bergantian
                                        $positionClass = $loop->odd ? 'timeline-item-left' : 'timeline-item-right';

                                        // Menentukan ikon dan warna berdasarkan tipe event
                                        $icon = 'ri-file-text-line';
                                        $color = 'secondary';
                                        switch ($history->event_type) {
                                            case 'agreement_created':
                                                $icon = 'ri-file-add-line';
                                                $color = 'primary';
                                                break;
                                            case 'location_added':
                                                $icon = 'ri-map-pin-add-line';
                                                $color = 'success';
                                                break;
                                            case 'location_removed':
                                                $icon = 'ri-map-pin-5-line';
                                                $color = 'danger';
                                                break;
                                            case 'deposit_changed':
                                                $icon = 'ri-money-dollar-circle-line';
                                                $color = 'info';
                                                break;
                                            case 'status_changed':
                                            case 'agreement_renewed':
                                                $icon = 'ri-refresh-line';
                                                $color = 'success';
                                                break;
                                            case 'agreement_terminated':
                                                $icon = 'ri-shield-x-line';
                                                $color = 'danger';
                                                break;
                                        }
                                    @endphp
                                    <li class="timeline-item {{ $positionClass }}">
                                        <span class="timeline-indicator timeline-indicator-{{ $color }}">
                                            <i class="icon-base ri {{ $icon }}"></i>
                                        </span>
                                        <div class="timeline-event card p-0">
                                            <div
                                                class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="card-title mb-0">{{ $history->notes }}</h6>
                                                <div class="meta"><small
                                                        class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2">
                                                        @if ($history->changer && $history->changer->img)
                                                            <img src="{{ asset('storage/' . $history->changer->img) }}"
                                                                alt="Avatar" class="rounded-circle" />
                                                        @else
                                                            <span
                                                                class="avatar-initial rounded-circle bg-label-secondary">{{ strtoupper(substr($history->changer->name ?? 'S', 0, 1)) }}</span>
                                                        @endif
                                                    </div>
                                                    <span>Oleh: <span
                                                            class="fw-medium">{{ $history->changer->name ?? 'Sistem' }}</span></span>
                                                </div>
                                            </div>
                                            <div class="timeline-event-time">
                                                {{ $history->created_at->translatedFormat('d M y, H:i') }}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-indicator timeline-indicator-secondary"><i
                                                class="icon-base ri-information-line"></i></span>
                                        <div class="timeline-event">
                                            <p class="text-center text-muted">Belum ada riwayat tercatat untuk perjanjian
                                                ini.</p>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
