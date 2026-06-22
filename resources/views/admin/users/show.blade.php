@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Profil: ' . ($user->name ?? 'N/A'))

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
            <a href="javascript:void(0)" class="btn {{ $user->is_active ?? true ? 'btn-primary' : 'btn-danger' }} mb-1">
                <i class="icon-base ti tabler-user-check icon-xs me-2"></i>{{ $user->is_active ?? true ? 'Aktif' : 'Nonaktif' }}
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
        @elseif($user->role === 'staff_keu')
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'setoran' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'setoran', 'page' => null]) }}">
                    <i class="icon-base ti tabler-currency-dollar icon-sm me-1_5"></i> Validasi Setoran
                </a>
            </li>
        @else
            <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profil</a></li>
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
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-check icon-lg"></i><span class="fw-medium mx-2">Status:</span> 
            @if($user->is_active ?? true)
                <span class="badge bg-label-success">Aktif</span>
            @else
                <span class="badge bg-label-danger">Nonaktif</span>
            @endif
          </li>
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
        </ul>
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
    @if(isset($paginatedData))
    <div class="card mb-6">
      <div class="card-header align-items-center d-flex justify-content-between">
        <h5 class="card-title mb-0"><i class="icon-base ti tabler-list-details icon-lg me-2"></i>Data Riwayat ({{ ucfirst(str_replace('_', ' ', $activeTab)) }})</h5>
        
        <form action="{{ route('admin.users.show', $user->id) }}" method="GET" class="d-flex align-items-center" id="autoSearchForm">
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
