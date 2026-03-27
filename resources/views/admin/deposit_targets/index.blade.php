@extends('layouts.app')

@section('title', 'Target Setoran Bulanan & Tahunan')

@push('styles')
    {{-- Load CSS ApexCharts bawaan template --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <style>
        /* Styling Accordion Premium */
        .premium-accordion .accordion-item {
            border: none;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.2rem;
            overflow: hidden;
        }

        .premium-accordion .accordion-button {
            background-color: #fff;
            padding: 1.25rem 1.5rem;
            border-radius: 12px !important;
            transition: all 0.3s ease;
        }

        .premium-accordion .accordion-button:not(.collapsed) {
            background-color: #f8faff;
            color: #696cff;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.05);
        }

        .premium-accordion .accordion-button:focus {
            box-shadow: none;
        }

        .year-badge {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .total-target-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #16a34a;
        }

        .sticky-form-card {
            position: sticky;
            top: 25px;
            z-index: 10;
            transition: all 0.3s ease-in-out;
        }
    </style>
@endpush

{{-- ✅ PANGGIL SKELETON LOADER DI SINI --}}
@section('skeleton')
    @include('layouts.partials._skeleton-deposit-targets')
@endsection

@section('content')

    {{-- Header & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            {{-- ✅ ICON DIPERBAIKI --}}
            <h4 class="fw-bold mb-1"><i class="ri icon-base ri-line-chart-line me-2 text-primary ri-22px"></i> Target Pendapatan
                Setoran</h4>
            <p class="text-muted mb-0">Kelola dan proyeksikan target setoran bulanan dan tahunan UPT.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Target Setoran</li>
            </ol>
        </nav>
    </div>

    {{-- GRAFIK APEXCHARTS PREMIUM --}}
    @if ($targets->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom pb-3">
                        {{-- ✅ ICON DIPERBAIKI --}}
                        <h5 class="card-title mb-0"><i
                                class="ri icon-base ri-bar-chart-grouped-line me-2 text-success ri-20px"></i> Grafik Proyeksi
                            Target (3 Tahun Terakhir)</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div id="targetChart"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: FORMULIR INPUT (Sticky) --}}
        <div class="col-lg-4 col-md-5">
            <div class="card sticky-form-card shadow-sm border-0" id="formContainer">
                <div class="card-header bg-primary text-white d-flex align-items-center rounded-top">
                    {{-- ✅ ICON DIPERBAIKI --}}
                    <i class="ri icon-base ri-target-line me-2 ri-24px text-white"></i>
                    <h5 class="card-title text-white mb-0">Set / Update Target</h5>
                </div>

                <form action="{{ route('masterdata.deposit-targets.store') }}" method="POST" id="targetForm">
                    @csrf
                    <div class="card-body mt-4">
                        <p class="text-muted text-sm mb-4">Pilih tahun dan bulan, lalu masukkan nominal target. Data bulan
                            yang sudah ada akan <strong class="text-primary">otomatis diperbarui</strong>.</p>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control fw-bold text-primary" id="year"
                                        name="year" placeholder="2026" value="{{ date('Y') }}" min="2020"
                                        max="2100" required />
                                    <label for="year">Tahun Proyeksi</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2" id="month" name="month" required>
                                        <option value="" disabled selected>-- Pilih Bulan --</option>
                                        @php
                                            $months = [
                                                1 => 'Januari',
                                                2 => 'Februari',
                                                3 => 'Maret',
                                                4 => 'April',
                                                5 => 'Mei',
                                                6 => 'Juni',
                                                7 => 'Juli',
                                                8 => 'Agustus',
                                                9 => 'September',
                                                10 => 'Oktober',
                                                11 => 'November',
                                                12 => 'Desember',
                                            ];
                                        @endphp
                                        @foreach ($months as $num => $name)
                                            <option value="{{ $num }}">{{ $num }} - {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="month">Bulan</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light text-primary fw-bold">Rp</span>
                                    <div class="form-floating form-floating-outline">
                                        {{-- INPUT DISPLAY UNTUK USER (Dengan Titik) --}}
                                        <input type="text" class="form-control fs-5 fw-semibold"
                                            id="target_amount_display" placeholder="0" required
                                            oninput="formatCurrency(this)" autocomplete="off" />

                                        {{-- INPUT HIDDEN UNTUK DATABASE (Tanpa Titik) --}}
                                        <input type="hidden" id="target_amount" name="target_amount" value="0"
                                            required>

                                        <label for="target_amount_display">Nominal Target</label>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Angka akan otomatis diformat.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end border-top pt-3 pb-3">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">Batal</button>
                        {{-- ✅ ICON DIPERBAIKI --}}
                        <button type="submit" class="btn btn-primary"><i class="ri icon-base ri-save-3-line me-1 ri-18px"></i>
                            Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: DATA AKUMULASI (Accordion) --}}
        <div class="col-lg-8 col-md-7">

            @if ($targets->isEmpty())
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}" alt="No Data"
                            class="img-fluid mb-4" width="150">
                        <h5>Belum Ada Target Tersimpan</h5>
                        <p class="text-muted">Silakan atur target pertama Anda menggunakan formulir di sebelah kiri.</p>
                    </div>
                </div>
            @else
                <div class="accordion premium-accordion" id="yearlyTargetAccordion">

                    @foreach ($targets as $index => $yearly)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $yearly->id }}">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $yearly->id }}"
                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">

                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md">
                                                {{-- ✅ ICON DIPERBAIKI --}}
                                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                                        class="ri icon-base ri-calendar-2-line ri-24px"></i></span>
                                            </div>
                                            <div>
                                                <span class="text-muted mb-0 d-block" style="font-size: 0.8rem;">Tahun
                                                    Proyeksi</span>
                                                <span class="year-badge text-primary">{{ $yearly->year }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-muted mb-0 d-block" style="font-size: 0.8rem;">Total Target
                                                Tahunan</span>
                                            <span class="total-target-text">Rp
                                                {{ number_format($yearly->total_target, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse-{{ $yearly->id }}"
                                class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                data-bs-parent="#yearlyTargetAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped m-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" width="10%">Bulan</th>
                                                    <th width="30%">Nama Bulan</th>
                                                    <th class="text-end" width="40%">Target Disetorkan</th>
                                                    <th class="text-center" width="20%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($yearly->monthlyTargets->sortBy('month') as $monthly)
                                                    <tr>
                                                        <td class="text-center fw-bold">
                                                            {{ str_pad($monthly->month, 2, '0', STR_PAD_LEFT) }}</td>
                                                        <td>{{ $months[$monthly->month] }}</td>
                                                        <td class="text-end fw-semibold text-dark">
                                                            Rp {{ number_format($monthly->target_amount, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{-- ✅ ICON DIPERBAIKI (icon-base ri-edit-box-line ri-18px) --}}
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary btn-icon rounded-pill shadow-sm"
                                                                onclick="editTarget({{ $yearly->year }}, {{ $monthly->month }}, {{ $monthly->target_amount }})"
                                                                data-bs-toggle="tooltip" title="Edit Target Ini">
                                                                <i class="ri icon-base ri-edit-box-line ri-18px"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-3">Belum ada
                                                            rincian bulan.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Load JS ApexCharts bawaan template --}}
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    {{-- Setup Data Chart dari Backend (Ambil 3 tahun terakhir agar grafiknya cantik) --}}
    @php
        $chartSeries = $targets
            ->take(3)
            ->map(function ($y) {
                $monthlyData = collect(range(1, 12))
                    ->map(function ($m) use ($y) {
                        $target = $y->monthlyTargets->firstWhere('month', $m);
                        return $target ? (int) $target->target_amount : 0;
                    })
                    ->values();

                return [
                    'name' => 'Tahun ' . $y->year,
                    'data' => $monthlyData,
                ];
            })
            ->values()
            ->toJson();
    @endphp

    <script>
        // 1. FORMAT RUPIAH OTOMATIS (Tanpa merubah value real di database)
        function formatCurrency(input) {
            let rawValue = input.value.replace(/\D/g, '');
            document.getElementById('target_amount').value = rawValue || 0;

            if (rawValue) {
                input.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                input.value = '';
            }
        }

        // 2. FUNGSI MAGIC EDIT
        function editTarget(year, month, amount) {
            document.getElementById('year').value = year;
            document.getElementById('month').value = month;

            document.getElementById('target_amount').value = amount;
            document.getElementById('target_amount_display').value = new Intl.NumberFormat('id-ID').format(amount);

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            const formCard = document.getElementById('formContainer');

            formCard.classList.add('shadow-lg', 'border', 'border-primary');
            setTimeout(() => {
                formCard.classList.remove('shadow-lg', 'border', 'border-primary');
                document.getElementById('target_amount_display').focus();
            }, 800);
        }

        function resetForm() {
            document.getElementById('targetForm').reset();
            document.getElementById('year').value = new Date().getFullYear();
            document.getElementById('target_amount').value = 0;
        }

        // 3. INISIALISASI APEXCHARTS
        document.addEventListener('DOMContentLoaded', function() {

            // Tooltip setup
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el)
            });

            // Render Grafik Jika Data Ada
            @if ($targets->isNotEmpty())
                const chartOptions = {
                    series: {!! $chartSeries !!},
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Work Sans, sans-serif'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ],
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                if (value >= 1000000) {
                                    return "Rp " + (value / 1000000).toFixed(1) + " Jt";
                                }
                                return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    colors: ['#696cff', '#03c3ec', '#71dd37'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left'
                    }
                };

                const chart = new ApexCharts(document.querySelector("#targetChart"), chartOptions);
                chart.render();
            @endif
        });
    </script>
@endpush
