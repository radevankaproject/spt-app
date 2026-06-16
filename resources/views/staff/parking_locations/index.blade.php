@extends('layouts.app')

@section('title', 'Manajemen Lokasi Parkir')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .filter-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-item {
            flex: 1 1 auto;
        }
        @media (max-width: 768px) {
            .filter-container > * { flex: 1 1 100%; }
        }
    </style>
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
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3 bg-transparent">
            <div class="card-title mb-0">
                <h5 class="mb-1 fw-bold">Daftar Semua Lokasi Parkir</h5>
                <p class="text-muted mb-0">Total {{ $parkingLocations->total() }} lokasi terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap w-100 w-md-auto">
                
                {{-- ✅ FORM FILTER PINTAR TERPADU --}}
                <form action="{{ route('masterdata.parking-locations.index') }}" method="GET" class="filter-container w-100 w-lg-auto">
                    
                    {{-- 1. Dropdown Ruas Jalan --}}
                    <div class="filter-item" style="min-width: 220px;">
                        <select name="road_section_id" class="form-select select2 shadow-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Ruas Jalan --</option>
                            @foreach($roadSections as $rs)
                                <option value="{{ $rs->id }}" {{ request('road_section_id') == $rs->id ? 'selected' : '' }}>
                                    {{ $rs->name }} ({{ $rs->zone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Dropdown Status --}}
                    <div class="filter-item" style="min-width: 160px;">
                        <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Status --</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>🟢 Tersedia</option>
                            <option value="tidak_tersedia" {{ request('status') == 'tidak_tersedia' ? 'selected' : '' }}>🔴 Tidak Tersedia</option>
                        </select>
                    </div>

                    {{-- 3. Search Input --}}
                    <div class="input-group filter-item shadow-sm" style="min-width: 250px;">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama lokasi..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="ri icon-base ri-search-line"></i></button>
                    </div>
                    
                    {{-- 4. Tombol Reset (Muncul jika ada filter aktif) --}}
                    @if(request('search') || request('road_section_id') || request('status'))
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-danger filter-item shadow-sm" data-bs-toggle="tooltip" title="Reset Semua Filter">
                            <i class="ri icon-base ri-refresh-line icon-22px"></i> Reset Seluruh Filter
                        </a>
                    @endif
                </form>

                <div class="d-flex gap-2 ms-md-3 mt-3 mt-md-0">
                    @if(Auth::user()->role !== 'leader')
                    
                    {{-- Tombol Bulk Delete (Sembunyi by default) --}}
                    <form action="{{ route('masterdata.parking-locations.bulkDeleteUnused') }}" method="POST" id="form-bulk-delete" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="selected_ids" id="selected_ids" value="">
                        <button type="button" class="btn btn-danger shadow-sm d-none" id="btn-bulk-delete" onclick="confirmBulkDelete()">
                            <i class="ri icon-base ri-delete-bin-7-line me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                        </button>
                    </form>

                    <a href="{{ route('masterdata.parking-locations.importCreate') }}" class="btn btn-secondary shadow-sm">
                        <i class="ri icon-base ri-upload-cloud-line me-1"></i> Impor
                    </a>
                    <a href="{{ route('masterdata.parking-locations.create') }}" class="btn btn-primary shadow-sm">
                        <i class="ri icon-base ri-add-line me-1"></i> Tambah
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body pt-3 p-0">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fw-bold m-3" role="alert">
                    <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">
                                <input class="form-check-input" type="checkbox" id="check-all">
                            </th>
                            <th width="20%">Nama Lokasi</th>
                            <th width="20%">Ruas Jalan</th>
                            <th width="10%">Zona</th>
                            <th width="15%">Status</th>
                            <th width="20%">Info Perjanjian</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($parkingLocations as $location)
                            <tr>
                                <td class="text-center">
                                    @if ($location->status == 'tersedia')
                                        <input class="form-check-input row-checkbox" type="checkbox" value="{{ $location->id }}">
                                    @else
                                        <input class="form-check-input" type="checkbox" disabled data-bs-toggle="tooltip" title="Terikat PKS">
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ Str::limit($location->name, 30) }}</span>
                                    <small class="text-muted d-block mb-1">Rp {{ number_format($location->daily_deposit, 0, ',', '.') }} / hari</small>
                                    @if($location->latitude && $location->longitude)
                                        <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" target="_blank" class="badge bg-label-info text-decoration-none" data-bs-toggle="tooltip" title="Lihat di Peta">
                                            <i class="ri-map-pin-fill me-1"></i> Buka Peta
                                        </a>
                                    @else
                                        <span class="badge bg-label-warning text-dark">
                                            <i class="ri-map-pin-add-line me-1"></i> Koordinat belum diatur
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $location->roadSection->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-label-dark">{{ $location->roadSection->zone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = $location->status == 'tersedia' ? 'bg-label-success' : 'bg-label-secondary';
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} fw-bold mb-1 d-inline-block">
                                        {{ strtoupper(str_replace('_', ' ', $location->status)) }}
                                    </span>
                                    @if(!$location->is_active)
                                        <span class="badge rounded-pill bg-label-danger fw-bold d-inline-block mt-1" data-bs-toggle="tooltip" title="{{ $location->keterangan }}">
                                            <i class="ri-close-circle-line me-1"></i>TUTUP/NONAKTIF
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($location->status == 'tidak_tersedia' && $location->agreements->isNotEmpty())
                                        @php
                                            $activeAgreement = $location->agreements->first();
                                            $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';
                                            $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                                ? asset('storage/'.$activeAgreement->fieldCoordinator->user->img)
                                                : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=24&rounded=true&bold=true";
                                        @endphp
                                        <div>
                                            <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}" class="fw-bold d-block text-primary">
                                                {{ $activeAgreement->agreement_number }}
                                            </a>
                                            <small class="text-muted d-flex align-items-center mt-1">
                                                <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle me-2 shadow-sm" width="20" height="20" style="object-fit: cover;">
                                                {{ Str::limit($cName, 15) }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">- Belum Terikat -</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill"
                                            href="{{ route('masterdata.parking-locations.show', $location->id) }}"
                                            data-bs-toggle="tooltip" title="Detail Lokasi">
                                            <i class="ri icon-base ri-eye-line ri-22px"></i>
                                        </a>

                                        @if(Auth::user()->role !== 'leader')
                                            <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                                href="{{ route('masterdata.parking-locations.edit', $location->id) }}"
                                                data-bs-toggle="tooltip" title="Edit Lokasi">
                                                <i class="ri icon-base ri-pencil-line ri-22px"></i>
                                            </a>
                                        @endif

                                        @if(Auth::user()->role !== 'leader')
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="ri ri-map-pin-time-line icon-32px text-muted opacity-50 mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">Tidak ada data dengan keyword <span class="text-muted text-primary">"{{ request('search') }}"</span> ditemukan</h6>
                                    <p class="text-muted small">Coba ubah filter pencarian, ruas jalan, atau status.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-lighter">
                <small class="text-muted fw-medium">Menampilkan {{ $parkingLocations->firstItem() ?? 0 }} - {{ $parkingLocations->lastItem() ?? 0 }} dari {{ $parkingLocations->total() }} data</small>
                <div>
                    {{-- ✅ Pastikan Pagination Menyimpan Semua Filter --}}
                    {{ $parkingLocations->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Aktifkan Select2
            if (jQuery().select2) {
                $('.select2').select2({
                    placeholder: '-- Semua Ruas Jalan --',
                    allowClear: true
                });
            }

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

            @if (session('locked_error'))
                let errorMessage = {!! json_encode(session('locked_error')) !!};
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak!',
                    html: '<p class="text-muted fs-6 mt-2">' + errorMessage + '</p>',
                    showConfirmButton: true,
                    confirmButtonText: '<i class="ri-check-line me-1"></i> Mengerti',
                    customClass: { confirmButton: 'btn btn-primary waves-effect waves-light rounded-pill px-4' },
                    buttonsStyling: false,
                    backdrop: `rgba(0,0,0,0.4)`
                });
            @endif
        });

        // Logika Checkbox
        const checkAll = document.getElementById('check-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const btnBulkDelete = document.getElementById('btn-bulk-delete');
        const selectedCountEl = document.getElementById('selected-count');
        const selectedIdsInput = document.getElementById('selected_ids');

        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            selectedCountEl.textContent = checkedCount;

            if (checkedCount > 0) {
                btnBulkDelete.classList.remove('d-none');
            } else {
                btnBulkDelete.classList.add('d-none');
                if(checkAll) checkAll.checked = false;
            }
            
            // Perbarui check-all state
            if(checkAll && rowCheckboxes.length > 0) {
                checkAll.checked = checkedCount === rowCheckboxes.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                rowCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBulkDeleteButton();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteButton);
        });

        // Konfirmasi Bulk Delete
        function confirmBulkDelete() {
            // Ambil id yang dicentang
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
            if(selectedIds.length === 0) return;
            
            selectedIdsInput.value = JSON.stringify(selectedIds);

            Swal.fire({
                title: 'Hapus Data Terpilih?',
                text: `Anda akan menghapus ${selectedIds.length} lokasi parkir. Tindakan ini tidak dapat dibatalkan!`,
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
                    document.getElementById('form-bulk-delete').submit();
                }
            })
        }
    </script>
@endpush