@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Pimpinan')

@section('page-style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .hero-card-leader { background: linear-gradient(135deg, #696cff 0%, #8b8eff 100%); }
        .stat-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .quick-stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; }
        .quick-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
        .quick-stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
        .quick-stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .glass-panel { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; }
        .premium-table tbody tr { transition: all 0.2s ease; }
        .premium-table tbody tr:hover { background-color: rgba(105, 108, 255, 0.05); }
        .select2-container--default .select2-selection--single { border: 1px solid #d9dee3; border-radius: 0.375rem; height: 38px; padding: 5px 15px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 10px; }
    </style>
@endsection



@section('content')

    {{-- ✅ 1. WELCOME BANNER PREMIUM --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm hero-card-leader text-white rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 position-relative">
                    <div class="row align-items-center relative z-2">
                        <div class="col-md-8 col-12 text-center text-md-start mb-4 mb-md-0">
                            <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                                <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </span>
                            <h3 class="text-white fw-bold mb-2">Selamat Datang, {{ $currentLeader->user->name ?? 'Pimpinan' }}! 👑</h3>
                            <p class="mb-4 opacity-75" style="font-size: 1.05rem;">
                                Pantau kinerja pendapatan, pergerakan titik parkir, dan aktivitas koordinator lapangan secara <em>real-time</em> di sini.
                            </p>
                            <div class="d-inline-flex flex-wrap gap-3">
                                <div class="glass-panel px-3 py-2 d-flex align-items-center">
                                    <i class="ti tabler-fingerprint ti tabler-xl me-2 opacity-75"></i>
                                    <div>
                                        <span class="d-block opacity-75 small fw-bold">NIP Pegawai</span>
                                        <span class="fw-bold">{{ $currentLeader->employee_number ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="glass-panel px-3 py-2 d-flex align-items-center">
                                    <i class="ti tabler-calendar-check ti tabler-xl me-2 opacity-75"></i>
                                    <div>
                                        <span class="d-block opacity-75 small fw-bold">Mulai Menjabat</span>
                                        <span class="fw-bold">{{ $currentLeader->start_date ? \Carbon\Carbon::parse($currentLeader->start_date)->translatedFormat('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center text-md-end">
                            <div class="d-inline-block position-relative rounded-circle p-1" style="background: linear-gradient(135deg, #f6d365 0%, #ffb142 100%);">
                                <img src="{{ $currentLeader && $currentLeader->user->img ? asset('storage/' . $currentLeader->user->img) : 'https://ui-avatars.com/api/?name='.urlencode($currentLeader->user->name ?? 'Pimpinan').'&background=fff&color=696cff' }}"
                                    alt="Avatar" class="rounded-circle shadow-lg border border-white border-3" style="width: 140px; height: 140px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <i class="ti tabler-bar-chart-box position-absolute text-white" style="font-size: 250px; left: -20px; bottom: -50px; opacity: 0.1; transform: rotate(-15deg);"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. QUICK STATS (6 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100 bg-label-success">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-success text-white mx-auto mb-2 shadow-sm"><i class="ti tabler-currency-dollar ti-md"></i></div>
                    <div class="quick-stat-value text-success" style="font-size: 1rem;">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</div>
                    <div class="quick-stat-label text-success mt-1">Thn {{ now()->year }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-info mx-auto mb-2"><i class="ti tabler-wallet ti-md"></i></div>
                    <div class="quick-stat-value text-info" style="font-size: 1rem;">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</div>
                    <div class="quick-stat-label text-muted mt-1">Bln Ini</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-primary mx-auto mb-2"><i class="ti tabler-file-text ti-md"></i></div>
                    <div class="quick-stat-value text-primary">{{ $totalAgreements }}</div>
                    <div class="quick-stat-label text-muted mt-1">PKS Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-warning mx-auto mb-2"><i class="ti tabler-map-pin ti-md"></i></div>
                    <div class="quick-stat-value text-warning">{{ $totalParkingLocations }}</div>
                    <div class="quick-stat-label text-muted mt-1">Titik Parkir</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-dark mx-auto mb-2"><i class="ti tabler-road ti-md"></i></div>
                    <div class="quick-stat-value">{{ $totalRoadSections }}</div>
                    <div class="quick-stat-label text-muted mt-1">Ruas Jalan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card quick-stat-card shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="stat-card-icon bg-label-secondary mx-auto mb-2"><i class="ti tabler-user-pin ti-md"></i></div>
                    <div class="quick-stat-value">{{ $totalFieldCoordinators }}</div>
                    <div class="quick-stat-label text-muted mt-1">Korlap</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 3. PENCARIAN CEPAT --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ti tabler-zoom-in me-2 text-primary"></i>Pencarian Data Cepat</h6>
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

    {{-- ✅ 4. MAP & TABEL LOKASI TERBARU --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="card-title mb-0 fw-bold"><i class="ti tabler-map-pin text-danger me-2"></i>Peta Sampel Lokasi Parkir</h6>
                        <small class="text-muted">50 Titik Acak (Real-time)</small>
                    </div>
                    <a href="{{ route('masterdata.parking-locations.map') }}" class="btn btn-sm btn-outline-danger rounded-pill">Lihat Peta Lengkap <i class="ti tabler-arrow-right ms-1"></i></a>
                </div>
                <div class="card-body p-0">
                    <div id="leader-map" style="height: 400px; width: 100%; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; z-index: 1;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="ti tabler-map-pin-plus text-warning me-2"></i>Lokasi Terbaru</h6>
                    @if ($totalParkingLocations > 10)
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-sm btn-outline-warning rounded-pill">Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 400px;">
                    <table class="table table-hover mb-0 align-middle premium-table">
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
                                        <a href="{{ route('masterdata.parking-locations.show', $loc->id) }}" class="fw-bold text-dark" style="font-size: 0.85rem;">{{ Str::limit($loc->name, 25) }}</a>
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">{{ Str::limit($loc->roadSection->name ?? '-', 25) }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-label-dark rounded-pill">{{ $loc->roadSection->zone ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-5 text-muted"><i class="ti tabler-map-pin-off ti-lg opacity-50 mb-2 d-block"></i> Belum ada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 5. GRAFIK APEXCHARTS --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <div>
                        <h6 class="card-title mb-0 fw-bold">Tren Setoran Tervalidasi</h6>
                        <small class="text-muted">Tahun {{ now()->year }}</small>
                    </div>
                    <div class="badge bg-label-success rounded-pill px-3 py-2"><i class="ti tabler-chart me-1"></i> Stabil</div>
                </div>
                <div class="card-body"><div id="deposit-chart"></div></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0 fw-bold">Kepadatan Titik Parkir</h6>
                    <small class="text-muted">Top 10 Ruas Jalan</small>
                </div>
                <div class="card-body"><div id="locations-per-road-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 6. TABEL PKS (TERBARU + SEGERA BERAKHIR) --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="ti tabler-file-text text-primary me-2"></i>Kontrak PKS Terbaru</h6>
                    @if ($totalAgreements > 10)
                        <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 320px;">
                    <table class="table table-hover mb-0 align-middle premium-table">
                        <thead class="bg-lighter position-sticky top-0 z-1">
                            <tr>
                                <th class="text-uppercase" style="font-size: 0.75rem;">No. Kontrak PKS</th>
                                <th class="text-uppercase" style="font-size: 0.75rem;">Mitra (Korlap)</th>
                                <th class="text-uppercase text-center" style="font-size: 0.75rem;">Titik</th>
                                <th class="text-uppercase text-end" style="font-size: 0.75rem;">Target/Bln</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAgreements as $pks)
                                <tr>
                                    <td>
                                        <a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="fw-bold text-primary">{{ $pks->agreement_number }}</a>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($pks->start_date)->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td><span class="fw-medium text-dark">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</span></td>
                                    <td class="text-center"><span class="badge bg-label-info rounded-pill">{{ $pks->active_parking_locations_count }}</span></td>
                                    <td class="text-end"><span class="fw-bold text-primary">Rp {{ number_format($pks->monthly_deposit_target, 0, ',', '.') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-muted"><i class="ti tabler-inbox-2 ti-xl opacity-50 mb-2 d-block"></i> Belum ada kontrak PKS.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header border-bottom bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="ti tabler-alert-triangle text-warning me-2"></i>PKS Segera Berakhir</h6>
                    <span class="badge bg-warning bg-opacity-10 text-warning fw-bold">30 Hari</span>
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 320px;">
                    <table class="table table-hover mb-0 align-middle premium-table">
                        <thead class="bg-lighter position-sticky top-0 z-1">
                            <tr>
                                <th class="text-uppercase" style="font-size: 0.75rem;">No. PKS</th>
                                <th class="text-uppercase" style="font-size: 0.75rem;">Korlap</th>
                                <th class="text-uppercase text-end" style="font-size: 0.75rem;">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringAgreements as $pks)
                                @php $daysLeft = now()->diffInDays($pks->end_date, false); @endphp
                                <tr>
                                    <td><a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="fw-bold text-warning">{{ $pks->agreement_number }}</a></td>
                                    <td>{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                    <td class="text-end"><span class="badge {{ $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning' }} fw-bold">{{ (int)$daysLeft }} hari</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-5 text-success"><i class="ti tabler-checks ti-lg opacity-50 mb-2 d-block"></i> Semua PKS aman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
@endsection

@section('page-script')
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {

            // 1. GRAFIK TREN SETORAN (AREA CHART)
            const depositChartEl = document.querySelector("#deposit-chart");
            if (depositChartEl) {
                new ApexCharts(depositChartEl, {
                    chart: { type: 'area', height: 320, parentHeightOffset: 0, toolbar: { show: false }, fontFamily: 'Public Sans, sans-serif' },
                    dataLabels: { enabled: false },
                    stroke: { show: true, curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                    series: [{ name: 'Nominal Valid', data: @json($depositChartData) }],
                    xaxis: { categories: @json($depositChartLabels), axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#a1acb8', fontSize: '13px' } } },
                    yaxis: { labels: { style: { colors: '#a1acb8', fontSize: '13px' }, formatter: (val) => "Rp " + (val / 1000000).toFixed(0) + " Jt" } },
                    colors: ['#71dd37'],
                    grid: { borderColor: '#eceef1', strokeDashArray: 4, xaxis: { lines: { show: true } } },
                    tooltip: { y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) } }
                }).render();
            }

            // 2. GRAFIK KEPADATAN TITIK (BAR CHART)
            const barChartEl = document.querySelector("#locations-per-road-chart");
            if (barChartEl) {
                new ApexCharts(barChartEl, {
                    chart: { type: 'bar', height: 320, parentHeightOffset: 0, toolbar: { show: false }, fontFamily: 'Public Sans, sans-serif' },
                    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4, distributed: true } },
                    dataLabels: { enabled: false },
                    series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                    xaxis: { categories: @json($barChartData['labels']), labels: { style: { colors: '#a1acb8' } } },
                    yaxis: { labels: { style: { colors: '#a1acb8', fontSize: '12px' } } },
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'],
                    grid: { show: false },
                    legend: { show: false }
                }).render();
            }

            // 3. LOGIKA QUICK SEARCH (SELECT 2 AJAX)
            function redirectToPage(event, baseUrl) {
                const data = event.params.data;
                if (data.id) { window.location.href = baseUrl.replace(':id', data.id); }
            }

            $('#pks-search-select').select2({
                placeholder: '🔍 Ketik No. PKS / Nama Korlap...',
                allowClear: true,
                ajax: { url: "{{ route('masterdata.search-agreements-ajax') }}", dataType: 'json', delay: 250, data: p => ({ q: p.term }), processResults: d => ({ results: d.results }), cache: true }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.agreements.show', ':id') }}'));

            $('#location-search-select').select2({
                placeholder: '🔍 Ketik nama titik lokasi...',
                allowClear: true,
                ajax: { url: "{{ route('masterdata.search-locations-ajax') }}", dataType: 'json', delay: 250, data: p => ({ q: p.term }), processResults: d => ({ results: d.results }), cache: true }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.parking-locations.show', ':id') }}'));

            $('#deposit-search-select').select2({
                placeholder: '🔍 Ketik 6 digit No. Referensi...',
                allowClear: true,
                ajax: { url: "{{ route('masterdata.search-deposits-ajax') }}", dataType: 'json', delay: 250, data: p => ({ q: p.term }), processResults: d => ({ results: d.results }), cache: true }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.deposit-transactions.show', ':id') }}'));

            // 4. MAP 50 TITIK ACAK (LEAFLET)
            setTimeout(() => {
                const mapEl = document.getElementById('leader-map');
                if(mapEl) {
                    const map = L.map('leader-map').setView([0.5070677, 101.4477793], 11);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
                    const locations = @json($randomMapLocations ?? []);
                    const bounds = [];
                    locations.forEach(loc => {
                        if (loc.latitude && loc.longitude) {
                            const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);
                            const roadName = loc.road_section ? loc.road_section.name : 'Tanpa Ruas';
                            const zone = loc.road_section ? loc.road_section.zone : '-';
                            const url = `/masterdata/parking-locations/${loc.id}`;
                            marker.bindPopup(`<div class="p-1"><h6 class="fw-bold mb-1">${loc.name}</h6><p class="small text-muted mb-2"><i class="ti tabler-road align-middle"></i> ${roadName} (Zona ${zone})</p><a href="${url}" class="btn btn-xs btn-outline-secondary w-100">Detail</a></div>`);
                            bounds.push([loc.latitude, loc.longitude]);
                        }
                    });
                    if (bounds.length > 0) { map.fitBounds(bounds, { padding: [100, 100], maxZoom: 12 }); }
                    map.invalidateSize();
                }
            }, 500);
        });
    </script>
@endsection