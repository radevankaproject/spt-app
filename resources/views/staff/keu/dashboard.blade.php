@extends('layouts.app')
@section('title', 'Dashboard Staff Keuangan')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />@endpush

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0 me-2">Grafik Setoran Tervalidasi ({{ now()->year }})</h5>
            </div>
            <div class="card-body">
                <div id="deposit-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-6">
            <div class="card-body text-center">
                <h5 class="text-sm fw-medium text-muted mb-2">SETORAN BULAN INI</h5>
                <p class="text-3xl fw-bold text-success">Rp {{ number_format($depositThisMonth, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-sm fw-medium text-muted mb-2">TOTAL SETORAN TAHUN INI</h5>
                <p class="text-3xl fw-bold">Rp {{ number_format($depositThisYear, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Sudah Setor (Bulan Ini)</h5>
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 400px;">
                <table class="table table-sm">
                    <tbody>@forelse($paidAgreements as $pks)<tr>
                            <td>{{$pks->agreement_number}}</td>
                            <td>{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</td>
                        </tr>@empty<tr>
                            <td class="text-center p-4">Tidak ada data.</td>
                        </tr>@endforelse</tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0 text-danger">Belum Setor (Bulan Ini)</h5>
            </div>
            <div class="table-responsive text-nowrap" style="max-height: 400px;">
                <table class="table table-sm">
                    <tbody>@forelse($unpaidAgreements as $pks)<tr>
                            <td>{{$pks->agreement_number}}</td>
                            <td>{{$pks->fieldCoordinator->user->name ?? 'N/A'}}</td>
                        </tr>@empty<tr>
                            <td class="text-center p-4">Semua PKS sudah setor.</td>
                        </tr>@endforelse</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendors-js')<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>@endpush
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartEl = document.querySelector("#deposit-chart");
        if (chartEl) {
            new ApexCharts(chartEl, {
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
                yaxis: {
                    labels: {
                        formatter: (val) => `Rp ${(val / 1000000).toFixed(1)} Jt`
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val)
                    }
                },
                colors: [config.colors.primary]
            }).render();
        }
    });
</script>
@endpush