@extends('layouts.app')

@section('title', 'Manajemen Lokasi Parkir')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Lokasi Parkir</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Lokasi Parkir</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Lokasi Parkir --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Lokasi Parkir</h5>
                <p class="text-muted mb-0">Total {{ $parkingLocations->total() }} lokasi terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <form action="{{ route('masterdata.parking-locations.index') }}" method="GET" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama lokasi..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="ri icon-base ri-search-line"></i></button>
                    </div>
                </form>
                <a href="{{ route('masterdata.parking-locations.importCreate') }}" class="btn btn-secondary">
                    <i class="ri icon-base ri-upload-cloud-line me-1"></i> Impor Data
                </a>
                <a href="{{ route('masterdata.parking-locations.create') }}" class="btn btn-primary">
                    <i class="ri icon-base ri-add-line me-1"></i> Tambah
                </a>
            </div>
        </div>

        <div class="card-body pt-3">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                    <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="30%">Nama Lokasi</th>
                            <th width="20%">Ruas Jalan</th>
                            <th width="10%">Zona</th>
                            <th width="15%">Status</th>
                            <th width="15%">Info Perjanjian</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($parkingLocations as $location)
                            <tr>
                                <td><span class="fw-medium text-dark">{{ $location->name }}</span></td>
                                <td>{{ $location->roadSection->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-label-dark">{{ $location->roadSection->zone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = $location->status == 'tersedia' ? 'bg-label-success' : 'bg-label-secondary';
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} fw-bold">
                                        {{ strtoupper(str_replace('_', ' ', $location->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($location->status == 'tidak_tersedia' && $location->agreements->isNotEmpty())
                                        @php
                                            $activeAgreement = $location->agreements->first();
                                            $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';

                                            // Cek apakah punya foto profil, jika tidak gunakan UI Avatar
                                            $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                                ? asset('storage/'.$activeAgreement->fieldCoordinator->user->img)
                                                : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=24&rounded=true&bold=true";
                                        @endphp
                                        <div>
                                            <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}" class="fw-medium d-block text-primary">
                                                {{ $activeAgreement->agreement_number }}
                                            </a>
                                            <small class="text-muted d-flex align-items-center mt-1">
                                                <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle me-2 shadow-sm" width="20" height="20" style="object-fit: cover;">
                                                {{ Str::limit($cName, 15) }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        {{-- ✅ Tombol Detail Selalu Muncul --}}
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill"
                                            href="{{ route('masterdata.parking-locations.show', $location->id) }}"
                                            data-bs-toggle="tooltip" title="Detail Lokasi">
                                            <i class="ri icon-base ri-eye-line ri-22px"></i>
                                        </a>

                                       {{-- ✅ Tombol Edit: Disabled jika Tidak Tersedia --}}
                                        @if ($location->status == 'tidak_tersedia')
                                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" disabled
                                                data-bs-toggle="tooltip" title="Tidak dapat diedit, sedang terikat PKS!">
                                                <i class="ri icon-base ri-pencil-line ri-22px opacity-50"></i>
                                            </button>
                                        @else
                                            <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                                href="{{ route('masterdata.parking-locations.edit', $location->id) }}"
                                                data-bs-toggle="tooltip" title="Edit Lokasi">
                                                <i class="ri icon-base ri-pencil-line ri-22px"></i>
                                            </a>
                                        @endif

                                        {{-- ✅ Tombol Delete: Disabled jika Tidak Tersedia --}}
                                        @if ($location->status == 'tidak_tersedia')
                                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" disabled
                                                data-bs-toggle="tooltip" title="Tidak dapat dihapus, sedang terikat PKS!">
                                                <i class="ri icon-base ri-delete-bin-7-line ri-22px opacity-50"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('masterdata.parking-locations.destroy', $location->id) }}" method="POST" class="form-delete d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus Lokasi">
                                                    <i class="ri icon-base ri-delete-bin-7-line ri-22px"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}" width="120" class="mb-3 opacity-50" alt="No Data">
                                    <p class="text-muted">Tidak ada data lokasi parkir ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{ $parkingLocations->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Aktifkan Tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert HANYA untuk konfirmasi Delete
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data lokasi parkir beserta file dokumen akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-outline-secondary waves-effect'
                        },
                        buttonsStyling: false,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });

            // ✅ POP-UP PREMIUM JIKA ADA YANG ISENG MENGAKSES URL EDIT LOKASI TERIKAT
            @if (session('locked_error'))
                // Kita amankan string PHP ke dalam variabel Javascript dulu
                let errorMessage = {!! json_encode(session('locked_error')) !!};

                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak!',
                    // Gabungkan variabel Javascript ke dalam HTML
                    html: '<p class="text-muted fs-6 mt-2">' + errorMessage + '</p>',
                    showConfirmButton: true,
                    confirmButtonText: '<i class="ri-check-line me-1"></i> Mengerti',
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect waves-light rounded-pill px-4'
                    },
                    buttonsStyling: false,
                    backdrop: `rgba(0,0,0,0.4)`
                });
            @endif
        });
    </script>
@endpush
