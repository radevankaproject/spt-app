@extends('layouts.app')

@section('title', 'Dashboard Bendahara')

@push('styles')
<style>
  /* ✅ CSS PREMIUM CUSTOM */
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
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
  }

  .glass-panel {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
  }

  .hover-link {
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .hover-link:hover {
    color: #696cff !important;
    transform: translateX(3px);
    display: inline-block;
  }
</style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-treasurer-dashboard')
@endsection

@php
    $treasurerName = explode(' ', Auth::user()->name)[0];
    $userAvatar = Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode($treasurerName).'&background=fff&color=696cff';
@endphp

@section('content')
{{-- ========================================== --}}
{{-- HEADER: WELCOME & GRAFIK APEXCHARTS --}}
{{-- ========================================== --}}
<div class="row g-4 mb-4">
  {{-- Welcome Card --}}
  <div class="col-xl-8 col-lg-7">
    <div class="card border-0 shadow-sm bg-gradient-primary text-white h-100" style="border-radius: 16px;">
      <div class="card-body p-4 p-md-5 d-flex align-items-center position-relative overflow-hidden">
        <div class="row w-100 align-items-center position-relative z-1">
          <div class="col-md-8 text-md-start text-center mb-4 mb-md-0">
            <span class="badge bg-white text-primary rounded-pill mb-3 fw-bold px-3 py-2 shadow-sm">Portal
              Bendahara</span>
            <h2 class="text-white fw-bold mb-2">Selamat datang, {{ $treasurerName }}! 👋</h2>
            <div class="badge border border-white text-white rounded-pill px-3 py-2 mb-3">
               <i class="ri ri-profile-line me-1 align-middle"></i> NIP: {{ Auth::user()->employee_number ? formatNip(Auth::user()->employee_number) : '-' }}
            </div>
            <p class="mb-4 opacity-75" style="font-size: 1.05rem;">
              Pusat kendali validasi setoran pendapatan parkir UPT. Pastikan untuk meneliti setiap bukti transfer sebelum
              mengesahkan transaksi.
            </p>

            {{-- Info Rekening (Glassmorphism) --}}
            <div class="glass-panel p-3 d-inline-flex align-items-center">
              <i class="ri ri-bank-card-2-line ri-2x me-3 opacity-75"></i>
              <div>
                <span class="d-block opacity-75 small text-uppercase fw-bold">Rekening Penerima (BLUD)</span>
                <span class="fw-bold fs-6">{{ $activeBankAccount->bank_name ?? 'N/A' }} - {{
                  $activeBankAccount->account_number ?? 'Belum diatur' }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 text-center text-md-end">
             <div class="d-inline-block position-relative rounded-circle p-1" style="background: linear-gradient(135deg, #f6d365 0%, #ffb142 100%);">
                 <img src="{{ $userAvatar }}" alt="Avatar" class="rounded-circle shadow-lg border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
             </div>
          </div>
        </div>
        
        <i class="ri ri-safe-2-line position-absolute text-white"
          style="font-size: 220px; right: -20px; bottom: -40px; opacity: 0.1; transform: rotate(-10deg);"></i>
      </div>
    </div>
  </div>

  {{-- ApexChart Card --}}
  <div class="col-xl-4 col-lg-5">
    <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
      <div class="card-header bg-transparent border-0 pb-0 text-center">
        <h6 class="fw-bold text-dark mb-0">Rasio Validasi (Bulan Ini)</h6>
        <small class="text-muted">Nominal Sah vs Pending</small>
      </div>
      <div class="card-body d-flex flex-column justify-content-center align-items-center">
        <div id="validationDonutChart" style="min-height: 200px;"></div>
      </div>
    </div>
  </div>
</div>

{{-- ========================================== --}}
{{-- STATISTIC CARDS --}}
{{-- ========================================== --}}
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 card-hover border-bottom border-warning border-3">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Antrean Validasi</h6>
            <h3 class="fw-bold text-dark mb-0">{{ $pendingValidationsCount }} <span
                class="fs-6 text-muted fw-normal">Trx</span></h3>
          </div>
          <div class="icon-shape bg-label-warning"><i class="ri ri-time-line ri-24px"></i></div>
        </div>
        <div class="d-flex align-items-center mt-1">
          <span class="badge bg-warning text-white rounded-pill px-2 py-1 small fw-bold shadow-sm"><i
              class="ri ri-alert-line align-bottom me-1"></i>Aksi Diperlukan</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 card-hover border-bottom border-danger border-3">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Nominal Pending</h6>
            <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</h3>
          </div>
          <div class="icon-shape bg-label-danger"><i class="ri ri ri-wallet-3-line ri-24px"></i></div>
        </div>
        <div class="mt-1">
          <small class="text-muted fw-medium">Uang yang belum disahkan ke sistem</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 card-hover border-bottom border-success border-3">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Kinerja Anda ({{
              now()->translatedFormat('M') }})</h6>
            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($validatedThisMonth, 0, ',', '.') }}</h3>
          </div>
          <div class="icon-shape bg-label-success"><i class="ri ri ri-check-double-line ri-24px"></i></div>
        </div>
        <div class="mt-1">
          <small class="text-muted fw-medium">Pendapatan sah bulan ini</small>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ========================================== --}}
{{-- DATA TABLES & TIMELINE --}}
{{-- ========================================== --}}
<div class="row g-4">
  {{-- KOLOM KIRI: MENUNGGU VALIDASI --}}
  <div class="col-xl-7 col-lg-7">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="ri ri-error-warning-fill me-2 text-warning ri-lg"></i>Antrean Validasi
          Terbaru</h6>
        <a href="{{ route('masterdata.deposit-transactions.index') }}"
          class="btn btn-sm btn-primary rounded-pill shadow-sm">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-lighter">
            <tr>
              <th class="text-uppercase" style="font-size: 0.75rem;">Detail Setoran</th>
              <th class="text-uppercase" style="font-size: 0.75rem;">Korlap / No PKS</th>
              <th class="text-end text-uppercase" style="font-size: 0.75rem;">Nominal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentPendingDeposits as $deposit)
            @php
            $refCode = $deposit->referal_code ?? 'TRX-'.str_pad($deposit->id, 5, '0', STR_PAD_LEFT);
            $detailRoute = Route::has('masterdata.deposit-transactions.show') ?
            route('masterdata.deposit-transactions.show', $deposit->id) : '#';
            @endphp
            <tr>
              <td>
                <a href="{{ $detailRoute }}" class="fw-bold text-primary d-block hover-link mb-1">{{ $refCode }}</a>
                <span class="text-muted small"><i class="ri ri-calendar-line align-bottom me-1"></i>{{
                  \Carbon\Carbon::parse($deposit->deposit_date)->translatedFormat('d M Y') }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-2">
                    <img
                      src="https://ui-avatars.com/api/?name={{ urlencode($deposit->agreement->fieldCoordinator->user->name ?? 'N/A') }}&background=random&color=fff"
                      class="rounded-circle">
                  </div>
                  <div>
                    <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{
                      Str::limit($deposit->agreement->fieldCoordinator->user->name ?? 'N/A', 15) }}</span>
                    <a href="{{ route('masterdata.agreements.show', $deposit->agreement_id) }}"
                      class="text-muted small hover-link">{{ $deposit->agreement->agreement_number ?? '-' }}</a>
                  </div>
                </div>
              </td>
              <td class="text-end">
                <span class="fw-bold text-dark d-block">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                <a href="{{ $detailRoute }}"
                  class="badge bg-label-warning rounded-pill mt-1 text-decoration-none">Review Sekarang</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center py-5">
                <div
                  class="avatar avatar-xl bg-label-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                  <i class="ri ri ri-check-double-line ri-2x"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Kerja Bagus!</h6>
                <p class="text-muted small mb-0">Tidak ada antrean setoran yang menunggu validasi saat ini.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- KOLOM KANAN: RIWAYAT TERAKHIR --}}
  <div class="col-xl-5 col-lg-5">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header border-bottom bg-transparent py-3">
        <h6 class="mb-0 fw-bold"><i class="ri ri-history-line me-2 text-info ri-lg"></i>Riwayat Validasi Anda</h6>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush timeline-validation">
          @forelse($recentValidatedDeposits as $val)
          @php
          $refCode = $val->referal_code ?? 'TRX-'.str_pad($val->id, 5, '0', STR_PAD_LEFT);
          $detailRoute = Route::has('masterdata.deposit-transactions.show') ?
          route('masterdata.deposit-transactions.show', $val->id) : '#';
          @endphp
          <li class="list-group-item d-flex justify-content-between align-items-center p-4 card-hover">
            <div class="d-flex align-items-center">
              <div
                class="avatar avatar-sm bg-label-success rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                <i class="ri ri-check-line"></i>
              </div>
              <div>
                <a href="{{ $detailRoute }}" class="mb-0 fw-bold text-dark d-block hover-link"
                  style="font-size: 0.9rem;">{{ Str::limit($val->agreement->fieldCoordinator->user->name ?? 'N/A', 18)
                  }}</a>
                <small class="text-muted">{{ $refCode }}</small>
              </div>
            </div>
            <div class="text-end">
              <h6 class="mb-0 fw-bold text-success">+ Rp {{ number_format($val->amount, 0, ',', '.') }}</h6>
              <small class="text-muted d-flex align-items-center justify-content-end" style="font-size: 0.65rem;">
                <i class="ri ri-time-line me-1"></i> {{ \Carbon\Carbon::parse($val->validation_date)->diffForHumans() }}
              </small>
            </div>
          </li>
          @empty
          <li class="list-group-item text-center py-5 border-0">
            <i class="ri ri-file-list-3-line ri-3x text-muted opacity-50 mb-2 d-block"></i>
            <span class="text-muted small">Anda belum memvalidasi transaksi apapun.</span>
          </li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- ✅ IMPORT APEXCHARTS --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
        
        // Ambil data dari Controller PHP ke dalam JavaScript
        const pendingAmount = {{ $pendingAmount ?? 0 }};
        const validatedAmount = {{ $validatedThisMonth ?? 0 }};
        
        // Cegah error chart jika semua data 0
        const seriesData = (pendingAmount === 0 && validatedAmount === 0) ? [0, 1] : [pendingAmount, validatedAmount];
        const chartColors = (pendingAmount === 0 && validatedAmount === 0) ? ['#e7e7eb', '#e7e7eb'] : ['#ffab00', '#71dd37'];

        // Konfigurasi ApexCharts Donut
        var options = {
            series: seriesData,
            labels: ['Tertunda (Pending)', 'Sah (Bulan Ini)'],
            chart: {
                type: 'donut',
                height: 240,
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: { enabled: true, delay: 150 },
                    dynamicAnimation: { enabled: true, speed: 350 }
                }
            },
            colors: chartColors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { fontSize: '0.85rem', color: '#a1acb8' },
                            value: {
                                fontSize: '1.2rem',
                                fontWeight: 700,
                                color: '#566a7f',
                                formatter: function (val) {
                                    // Format angka ke Rupiah saat di hover/tengah
                                    if(val === 1 && pendingAmount === 0 && validatedAmount === 0) return "Rp 0";
                                    return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total Rp Diproses',
                                color: '#a1acb8',
                                formatter: function (w) {
                                    const total = pendingAmount + validatedAmount;
                                    return "Rp " + new Intl.NumberFormat('id-ID').format(total);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false }, // Matikan label di atas chart biar clean
            stroke: { show: true, colors: ['#fff'], width: 3 },
            legend: {
                show: true,
                position: 'bottom',
                markers: { offsetX: -2 },
                itemMargin: { horizontal: 10, vertical: 0 }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        if(value === 1 && pendingAmount === 0 && validatedAmount === 0) return "Rp 0";
                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#validationDonutChart"), options);
        chart.render();
    });
</script>
@endpush