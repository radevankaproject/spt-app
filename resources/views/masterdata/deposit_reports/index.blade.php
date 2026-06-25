@extends('layouts.contentNavbarLayout')

@section('title', 'Laporan Transaksi Setoran')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
        {{-- ============================================= --}}
    {{-- HERO HEADER --}}
    {{-- ============================================= --}}
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-report-money me-2"></i>Laporan Setoran</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Laporan rekapitulasi setoran parkir.</p>
            </div>
        </div>
        <i class="ti tabler-report-money position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-bottom pb-3 p-4">
            <h5 class="card-title mb-0"><i class="ti tabler-filter me-1"></i> Filter Laporan Keuangan</h5>
        </div>
        <div class="card-body pt-4 p-4">
            <form action="{{ route('masterdata.deposit-reports.index') }}" method="GET">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="report_type" class="form-label fw-medium">Tipe Laporan</label>
                        <select name="report_type" id="report_type" class="form-select">
                            <option value="monthly" @selected(($reportType ?? 'monthly') == 'monthly')>Rekap Bulanan</option>
                            <option value="yearly" @selected(($reportType ?? '') == 'yearly')>Rekap Tahunan</option>
                        </select>
                    </div>

                    <div class="col-md-3 filter-group" id="monthly-filter">
                        <label for="specific_month" class="form-label fw-medium">Pilih Bulan</label>
                        <select name="specific_month" id="specific_month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" @selected(($specificMonth ?? date('m')) == sprintf('%02d', $m))>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 filter-group" id="yearly-filter">
                        <label for="specific_year" class="form-label fw-medium">Pilih Tahun</label>
                        <input type="number" name="specific_year" id="specific_year" min="2020" max="{{ date('Y') + 5 }}" class="form-control" value="{{ $specificYear ?? date('Y') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="search" class="form-label fw-medium">Cari No. PKS</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-search"></i></span>
                            <input type="text" name="search" placeholder="Contoh: PKS/01..." class="form-control" value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="field_coordinator_id" class="form-label fw-medium">Filter Koordinator Lapangan</label>
                        <select name="field_coordinator_id" id="field_coordinator_id" class="form-select select2">
                            <option value="">-- Semua Koordinator Lapangan --</option>
                            @foreach ($fieldCoordinators as $fc)
                                <option value="{{ $fc->id }}" @selected(($fieldCoordinatorId ?? '') == $fc->id)>{{ $fc->user->name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4 text-end border-top mt-4">
                    <a href="{{ route('masterdata.deposit-reports.index') }}" class="btn btn-outline-secondary me-2 rounded-pill"><i class="ti tabler-refresh me-1"></i> Reset</a>
                    <button type="submit" class="btn btn-primary rounded-pill btn-action me-2"><i class="ti tabler-zoom-in me-1"></i> Tampilkan</button>
                    <button type="submit" name="print_pdf" value="true" formtarget="_blank" class="btn btn-danger rounded-pill"><i class="ti tabler-file-type-pdf me-1"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Laporan --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header border-bottom pb-3 p-4">
            <h5 class="card-title mb-0 text-primary">{{ $reportTitle }}</h5>
        </div>

        <div class="card-body border-bottom pt-4 pb-4 bg-lighter p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="ti tabler-bar-chart-2 me-1"></i> {{ $chartTitle }}</h6>
                <span class="badge bg-label-primary px-3 py-2"><i class="ti tabler-calendar-event me-1"></i> Periode: {{ $reportType == 'yearly' ? 'Tahunan' : 'Bulanan' }}</span>
            </div>
            <div style="height: 350px; width: 100%;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        <div class="card-body pt-3 p-4">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th class="fw-bold">No. Dokumen PKS</th>
                            <th class="fw-bold">Koordinator</th>
                            <th class="fw-bold">Tgl Pembayaran</th>
                            <th class="text-end fw-bold">Nilai Setoran (Rp)</th>
                            <th class="text-center fw-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td><span class="fw-medium text-primary">{{ $report->agreement->agreement_number ?? 'N/A' }}</span></td>
                                <td>{{ $report->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                <td>{{ $report->deposit_date->format('d M Y') }}</td>
                                <td class="text-end fw-medium text-success">{{ number_format($report->amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($report->is_validated)
                                        <span class="badge rounded-pill bg-label-success px-3"><i class="ti tabler-checks me-1"></i> Sah</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-warning px-3"><i class="ti tabler-clock me-1"></i> Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="ti tabler-inbox-2 ti-xl mb-2 d-block"></i> Tidak ada data setoran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-top">
                        <tr class="bg-lighter">
                            <td colspan="3" class="text-end fw-bold fs-6">TARGET PROYEKSI PENDAPATAN:</td>
                            <td class="text-end fw-bold fs-6 text-warning">Rp {{ number_format($totalTargetAmount, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5">TOTAL SETORAN (SAH):</td>
                            <td class="text-end fw-bold fs-4 text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                            <td class="text-center fw-bold fs-5 {{ $percentage >= 100 ? 'text-success' : 'text-danger' }}">
                                {{ $percentage }}%
                                <i class="ri {{ $percentage >= 100 ? 'ti tabler-arrow-up text-success' : 'ti tabler-arrow-down text-danger' }}"></i>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('page-script')
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            $('.select2').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({ placeholder: '-- Cari atau Pilih Korlap --', allowClear: true, dropdownParent: $(this).parent() });
            });

            const reportTypeSelect = document.getElementById('report_type');
            function toggleFilterVisibility() {
                document.getElementById('monthly-filter').style.display = (reportTypeSelect.value === 'yearly') ? 'none' : 'block';
            }
            reportTypeSelect.addEventListener('change', toggleFilterVisibility);
            toggleFilterVisibility();

            // ✅ MIXED CHART.JS (BAR + DYNAMIC LINE)
            const ctx = document.getElementById('reportChart');
            if (ctx) {
                const labels = {!! $chartLabels !!};
                const actualData = {!! $chartValues !!};
                const targetData = {!! $chartTargets !!}; // Ini kosong kalau bulanan
                const hasData = actualData.some(val => val > 0) || (targetData.length > 0 && targetData.some(val => val > 0));

                if (labels.length > 0 && hasData) {

                    // Kita build dataset secara dinamis
                    let datasets = [];

                    // Jika ada data target (Mode Tahunan), tambahkan dataset garis!
                    if (targetData.length > 0) {
                        datasets.push({
                            type: 'line',
                            label: 'Target (Rp)',
                            data: targetData,
                            borderColor: 'rgba(255, 171, 0, 1)', // Warna Kuning/Oranye
                            backgroundColor: 'rgba(255, 171, 0, 0.15)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(255, 171, 0, 1)',
                            pointRadius: 4,
                            tension: 0.3,
                            fill: true,
                            order: 1 // Muncul di atas Bar
                        });
                    }

                    // Dataset Bar Actual selalu ada (Tahunan maupun Bulanan)
                    datasets.push({
                        type: 'bar',
                        label: 'Total Setoran Sah (Rp)',
                        data: actualData,
                        backgroundColor: 'rgba(105, 108, 255, 0.85)', // Warna Primary Biru
                        hoverBackgroundColor: 'rgba(105, 108, 255, 1)',
                        borderRadius: 6,
                        barPercentage: 0.5,
                        order: 2
                    });

                    new Chart(ctx.getContext('2d'), {
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', align: 'start' },
                                tooltip: {
                                    callbacks: { label: function(context) { return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw); } }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value === 0) return 'Rp 0';
                                            if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    },
                                    grid: { borderDash: [4, 4], color: '#ebebeb' }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                } else {
                    ctx.parentElement.innerHTML = '<div class="d-flex h-100 align-items-center justify-content-center text-muted"><div class="text-center"><i class="ti tabler-bar-chart-2 ti-xl mb-2"></i><br>Belum ada data setoran yang tervalidasi atau Target belum diatur</div></div>';
                }
            }
        });
    </script>
@endsection
