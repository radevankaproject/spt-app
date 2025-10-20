@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    {{-- Style tambahan untuk perfect-scrollbar --}}
    <style>
        .perfect-scrollbar-table {
            position: relative;
            max-height: 350px;
            /* Atur tinggi maksimal tabel */
        }
    </style>
@endpush

@section('content')
    {{-- ✅ PERUBAHAN DI SINI: Form Pencarian dengan Tombol --}}
    <div class="card mb-6">
        <div class="card-body">
            <form action="{{ route('admin.agreements.find') }}" method="POST">
                @csrf
                <label for="search-agreement" class="form-label fw-medium">Pencarian Cepat PKS</label>
                <div class="input-group">
                    <input type="search" id="search-agreement" name="agreement_number" class="form-control"
                        placeholder="Masukkan Nomor PKS..." required>
                    <button class="btn btn-primary" type="submit" id="button-addon2">
                        <i class="icon-base ri ri-search-line"></i>
                    </button>
                </div>
                @if (session('error'))
                    <div class="text-danger small mt-1">{{ session('error') }}</div>
                @endif
            </form>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Grafik Setoran Tervalidasi (Tahun {{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    <div id="deposit-mixed-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="row g-6">
                <div class="col-md-6 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-4">
                                        @if (
                                            $currentLeader &&
                                                $currentLeader->user &&
                                                $currentLeader->user->img &&
                                                file_exists(public_path($currentLeader->user->img)))
                                            <img src="{{ asset($currentLeader->user->img) }}" alt="Avatar"
                                                class="rounded-circle">
                                        @else
                                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar"
                                                class="rounded-circle">
                                        @endif
                                    </div>
                                    <div class="me-2">
                                        <h5 class="mb-0">{{ $currentLeader->user->name ?? 'Belum Ada' }}</h5>
                                        <p class="card-subtitle mb-0">Pimpinan Saat Ini</p>
                                    </div>
                                </div>
                                <span class="badge bg-label-primary">Pihak Pertama</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($activeBankAccount)
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="mb-0">Rekening BLUD Aktif</p>
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 me-2">{{ $activeBankAccount->account_number }}</h5>
                                            <span
                                                class="badge bg-label-success rounded-pill">{{ $activeBankAccount->bank_name }}</span>
                                        </div>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-secondary"><i
                                                class="icon-base ri-bank-line ri-22px"></i></span>
                                    </div>
                                </div>
                            @else
                                <p class="text-center mb-0">Tidak ada rekening BLUD yang aktif.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="text-sm font-medium text-default-600 mb-2">TOTAL SETORAN TAHUN INI</h5>
                            <p class="text-3xl font-bold text-default-900">Rp
                                {{ number_format($currentYearValidatedDeposit, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Per Zona</h5>
                </div>
                <div class="card-body">
                    <div class="row g-6 align-items-center">
                        <div class="col-md-6">
                            <div id="road-section-zone-chart"></div>
                            <p class="text-center fw-medium mt-4">Total Ruas Jalan</p>
                        </div>
                        <div class="col-md-6">
                            <div id="parking-location-zone-chart"></div>
                            <p class="text-center fw-medium mt-4">Total Titik Lokasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top 10 Ruas Jalan (by Titik Lokasi)</h5>
                </div>
                <div class="card-body">
                    <div id="locations-per-road-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row g-6">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Setoran Terbaru</h5>
                        </div>
                        {{-- ✅ PERUBAHAN 2: Tambahkan class untuk Perfect Scrollbar --}}
                        <div class="table-responsive text-nowrap perfect-scrollbar-table">
                            <table class="table table-sm">
                                <tbody>
                                    @forelse ($recentDeposits as $deposit)
                                        <tr>
                                            <td>{{ Str::limit($deposit->agreement->fieldCoordinator->user->name, 15) ?? 'N/A' }}
                                            </td>
                                            <td><span class="fw-medium">Rp
                                                    {{ number_format($deposit->amount, 0, ',', '.') }}</span></td>
                                            <td class="text-end">{{ $deposit->deposit_date->format('d M') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Tidak ada data setoran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center py-3"><a
                                href="{{ route('masterdata.deposit-transactions.index') }}" class="btn-link">Lihat Semua
                                Setoran</a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Lokasi Parkir Terbaru</h5>
                        </div>
                        <div class="table-responsive text-nowrap perfect-scrollbar-table">
                            <table class="table table-sm">
                                <tbody>
                                    @forelse ($recentParkingLocations as $location)
                                        <tr>
                                            <td>{{ Str::limit($location->name, 20) }}</td>
                                            <td><span
                                                    class="badge bg-label-info">{{ $location->roadSection->name ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">Tidak ada data lokasi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center py-3"><a
                                href="{{ route('masterdata.parking-locations.index') }}" class="btn-link">Lihat Semua
                                Lokasi</a></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Koordinator Terbaru</h5>
                        </div>
                        <div class="table-responsive text-nowrap perfect-scrollbar-table">
                            <table class="table table-sm">
                                <tbody>
                                    @forelse ($recentCoordinators as $coordinator)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2"><img
                                                           src="{{ $coordinator->user && $coordinator->user->img ? asset('storage/' . $coordinator->user->img) : strtoupper(substr($coordinator->user->name ?? 'K', 0, 2)) }}"
                                                            alt="Avatar" class="rounded-circle"></div>
                                                    <span>{{ Str::limit($coordinator->user->name, 15) }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $coordinator->phone_number }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">Tidak ada data koordinator.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center py-3"><a href="{{ route('admin.field-coordinators.index') }}"
                                class="btn-link">Lihat Semua Korlap</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ... (script untuk grafik tidak berubah) ...
            const primaryColor = config.colors.primary;
            const infoColor = config.colors.info;
            const warningColor = config.colors.warning;
            const successColor = config.colors.success;
            const dangerColor = config.colors.danger;

            // 1. Mixed Chart: Setoran per Bulan
            const mixedChartEl = document.querySelector("#deposit-mixed-chart");
            if (mixedChartEl) {
                const mixedChartOptions = {
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false,
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        width: [0, 2, 4],
                        curve: 'smooth'
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '50%'
                        }
                    },
                    series: [{
                            name: 'Setoran (Rp)',
                            type: 'bar',
                            data: @json($mainChartData)
                        },
                        {
                            name: 'Target (Contoh)',
                            type: 'line',
                            data: [2000000, 2200000, 2500000, 2100000, 2800000, 3000000, 3200000, 3500000,
                                3100000, 2900000, 3300000, 3800000
                            ]
                        }
                    ],
                    xaxis: {
                        categories: @json($mainChartLabels)
                    },
                    yaxis: [{
                        title: {
                            text: 'Rupiah (Rp)'
                        },
                        labels: {
                            formatter: val => `Rp ${(val / 1000000).toFixed(1)} Jt`
                        }
                    }],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: (val) => "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        }
                    },
                    colors: [primaryColor, dangerColor]
                };
                new ApexCharts(mixedChartEl, mixedChartOptions).render();
            }

            // 2. Polar Area: Ruas Jalan per Zona
            const roadSectionZoneEl = document.querySelector("#road-section-zone-chart");
            if (roadSectionZoneEl) {
                const roadSectionZoneOptions = {
                    series: @json($zoneChartData['roadSections']),
                    chart: {
                        height: 280,
                        type: 'polarArea'
                    },
                    labels: @json($zoneChartData['labels']),
                    stroke: {
                        colors: ['#fff']
                    },
                    fill: {
                        opacity: 0.8
                    },
                    colors: [primaryColor, successColor]
                };
                new ApexCharts(roadSectionZoneEl, roadSectionZoneOptions).render();
            }

            // 3. Polar Area: Lokasi per Zona
            const parkingLocationZoneEl = document.querySelector("#parking-location-zone-chart");
            if (parkingLocationZoneEl) {
                const parkingLocationZoneOptions = {
                    series: @json($zoneChartData['parkingLocations']),
                    chart: {
                        height: 280,
                        type: 'polarArea'
                    },
                    labels: @json($zoneChartData['labels']),
                    stroke: {
                        colors: ['#fff']
                    },
                    fill: {
                        opacity: 0.8
                    },
                    colors: [infoColor, warningColor]
                };
                new ApexCharts(parkingLocationZoneEl, parkingLocationZoneOptions).render();
            }

            // 4. Bar Chart: Lokasi per Ruas Jalan
            const locationsPerRoadEl = document.querySelector("#locations-per-road-chart");
            if (locationsPerRoadEl) {
                const locationsPerRoadOptions = {
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '60%',
                            borderRadius: 5
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: [{
                        name: 'Jumlah Titik',
                        data: @json($barChartData['data'])
                    }],
                    xaxis: {
                        categories: @json($barChartData['labels'])
                    },
                    colors: [config.colors.primary]
                };
                new ApexCharts(locationsPerRoadEl, locationsPerRoadOptions).render();
            }

            // ✅ PERUBAHAN 3: Inisialisasi Perfect Scrollbar
            const scrollableTables = document.querySelectorAll('.perfect-scrollbar-table');
            if (scrollableTables.length) {
                scrollableTables.forEach(el => {
                    new PerfectScrollbar(el, {
                        wheelPropagation: false
                    });
                });
            }
        });
    </script>
@endpush
