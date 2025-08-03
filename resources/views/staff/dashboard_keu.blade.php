@extends('layouts.app')
@section('title', 'Dashboard Staff Keuangan')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('staff-keuangan.dashboard') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="ref" value="{{ $search }}"
                        placeholder="Cari setoran (6 digit akhir no referensi)..." class="form-control" maxlength="6">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
            @if ($search)
                <hr>
                <h6>Hasil Setoran:</h6>
                <ul>
                    @forelse($searchDeposits as $dep)
                        <li>
                            {{ $dep->agreement->agreement_number ?? '-' }} -
                            {{ $dep->agreement->fieldCoordinator->user->name ?? '-' }} |
                            Rp{{ number_format($dep->amount, 0, ',', '.') }}
                        </li>
                    @empty
                        <li><i>Tidak ada data</i></li>
                    @endforelse
                </ul>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6>Jumlah Setoran Bulan Ini</h6>
                    <h2>Rp{{ number_format($currentMonthDeposit, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6>Jumlah Setoran Tahun {{ date('Y') }}</h6>
                    <h2>Rp{{ number_format($currentYearDeposit, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Grafik Setoran ({{ date('Y') }})</div>
        <div class="card-body">
            <div id="deposit-chart"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">PKS Sudah Setor Bulan Ini</div>
                <ul class="list-group list-group-flush">
                    @foreach ($agreementsPaid as $a)
                        <li class="list-group-item">
                            {{ $a->agreement_number }} - {{ $a->fieldCoordinator->user->name ?? '-' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">PKS Belum Setor Bulan Ini</div>
                <ul class="list-group list-group-flush">
                    @foreach ($agreementsUnpaid as $a)
                        <li class="list-group-item">
                            {{ $a->agreement_number }} - {{ $a->fieldCoordinator->user->name ?? '-' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Setoran',
                data: @json($mainChartData),
            }],
            xaxis: {
                categories: @json($mainChartLabels)
            }
        };
        new ApexCharts(document.querySelector("#deposit-chart"), options).render();
    </script>
@endpush
