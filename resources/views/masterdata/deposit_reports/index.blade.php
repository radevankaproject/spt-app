@extends('layouts.app')

@section('title', 'Laporan Transaksi Setoran')

@section('skeleton')
    @include('layouts.partials._skeleton-deposit-report-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Laporan Transaksi Setoran</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Laporan Setoran</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('masterdata.deposit-reports.index') }}" method="GET" id="report-filter-form">
                <div class="row g-4">
                    {{-- Tipe Laporan --}}
                    <div class="col-md-3">
                        <label for="report_type" class="form-label">Tipe Laporan</label>
                        <select name="report_type" id="report_type" class="form-select">
                            <option value="daily" @selected(($reportType ?? 'daily') == 'daily')>Harian</option>
                            <option value="monthly" @selected(($reportType ?? '') == 'monthly')>Bulanan</option>
                            <option value="yearly" @selected(($reportType ?? '') == 'yearly')>Tahunan</option>
                            <option value="custom_range" @selected(($reportType ?? '') == 'custom_range')>Rentang Waktu</option>
                        </select>
                    </div>

                    {{-- Filter Harian --}}
                    <div class="col-md-3 filter-group" id="daily-filter">
                        <label for="specific_date" class="form-label">Pilih Tanggal</label>
                        {{-- ✅ INPUT DIUBAH DARI type="date" MENJADI type="text" --}}
                        <input type="text" name="specific_date" id="specific_date" class="form-control"
                            placeholder="YYYY-MM-DD" value="{{ $specificDate ?? date('Y-m-d') }}">
                    </div>

                    {{-- Filter Bulanan --}}
                    <div class="col-md-3 filter-group" id="monthly-filter">
                        <label for="specific_month" class="form-label">Pilih Bulan</label>
                        <select name="specific_month" id="specific_month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" @selected(($specificMonth ?? date('m')) == sprintf('%02d', $m))>
                                    {{ Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter Tahunan --}}
                    <div class="col-md-2 filter-group" id="yearly-filter">
                        <label for="specific_year" class="form-label">Pilih Tahun</label>
                        <input type="number" name="specific_year" id="specific_year" min="2020"
                            max="{{ date('Y') + 5 }}" class="form-control" value="{{ $specificYear ?? date('Y') }}">
                    </div>

                    {{-- Filter Rentang Waktu --}}
                    <div class="col-md-4 filter-group" id="custom-range-filter">
                        <label class="form-label">Pilih Rentang Tanggal</label>
                        <div class="input-group">
                             {{-- ✅ INPUT DIUBAH DARI type="date" MENJADI type="text" --}}
                            <input type="text" name="start_date" id="start_date" class="form-control"
                                placeholder="YYYY-MM-DD" value="{{ $startDate ?? '' }}">
                            <span class="input-group-text">s/d</span>
                             {{-- ✅ INPUT DIUBAH DARI type="date" MENJADI type="text" --}}
                            <input type="text" name="end_date" id="end_date" class="form-control"
                                placeholder="YYYY-MM-DD" value="{{ $endDate ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-1">
                    {{-- Filter Koordinator & Pencarian Umum (Tidak berubah) --}}
                    <div class="col-md-6">
                        <label for="field_coordinator_id" class="form-label">Filter Koordinator</label>
                        <select name="field_coordinator_id" id="field_coordinator_id" class="form-select select2">
                            <option value="">Semua Koordinator</option>
                            @foreach ($fieldCoordinators as $fc)
                                <option value="{{ $fc->id }}" @selected(($fieldCoordinatorId ?? '') == $fc->id)>
                                    {{ $fc->user->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">Cari No. PKS</label>
                        <input type="text" name="search" id="search" placeholder="Cari berdasarkan nomor PKS..."
                            class="form-control" value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="pt-4 text-end">
                    <a href="{{ route('masterdata.deposit-reports.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                    <button type="submit" class="btn btn-primary me-2">Tampilkan Laporan</button>
                    <button type="submit" name="print_pdf" value="true" formtarget="_blank" class="btn btn-outline-danger">
                        <i class="icon-base ri ri-printer-line me-2"></i>Cetak PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Laporan --}}
    <div class="card mt-6">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $reportTitle }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. PKS</th>
                            <th>Koordinator</th>
                            <th>Tgl Setor</th>
                            <th class="text-end">Jumlah (Rp)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td><span class="fw-medium">{{ $report->agreement->agreement_number ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $report->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                <td>{{ $report->deposit_date->format('d M Y') }}</td>
                                <td class="text-end">{{ number_format($report->amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($report->is_validated)
                                        <span class="badge rounded-pill bg-label-success">Tervalidasi</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data setoran untuk filter yang
                                    dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-medium">Total Setoran Tervalidasi</td>
                            <td class="text-end fw-medium">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inisialisasi Select2
            $('.select2').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Koordinator',
                    allowClear: true,
                    dropdownParent: $(this).parent()
                });
            });

            flatpickr("#specific_date", { dateFormat: "Y-m-d" });
            flatpickr("#start_date", { dateFormat: "Y-m-d" });
            flatpickr("#end_date", { dateFormat: "Y-m-d" });

            // Logika untuk menampilkan/menyembunyikan filter
            const reportTypeSelect = document.getElementById('report_type');
            const filters = {
                daily: ['daily-filter'],
                monthly: ['monthly-filter', 'yearly-filter'],
                yearly: ['yearly-filter'],
                custom_range: ['custom-range-filter']
            };

            function toggleFilterVisibility() {
                const selectedType = reportTypeSelect.value;
                // Sembunyikan semua filter group
                document.querySelectorAll('.filter-group').forEach(el => el.style.display = 'none');
                // Tampilkan filter yang relevan
                if (filters[selectedType]) {
                    filters[selectedType].forEach(id => document.getElementById(id).style.display = 'block');
                }
            }

            reportTypeSelect.addEventListener('change', toggleFilterVisibility);
            toggleFilterVisibility(); // Panggil saat halaman dimuat
        });
    </script>
@endpush
