@extends('layouts.app')

@section('title', 'Profil Bendahara: ' . ($treasurer->user->name ?? 'N/A'))

@section('skeleton')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('layouts.partials._skeleton-field-coordinator-show')
    </div>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Detail Profil Bendahara</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.treasurers.index') }}">Bendahara</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.treasurers.index') }}" class="btn btn-outline-secondary shadow-sm"><i class="ri ri-arrow-left-line me-1"></i> Kembali ke Daftar</a>
            </div>
        </div>

        @php
            $uName = $treasurer->user->name ?? 'N/A';
            $uAvatar = ($treasurer->user && $treasurer->user->img) ? asset('storage/' . $treasurer->user->img) : "https://ui-avatars.com/api/?name=".urlencode($uName)."&background=auto&color=fff&rounded=true&size=120";
            $isActive = $treasurer->user ? $treasurer->user->is_active : false;
        @endphp

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body pt-5 text-center">
                        <div class="user-avatar-section mb-4">
                            <div class="position-relative d-inline-block">
                                <img class="img-fluid rounded-circle shadow-sm {{ !$isActive ? 'opacity-50' : '' }}" style="object-fit: cover; width: 120px; height: 120px;" src="{{ $uAvatar }}" alt="Avatar" />
                            </div>
                            <h5 class="mt-3 mb-1 fw-bold {{ !$isActive ? 'text-muted' : 'text-dark' }}">{{ $uName }}</h5>
                            @if($isActive) <span class="badge bg-label-primary rounded-pill px-3 py-2 mt-1"><i class="ri ri-vip-crown-line me-1"></i> Bendahara Aktif</span>
                            @else <span class="badge bg-label-danger rounded-pill px-3 py-2 mt-1"><i class="ri ri-history-line me-1"></i> Purna Tugas</span> @endif
                        </div>

                        <h6 class="pb-2 border-bottom text-start mb-3 mt-4">Informasi Personal</h6>
                        <ul class="list-unstyled mb-4 text-start small">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-fingerprint-line text-muted me-2 ri-20px"></i>
                                <div><span class="d-block text-muted" style="font-size: 0.7rem;">Nomor Induk Pegawai (NIP)</span><span class="fw-medium text-dark">{{ formatNip($treasurer->employee_number) }}</span></div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-mail-line text-muted me-2 ri-20px"></i>
                                <div><span class="d-block text-muted" style="font-size: 0.7rem;">Email (Username)</span><span class="fw-medium text-dark">{{ $treasurer->user->email ?? '-' }} ({{ $treasurer->user->username ?? '-' }})</span></div>
                            </li>
                            @php
                                $statusLabel = 'Bendahara Definitif (Tetap)';
                                if($treasurer->status_jabatan == 'plt') $statusLabel = 'Pelaksana Tugas (Plt)';
                                if($treasurer->status_jabatan == 'plh') $statusLabel = 'Pelaksana Harian (Plh)';
                            @endphp
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-briefcase-4-line text-muted me-2 ri-20px"></i>
                                <div><span class="d-block text-muted" style="font-size: 0.7rem;">Status Jabatan</span><span class="fw-medium text-primary">{{ $statusLabel }}</span></div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-calendar-check-line text-muted me-2 ri-20px"></i>
                                <div><span class="d-block text-muted" style="font-size: 0.7rem;">Mulai Menjabat</span><span class="fw-medium text-dark">{{ $treasurer->start_date ? \Carbon\Carbon::parse($treasurer->start_date)->translatedFormat('d F Y') : '-' }}</span></div>
                            </li>
                        </ul>

                        @if($treasurer->histories->isNotEmpty())
                            <h6 class="pb-2 border-bottom text-start mb-3 mt-5"><i class="ri ri-history-line me-1"></i> Jejak Riwayat Jabatan</h6>
                            <ul class="timeline mb-0 text-start ps-3" style="border-left: 2px solid #e1e4e8; list-style-type: none;">
                                @foreach($treasurer->histories as $history)
                                    <li class="mb-3 position-relative" style="padding-left: 15px;">
                                        <span class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -24px; top: 5px; border: 2px solid #fff;"></span>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                @if($history->status_jabatan == 'tetap') Bendahara Definitif (Tetap)
                                                @elseif($history->status_jabatan == 'plt') Pelaksana Tugas (Plt)
                                                @else Pelaksana Harian (Plh) @endif
                                            </span>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($history->start_date)->translatedFormat('d M Y') }} <i class="ri ri-arrow-right-line align-middle mx-1"></i> {{ $history->end_date ? \Carbon\Carbon::parse($history->end_date)->translatedFormat('d M Y') : 'Sekarang' }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
