@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 2rem 2rem 1.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .profile-avatar-wrap {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.4);
        overflow: hidden;
        flex-shrink: 0;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .profile-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-avatar-initial {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 2px;
    }
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
    }
    .stat-card .stat-icon-bg {
        position: absolute;
        bottom: -10px;
        right: -10px;
        font-size: 5rem;
        opacity: 0.06;
        line-height: 1;
    }
    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .activity-row {
        transition: background-color 0.15s ease;
    }
    .activity-row:hover {
        background-color: rgba(105, 108, 255, 0.04);
    }
</style>
@endpush

@section('skeleton')
    @include('layouts.partials._skeleton-profile')
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Akun /</span> Profil Saya</h4>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('profile.settings') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="ri icon-base ri-settings-4-line me-1"></i> Edit Profil & Sandi
            </a>
        </div>
    </div>

    @php
        $nameParts = explode(' ', trim($user->name));
        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

        $roleLabels = [
            'admin' => ['text' => 'Administrator Sistem', 'icon' => 'ri-shield-keyhole-line'],
            'leader' => ['text' => 'Kepala UPT / Pimpinan', 'icon' => 'ri-vip-crown-line'],
            'staff_pks' => ['text' => 'Staff Administrasi PKS', 'icon' => 'ri-file-list-3-line'],
            'staff_keu' => ['text' => 'Staff Keuangan', 'icon' => 'ri-money-dollar-circle-line'],
            'treasurer' => ['text' => 'Bendahara Penerimaan', 'icon' => 'ri-wallet-3-line'],
            'field_coordinator' => ['text' => 'Koordinator Lapangan', 'icon' => 'ri-map-pin-user-line'],
        ];
        $currentRole = $roleLabels[$user->role] ?? ['text' => ucfirst($user->role), 'icon' => 'ri-user-line'];
    @endphp

    {{-- HERO CARD --}}
    <div class="profile-hero mb-4 shadow-lg">
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 position-relative" style="z-index:1;">
            <div class="profile-avatar-wrap">
                @if ($user->img)
                    <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}">
                @else
                    <span class="profile-avatar-initial">{{ $initials }}</span>
                @endif
            </div>
            <div class="text-center text-sm-start">
                <h3 class="fw-bold mb-1 text-white">{{ $user->name }}</h3>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                        <i class="ri icon-base {{ $currentRole['icon'] }} me-1"></i> {{ $currentRole['text'] }}
                    </span>
                </div>
                <p class="mb-0 opacity-75 small">
                    <i class="ri icon-base ri-mail-line me-1"></i> {{ $user->email }}
                    @if($user->phone_number)
                        <span class="mx-2">·</span>
                        <i class="ri icon-base ri-phone-line me-1"></i> {{ $user->phone_number }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Info Details --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="ri icon-base ri-user-3-line me-2 text-primary"></i>Detail Informasi</h6>
                </div>
                <div class="card-body py-2">
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Nama Lengkap</small>
                        <span class="fw-medium text-dark">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Username</small>
                        <span class="fw-medium text-dark">{{ $user->username }}</span>
                    </div>
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Alamat Email</small>
                        <span class="fw-medium text-dark">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">No. Telepon</small>
                        <span class="fw-medium text-dark">{{ $user->phone_number ?? '-' }}</span>
                    </div>
                    @if($user->employee_number)
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">NIP</small>
                        <span class="fw-medium text-dark">{{ formatNip($user->employee_number) }}</span>
                    </div>
                    @endif
                    @if($user->role === 'leader' && $user->leader)
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Status Jabatan</small>
                        <span class="badge bg-label-{{ $user->leader->status_jabatan === 'aktif' ? 'success' : 'secondary' }} text-uppercase">{{ $user->leader->status_jabatan ?? '-' }}</span>
                    </div>
                    @endif
                    @if($user->role === 'field_coordinator' && $user->fieldCoordinator)
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Alamat</small>
                        <span class="fw-medium text-dark">{{ $user->fieldCoordinator->address ?? '-' }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px;">Bergabung Sejak</small>
                        <span class="fw-medium text-dark">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Stats & Activity --}}
        <div class="col-xl-8 col-lg-7">

            {{-- Statistics Cards --}}
            <div class="row g-4 mb-4">
                @if(in_array($user->role, ['admin', 'staff_pks']))
                    <div class="col-sm-6 col-lg-4">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted fw-semibold mb-1 small">Korlap Dikelola</p>
                                        <h3 class="fw-bold mb-0">{{ $stats['korlapCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri icon-base ri-user-settings-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg"><i class="ri icon-base ri-team-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted fw-semibold mb-1 small">Zona Diperbarui</p>
                                        <h3 class="fw-bold mb-0">{{ $stats['roadSectionCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-success"><i class="ri icon-base ri-route-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg"><i class="ri icon-base ri-route-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted fw-semibold mb-1 small">PKS Tervalidasi</p>
                                        <h3 class="fw-bold mb-0">{{ $stats['agreementPdfCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-info"><i class="ri icon-base ri-file-text-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg"><i class="ri icon-base ri-file-text-line"></i></span>
                            </div>
                        </div>
                    </div>

                @elseif($user->role === 'staff_keu')
                    <div class="col-sm-6">
                        <div class="card stat-card shadow-lg h-100 text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-white opacity-75 fw-semibold mb-1 small text-uppercase" style="letter-spacing:0.5px;">Nominal Divalidasi</p>
                                        <h3 class="fw-bold mb-0 text-white">Rp {{ number_format($stats['validatedDepositsAmount'] ?? 0, 0, ',', '.') }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-white text-success"><i class="ri icon-base ri-money-dollar-circle-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg text-white"><i class="ri icon-base ri-wallet-3-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted fw-semibold mb-1 small">Transaksi Setoran</p>
                                        <h3 class="fw-bold mb-0">{{ $stats['validatedDepositsCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri icon-base ri-check-double-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg"><i class="ri icon-base ri-bank-card-line"></i></span>
                            </div>
                        </div>
                    </div>

                @elseif($user->role === 'treasurer')
                    <div class="col-sm-6">
                        <div class="card stat-card shadow-lg h-100 text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-white opacity-75 fw-semibold mb-1 small text-uppercase" style="letter-spacing:0.5px;">Setoran Masa Jabatan</p>
                                        <h3 class="fw-bold mb-0 text-white">Rp {{ number_format($stats['termDepositsAmount'] ?? 0, 0, ',', '.') }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-white text-danger"><i class="ri icon-base ri-wallet-3-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg text-white"><i class="ri icon-base ri-safe-2-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted fw-semibold mb-1 small">Total Transaksi</p>
                                        <h3 class="fw-bold mb-0">{{ $stats['termDepositsCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-label-warning"><i class="ri icon-base ri-exchange-funds-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg"><i class="ri icon-base ri-exchange-funds-line"></i></span>
                            </div>
                        </div>
                    </div>

                @elseif($user->role === 'leader')
                    <div class="col-sm-6">
                        <div class="card stat-card shadow-lg h-100 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-white opacity-75 fw-semibold mb-1 small text-uppercase" style="letter-spacing:0.5px;">PKS Ditandatangani</p>
                                        <h3 class="fw-bold mb-0 text-white">{{ $stats['signedAgreementsCount'] ?? 0 }}</h3>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-3 bg-white text-primary"><i class="ri icon-base ri-quill-pen-line ri-24px"></i></span>
                                    </div>
                                </div>
                                <span class="stat-icon-bg text-white"><i class="ri icon-base ri-draft-line"></i></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Recent Activity --}}
            @if(in_array($user->role, ['admin', 'staff_pks', 'staff_keu']))
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="ri icon-base ri-history-line me-2 text-primary"></i>Aktivitas Terakhir</h6>
                            <span class="badge bg-label-secondary rounded-pill px-3 ms-2">10 Terakhir</span>
                        </div>
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-transparent border-end-0"><i class="ri icon-base ri-search-line text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchActivity" placeholder="Cari aktivitas...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle" id="activityTable">
                                <thead class="table-light">
                                    <tr>
                                        @if(in_array($user->role, ['admin', 'staff_pks']))
                                            <th class="py-3 ps-4 text-uppercase small fw-semibold text-muted">Lokasi Parkir</th>
                                            <th class="py-3 text-uppercase small fw-semibold text-muted">Keterangan</th>
                                            <th class="py-3 pe-4 text-uppercase small fw-semibold text-muted text-end">Waktu</th>
                                        @elseif($user->role === 'staff_keu')
                                            <th class="py-3 ps-4 text-uppercase small fw-semibold text-muted">No PKS</th>
                                            <th class="py-3 text-uppercase small fw-semibold text-muted">Nominal</th>
                                            <th class="py-3 pe-4 text-uppercase small fw-semibold text-muted text-end">Waktu Setor</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivities as $activity)
                                        <tr class="activity-row">
                                            @if(in_array($user->role, ['admin', 'staff_pks']))
                                                <td class="py-3 ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <span class="avatar-initial rounded bg-label-primary"><i class="ri icon-base ri-map-pin-line"></i></span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{{ $activity->parkingLocation->name ?? 'Lokasi Dihapus' }}</h6>
                                                            <small class="text-muted">{{ $activity->parkingLocation->roadSection->name ?? '-' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @php $action = strtolower($activity->action ?? ''); @endphp
                                                    @if(str_contains($action, 'create'))
                                                        <span class="badge bg-label-success rounded-pill px-3"><i class="ri icon-base ri-add-line me-1"></i>Dibuat</span>
                                                    @elseif(str_contains($action, 'update'))
                                                        <span class="badge bg-label-warning rounded-pill px-3"><i class="ri icon-base ri-pencil-line me-1"></i>Diperbarui</span>
                                                    @elseif(str_contains($action, 'delete'))
                                                        <span class="badge bg-label-danger rounded-pill px-3"><i class="ri icon-base ri-delete-bin-line me-1"></i>Dihapus</span>
                                                    @else
                                                        <span class="badge bg-label-secondary rounded-pill px-3">{{ $activity->action }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 pe-4 text-end text-muted small">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </td>
                                            @elseif($user->role === 'staff_keu')
                                                <td class="py-3 ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <span class="avatar-initial rounded bg-label-info"><i class="ri icon-base ri-file-list-3-line"></i></span>
                                                        </div>
                                                        <span class="fw-semibold">{{ $activity->agreement->agreement_number ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="fw-bold text-success">Rp {{ number_format($activity->amount, 0, ',', '.') }}</span>
                                                </td>
                                                <td class="py-3 pe-4 text-end text-muted small">
                                                    {{ \Carbon\Carbon::parse($activity->payment_date)->translatedFormat('d M Y') }}
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr id="emptyRow">
                                            <td colspan="3" class="text-center py-5">
                                                <i class="ri icon-base ri-inbox-line ri-3x text-muted opacity-50 d-block mb-2"></i>
                                                <p class="text-muted mb-0">Belum ada aktivitas yang tercatat.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    {{-- Hidden row for search not found --}}
                                    <tr id="notFoundRow" style="display: none;">
                                        <td colspan="3" class="text-center py-5">
                                            <i class="ri icon-base ri-search-eye-line ri-3x text-muted opacity-50 d-block mb-2"></i>
                                            <p class="text-muted mb-0">Pencarian tidak ditemukan.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchActivity');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#activityTable tbody tr.activity-row');
                const notFoundRow = document.getElementById('notFoundRow');
                let matchCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = '';
                        matchCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (notFoundRow) {
                    notFoundRow.style.display = (matchCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }
    });
</script>
@endpush
