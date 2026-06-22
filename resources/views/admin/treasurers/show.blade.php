@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Profil Bendahara: ' . ($treasurer->user->name ?? 'N/A'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('content')
@php
    $user = $treasurer->user;
    $nameParts = explode(' ', trim($user->name));
    $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
    $avatarUrl = $user->img ? asset('storage/' . $user->img) : "https://ui-avatars.com/api/?name=".urlencode($user->name)."&background=auto&color=fff&rounded=true&size=120";
    $isActive = $user->is_active ?? true;
@endphp

<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top" />
      </div>
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $avatarUrl }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded-circle user-profile-img" style="width: 120px; height: 120px; object-fit: cover; border: 5px solid #fff;" />
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6">{{ $user->name }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-wallet icon-lg"></i><span class="fw-medium">Bendahara Penerimaan</span></li>
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-mail icon-lg"></i><span class="fw-medium">{{ $user->email }}</span></li>
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-calendar icon-lg"></i><span class="fw-medium"> Bergabung {{ $user->created_at->translatedFormat('F Y') }}</span></li>
              </ul>
            </div>
            <a href="javascript:void(0)" class="btn {{ $isActive ? 'btn-primary' : 'btn-danger' }} mb-1">
                <i class="icon-base ti tabler-user-check icon-xs me-2"></i>{{ $isActive ? 'Aktif' : 'Nonaktif' }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->



<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        <p class="card-text text-uppercase text-body-secondary small mb-0">Detail Informasi</p>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-user icon-lg"></i><span class="fw-medium mx-2">Nama Lengkap:</span> <span>{{ $user->name }}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-id icon-lg"></i><span class="fw-medium mx-2">Username:</span> <span>{{ $user->username }}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-briefcase icon-lg"></i><span class="fw-medium mx-2">Status Jabatan:</span> <span class="badge bg-label-{{ $treasurer->status_jabatan === 'aktif' ? 'success' : 'secondary' }} text-uppercase">{{ $treasurer->status_jabatan ?? '-' }}</span></li>
          <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-fingerprint icon-lg"></i><span class="fw-medium mx-2">NIP:</span> <span>{{ formatNip($treasurer->employee_number) }}</span></li>
        </ul>
        
        <p class="card-text text-uppercase text-body-secondary small mb-0 mt-4">Kontak & Alamat</p>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-phone-call icon-lg"></i><span class="fw-medium mx-2">No. HP:</span>
            <span>{{ $treasurer->phone_number ?? '-' }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-mail icon-lg"></i><span class="fw-medium mx-2">Email:</span>
            <span>{{ $user->email }}</span>
          </li>
        </ul>
      </div>
    </div>
    <!--/ About User -->

    <!-- Profile Overview -->
    <div class="card mb-6">
      <div class="card-body">
        <p class="card-text text-uppercase text-body-secondary small">Overview Kinerja</p>
        <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-currency-dollar icon-lg text-success"></i><span class="fw-medium mx-2">Total Nominal Tervalidasi:</span> <span>Rp {{ number_format($totalValidatedAmount, 0, ',', '.') }}</span></li>
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-checks icon-lg text-primary"></i><span class="fw-medium mx-2">Total Setoran Divalidasi:</span> <span>{{ $deposits->total() }}</span></li>
        </ul>
      </div>
    </div>
    <!--/ Profile Overview -->
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7">
    <div class="nav-align-top mb-6">
      <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-2 gap-lg-0" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link {{ request('page') || request('search') ? '' : 'active' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-riwayat" aria-controls="navs-riwayat" aria-selected="true">
            <i class="ti tabler-timeline me-1_5"></i> Riwayat Jabatan
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{ request('page') || request('search') ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-audit" aria-controls="navs-audit" aria-selected="false">
            <i class="ti tabler-list-details me-1_5"></i> Jejak Audit Validasi
          </button>
        </li>
      </ul>
      <div class="tab-content p-0 shadow-none bg-transparent">
        <div class="tab-pane fade {{ request('page') || request('search') ? '' : 'show active' }}" id="navs-riwayat" role="tabpanel">
          <!-- Activity Timeline -->
          <div class="card card-action mb-6">
            <div class="card-header align-items-center">
              <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Bendahara</h5>
            </div>
            <div class="card-body pt-3">
              <ul class="timeline mb-0">
                <!-- Jabatan Saat Ini (jika ada) -->
                @if(!$treasurer->end_date)
                <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point timeline-point-primary"></span>
                  <div class="timeline-event">
                    <div class="timeline-header mb-3">
                      <h6 class="mb-0">Menjabat sebagai {{ strtoupper($treasurer->status_jabatan ?? 'TETAP') }}</h6>
                      <small class="text-body-secondary">Sekarang</small>
                    </div>
                    <p class="mb-2">Mulai: {{ \Carbon\Carbon::parse($treasurer->start_date)->translatedFormat('d F Y') }}</p>
                  </div>
                </li>
                @endif
                
                <!-- History -->
                @forelse($treasurer->histories as $history)
                <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point timeline-point-secondary"></span>
                  <div class="timeline-event">
                    <div class="timeline-header mb-3">
                      <h6 class="mb-0 text-muted">Menjabat sebagai {{ strtoupper($history->status_jabatan ?? 'TETAP') }}</h6>
                      <small class="text-body-secondary">{{ \Carbon\Carbon::parse($history->end_date)->diffForHumans() }}</small>
                    </div>
                    <p class="mb-2 text-muted">Mulai: {{ \Carbon\Carbon::parse($history->start_date)->translatedFormat('d F Y') }} <br> Berakhir: {{ \Carbon\Carbon::parse($history->end_date)->translatedFormat('d F Y') }}</p>
                  </div>
                </li>
                @empty
                @if($treasurer->end_date)
                <li class="timeline-item timeline-item-transparent border-0 pb-0">
                   <div class="timeline-event">
                      <p class="text-muted">Tidak ada riwayat jabatan lain.</p>
                   </div>
                </li>
                @endif
                @endforelse
              </ul>
            </div>
          </div>
          <!--/ Activity Timeline -->
        </div>

        <div class="tab-pane fade {{ request('page') || request('search') ? 'show active' : '' }}" id="navs-audit" role="tabpanel">
          <!-- Projects table -->
          <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="card-title mb-0"><i class="icon-base ti tabler-list-details icon-lg me-2"></i>Jejak Audit Validasi Setoran</h5>
              
              <form action="{{ route('admin.treasurers.show', $treasurer->id) }}" method="GET" class="d-flex align-items-center" id="autoSearchForm">
                  <div class="input-group input-group-sm">
                      <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                      <input type="text" name="search" class="form-control" id="autoSearchInput" placeholder="Cari Trx / PKS / Korlap..." value="{{ request('search') }}">
                  </div>
              </form>
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0">
                  <thead class="border-bottom">
                      <tr>
                          <th class="ps-4">No Setoran</th>
                          <th>Korlap</th>
                          <th>Nominal</th>
                          <th class="pe-4 text-end">Tgl Validasi</th>
                      </tr>
                  </thead>
                  <tbody>
                      @forelse($deposits as $deposit)
                          <tr>
                              <td class="ps-4">
                                  <span class="fw-medium text-primary">{{ $deposit->referral_code ?? 'TRX-'.$deposit->id }}</span><br>
                                  <small class="text-muted">{{ $deposit->agreement->agreement_number ?? '-' }}</small>
                              </td>
                              <td>{{ $deposit->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                              <td>
                                  <span class="badge bg-label-success">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                              </td>
                              <td class="pe-4 text-end text-muted small">
                                  {{ \Carbon\Carbon::parse($deposit->validation_date ?? $deposit->deposit_date)->translatedFormat('d M Y H:i') }}
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="4" class="text-center py-5 text-muted">
                                  <i class="icon-base ti tabler-inbox icon-xl mb-3 d-block"></i>
                                  Belum ada riwayat validasi setoran yang dilakukan.
                              </td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
            </div>
            @if($deposits->hasPages())
            <div class="card-footer py-3">
                {{ $deposits->links('pagination::bootstrap-5') }}
            </div>
            @endif
          </div>
          <!--/ Projects table -->
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ User Profile Content -->
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchInput = document.getElementById('autoSearchInput');
        let searchForm = document.getElementById('autoSearchForm');
        let timeout = null;

        if (searchInput && searchForm) {
            let val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            searchInput.focus();

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });
        }
    });
</script>
@endsection
