@extends('layouts.contentNavbarLayout')

@section('title', 'Profil Saya')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
<style>
    .stat-card-link {
        display: block;
        text-decoration: none !important;
        color: inherit;
        border-radius: 0.375rem;
    }
    .stat-card-link:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endsection

@section('content')
@php
    $nameParts = explode(' ', trim($user->name));
    $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

    $roleLabels = [
        'admin' => ['text' => 'Administrator Sistem', 'icon' => 'ti tabler-user-shield'],
        'leader' => ['text' => 'Kepala UPT / Pimpinan', 'icon' => 'ti tabler-crown'],
        'staff_pks' => ['text' => 'Staff Administrasi PKS', 'icon' => 'ti tabler-file-text'],
        'staff_keu' => ['text' => 'Staff Keuangan', 'icon' => 'ti tabler-currency-dollar'],
        'treasurer' => ['text' => 'Bendahara Penerimaan', 'icon' => 'ti tabler-wallet'],
        'field_coordinator' => ['text' => 'Koordinator Lapangan', 'icon' => 'ti tabler-user-pin'],
    ];
    $currentRole = $roleLabels[$user->role] ?? ['text' => ucfirst($user->role), 'icon' => 'ti tabler-user'];
    
    $avatarUrl = $user->img ? asset('storage/' . $user->img) : "https://ui-avatars.com/api/?name=".urlencode($user->name)."&background=auto&color=fff&rounded=true&size=120";
    
    $activeTab = request()->query('tab', in_array($user->role, ['admin', 'staff_pks']) ? 'korlap' : ($user->role === 'staff_keu' ? 'setoran' : 'profile'));
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
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base {{ $currentRole['icon'] }} icon-lg"></i><span class="fw-medium">{{ $currentRole['text'] }}</span></li>
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-mail icon-lg"></i><span class="fw-medium">{{ $user->email }}</span></li>
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-calendar icon-lg"></i><span class="fw-medium"> Bergabung {{ $user->created_at->translatedFormat('F Y') }}</span></li>
              </ul>
            </div>
            <a href="{{ route('profile.settings') }}" class="btn btn-primary mb-1">
                <i class="icon-base ti tabler-settings icon-xs me-2"></i>Edit Profil & Sandi
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Navbar Pills -->
<div class="row">
  <div class="col-md-12">
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-2 gap-lg-0">
        @if(in_array($user->role, ['admin', 'staff_pks']))
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'korlap' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'korlap', 'page' => null]) }}">
                    <i class="icon-base ti tabler-users icon-sm me-1_5"></i> Data Korlap
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'zona' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'zona', 'page' => null]) }}">
                    <i class="icon-base ti tabler-route icon-sm me-1_5"></i> Data Zona
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'pks' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'pks', 'page' => null]) }}">
                    <i class="icon-base ti tabler-file-text icon-sm me-1_5"></i> Data PKS
                </a>
            </li>
        @elseif($user->role === 'field_coordinator')
            <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profil</a></li>
            <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="icon-base ti tabler-map-pin icon-sm me-1_5"></i> Area Parkir & PKS</a></li>
        @endif
      </ul>
    </div>
  </div>
</div>
<!--/ Navbar Pills -->

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
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-check icon-lg"></i><span class="fw-medium mx-2">Status:</span> <span class="badge bg-label-success">Aktif</span></li>
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-crown icon-lg"></i><span class="fw-medium mx-2">Role:</span> <span>{{ $currentRole['text'] }}</span></li>
          @if($user->employee_number)
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-fingerprint icon-lg"></i><span class="fw-medium mx-2">NIP:</span> <span>{{ formatNip($user->employee_number) }}</span></li>
          @endif
        </ul>
        
        <p class="card-text text-uppercase text-body-secondary small mb-0 mt-4">Kontak & Alamat</p>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-phone-call icon-lg"></i><span class="fw-medium mx-2">No. HP:</span>
            <span>{{ $user->phone_number ?? '-' }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-mail icon-lg"></i><span class="fw-medium mx-2">Email:</span>
            <span>{{ $user->email }}</span>
          </li>
          @if($user->role === 'field_coordinator' && $user->fieldCoordinator)
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-map-pin icon-lg"></i><span class="fw-medium mx-2">Alamat:</span>
            <span>{{ $user->fieldCoordinator->address ?? '-' }}</span>
          </li>
          @endif
        </ul>

        @if($user->role === 'leader' && $user->leader)
        <p class="card-text text-uppercase text-body-secondary small mb-0 mt-4">Pimpinan</p>
        <ul class="list-unstyled my-3 py-1">
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-briefcase icon-lg"></i><span class="fw-medium mx-2">Status Jabatan:</span> <span class="badge bg-label-{{ $user->leader->status_jabatan === 'aktif' ? 'success' : 'secondary' }} text-uppercase">{{ $user->leader->status_jabatan ?? '-' }}</span></li>
        </ul>
        @endif
      </div>
    </div>
    <!--/ About User -->

    @if(isset($stats) && !empty($stats))
    <!-- Profile Overview -->
    <div class="card mb-6">
      <div class="card-body">
        <p class="card-text text-uppercase text-body-secondary small">Overview Kinerja</p>
        <ul class="list-unstyled mb-0">
          @if(in_array($user->role, ['admin', 'staff_pks']))
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-users icon-lg text-primary"></i><span class="fw-medium mx-2">Korlap Dikelola:</span> <span>{{ $stats['korlapCount'] ?? 0 }}</span></li>
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-route icon-lg text-success"></i><span class="fw-medium mx-2">Zona Diperbarui:</span> <span>{{ $stats['roadSectionCount'] ?? 0 }}</span></li>
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-file-text icon-lg text-info"></i><span class="fw-medium mx-2">PKS Tervalidasi:</span> <span>{{ $stats['agreementPdfCount'] ?? 0 }}</span></li>
          @elseif($user->role === 'staff_keu')
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-currency-dollar icon-lg text-success"></i><span class="fw-medium mx-2">Nominal Divalidasi:</span> <span>Rp {{ number_format($stats['validatedDepositsAmount'] ?? 0, 0, ',', '.') }}</span></li>
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-checks icon-lg text-primary"></i><span class="fw-medium mx-2">Transaksi Setoran:</span> <span>{{ $stats['validatedDepositsCount'] ?? 0 }}</span></li>
          @elseif($user->role === 'treasurer')
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-wallet icon-lg text-danger"></i><span class="fw-medium mx-2">Setoran Masa Jabatan:</span> <span>Rp {{ number_format($stats['termDepositsAmount'] ?? 0, 0, ',', '.') }}</span></li>
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-cash icon-lg text-warning"></i><span class="fw-medium mx-2">Total Transaksi:</span> <span>{{ $stats['termDepositsCount'] ?? 0 }}</span></li>
          @elseif($user->role === 'leader')
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-writing-sign icon-lg text-primary"></i><span class="fw-medium mx-2">PKS Ditandatangani:</span> <span>{{ $stats['signedAgreementsCount'] ?? 0 }}</span></li>
          @endif
        </ul>
      </div>
    </div>
    <!--/ Profile Overview -->
    @endif
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Activity Table -->
    @if($user->role === 'leader')
      <div class="nav-align-top mb-6">
        <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-2 gap-lg-0" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link {{ request('page') || request('search') ? '' : 'active' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-riwayat" aria-controls="navs-riwayat" aria-selected="true">
              <i class="ti tabler-timeline me-1_5"></i> Riwayat Jabatan
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link {{ request('page') || request('search') ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pks" aria-controls="navs-pks" aria-selected="false">
              <i class="ti tabler-file-text me-1_5"></i> Daftar PKS
            </button>
          </li>
        </ul>
        <div class="tab-content p-0 shadow-none bg-transparent">
          <div class="tab-pane fade {{ request('page') || request('search') ? '' : 'show active' }}" id="navs-riwayat" role="tabpanel">
            <div class="card card-action mb-6">
              <div class="card-header align-items-center">
                <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Pimpinan</h5>
              </div>
              <div class="card-body pt-3">
                <ul class="timeline mb-0">
                  @if(!$user->leader->end_date)
                  <li class="timeline-item timeline-item-transparent">
                    <span class="timeline-point timeline-point-primary"></span>
                    <div class="timeline-event">
                      <div class="timeline-header mb-3">
                        <h6 class="mb-0">Menjabat sebagai {{ strtoupper($user->leader->status_jabatan) }}</h6>
                        <small class="text-body-secondary">Sekarang</small>
                      </div>
                      <p class="mb-2">Mulai: {{ \Carbon\Carbon::parse($user->leader->start_date)->translatedFormat('d F Y') }}</p>
                    </div>
                  </li>
                  @endif
                  
                  @forelse($user->leader->histories as $history)
                  <li class="timeline-item timeline-item-transparent {{ $loop->last && $user->leader->end_date ? 'border-transparent' : '' }}">
                    <span class="timeline-point timeline-point-secondary"></span>
                    <div class="timeline-event">
                      <div class="timeline-header mb-3">
                        <h6 class="mb-0">Menjabat sebagai {{ strtoupper($history->status_jabatan) }}</h6>
                        <small class="text-body-secondary">Selesai: {{ \Carbon\Carbon::parse($history->end_date)->translatedFormat('d M Y') }}</small>
                      </div>
                      <p class="mb-2">Mulai: {{ \Carbon\Carbon::parse($history->start_date)->translatedFormat('d M Y') }}</p>
                    </div>
                  </li>
                  @empty
                  @if($user->leader->end_date)
                  <li class="timeline-item timeline-item-transparent border-transparent">
                     <span class="timeline-point timeline-point-secondary"></span>
                     <div class="timeline-event">
                         <p class="text-muted mb-0">Tidak ada data riwayat jabatan lama.</p>
                     </div>
                  </li>
                  @endif
                  @endforelse
                </ul>
              </div>
            </div>
          </div>
          <div class="tab-pane fade {{ request('page') || request('search') ? 'show active' : '' }}" id="navs-pks" role="tabpanel">
            <div class="card mb-6">
              <div class="card-header align-items-center d-flex justify-content-between">
                <h5 class="card-title mb-0"><i class="icon-base ti tabler-list-details icon-lg me-2"></i>Daftar PKS Ditandatangani</h5>
                <form action="{{ route('profile.index') }}" method="GET" class="d-flex align-items-center" id="autoSearchForm">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control" id="autoSearchInput" placeholder="Cari data..." value="{{ request('search') }}">
                    </div>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>No PKS</th>
                            <th>Korlap</th>
                            <th class="text-end">Tgl TTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedData ?? collect() as $data)
                            <tr>
                                <td class="fw-medium">{{ $data->agreement_number ?? '-' }}</td>
                                <td>{{ $data->fieldCoordinator->user->name ?? '-' }}</td>
                                <td class="text-end text-muted small">{{ \Carbon\Carbon::parse($data->signed_date)->translatedFormat('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="icon-base ti tabler-inbox icon-xl text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada data PKS yang ditandatangani.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
              @if(isset($paginatedData) && $paginatedData->hasPages())
              <div class="card-footer py-3">
                  {{ $paginatedData->links('pagination::bootstrap-5') }}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @elseif($user->role === 'treasurer')
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
            <div class="card card-action mb-6">
              <div class="card-header align-items-center">
                <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Bendahara</h5>
              </div>
              <div class="card-body pt-3">
                <ul class="timeline mb-0">
                  @if(!$user->treasurer->end_date)
                  <li class="timeline-item timeline-item-transparent">
                    <span class="timeline-point timeline-point-primary"></span>
                    <div class="timeline-event">
                      <div class="timeline-header mb-3">
                        <h6 class="mb-0">Menjabat sebagai {{ strtoupper($user->treasurer->status_jabatan ?? 'TETAP') }}</h6>
                        <small class="text-body-secondary">Sekarang</small>
                      </div>
                      <p class="mb-2">Mulai: {{ \Carbon\Carbon::parse($user->treasurer->start_date)->translatedFormat('d F Y') }}</p>
                    </div>
                  </li>
                  @endif
                  
                  @forelse($user->treasurer->histories as $history)
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
                  @if($user->treasurer->end_date)
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
          </div>
          <div class="tab-pane fade {{ request('page') || request('search') ? 'show active' : '' }}" id="navs-audit" role="tabpanel">
            <div class="card mb-6">
              <div class="card-header align-items-center d-flex justify-content-between">
                <h5 class="card-title mb-0"><i class="icon-base ti tabler-list-details icon-lg me-2"></i>Jejak Audit Validasi Setoran</h5>
                <form action="{{ route('profile.index') }}" method="GET" class="d-flex align-items-center" id="autoSearchForm">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control" id="autoSearchInput" placeholder="Cari data..." value="{{ request('search') }}">
                    </div>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>Kode Ref / No PKS</th>
                            <th>Korlap</th>
                            <th>Nominal</th>
                            <th class="text-end">Tgl Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedData ?? collect() as $data)
                            <tr>
                                <td>
                                    <span class="d-block fw-semibold text-primary">{{ $data->referral_code ?? 'TRX-'.$data->id }}</span>
                                    <small class="text-muted">{{ $data->agreement->agreement_number ?? '-' }}</small>
                                </td>
                                <td>{{ $data->agreement->fieldCoordinator->user->name ?? '-' }}</td>
                                <td><span class="badge bg-label-success">Rp {{ number_format($data->amount, 0, ',', '.') }}</span></td>
                                <td class="text-end text-muted small">{{ \Carbon\Carbon::parse($data->validation_date ?? $data->deposit_date)->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="icon-base ti tabler-inbox icon-xl text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada data validasi setoran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
              @if(isset($paginatedData) && $paginatedData->hasPages())
              <div class="card-footer py-3">
                  {{ $paginatedData->links('pagination::bootstrap-5') }}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @elseif(isset($paginatedData))
    <div class="card mb-6">
      <div class="card-header align-items-center d-flex justify-content-between">
        <h5 class="card-title mb-0"><i class="icon-base ti tabler-list-details icon-lg me-2"></i>Data Riwayat ({{ ucfirst(str_replace('_', ' ', $activeTab)) }})</h5>
        
        <form action="{{ route('profile.index') }}" method="GET" class="d-flex align-items-center" id="autoSearchForm">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                <input type="text" name="search" class="form-control" id="autoSearchInput" placeholder="Cari data..." value="{{ request('search') }}">
            </div>
        </form>
      </div>
      
      <div class="table-responsive">
        <table class="table table-hover border-top">
            <thead>
                <tr>
                    @if($activeTab === 'korlap')
                        <th>Nama Korlap</th>
                        <th>KTP</th>
                        <th>Telepon</th>
                    @elseif($activeTab === 'zona')
                        <th>Nama Zona</th>
                        <th>Kode Zona</th>
                    @elseif($activeTab === 'pks')
                        <th>No PKS</th>
                        <th>Korlap</th>
                        <th>Tgl Mulai</th>
                    @elseif(in_array($activeTab, ['setoran', 'term_deposits']))
                        <th>Kode Ref / No PKS</th>
                        <th>Korlap</th>
                        <th>Nominal</th>
                        <th class="text-end">Tgl Validasi</th>
                    @elseif($activeTab === 'signed_agreements')
                        <th>No PKS</th>
                        <th>Korlap</th>
                        <th class="text-end">Tgl TTD</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($paginatedData as $data)
                    <tr>
                        @if($activeTab === 'korlap')
                            <td class="fw-medium">{{ $data->user->name ?? '-' }}</td>
                            <td>{{ $data->id_card_number ?? '-' }}</td>
                            <td>{{ $data->user->phone_number ?? '-' }}</td>
                        @elseif($activeTab === 'zona')
                            <td class="fw-medium">{{ $data->name ?? '-' }}</td>
                            <td>{{ $data->section_code ?? '-' }}</td>
                        @elseif($activeTab === 'pks')
                            <td class="fw-medium">{{ $data->agreement->agreement_number ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        @php
                                            $aName = $data->agreement->fieldCoordinator->user->name ?? '-';
                                            $aImg = $data->agreement->fieldCoordinator->user->img ?? null;
                                            $aUrl = $aImg ? asset('storage/' . $aImg) : "https://ui-avatars.com/api/?name=".urlencode($aName)."&background=random&color=fff";
                                        @endphp
                                        <img src="{{ $aUrl }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <span>{{ $aName }}</span>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($data->agreement->start_date)->translatedFormat('d M Y') }}</td>
                        @elseif(in_array($activeTab, ['setoran', 'term_deposits']))
                            <td>
                                <span class="d-block fw-semibold text-primary">{{ $data->referral_code ?? 'TRX-'.$data->id }}</span>
                                <small class="text-muted">{{ $data->agreement->agreement_number ?? '-' }}</small>
                            </td>
                            <td>{{ $data->agreement->fieldCoordinator->user->name ?? '-' }}</td>
                            <td><span class="badge bg-label-success">Rp {{ number_format($data->amount, 0, ',', '.') }}</span></td>
                            <td class="text-end text-muted small">{{ \Carbon\Carbon::parse($data->validation_date ?? $data->deposit_date)->translatedFormat('d M Y H:i') }}</td>
                        @elseif($activeTab === 'signed_agreements')
                            <td class="fw-medium">{{ $data->agreement_number ?? '-' }}</td>
                            <td>{{ $data->fieldCoordinator->user->name ?? '-' }}</td>
                            <td class="text-end text-muted small">{{ \Carbon\Carbon::parse($data->signed_date)->translatedFormat('d M Y') }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="icon-base ti tabler-inbox icon-xl text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data riwayat yang tercatat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
      @if(isset($paginatedData) && $paginatedData->hasPages())
      <div class="card-footer py-3">
          {{ $paginatedData->links('pagination::bootstrap-5') }}
      </div>
      @endif
    </div>
    @else
    <div class="alert alert-info">
        <h6 class="alert-heading mb-1"><i class="icon-base ti tabler-info-circle me-1"></i>Informasi</h6>
        <span>Tidak ada riwayat aktivitas yang perlu ditampilkan untuk pengguna ini.</span>
    </div>
    @endif
    <!--/ Activity Table -->
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
