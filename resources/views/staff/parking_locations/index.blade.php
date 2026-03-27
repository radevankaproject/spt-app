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
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Lokasi Parkir</h5>
                <p class="text-muted mb-0">Total {{ $parkingLocations->total() }} lokasi terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-4">
                <form action="{{ route('masterdata.parking-locations.index') }}" method="GET"
                    class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama lokasi..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="icon-base ri ri-search-line"></i></button>
                    </div>
                </form>
                <a href="{{ route('masterdata.parking-locations.importCreate') }}" class="btn btn-secondary me-2">
                    <i class="icon-base ri ri-upload-cloud-line me-2"></i> Impor Data
                </a>
                <a href="{{ route('masterdata.parking-locations.create') }}" class="btn btn-primary">
                    <i class="icon-base ri ri-add-line me-2"></i>Tambah Lokasi
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Lokasi</th>
                            <th>Ruas Jalan</th>
                            <th>Zona</th>
                            <th>Status</th>
                            <th>Info Perjanjian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($parkingLocations as $location)
                            <tr>
                                <td><span class="fw-medium">{{ $location->name }}</span></td>
                                <td>{{ $location->roadSection->name ?? 'N/A' }}</td>
                                {{-- ✅ Kolom Zona Ditambahkan --}}
                                <td>
                                    <span
                                        class="badge rounded-pill bg-label-dark">{{ $location->roadSection->zone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass =
                                            $location->status == 'tersedia' ? 'bg-label-success' : 'bg-label-danger';
                                    @endphp
                                    <span
                                        class="badge rounded-pill {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $location->status)) }}</span>
                                </td>
                                <td>
                                    {{-- ✅ Info PKS jika status 'tidak tersedia' --}}
                                    @if ($location->status == 'tidak_tersedia' && $location->agreements->isNotEmpty())
                                        @php $activeAgreement = $location->agreements->first(); @endphp
                                        <div>
                                            <span class="fw-medium d-block">{{ $activeAgreement->agreement_number }}</span>
                                            <small
                                                class="text-muted">{{ $activeAgreement->fieldCoordinator->user->name ?? 'N/A' }}</small>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        {{-- ✅ Tombol Hapus hanya muncul jika status 'tersedia' --}}
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.parking-locations.show', $location->id) }}"
                                            data-bs-toggle="tooltip" title="Details Lokasi">
                                            <i class="icon-base ri ri-eye-line icon-22px"></i>
                                        </a>
                                        @if ($location->status == 'tersedia')
                                            <a class="btn btn-sm btn-icon"
                                                href="{{ route('masterdata.parking-locations.edit', $location->id) }}"
                                                data-bs-toggle="tooltip" title="Edit Lokasi">
                                                <i class="icon-base ri ri-pencil-line icon-22px"></i>
                                            </a>
                                            <form
                                                action="{{ route('masterdata.parking-locations.destroy', $location->id) }}"
                                                method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                    title="Hapus Lokasi">
                                                    <i class="icon-base ri ri-delete-bin-line icon-22px"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data lokasi parkir ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $parkingLocations->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data lokasi parkir yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6f6b7d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });
        });
    </script>
@endpush
