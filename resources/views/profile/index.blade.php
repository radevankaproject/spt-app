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
    {{-- ============================================= --}}
    {{-- HERO HEADER --}}
    {{-- ============================================= --}}
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-user me-2"></i>Profil Pengguna</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Atur informasi akun dan preferensi.</p>
            </div>
        </div>
        <i class="ti tabler-user position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>
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

@if($user->role !== 'field_coordinator')
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
            <a href="{{ route('profile.settings') }}" class="btn btn-primary rounded-pill btn-action mb-1">
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
            <li class="nav-item"><a class="nav-link {{ $activeTab == 'profile' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'profile', 'page' => null]) }}"><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profil</a></li>
            <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="icon-base ti tabler-map-pin icon-sm me-1_5"></i> Area Parkir & PKS</a></li>
        @endif
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'aktivitas' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'aktivitas', 'page' => null]) }}">
                <i class="icon-base ti tabler-activity icon-sm me-1_5"></i> Aktivitas Akun
            </a>
        </li>
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
      <div class="card-body p-4">
        <p class="card-text text-uppercase text-body-secondary small mb-0">Detail Informasi</p>
        <table class="table table-borderless m-0 my-3 py-1">
            <tbody>
                <tr>
                    <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-user icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Nama Lengkap</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-id icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Username</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $user->username }}</td>
                </tr>
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-check icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Status</td>
                    <td class="pe-0 py-2 align-middle text-end w-50"><span class="badge bg-label-success">Aktif</span></td>
                </tr>
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-crown icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Role</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $currentRole['text'] }}</td>
                </tr>
                @if($user->employee_number)
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-fingerprint icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">NIP</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ formatNip($user->employee_number) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <p class="card-text text-uppercase text-body-secondary small mb-0 mt-4">Kontak & Alamat</p>
        <table class="table table-borderless m-0 my-3 py-1">
            <tbody>
                <tr>
                    <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-phone-call icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">No. HP</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $user->phone_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-mail icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Email</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $user->email }}</td>
                </tr>
                @if($user->role === 'field_coordinator' && $user->fieldCoordinator)
                <tr>
                    <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-map-pin icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Alamat</td>
                    <td class="pe-0 py-2 align-middle text-end w-50">{{ $user->fieldCoordinator->address ?? '-' }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($user->role === 'leader' && $user->leader)
        <p class="card-text text-uppercase text-body-secondary small mb-0 mt-4">Pimpinan</p>
        <table class="table table-borderless m-0 my-3 py-1">
            <tbody>
                <tr>
                    <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-briefcase icon-lg"></i></td>
                    <td class="px-0 py-2 align-middle fw-medium">Status Jabatan</td>
                    <td class="pe-0 py-2 align-middle text-end w-50"><span class="badge bg-label-{{ $user->leader->status_jabatan === 'aktif' ? 'success' : 'secondary' }} text-uppercase">{{ $user->leader->status_jabatan ?? '-' }}</span></td>
                </tr>
            </tbody>
        </table>
        @endif
      </div>
    </div>
    <!--/ About User -->

    @if(isset($stats) && !empty($stats))
    <!-- Profile Overview -->
    <div class="card mb-6">
      <div class="card-body p-4">
        <p class="card-text text-uppercase text-body-secondary small">Overview Kinerja</p>
        <table class="table table-borderless m-0">
          <tbody>
          @if(in_array($user->role, ['admin', 'staff_pks']))
            <tr>
                <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-users icon-lg text-primary"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Korlap Dikelola</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['korlapCount'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-route icon-lg text-success"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Zona Diperbarui</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['roadSectionCount'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-file-text icon-lg text-info"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">PKS Tervalidasi</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['agreementPdfCount'] ?? 0 }}</td>
            </tr>
          @elseif($user->role === 'staff_keu')
            <tr>
                <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-currency-dollar icon-lg text-success"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Nominal Divalidasi</td>
                <td class="pe-0 py-2 align-middle text-end w-50">Rp {{ number_format($stats['validatedDepositsAmount'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-checks icon-lg text-primary"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Transaksi Setoran</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['validatedDepositsCount'] ?? 0 }}</td>
            </tr>
          @elseif($user->role === 'treasurer')
            <tr>
                <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-wallet icon-lg text-danger"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Setoran Masa Jabatan</td>
                <td class="pe-0 py-2 align-middle text-end w-50">Rp {{ number_format($stats['termDepositsAmount'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="ps-0 py-2 align-middle"><i class="icon-base ti tabler-cash icon-lg text-warning"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">Total Transaksi</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['termDepositsCount'] ?? 0 }}</td>
            </tr>
          @elseif($user->role === 'leader')
            <tr>
                <td class="ps-0 py-2 align-middle" style="width: 40px;"><i class="icon-base ti tabler-writing-sign icon-lg text-primary"></i></td>
                <td class="px-0 py-2 align-middle fw-medium">PKS Ditandatangani</td>
                <td class="pe-0 py-2 align-middle text-end w-50">{{ $stats['signedAgreementsCount'] ?? 0 }}</td>
            </tr>
          @endif
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Profile Overview -->
    @endif
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Activity Table -->
    @if($activeTab === 'aktivitas')
      <div class="card mb-6">
        <div class="card-header align-items-center d-flex justify-content-between p-4">
          <h5 class="card-title mb-0"><i class="icon-base ti tabler-activity icon-lg me-2"></i>Aktivitas Akun Saya</h5>
          <form action="{{ route('profile.index') }}" method="GET" class="d-flex align-items-center">
              <input type="hidden" name="tab" value="aktivitas">
              <div class="input-group input-group-sm">
                  <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                  <input type="text" name="search" class="form-control" placeholder="Cari aktivitas..." value="{{ request('search') }}" onchange="this.form.submit()">
              </div>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table table-hover border-top">
              <thead>
                  <tr>
                      <th>Aksi</th>
                      <th>Deskripsi</th>
                      <th>IP & Browser</th>
                      <th class="text-end">Waktu</th>
                  </tr>
              </thead>
              <tbody>
                  @forelse($paginatedData ?? collect() as $data)
                      <tr>
                          <td class="fw-medium text-primary">{{ $data->action ?? '-' }}</td>
                          <td>{{ $data->description ?? '-' }}</td>
                          <td>
                              <div class="d-flex flex-column">
                                  <small class="mb-1"><i class="ti tabler-network"></i> {{ $data->ip_address ?? '-' }}</small>
                                  @if($data->user_agent)
                                  <div>
                                      <a class="text-muted small d-inline-flex align-items-center" data-bs-toggle="collapse" href="#collapseBrowser{{ $data->id }}" role="button" aria-expanded="false" aria-controls="collapseBrowser{{ $data->id }}">
                                          <i class="ti tabler-browser me-1"></i> Detail Browser <i class="ti tabler-chevron-down ms-1" style="font-size: 10px;"></i>
                                      </a>
                                      <div class="collapse mt-2" id="collapseBrowser{{ $data->id }}">
                                          <div class="p-2 bg-label-secondary rounded small text-break" style="max-width: 300px; font-size: 0.75rem; line-height: 1.4;">
                                              {{ $data->user_agent }}
                                          </div>
                                      </div>
                                  </div>
                                  @else
                                  <small class="text-muted"><i class="ti tabler-browser me-1"></i> -</small>
                                  @endif
                              </div>
                          </td>
                          <td class="text-end text-muted small">{{ $data->created_at ? $data->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="4" class="text-center py-5">
                              <i class="icon-base ti tabler-inbox icon-xl text-muted mb-3"></i>
                              <p class="text-muted mb-0">Belum ada aktivitas yang tercatat.</p>
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
    @elseif($user->role === 'leader')
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
              <div class="card-header align-items-center p-4">
                <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Pimpinan</h5>
              </div>
              <div class="card-body pt-3 p-4">
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
              <div class="card-header align-items-center d-flex justify-content-between p-4">
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
              <div class="card-header align-items-center p-4">
                <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Bendahara</h5>
              </div>
              <div class="card-body pt-3 p-4">
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
              <div class="card-header align-items-center d-flex justify-content-between p-4">
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
      <div class="card-header align-items-center d-flex justify-content-between p-4">
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
@else
@php
    $fieldCoordinator = $user->fieldCoordinator;
    $activeAgreements = $agreementsInYear->filter(function($pks) {
        return in_array($pks->status, ['active', 'pending_renewal']);
    });
    $historyAgreements = $agreementsInYear->filter(function($pks) {
        return !in_array($pks->status, ['active', 'pending_renewal']);
    });
@endphp

        {{-- ✅ FILTER TAHUN (Nav Pills Premium) --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Rapor & Portofolio Koordinator</h4>
                <p class="text-muted mb-0">Memantau kinerja kontrak berdasarkan tahun anggaran.</p>
            </div>
            <div>
                <ul class="nav nav-pills flex-nowrap overflow-auto hide-scrollbar" style="white-space: nowrap;">
                    @foreach($availableYears as $year)
                        <li class="nav-item me-2">
                            <a class="nav-link {{ $year == $selectedYear ? 'active shadow-sm' : 'bg-white border' }}"
                               href="{{ request()->fullUrlWithQuery(['year' => $year, 'page' => null]) }}">
                               <i class="ti tabler-calendar me-1"></i> Tahun {{ $year }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            {{-- KOLOM KIRI: PROFIL & STATISTIK --}}
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body pt-5 text-center p-4">
                        <div class="user-avatar-section mb-4">
                            @if ($fieldCoordinator->user && $fieldCoordinator->user->img)
                                <img class="img-fluid rounded-circle shadow-sm" style="object-fit: cover;"
                                    src="{{ asset('storage/'.$fieldCoordinator->user->img) }}" height="120"
                                    width="120" alt="Avatar" />
                            @else
                                <div class="avatar avatar-xl mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-warning" style="font-size: 2rem;">
                                        {{ strtoupper(substr($fieldCoordinator->user->name ?? 'K', 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                            <h5 class="mt-3 mb-1 fw-bold">{{ $fieldCoordinator->user->name }}</h5>
                            <span class="badge bg-label-warning rounded-pill">Koordinator Lapangan</span>
                        </div>

                        {{-- ✅ STATISTIK BERDASARKAN TAHUN --}}
                        <div class="bg-lighter rounded-3 p-3 mb-4 text-start">
                            <h6 class="fw-bold text-primary mb-3"><i class="ti tabler-chart-bar me-1"></i> Statistik Tahun {{ $selectedYear }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Kontrak (PKS)</span>
                                <span class="fw-bold text-dark">{{ $totalAgreementsCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Titik Lokasi Dikelola</span>
                                <span class="fw-bold text-dark">{{ $activeParkingLocationsCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="text-muted">Total Setoran Masuk</span>
                                <span class="fw-bold text-success">Rp {{ number_format($totalValidatedDeposit, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <h6 class="pb-2 border-bottom text-start mb-3">Informasi Personal</h6>
                        <ul class="list-unstyled mb-4 text-start small">
                            <li class="mb-2 d-flex align-items-center"><i class="ti tabler-mail text-muted me-2"></i> <span>{{ $fieldCoordinator->user->email }}</span></li>
                            <li class="mb-2 d-flex align-items-center"><i class="ti tabler-phone text-muted me-2"></i> <span>{{ $fieldCoordinator->phone_number }}</span></li>
                            <li class="mb-2 d-flex align-items-center"><i class="ti tabler-id text-muted me-2"></i> <span>{{ $fieldCoordinator->id_card_number }}</span></li>
                            <li class="d-flex align-items-start"><i class="ti tabler-map-pin text-muted me-2 mt-1"></i> <span>{{ $fieldCoordinator->address }}</span></li>
                        </ul>
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('profile.settings') }}" class="btn btn-primary rounded-pill btn-action mb-1">
                                <i class="icon-base ti tabler-settings icon-xs me-2"></i>Edit Profil & Sandi
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KARTU KTP --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="pb-2 border-bottom mb-3"><i class="ti tabler-discount-check me-1"></i> Dokumen KTP</h6>
                        @if ($fieldCoordinator->id_card_img)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ktpModal">
                                <img src="{{ asset('storage/'.$fieldCoordinator->id_card_img) }}" alt="Foto KTP"
                                    class="img-fluid rounded-3 shadow-sm" style="cursor: zoom-in;">
                            </a>
                        @else
                            <div class="text-center py-4 bg-lighter rounded-3">
                                <i class="icon-base ti tabler-photo icon-22px"></i>
                                <small class="text-muted">Belum ada KTP</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PORTOFOLIO KONTRAK --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- 1. PKS SEDANG BERJALAN (AKTIF) --}}
                <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="ti tabler-player-play text-primary me-2"></i> Kontrak Berjalan (Tahun {{ $selectedYear }})</h5>
                @forelse ($activeAgreements as $pks)
                    <div class="card mb-4 border-primary border-opacity-25 shadow-sm">
                        <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center py-3 p-4">
                            <div>
                                <h5 class="mb-0 fw-bold text-primary">{{ $pks->agreement_number }}</h5>
                                <small class="text-muted">{{ $pks->start_date->translatedFormat('d M Y') }} - {{ $pks->end_date->translatedFormat('d M Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $pks->status == 'active' ? 'success' : 'warning' }} rounded-pill px-3">
                                {{ ucwords(str_replace('_', ' ', $pks->status)) }}
                            </span>
                        </div>
                        <div class="card-body pt-3 p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle bg-label-info"><i class="ti tabler-user-hexagon"></i></span></div>
                                        <div>
                                            <p class="mb-0 fw-medium text-dark">{{ $pks->activeParkingLocations->count() }} Lokasi</p>
                                            <small class="text-muted">Dikelola saat ini</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle bg-label-success"><i class="ti tabler-cash"></i></span></div>
                                        <div>
                                            <p class="mb-0 fw-medium text-dark">Rp {{ number_format($pks->total_deposit ?? 0, 0, ',', '.') }}</p>
                                            <small class="text-muted">Setoran Masuk</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top text-end">
                                @if($pks->signed_document_path)
                                <a href="{{ Storage::url($pks->signed_document_path) }}" target="_blank" class="btn btn-sm btn-outline-success me-2"><i class="ti tabler-file-check me-1"></i> File Scan Asli</a>
                                @endif
                                <a href="{{ route('masterdata.agreements.show', $pks->id) }}" class="btn btn-sm btn-primary shadow-sm rounded-pill"><i class="ti tabler-eye me-1"></i> Buka Detail PKS</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card mb-5 border-0 shadow-sm bg-lighter">
                        <div class="card-body text-center py-5 p-4">
                            <i class="ti tabler-folder-off" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">Tidak ada Kontrak Aktif</h6>
                            <p class="text-muted mb-0">Koordinator ini tidak memiliki PKS yang sedang berjalan di tahun {{ $selectedYear }}.</p>
                        </div>
                    </div>
                @endforelse

                {{-- 2. ARSIP PKS (KEDALUWARSA/DIPUTUS) --}}
                <h5 class="fw-bold mb-3 mt-5 d-flex align-items-center"><i class="ti tabler-archive text-secondary me-2"></i> Riwayat Kontrak Selesai</h5>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th>No. Dokumen PKS</th>
                                    <th>Masa Berlaku</th>
                                    <th>Total Setoran</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($historyAgreements as $history)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $history->agreement_number }}</span></td>
                                        <td>
                                            <small class="d-block text-muted">Mulai: {{ $history->start_date->format('d/m/Y') }}</small>
                                            <small class="d-block text-danger">Akhir: {{ $history->end_date->format('d/m/Y') }}</small>
                                        </td>
                                        <td><span class="fw-medium text-success">Rp {{ number_format($history->total_deposit ?? 0, 0, ',', '.') }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $history->status == 'expired' ? 'danger' : 'dark' }} rounded-pill">
                                                {{ ucwords(str_replace('_', ' ', $history->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($history->signed_document_path)
                                            <a href="{{ Storage::url($history->signed_document_path) }}" target="_blank" class="btn btn-sm btn-icon btn-text-success rounded-pill me-1" data-bs-toggle="tooltip" title="File Scan Asli">
                                                <i class="ti tabler-file-check icon-22px"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('masterdata.agreements.show', $history->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Lihat Histori">
                                                <i class="ti tabler-arrow-right-circle icon-22px"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada riwayat kontrak yang selesai di tahun ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal KTP --}}
        <div class="modal fade" id="ktpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white">Dokumen KTP</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img src="{{ $fieldCoordinator->id_card_img ? asset('storage/'.$fieldCoordinator->id_card_img) : '' }}"
                            class="img-fluid w-100" alt="Foto KTP">
                    </div>
                </div>
            </div>
        </div>
@endif
@endsection

@section('page-script')
<script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide scrollbar class untuk nav-pills tahun agar rapi di mobile
        const style = document.createElement('style');
        style.innerHTML = `
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        `;
        document.head.appendChild(style);

        // Aktifkan Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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
