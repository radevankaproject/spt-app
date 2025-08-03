@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="row g-6">
        {{-- ✅ BAGIAN PENCARIAN BARU DENGAN SELECT2 --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4 align-items-end">
                        <!-- Pencarian PKS -->
                        <div class="col-md-4">
                            <label for="pks-search-select" class="form-label">Cari PKS (No / Korlap)</label>
                            <select id="pks-search-select" class="form-select"></select>
                        </div>
                        <!-- Pencarian Titik Lokasi -->
                        <div class="col-md-4">
                            <label for="location-search-select" class="form-label">Cari Titik Lokasi</label>
                            <select id="location-search-select" class="form-select"></select>
                        </div>
                        <!-- Pencarian Setoran -->
                        <div class="col-md-4">
                            <label for="deposit-search-select" class="form-label">Cari Setoran (6 Digit Terakhir
                                Ref)</label>
                            <select id="deposit-search-select" class="form-select"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget yang sudah ada (tidak berubah) --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg me-4"><img
                            src="{{ $currentLeader && $currentLeader->user->img ? asset($currentLeader->user->img) : asset('assets/img/avatars/1.png') }}"
                            alt="Avatar" class="rounded-circle"></div>
                    <div>
                        <h5 class="mb-0">Selamat Datang, {{ $currentLeader->user->name ?? 'Pimpinan' }}</h5><small
                            class="text-muted">NIP: {{ $currentLeader->employee_number ?? '-' }}</small>
                        <p class="mb-0">Mulai Menjabat: {{ $currentLeader->start_date->translatedFormat('d F Y') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="text-sm fw-medium text-muted mb-2">TOTAL SETORAN TAHUN INI</h5>
                    <p class="text-3xl fw-bold">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0 me-2">Grafik Setoran Tervalidasi ({{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    <div id="deposit-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top 10 Ruas Jalan (by Titik Lokasi)</h5>
                </div>
                <div class="card-body">
                    <div id="locations-per-road-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">PKS Terbaru</h5>
                    @if ($totalAgreements > 10)
                        <a href="{{ route('masterdata.agreements.index') }}" class="badge bg-label-primary">Lihat Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 280px;">
                    <table class="table table-sm">
                        <tbody>
                            @forelse($recentAgreements as $pks)
                                <tr>
                                    <td>{{ $pks->agreement_number }}</td>
                                    <td>{{ $pks->fieldCoordinator->user->name ?? 'N/A' }}</td>
                            </tr>@empty<tr>
                                    <td class="text-center">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lokasi Terbaru</h5>
                    @if ($totalParkingLocations > 10)
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="badge bg-label-primary">Lihat
                            Semua</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap" style="max-height: 280px;">
                    <table class="table table-sm">
                        <tbody>
                            @forelse($recentParkingLocations as $loc)
                                <tr>
                                    <td>{{ $loc->name }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $loc->roadSection->zone }}</span></td>
                            </tr>@empty<tr>
                                    <td class="text-center">Tidak ada data.</td>
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
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Grafik Setoran
            const depositChartEl = document.querySelector("#deposit-chart");
            if (depositChartEl) {
                new ApexCharts(depositChartEl, {
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Total Setoran',
                        data: @json($depositChartData)
                    }],
                    xaxis: {
                        categories: @json($depositChartLabels)
                    },
                    colors: [config.colors.success]
                }).render();
            }

            // Grafik Lokasi
            const barChartEl = document.querySelector("#locations-per-road-chart");
            if (barChartEl) {
                new ApexCharts(barChartEl, {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '70%',
                            borderRadius: 5
                        }
                    },
                    series: [{
                        name: 'Jumlah Titik',
                        data: @json($barChartData['data'])
                    }],
                    xaxis: {
                        categories: @json($barChartData['labels'])
                    },
                    colors: [config.colors.info]
                }).render();
            }

            // ✅ FUNGSI UNTUK MENGARAHKAN KE HALAMAN DETAIL
            function redirectToPage(event, baseUrl) {
                const data = event.params.data;
                if (data.id) {
                    let url = baseUrl.replace(':id', data.id);
                    window.location.href = url;
                }
            }

            // Inisialisasi Select2 untuk Pencarian PKS
            $('#pks-search-select').select2({
                placeholder: 'Ketik No. PKS / Nama Korlap...',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('masterdata.search-agreements-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({
                        q: p.term
                    }),
                    processResults: d => ({
                        results: d.results
                    }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e, '{{ route('masterdata.agreements.show', ':id') }}'));

            // ✅ Inisialisasi Select2 untuk Pencarian Titik Lokasi
            $('#location-search-select').select2({
                placeholder: 'Ketik nama titik lokasi...',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('masterdata.search-locations-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({
                        q: p.term
                    }),
                    processResults: d => ({
                        results: d.results
                    }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e,
                '{{ route('masterdata.parking-locations.show', ':id') }}'));

            // ✅ Inisialisasi Select2 untuk Pencarian Setoran
            $('#deposit-search-select').select2({
                placeholder: 'Ketik 6 digit terakhir No. Referensi...',
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('masterdata.search-deposits-ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: p => ({
                        q: p.term
                    }),
                    processResults: d => ({
                        results: d.results
                    }),
                    cache: true
                }
            }).on('select2:select', e => redirectToPage(e,
                '{{ route('masterdata.deposit-transactions.show', ':id') }}'));
        });
    </script>
@endpush
