@extends('layouts.app')
@section('title', 'Dashboard Staff PKS')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@section('content')
    <div class="row g-6">
        <div class="col-lg-8">
            <div class="card mb-6">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-4">
                            <img src="{{ $currentLeader && $currentLeader->user->img ? asset($currentLeader->user->img) : asset('assets/img/avatars/1.png') }}"
                                alt="Avatar" class="rounded-circle">
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $currentLeader->user->name ?? 'Belum Ada' }}</h5>
                            <small class="text-muted">Pimpinan Saat Ini (NIP:
                                {{ $currentLeader->employee_number ?? '-' }})</small>
                            <p class="mb-0">Mulai Menjabat:
                                {{ $currentLeader->start_date->translatedFormat('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top 10 Ruas Jalan (by Titik Lokasi)</h5>
                </div>
                <div class="card-body">
                    <div id="locations-per-road-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-6">
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
            <div class="card">
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
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                    colors: [config.colors.primary]
                }).render();
            }
        });
    </script>
@endpush
