@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        /* ✅ PREMIUM CUSTOM CSS */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #8b8eff 100%);
        }
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(105, 108, 255, 0.15) !important;
        }
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        /* Styling khusus Select2 agar menyatu dengan Bootstrap 5 */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
            height: 38px;
            padding: 5px 15px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 10px;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
        }
    </style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-leader-dashboard')
@endsection

@section('content')

    {{-- ========================================== --}}
    {{-- 1. WELCOME BANNER PREMIUM                  --}}
    {{-- ========================================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 position-relative">
                    <div class="row align-items-center relative z-2">
                        <div class="col-md-8 col-12 text-center text-md-start mb-4 mb-md-0">
                            <h3 class="text-white fw-bold mb-2">Selamat Datang, {{ $currentLeader->user->name ?? 'Pimpinan' }}! 👑</h3>
                            <p class="mb-4 opacity-75" style="font-size: 1.05rem;">
                                Pantau kinerja pendapatan, pergerakan titik parkir, dan aktivitas koordinator lapangan secara *real-time* di sini.
                            </p>
                            <div class="d-inline-flex flex-wrap gap-3">
                                <div class="glass-panel px-3 py-2 d-flex align-items-center">
                                    <i class="ri ri-fingerprint-line ri-xl me-2 opacity-75"></i>
                                    <div>
                                        <span class="d-block opacity-75 small fw-bold">NIP Pegawai</span>
                                        <span class="fw-bold">{{ $currentLeader->employee_number ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="glass-panel px-3 py-2 d-flex align-items-center">
                                    <i class="ri ri-calendar-check-line ri-xl me-2 opacity-75"></i>
                                    <div>
                                        <span class="d-block opacity-75 small fw-bold">Mulai Menjabat</span>
                                        <span class="fw-bold">{{ $currentLeader->start_date ? \Carbon\Carbon::parse($currentLeader->start_date)->translatedFormat('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center text-md-end">
                            <img src="{{ $currentLeader && $currentLeader->user->img ? asset('storage/' . $currentLeader->user->img) : 'https://ui-avatars.com/api/?name='.urlencode($currentLeader->user->name ?? 'Pimpinan').'&background=fff&color=696cff' }}" 
                                alt="Avatar" class="rounded-circle shadow-lg border border-white border-3" style="width: 140px; height: 140px; object-fit: cover;">
                        </div>
                    </div>
                    <i class="ri ri-bar-chart-box-line position-absolute text-white" style="font-size: 250px; left: -20px; bottom: -50px; opacity: 0.1; transform: rotate(-15deg);"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 2. METRIK KPI (KEY PERFORMANCE INDICATOR)  --}}
    {{-- ========================================== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div><h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.75rem;">Total Setoran (Tahun Ini)</h6></div>
                        <div class="icon-shape bg-label-success"><i class="ri ri-money-dollar-circle-line ri-24px"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold"><i class="ri ri-arrow-up-line align-bottom"></i> Pendapatan Daerah</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div><h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.75rem;">Setoran (Bulan Ini)</h6></div>
                        <div class="icon-shape bg-label-info"><i class="ri ri-wallet-3-line ri-24px"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</h3>
                    <small class="text-muted">Total validasi bulan {{ now()->translatedFormat('F') }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div><h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.75rem;">Total Mitra PKS</h6></div>
                        <div class="icon-shape bg-label-primary"><i class="ri ri-file-text-line ri-24px"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalAgreements, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Kontrak</span></h3>
                    <a href="{{ route('masterdata.agreements.index') }}" class="text-primary small fw-medium">Lihat detail <i class="ri ri-arrow-right-s-line align-bottom"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div><h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.75rem;">Titik Parkir Aktif</h6></div>
                        <div class="icon-shape bg-label-warning"><i class="ri ri-map-pin-line ri-24px"></i></div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalParkingLocations, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Lokasi</span></h3>
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="text-warning small fw-medium">Lihat detail <i class="ri ri-arrow-right-s-line align-bottom"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 3. PENCARIAN CEPAT (QUICK FINDER)          --}}
    {{-- ========================================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ri ri-search-eye-line me-2 text-primary"></i>Pencarian Data Cepat</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="pks-search-select" class="form-label fw-bold text-muted small text-uppercase">Cari PKS / Korlap</label>
                            <select id="pks-search-select" class="form-select w-100"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="location-search-select" class="form-label fw-bold text-muted small text-uppercase">Cari Titik Lokasi</label>
                            <select id="location-search-select" class="form-select w-100"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="deposit-search-select" class="form-label fw-bold text-muted small text-uppercase">Cari Setoran (No Ref)</label>
                            <select id="deposit-search-select" class="form-select w-100"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 4. GRAFIK APEXCHARTS                       --}}
    {{-- ========================================== --}}
    <div class="row g-4 mb-4">
        {{-- Area Chart: Tren Setoran --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <div>
                        <h6 class="card-title mb-0 fw-bold">Tren Setoran Tervalidasi</h6>
                        <small class="text-muted">Tahun {{ now()->year }}</small>
                    </div>
                    <div class="badge bg-label-success rounded-pill px-3 py-2"><i class="ri ri-line-chart-line me-1"></i> Stabil</div>
                </div>
                <div class="card-body">
                    <div id="deposit-chart"></div>
                </div>
            </div>
        </div>
        
        {{-- Bar Chart: Top Ruas Jalan --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0 fw-bold">Kepadatan Titik Parkir</h6>
                    <small class="text-muted">Top 10 Ruas Jalan</small>
                </div>
                <div class="card-body">
                    <div id="locations-per-road-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 5. TABEL AKTIVITAS TERBARU                 --}}
    {{-- ========================================== --}}
    <div class="row g-4">
        {{-- PKS Terbaru --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="ri ri-file-list-3-line text-primary me-2"></i>Kontrak PKS Terbaru</h6>
                    @if ($totalAgreements > 10)
                        <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 320px;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-lighter position-sticky top-0 z-1">
                            <tr>
                                <th class="text-uppercase" style="font-size: 0.75rem;">No. Kontrak PKS</th>
                                <th class="text-uppercase" style="font-size: 0.75rem;">Mitra (Korlap)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAgreements as $pks)
                                <tr>
                                    <td>
                                        <a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="fw-bold text-primary">{{ $pks->agreement_number }}</a>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($pks->start_date)->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-dark">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">
                                        <i class="ri ri-inbox-2-line ri-3x opacity-50 mb-2 d-block"></i> Belum ada kontrak PKS.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Titik Parkir Terbaru --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="ri ri-map-pin-add-line text-warning me-2"></i>Lokasi Parkir Terbaru</h6>
                    @if ($totalParkingLocations > 10)
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-sm btn-outline-warning rounded-pill">Lihat Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 320px;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-lighter position-sticky top-0 z-1">
                            <tr>
                                <th class="text-uppercase" style="font-size: 0.75rem;">Nama Lokasi</th>
                                <th class="text-uppercase text-end" style="font-size: 0.75rem;">Zona</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentParkingLocations as $loc)
                                <tr>
                                    <td>
                                        <a href="{{ route('masterdata.parking-locations.show', $loc->id) }}" class="fw-bold text-dark">{{ Str::limit($loc->name, 35) }}</a>
                                        <small class="d-block text-muted">{{ $loc->roadSection->name ?? '-' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-label-dark rounded-pill">{{ $loc->roadSection->zone ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">
                                        <i class="ri ri-map-pin-off-line ri-3x opacity-50 mb-2 d-block"></i> Belum ada lokasi terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // ==========================================
            // 1. GRAFIK TREN SETORAN (AREA CHART)
            // ==========================================
            const depositChartEl = document.querySelector("#deposit-chart");
            if (depositChartEl) {
                new ApexCharts(depositChartEl, {
                    chart: {
                        type: 'area',
                        height: 320,
                        parentHeightOffset: 0,
                        toolbar: { show: false },
                        fontFamily: 'Outfit, sans-serif'
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    series: [{
                        name: 'Nominal Valid',
                        data: @json($depositChartData)
                    }],
                    xaxis: {
                        categories: @json($depositChartLabels),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#a1acb8', fontSize: '13px' } }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#a1acb8', fontSize: '13px' },
                            formatter: function (val) {
                                return "Rp " + (val / 1000000).toFixed(0) + " Jt";
                            }
                        }
                    },
                    colors: ['#71dd37'], // Success Color
                    grid: { borderColor: '#eceef1', strokeDashArray: 4, xaxis: { lines: { show: true } } },
                    tooltip: {
                        y: { formatter: function(val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val); } }
                    }
                }).render();
            }

            // ==========================================
            // 2. GRAFIK KEPADATAN TITIK (BAR CHART)
            // ==========================================
            const barChartEl = document.querySelector("#locations-per-road-chart");
            if (barChartEl) {
                new ApexCharts(barChartEl, {
                    chart: {
                        type: 'bar',
                        height: 320,
                        parentHeightOffset: 0,
                        toolbar: { show: false },
                        fontFamily: 'Outfit, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '60%',
                            borderRadius: 4,
                            distributed: true // Bikin warnanya beda-beda tiap bar
                        }
                    },
                    dataLabels: { enabled: false },
                    series: [{
                        name: 'Jumlah Titik',
                        data: @json($barChartData['data'])
                    }],
                    xaxis: {
                        categories: @json($barChartData['labels']),
                        labels: { style: { colors: '#a1acb8' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#a1acb8', fontSize: '12px' } }
                    },
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'],
                    grid: { show: false }, // Hilangkan grid biar lebih clean
                    legend: { show: false }
                }).render();
            }

            // ==========================================
            // 3. LOGIKA QUICK SEARCH (SELECT 2 AJAX)
            // ==========================================
            function redirectToPage(event, baseUrl) {
                const data = event.params.data;
                if (data.id) {
                    window.location.href = baseUrl.replace(':id', data.id);
                }
            }

            // Pencarian PKS
            $('#pks-search-select').select2({
                placeholder: '🔍 Ketik No. PKS / Nama Korlap...',
                allowClear: true,
                ajax: {
                    url: "{{ route('masterdata.search-agreements-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({ q: p.term }),
                    processResults: d => ({ results: d.results }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.agreements.show', ':id') }}'));

            // Pencarian Titik Lokasi
            $('#location-search-select').select2({
                placeholder: '🔍 Ketik nama titik lokasi...',
                allowClear: true,
                ajax: {
                    url: "{{ route('masterdata.search-locations-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({ q: p.term }),
                    processResults: d => ({ results: d.results }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.parking-locations.show', ':id') }}'));

            // Pencarian Setoran
            $('#deposit-search-select').select2({
                placeholder: '🔍 Ketik 6 digit No. Referensi...',
                allowClear: true,
                ajax: {
                    url: "{{ route('masterdata.search-deposits-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({ q: p.term }),
                    processResults: d => ({ results: d.results }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.deposit-transactions.show', ':id') }}'));
        });
    </script>
@endpush