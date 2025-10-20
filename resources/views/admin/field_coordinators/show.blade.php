@extends('layouts.app')

@section('title', 'Detail Koordinator: ' . $fieldCoordinator->user->name)

@section('skeleton')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('layouts.partials._skeleton-field-coordinator-show')
    </div>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if ($fieldCoordinator->user && $fieldCoordinator->user->img)
                                    <img class="img-fluid rounded-3 mb-4"
                                        src="{{ asset('storage/' . $fieldCoordinator->user->img) }}" height="120"
                                        width="120" alt="User avatar" />
                                @else
                                    <div class="avatar avatar-xl mb-4">
                                        <span
                                            class="avatar-initial rounded-3 bg-label-warning">{{ strtoupper(substr($fieldCoordinator->user->name ?? 'K', 0, 2)) }}</span>
                                    </div>
                                @endif
                                <div class="user-info text-center">
                                    <h5 class="mb-2">{{ $fieldCoordinator->user->name }}</h5>
                                    <span class="badge bg-label-warning rounded-pill">Koordinator Lapangan</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                            <div class="d-flex align-items-center me-5 gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded-3">
                                        <i class="icon-base ri ri-file-text-line ri-24px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $activeAgreementsCount }}</h5>
                                    <span>PKS Aktif</span>
                                </div>
                            </div>
                            {{-- ✅ TAMBAHAN: Jumlah Titik Lokasi --}}
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded-3">
                                        <i class="icon-base ri ri-map-pin-2-line ri-24px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $totalParkingLocationsCount }}</h5>
                                    <span>Titik Lokasi</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4">Details</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2"><span
                                        class="fw-medium text-heading me-2">Username:</span><span>{{ $fieldCoordinator->user->username }}</span>
                                </li>
                                <li class="mb-2"><span
                                        class="fw-medium text-heading me-2">Email:</span><span>{{ $fieldCoordinator->user->email }}</span>
                                </li>
                                <li class="mb-2"><span
                                        class="fw-medium text-heading me-2">Kontak:</span><span>{{ $fieldCoordinator->phone_number }}</span>
                                </li>
                                <li class="mb-2"><span class="fw-medium text-heading me-2">No.
                                        KTP:</span><span>{{ $fieldCoordinator->id_card_number }}</span></li>
                                <li class="mb-2"><span
                                        class="fw-medium text-heading me-2">Alamat:</span><span>{{ $fieldCoordinator->address }}</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('admin.field-coordinators.edit', $fieldCoordinator->id) }}"
                                    class="btn btn-primary me-4">Edit Profil</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ✅ TAMBAHAN: Kartu Foto KTP --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="pb-4 border-bottom mb-4">Foto KTP</h5>
                        @if ($fieldCoordinator->id_card_img)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ktpModal">
                                <img src="{{ asset('storage/' . $fieldCoordinator->id_card_img) }}" alt="Foto KTP"
                                    class="img-fluid rounded-3">
                            </a>
                        @else
                            <p class="text-muted text-center">Foto KTP tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Lokasi Parkir Aktif</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Lokasi</th>
                                    <th>Ruas Jalan</th>
                                    <th>No. PKS Terkait</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeParkingLocations as $location)
                                    <tr>
                                        <td><span class="fw-medium">{{ $location->name }}</span></td>
                                        <td>{{ $location->roadSection->name ?? 'N/A' }}</td>
                                        {{-- Cari PKS aktif mana yang memiliki lokasi ini --}}
                                        @php
                                            $relatedPKS = $fieldCoordinator->agreements->first(function (
                                                $agreement,
                                            ) use ($location) {
                                                return $agreement->status == 'active' &&
                                                    $agreement->activeParkingLocations->contains('id', $location->id);
                                            });
                                        @endphp
                                        <td><span
                                                class="badge bg-label-info">{{ $relatedPKS->agreement_number ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada lokasi parkir aktif yang dikelola.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Setoran Tervalidasi</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. PKS</th>
                                    <th>Tanggal Setor</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allTransactions as $transaction)
                                    <tr>
                                        <td><span class="fw-medium">{{ $transaction->agreement->agreement_number }}</span>
                                        </td>
                                        <td>{{ $transaction->deposit_date->translatedFormat('d M Y') }}</td>
                                        <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada riwayat setoran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ktpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $fieldCoordinator->id_card_img ? asset('storage/' . $fieldCoordinator->id_card_img) : '' }}"
                            class="img-fluid" alt="Foto KTP">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
