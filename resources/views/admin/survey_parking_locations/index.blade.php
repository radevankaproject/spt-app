@extends('layouts.contentNavbarLayout')

@section('title', 'Survey Lokasi Parkir')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-clipboard-check me-2"></i>Survey Lokasi Parkir</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola data survey titik lokasi parkir dan jukir secara berkala.</p>
            </div>
        </div>
        <i class="ti tabler-clipboard-check position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <div class="glass-card anim-2 border-0 overflow-hidden mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3 p-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Survey</h5>
                <p class="text-muted mb-0">Total {{ count($surveys) }} data survey terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <a href="{{ route('admin.survey-parking-locations.create') }}" class="btn btn-primary rounded-pill btn-action">
                    <i class="ri icon-base ti tabler-plus me-1"></i> Tambah Survey
                </a>
            </div>
        </div>

        <div class="card-body border-bottom p-4 bg-lighter">
            <form action="{{ route('admin.survey-parking-locations.index') }}" method="GET" class="row g-3">
                <div class="col-md-12 mb-2">
                    <label class="form-label fw-bold mb-1">Pilih Zona</label>
                    <div class="d-flex flex-wrap gap-4 align-items-center mt-1">
                        <div class="form-check form-check-primary mb-0">
                            <input class="form-check-input" style="width: 1.3em; height: 1.3em; cursor: pointer;" type="radio" name="zone" id="zone_all" value="" {{ request('zone') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label fw-bold text-dark ms-1 mt-1" style="cursor: pointer;" for="zone_all">Semua Zona</label>
                        </div>
                        @foreach ($zones as $zone)
                            <div class="form-check form-check-primary mb-0">
                                <input class="form-check-input" style="width: 1.3em; height: 1.3em; cursor: pointer;" type="radio" name="zone" id="zone_{{ $zone }}" value="{{ $zone }}" {{ request('zone') == $zone ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label fw-bold text-dark ms-1 mt-1" style="cursor: pointer;" for="zone_{{ $zone }}">Zona {{ $zone }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ruas Jalan</label>
                    <select name="road_section_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">Semua Ruas Jalan</option>
                        @foreach ($roadSections as $rs)
                            <option value="{{ $rs->id }}" {{ request('road_section_id') == $rs->id ? 'selected' : '' }}>{{ $rs->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pencarian Titik Parkir</label>
                    <div class="input-group input-group-merge rounded-pill overflow-hidden shadow-sm bg-white">
                        <span class="input-group-text border-0 bg-transparent"><i class="ti tabler-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 px-2 bg-transparent" placeholder="Cari nama titik parkir..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 shadow-sm"><i class="ti tabler-filter me-1"></i> Filter</button>
                        @if(request('zone') || request('road_section_id') || request('search'))
                            <a href="{{ route('admin.survey-parking-locations.index') }}" class="btn btn-label-secondary rounded-pill btn-icon shadow-sm" data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="ti tabler-refresh"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body pt-3 p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fw-bold" role="alert">
                    <i class="ti tabler-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th scope="col" class="text-uppercase fw-bold text-primary" width="5%">No</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Titik Parkir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Jukir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Tanggal Survey</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Tajuk</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Tanam</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Surveyor</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($surveys as $index => $survey)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-medium text-dark text-wrap d-inline-block" style="min-width: 200px; max-width: 300px;">{{ $survey->parkingLocation->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-info fw-bold">{{ $survey->jukir->nama_jukir ?? '-' }}</span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($survey->survey_date)->translatedFormat('F Y') }}
                                </td>
                                <td class="font-monospace text-primary fw-semibold">
                                    {{ $survey->survey_tajuk !== null ? 'Rp ' . number_format((float)$survey->survey_tajuk, 0, ',', '.') : '-' }}
                                </td>
                                <td class="font-monospace text-info fw-semibold">
                                    {{ $survey->survey_tanam !== null ? 'Rp ' . number_format((float)$survey->survey_tanam, 0, ',', '.') : '-' }}
                                </td>
                                <td>
                                    {{ $survey->surveyor ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if($survey->parking_location_id)
                                            <a class="btn btn-sm btn-icon btn-text-info rounded-pill"
                                                href="{{ route('masterdata.parking-locations.show', $survey->parking_location_id) }}"
                                                data-bs-toggle="tooltip" title="Lihat Titik Parkir">
                                                <i class="ri icon-base ti tabler-eye icon-22px"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            href="{{ route('admin.survey-parking-locations.edit', $survey->id) }}"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="ri icon-base ti tabler-pencil icon-22px"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.survey-parking-locations.destroy', $survey->id) }}"
                                            method="POST" class="d-inline" id="deleteForm{{ $survey->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-text-danger rounded-pill"
                                                onclick="confirmDelete({{ $survey->id }})"
                                                data-bs-toggle="tooltip" title="Hapus">
                                                <i class="ri icon-base ti tabler-trash icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-center py-5">
                                        <div class="icon-glass bg-label-secondary mx-auto mb-3">
                                            <i class="ti tabler-folder-off fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Tidak Ada Data</h5>
                                        <p class="text-muted mb-0">Belum ada data survey yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js", "resources/assets/vendor/libs/select2/select2.js"])
    <script type="module">
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Ruas Jalan",
                allowClear: true
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data survey ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>
@endsection
