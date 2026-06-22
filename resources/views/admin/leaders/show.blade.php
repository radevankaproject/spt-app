@extends('layouts.contentNavbarLayout')

@section('title', 'Profil Pimpinan: ' . ($leader->user->name ?? 'N/A'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('content')
@php
    $user = $leader->user;
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
                <li class="list-inline-item d-flex gap-2 align-items-center"><i class="icon-base ti tabler-crown icon-lg"></i><span class="fw-medium">Kepala UPT / Pimpinan</span></li>
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
          <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-briefcase icon-lg"></i><span class="fw-medium mx-2">Status Jabatan:</span> <span class="badge bg-label-{{ $leader->status_jabatan === 'aktif' ? 'success' : 'secondary' }} text-uppercase">{{ $leader->status_jabatan ?? '-' }}</span></li>
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

    <!-- Profile Overview -->
    <div class="card mb-6">
      <div class="card-body">
        <p class="card-text text-uppercase text-body-secondary small">Overview Kinerja ({{ $selectedYear }})</p>
        <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-center mb-4"><i class="icon-base ti tabler-writing-sign icon-lg text-primary"></i><span class="fw-medium mx-2">PKS Ditandatangani:</span> <span>{{ $totalAgreementsCount }}</span></li>
            <li class="d-flex align-items-center mb-2"><i class="icon-base ti tabler-file-check icon-lg text-success"></i><span class="fw-medium mx-2">PKS Aktif:</span> <span>{{ $activeAgreementsCount }}</span></li>
        </ul>
      </div>
    </div>
    <!--/ Profile Overview -->
    
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7">
    <div class="nav-align-top mb-6">
      <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-2 gap-lg-0" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link {{ request('page') || request('year') || request('search') ? '' : 'active' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-riwayat" aria-controls="navs-riwayat" aria-selected="true">
            <i class="ti tabler-timeline me-1_5"></i> Riwayat Jabatan
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{ request('page') || request('year') || request('search') ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pks" aria-controls="navs-pks" aria-selected="false">
            <i class="ti tabler-writing-sign me-1_5"></i> Daftar TTD PKS
          </button>
        </li>
      </ul>
      <div class="tab-content p-0 shadow-none bg-transparent">
        <!-- Tab Riwayat Jabatan -->
        <div class="tab-pane fade {{ request('page') || request('year') || request('search') ? '' : 'show active' }}" id="navs-riwayat" role="tabpanel">
          <div class="card card-action mb-6">
            <div class="card-header align-items-center">
              <h5 class="card-action-title mb-0"><i class="icon-base ti tabler-timeline icon-lg me-2"></i>Riwayat Jabatan Pimpinan</h5>
            </div>
            <div class="card-body pt-3">
              <ul class="timeline mb-0">
                <!-- Jabatan Saat Ini (jika ada) -->
                @if(!$leader->end_date)
                <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point timeline-point-primary"></span>
                  <div class="timeline-event">
                    <div class="timeline-header mb-3">
                      <h6 class="mb-0">Menjabat sebagai {{ strtoupper($leader->status_jabatan) }}</h6>
                      <small class="text-body-secondary">Sekarang</small>
                    </div>
                    <p class="mb-2">Mulai: {{ \Carbon\Carbon::parse($leader->start_date)->translatedFormat('d F Y') }}</p>
                  </div>
                </li>
                @endif
                
                <!-- Riwayat Sebelumnya -->
                @forelse($leader->histories as $history)
                <li class="timeline-item timeline-item-transparent {{ $loop->last && $leader->end_date ? 'border-transparent' : '' }}">
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
                @if($leader->end_date)
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
        
        <!-- Tab Daftar PKS -->
        <div class="tab-pane fade {{ request('page') || request('year') || request('search') ? 'show active' : '' }}" id="navs-pks" role="tabpanel">
          <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="card-title mb-0"><i class="icon-base ti tabler-writing-sign icon-lg me-2"></i>Daftar PKS yang di tandatangani</h5>
              
              <form action="{{ route('admin.leaders.show', $leader->id) }}" method="GET" class="d-flex align-items-center gap-2" id="autoSearchForm">
                  <select name="year" class="form-select form-select-sm" style="min-width: 120px;" onchange="this.form.submit()">
                      @foreach($availableYears as $year)
                          <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                      @endforeach
                  </select>
                  <div class="input-group input-group-sm" style="max-width: 200px;">
                      <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                      <input type="text" name="search" class="form-control" id="autoSearchInput" placeholder="Cari PKS / Korlap..." value="{{ request('search') }}">
                  </div>
              </form>
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0">
                  <thead class="border-bottom">
                      <tr>
                          <th class="ps-4">No PKS</th>
                          <th>Korlap</th>
                          <th>Status</th>
                          <th class="pe-4">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                      @forelse($agreementsInYear as $agreement)
                          <tr>
                              <td class="ps-4">
                                  <span class="fw-medium text-primary">{{ $agreement->agreement_number }}</span><br>
                                  <small class="text-muted">{{ \Carbon\Carbon::parse($agreement->start_date)->translatedFormat('d M Y') }}</small>
                              </td>
                              <td>{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                              <td>
                                  @if($agreement->status == 'active')
                                      <span class="badge bg-label-success">Aktif</span>
                                  @elseif($agreement->status == 'pending_renewal')
                                      <span class="badge bg-label-warning">Perlu Perpanjangan</span>
                                  @elseif($agreement->status == 'expired')
                                      <span class="badge bg-label-danger">Kedaluwarsa</span>
                                  @elseif($agreement->status == 'terminated')
                                      <span class="badge bg-label-dark">Dihentikan</span>
                                  @else
                                      <span class="badge bg-label-secondary">{{ ucfirst($agreement->status) }}</span>
                                  @endif
                              </td>
                              <td class="pe-4">
                                  <a href="{{ route('masterdata.agreements.show', $agreement->id) }}" class="btn btn-sm btn-outline-primary"><i class="ti tabler-eye icon-xs me-1"></i>Detail</a>
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="4" class="text-center py-5 text-muted">
                                  <i class="icon-base ti tabler-inbox icon-xl mb-3 d-block"></i>
                                  Tidak ada data penandatanganan PKS pada tahun ini.
                              </td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
            </div>
            @if($agreementsInYear->hasPages())
            <div class="card-footer py-3 border-top">
                {{ $agreementsInYear->links('pagination::bootstrap-5') }}
            </div>
            @endif
          </div>
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
