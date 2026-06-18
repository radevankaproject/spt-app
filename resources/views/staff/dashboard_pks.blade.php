@extends('layouts.contentNavbarLayout')
@section('title', 'Dashboard Staff PKS')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('staff-pks.dashboard') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="q" value="{{ $search }}"
                        placeholder="Cari Titik Lokasi, PKS, atau Korlap..." class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
            @if ($search)
                <hr>
                <h6>Hasil Pencarian:</h6>
                <b>Lokasi:</b>
                <ul>
                    @forelse($searchResults['locations'] as $loc)
                        <li>{{ $loc->name }} - {{ $loc->roadSection->name ?? '-' }}</li>
                    @empty
                        <li><i>Tidak ada</i></li>
                    @endforelse
                </ul>
                <b>PKS:</b>
                <ul>
                    @forelse($searchResults['agreements'] as $pks)
                        <li>{{ $pks->agreement_number }} - {{ $pks->fieldCoordinator->user->name ?? '-' }}</li>
                    @empty
                        <li><i>Tidak ada</i></li>
                    @endforelse
                </ul>
                <b>Koordinator:</b>
                <ul>
                    @forelse($searchResults['coordinators'] as $korlap)
                        <li>{{ $korlap->user->name ?? '-' }}</li>
                    @empty
                        <li><i>Tidak ada</i></li>
                    @endforelse
                </ul>
            @endif
        </div>
    </div>

    {{-- Card pimpinan --}}
    @if ($currentLeader && $currentLeader->user)
        <div class="card mb-3">
            <div class="card-body d-flex align-items-center">
                <img src="{{ asset($currentLeader->user->img ?? 'assets/img/illustrations/faq-illustration.png') }}"
                    width="100" class="rounded me-3" alt="Foto Pimpinan">
                <div>
                    <h5>{{ $currentLeader->user->name }}</h5>
                    <div>NIP: {{ $currentLeader->employee_number }}</div>
                    <div>Jabatan: Pimpinan BLUD UPT Perparkiran</div>
                    <div>Mulai Menjabat: {{ $startDate ? $startDate->translatedFormat('d F Y') : '-' }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">10 Lokasi Terbaru</div>
                <ul class="list-group list-group-flush">
                    @foreach ($recentLocations as $loc)
                        <li class="list-group-item">
                            {{ $loc->name }} <span class="badge bg-info">{{ $loc->roadSection->zone ?? '-' }}</span>
                        </li>
                    @endforeach
                </ul>
                @if ($totalLocations > 10)
                    <div class="card-footer">
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="badge bg-primary">Lihat
                            Selengkapnya</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">10 PKS Terbaru</div>
                <ul class="list-group list-group-flush">
                    @foreach ($recentAgreements as $pks)
                        <li class="list-group-item">
                            {{ $pks->agreement_number }} - {{ $pks->fieldCoordinator->user->name ?? '-' }}
                        </li>
                    @endforeach
                </ul>
                @if ($totalAgreements > 10)
                    <div class="card-footer">
                        <a href="{{ route('masterdata.agreements.index') }}" class="badge bg-primary">Lihat
                            Selengkapnya</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Grafik Jumlah Lokasi per Ruas Jalan</div>
        <div class="card-body">
            <div id="locations-bar-chart"></div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}" defer></script>
    <script>
        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Lokasi',
                data: @json($barChartData['data']),
            }],
            xaxis: {
                categories: @json($barChartData['labels'])
            }
        };
        new ApexCharts(document.querySelector("#locations-bar-chart"), options).render();
    </script>
@endsection
