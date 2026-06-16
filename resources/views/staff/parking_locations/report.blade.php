@extends('layouts.app')

@section('title', 'Laporan Titik Lokasi Parkir')

@section('skeleton')
    @include('layouts.partials._skeleton-parking-locations-report')
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
        /* Styling tambahan agar lebih premium */
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
        }
        .form-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #566a7f;
            margin-bottom: 0.5rem;
        }
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: #566a7f;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .table td {
            vertical-align: middle;
            padding: 1rem;
        }
        .badge {
            padding: 0.4em 0.8em;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .custom-checkbox .form-check-input {
            width: 1.25em;
            height: 1.25em;
            margin-top: 0.1em;
            cursor: pointer;
        }
        .custom-checkbox .form-check-label {
            cursor: pointer;
            padding-top: 0.15em;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="ri ri-file-list-3-line text-primary me-2 align-middle" style="font-size: 1.5rem;"></i>
            Laporan Titik Lokasi Parkir
        </h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Laporan Titik Parkir</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0">
        <div class="card-header border-bottom bg-transparent pb-3 pt-4">
            <h5 class="mb-0 fw-bold d-flex align-items-center text-secondary">
                <i class="ri ri-filter-3-fill me-2"></i> Filter Data Laporan
            </h5>
        </div>
        <div class="card-body pt-4">
            <form method="GET" action="{{ route('admin.parking-locations.report') }}" class="row g-4">
                
                <!-- Filter Zona -->
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-bold">
                        <i class="ri ri-map-pin-range-line me-1 text-primary"></i> Zona
                    </label>
                    <div class="btn-group w-100 shadow-sm" role="group" aria-label="Pilih Zona">
                        <input type="radio" class="btn-check zone-radio" name="zone" id="zone_all" value="" autocomplete="off" {{ request('zone') == '' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="zone_all">Semua</label>

                        @foreach($zones as $z)
                            <input type="radio" class="btn-check zone-radio" name="zone" id="zone_{{ Str::slug($z) }}" value="{{ $z }}" autocomplete="off" {{ request('zone') == $z ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="zone_{{ Str::slug($z) }}">{{ $z }}</label>
                        @endforeach
                    </div>
                </div>

                <!-- Filter Ruas Jalan -->
                <div class="col-md-8 col-lg-5">
                    <label for="road_section_id" class="form-label fw-bold">
                        <i class="ri ri-road-map-line me-1 text-primary"></i> Ruas Jalan
                    </label>
                    <select name="road_section_id[]" id="road_section_id" class="form-select select2 shadow-none" multiple="multiple" data-placeholder="-- Pilih Zona Terlebih Dahulu --" disabled>
                        <!-- Options dipopulate via JS -->
                    </select>
                </div>

                <!-- Filter Korlap -->
                <div class="col-md-12 col-lg-4">
                    <label for="korlap_id" class="form-label fw-bold">
                        <i class="ri ri-user-star-line me-1 text-primary"></i> Koordinator Lapangan
                    </label>
                    <select name="korlap_id" id="korlap_id" class="form-select select2 shadow-none" data-placeholder="-- Pilih Ruas Jalan Dulu --" disabled>
                        <option value="">-- Semua Korlap --</option>
                        @foreach($korlaps as $korlap)
                            <option value="{{ $korlap->id }}" {{ request('korlap_id') == $korlap->id ? 'selected' : '' }}>{{ $korlap->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Checkbox & Buttons -->
                <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4 pt-2 border-top">
                    <div class="form-check custom-checkbox mb-3 mb-md-0 d-flex align-items-center">
                        <input type="checkbox" id="no_agreement" name="no_agreement" value="1" {{ request('no_agreement') == '1' ? 'checked' : '' }} class="form-check-input rounded border-gray-400 text-primary shadow-sm focus:ring-primary">
                        <label for="no_agreement" class="form-check-label ms-2 fw-semibold text-dark user-select-none">
                            <span class="badge bg-label-warning px-2 py-1"><i class="ri ri-alert-line me-1"></i> Tampilkan Titik Tanpa Koordinator (Belum PKS)</span>
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.parking-locations.report') }}" class="btn btn-outline-secondary px-4">
                            <i class="ri ri-refresh-line me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="ri ri-search-line me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card border-0">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-bottom bg-transparent pb-3 pt-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="ri ri-table-alt-line me-2 text-primary"></i> Hasil Laporan
            </h5>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('admin.parking-locations.report.export-pdf', request()->all()) }}" class="btn btn-danger shadow-sm px-3" data-bs-toggle="tooltip" title="Download PDF">
                    <i class="ri ri-file-pdf-2-line me-2"></i> PDF
                </a>
                <a href="{{ route('admin.parking-locations.report.export-excel', request()->all()) }}" class="btn btn-success shadow-sm px-3" data-bs-toggle="tooltip" title="Download Excel">
                    <i class="ri ri-file-excel-2-line me-2"></i> Excel
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-muted" width="5%">#</th>
                            <th width="20%"><i class="ri ri-map-pin-line align-text-bottom me-1"></i> Titik Lokasi</th>
                            <th width="20%"><i class="ri ri-road-map-line align-text-bottom me-1"></i> Ruas Jalan</th>
                            <th class="text-center" width="10%"><i class="ri ri-map-pin-range-line align-text-bottom me-1"></i> Zona</th>
                            <th width="20%"><i class="ri ri-user-star-line align-text-bottom me-1"></i> Koordinator</th>
                            <th class="text-center" width="10%"><i class="ri ri-toggle-line align-text-bottom me-1"></i> Status</th>
                            <th class="text-end" width="15%"><i class="ri ri-money-dollar-circle-line align-text-bottom me-1"></i> Setoran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($parkingLocations as $index => $location)
                            <tr>
                                <td class="text-center text-muted">{{ $parkingLocations->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $location->name }}</span>
                                </td>
                                <td><span class="text-body">{{ $location->roadSection->name ?? '-' }}</span></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-dark px-3">{{ $location->roadSection->zone ?? '-' }}</span>
                                </td>
                                <td>
                                    @php
                                        $activeAgreement = $location->agreements->first();
                                    @endphp
                                    @if($activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                @php $cName = $activeAgreement->fieldCoordinator->user->name; @endphp
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($cName) }}&background=random&color=fff&rounded=true" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <span class="fw-semibold text-primary">{{ $cName }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic"><i class="ri ri-close-circle-line align-text-bottom me-1"></i>Belum Ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($location->status == 'tersedia')
                                        <span class="badge bg-label-success fw-bold"><i class="ri ri-checkbox-circle-line me-1"></i>Tersedia</span>
                                    @elseif($location->status == 'tidak_tersedia')
                                        <span class="badge bg-label-secondary fw-bold"><i class="ri ri-close-circle-line me-1"></i>Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success font-monospace">
                                    {{ number_format($location->daily_deposit, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <i class="ri ri-file-search-line text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold text-dark mb-1">Tidak ada data titik lokasi parkir</h6>
                                        <p class="text-muted small mb-0">Coba sesuaikan filter untuk menemukan data.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center p-4 border-top bg-lighter">
                <small class="text-muted fw-semibold">Menampilkan {{ $parkingLocations->firstItem() ?? 0 }} - {{ $parkingLocations->lastItem() ?? 0 }} dari {{ $parkingLocations->total() }} data</small>
                <div class="pagination-wrapper">
                    {{ $parkingLocations->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (jQuery().select2) {
                $('.select2').select2({
                    allowClear: true
                });
            }

            const roadSectionsData = @json($roadSections);
            const oldSelectedRoadSections = @json(is_array(request('road_section_id')) ? request('road_section_id') : (request('road_section_id') ? [request('road_section_id')] : []));

            const zoneRadios = document.querySelectorAll('.zone-radio');
            const roadSectionSelect = $('#road_section_id');
            const korlapSelect = $('#korlap_id');
            const noAgreementCb = document.getElementById('no_agreement');

            // Mencegah select2 men-trigger event 'change' berulang saat kita memodifikasi valuenya
            let isUpdating = false;

            function renderRoadSections(selectedZone) {
                isUpdating = true;
                roadSectionSelect.empty();
                
                if (selectedZone) {
                    const filteredRs = roadSectionsData.filter(rs => rs.zone === selectedZone);
                    filteredRs.forEach(rs => {
                        const isSelected = oldSelectedRoadSections.includes(String(rs.id)) || oldSelectedRoadSections.includes(rs.id) ? 'selected' : '';
                        roadSectionSelect.append(`<option value="${rs.id}" ${isSelected}>${rs.name}</option>`);
                    });
                }
                
                roadSectionSelect.trigger('change');
                isUpdating = false;
            }

            function updateState() {
                if (isUpdating) return;

                const selectedZone = document.querySelector('.zone-radio:checked').value;
                
                if (selectedZone) {
                    roadSectionSelect.prop('disabled', false);
                    roadSectionSelect.select2({ placeholder: '-- Pilih Ruas Jalan --', allowClear: true });
                } else {
                    roadSectionSelect.prop('disabled', true).val(null).trigger('change.select2');
                    roadSectionSelect.select2({ placeholder: '-- Pilih Zona Terlebih Dahulu --', allowClear: true });
                }
                
                const selectedRoads = roadSectionSelect.val() || [];
                
                if (selectedRoads.length > 0 && !noAgreementCb.checked) {
                    korlapSelect.prop('disabled', false);
                    korlapSelect.select2({ placeholder: '-- Semua Korlap --', allowClear: true });
                } else {
                    korlapSelect.prop('disabled', true).val('').trigger('change.select2');
                    korlapSelect.select2({ placeholder: '-- Pilih Ruas Jalan Dulu --', allowClear: true });
                }
            }

            zoneRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    renderRoadSections(this.value);
                    updateState();
                });
            });

            roadSectionSelect.on('change', updateState);
            noAgreementCb.addEventListener('change', updateState);

            // Initial load state
            const initialZone = document.querySelector('.zone-radio:checked').value;
            renderRoadSections(initialZone);
            updateState();
            
            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
