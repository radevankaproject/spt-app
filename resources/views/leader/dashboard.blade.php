@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard Pimpinan')

@section('page-style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
        .select2-container--default .select2-selection--single { border: 1px solid rgba(105, 108, 255, 0.2); border-radius: 0.75rem; height: 42px; padding: 7px 15px; background: rgba(255,255,255,0.7); backdrop-filter: blur(5px); }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; right: 10px; }
    </style>
@endsection



@section('content')

    {{-- ✅ 1. WELCOME BANNER PREMIUM --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="fintech-card shadow-lg text-white p-4 p-lg-5 animate__animated animate__fadeInDown">
                <i class="ti tabler-crown position-absolute text-white opacity-10" style="font-size: 8rem; right: -1%; top: -10%; transform: rotate(15deg);"></i>
                <div class="row align-items-center position-relative z-1">
                    <div class="col-md-8 col-12 text-center text-md-start mb-4 mb-md-0">
                        <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">
                            <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                        <h3 class="text-white fw-bold mb-2">Selamat Datang, {{ $currentLeader->user->name ?? 'Pimpinan' }}! 👑</h3>
                        <p class="mb-4 opacity-75" style="font-size: 1.05rem;">
                            Pantau kinerja pendapatan, pergerakan titik parkir, dan aktivitas koordinator lapangan secara <em>real-time</em> di sini.
                        </p>
                        <div class="d-inline-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                            <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="ti tabler-fingerprint me-1"></i> NIP: <strong>{{ $currentLeader->employee_number ? formatNip($currentLeader->employee_number) : '-' }}</strong>
                            </div>
                            <div class="bg-white text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="ti tabler-calendar-check me-1"></i> Mulai: <strong>{{ $currentLeader->start_date ? \Carbon\Carbon::parse($currentLeader->start_date)->translatedFormat('d M Y') : '-' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 text-center text-md-end">
                        <div class="position-relative d-inline-block">
                            <div class="position-absolute w-100 h-100 rounded-circle bg-white opacity-25" style="top: 10px; left: -10px; filter: blur(20px);"></div>
                            <img src="{{ $currentLeader && $currentLeader->user->img ? asset('storage/' . $currentLeader->user->img) : 'https://ui-avatars.com/api/?name='.urlencode($currentLeader->user->name ?? 'Pimpinan').'&background=fff&color=696cff' }}"
                                alt="Avatar" class="rounded-circle gold-frame-glow position-relative" style="width: 140px; height: 140px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle p-2" title="Online" style="margin-bottom: 10px; margin-right: 10px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. QUICK STATS (6 kartu) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-green text-center p-3 h-100 animate__animated animate__zoomIn animate__fast">
                <div class="stat-glow-icon bg-success text-white mx-auto mb-3 shadow-sm"><i class="ti tabler-currency-dollar ti-md"></i></div>
                <h4 class="fw-bolder text-success mb-1" style="font-size: 1rem;">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</h4>
                <span class="text-success small fw-bold text-uppercase">Thn {{ now()->year }}</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-purple text-center p-3 h-100 animate__animated animate__bounceIn animate__fast" style="animation-delay: 0.1s;">
                <div class="stat-glow-icon bg-white text-info mx-auto mb-3"><i class="ti tabler-wallet ti-md"></i></div>
                <h4 class="fw-bolder text-info mb-1" style="font-size: 1rem;">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</h4>
                <span class="text-muted small fw-bold text-uppercase">Bln Ini</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-blue text-center p-3 h-100 animate__animated animate__fadeInUp animate__fast" style="animation-delay: 0.2s;">
                <div class="stat-glow-icon bg-white text-primary mx-auto mb-3"><i class="ti tabler-file-text ti-md"></i></div>
                <h3 class="fw-bolder text-primary mb-1">{{ $totalAgreements }}</h3>
                <span class="text-muted small fw-bold text-uppercase">PKS Aktif</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-orange text-center p-3 h-100 animate__animated animate__flipInX animate__fast" style="animation-delay: 0.3s;">
                <div class="stat-glow-icon bg-white text-warning mx-auto mb-3"><i class="ti tabler-map-pin ti-md"></i></div>
                <h3 class="fw-bolder text-warning mb-1">{{ $totalParkingLocations }}</h3>
                <span class="text-muted small fw-bold text-uppercase">Titik Parkir</span>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <a href="{{ route('masterdata.road-sections.index') }}" class="text-decoration-none d-block h-100">
                <div class="fintech-card fintech-card text-center p-3 h-100 animate__animated animate__slideInUp animate__fast" style="animation-delay: 0.4s;">
                    <div class="stat-glow-icon bg-white text-dark mx-auto mb-3"><i class="ti tabler-road ti-md"></i></div>
                    <h3 class="fw-bolder text-dark mb-1">{{ $totalRoadSections }}</h3>
                    <span class="text-muted small fw-bold text-uppercase">Ruas Jalan</span>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="fintech-card fintech-card-green text-center p-3 h-100 animate__animated animate__lightSpeedInRight animate__fast" style="animation-delay: 0.5s;">
                <div class="stat-glow-icon bg-white text-secondary mx-auto mb-3"><i class="ti tabler-user-pin ti-md"></i></div>
                <h3 class="fw-bolder text-secondary mb-1">{{ $totalFieldCoordinators }}</h3>
                <span class="text-muted small fw-bold text-uppercase">Korlap</span>
            </div>
        </div>
    </div>

    {{-- ✅ 3. PENCARIAN CEPAT --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="p-3 border-bottom bg-transparent">
                    <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-zoom-in me-2 text-primary"></i>Pencarian Data Cepat</h6>
                </div>
                <div class="p-4">
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
            <div class="glass-card h-100 p-0 animate__animated animate__slideInLeft animate__delay-1s">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-transparent">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="ti tabler-map-pin text-danger me-2"></i>Peta Sampel Lokasi Parkir</h6>
                        <small class="text-muted">50 Titik Acak (Real-time)</small>
                    </div>
                    <a href="{{ route('masterdata.parking-locations.map') }}" class="btn btn-xs btn-danger rounded-pill shadow-sm">Lihat Peta Lengkap <i class="ti tabler-arrow-right ms-1"></i></a>
                </div>
                <div class="p-0">
                    <div id="leader-map" style="height: 400px; width: 100%; border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem; z-index: 1;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card h-100 p-0 animate__animated animate__slideInRight animate__delay-1s">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-warning bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-warning"><i class="ti tabler-map-pin-plus me-2"></i>Lokasi Terbaru</h6>
                    @if ($totalParkingLocations > 10)
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-xs btn-warning rounded-pill shadow-sm">Semua</a>
                    @endif
                </div>
                <div class="p-2" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse($recentParkingLocations as $loc)
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.parking-locations.show', $loc->id) }}'">
                                    <td class="py-2">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ Str::limit($loc->name, 25) }}</span>
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">{{ Str::limit($loc->roadSection->name ?? '-', 25) }}</small>
                                    </td>
                                    <td class="text-end py-2 align-middle">
                                        <span class="badge bg-label-dark rounded-pill fw-bold">{{ $loc->roadSection->zone ?? 'N/A' }}</span>
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
            <div class="glass-card h-100 p-0 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 1.2s;">
                <div class="p-4 border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Tren Setoran Tervalidasi</h6>
                        <small class="text-muted">Tahun {{ now()->year }}</small>
                    </div>
                    <div class="badge bg-label-success rounded-pill px-3 py-2"><i class="ti tabler-chart-line me-1"></i> Stabil</div>
                </div>
                <div class="p-4"><div id="deposit-chart"></div></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card h-100 p-0 animate__animated animate__zoomIn animate__delay-1s" style="animation-delay: 1.4s;">
                <div class="p-4 border-bottom bg-transparent">
                    <h6 class="mb-0 fw-bold text-dark">Kepadatan Titik Parkir</h6>
                    <small class="text-muted">Top 10 Ruas Jalan</small>
                </div>
                <div class="p-4"><div id="locations-per-road-chart"></div></div>
            </div>
        </div>
    </div>

    {{-- ✅ 6. TABEL PKS (TERBARU + SEGERA BERAKHIR) --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s" style="animation-delay: 1.6s;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-primary bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-primary"><i class="ti tabler-file-text me-2"></i>Kontrak PKS Terbaru</h6>
                    @if ($totalAgreements > 10)
                        <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-xs btn-primary rounded-pill shadow-sm">Lihat Semua</a>
                    @endif
                </div>
                <div class="p-2" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse($recentAgreements as $pks)
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.agreements.show', $pks->id) }}'">
                                    <td class="py-2">
                                        <span class="fw-bold text-primary">{{ $pks->agreement_number }}</span>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($pks->start_date)->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td class="py-2 align-middle"><span class="fw-medium text-dark">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</span></td>
                                    <td class="text-center py-2 align-middle"><span class="badge bg-label-info rounded-pill">{{ $pks->active_parking_locations_count }} Titik</span></td>
                                    <td class="text-end py-2 align-middle"><span class="fw-bold text-success">Rp {{ number_format($pks->monthly_deposit_target, 0, ',', '.') }}</span></td>
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
            <div class="glass-card h-100 p-0 animate__animated animate__fadeInUp animate__delay-1s" style="animation-delay: 1.8s;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-warning bg-opacity-10" style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h6 class="fw-bold mb-0 text-warning"><i class="ti tabler-alert-triangle me-2"></i>PKS Segera Berakhir</h6>
                    <span class="badge bg-warning bg-opacity-25 text-warning fw-bold">30 Hari</span>
                </div>
                <div class="p-2" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-borderless premium-table mb-0">
                        <tbody>
                            @forelse($expiringAgreements as $pks)
                                @php $daysLeft = now()->diffInDays($pks->end_date, false); @endphp
                                <tr class="premium-list-item" style="cursor: pointer;" onclick="window.location='{{ route('masterdata.agreements.show', $pks->id) }}'">
                                    <td class="py-2"><span class="fw-bold text-warning">{{ $pks->agreement_number }}</span></td>
                                    <td class="py-2 align-middle text-dark">{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                    <td class="text-end py-2 align-middle"><span class="badge {{ $daysLeft <= 7 ? 'bg-label-danger' : 'bg-label-warning' }} fw-bold">{{ (int)$daysLeft }} hari</span></td>
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
                    chart: { 
                        type: 'bar', height: 320, parentHeightOffset: 0, toolbar: { show: false }, fontFamily: 'Public Sans, sans-serif',
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const ids = @json($barChartData['ids'] ?? []);
                                const id = ids[config.dataPointIndex];
                                if (id) {
                                    let url = "{{ route('masterdata.road-sections.show', ':id') }}";
                                    window.location.href = url.replace(':id', id);
                                }
                            }
                        }
                    },
                    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4, distributed: true } },
                    dataLabels: { enabled: false },
                    series: [{ name: 'Jumlah Titik', data: @json($barChartData['data']) }],
                    xaxis: { categories: @json($barChartData['labels']), labels: { style: { colors: '#a1acb8' } } },
                    yaxis: { labels: { style: { colors: '#a1acb8', fontSize: '12px' } } },
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'],
                    grid: { show: false },
                    legend: { show: false },
                    states: { hover: { filter: { type: 'darken', value: 0.9 } }, active: { filter: { type: 'darken', value: 0.8 } } }
                }).render();
                // Add pointer cursor
                document.querySelector("#locations-per-road-chart").style.cursor = "pointer";
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
                            const imageHtml = loc.image 
                                ? `<img src="/storage/${loc.image}" alt="Location" style="width: 100%; height: 100%; object-fit: cover;">` 
                                : `<i class="ti tabler-map-pin-filled ti-sm"></i>`;
                                
                            let popupContent = `
                                <div class="p-2 text-center" style="min-width: 180px;">
                                    <div class="mb-2 mx-auto d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle overflow-hidden shadow-sm" style="width: 56px; height: 56px;">
                                        ${imageHtml}
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">${loc.name}</h6>
                                    <p class="small text-muted mb-3"><i class="ti tabler-road align-middle text-primary me-1"></i> ${roadName} <br> <span class="badge bg-label-dark mt-2 px-3 py-1 rounded-pill shadow-sm">Zona ${zone}</span></p>
                                    <a href="${url}" class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm">Lihat Detail <i class="ti tabler-arrow-right ms-1 icon-xs"></i></a>
                                </div>
                            `;
                            marker.bindPopup(popupContent, { className: 'premium-leaflet-popup' });
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