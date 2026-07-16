@extends('layouts.contentNavbarLayout')

@section('title', 'Histori Perjalanan PKS')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/timeline.css') }}" />
    <style>
        .timeline-event.card { border: none; box-shadow: 0 0.125rem 0.25rem 0 rgba(161, 172, 184, 0.15); transition: all 0.3s ease; }
        .timeline-event.card:hover { box-shadow: 0 0.25rem 0.75rem 0 rgba(105, 108, 255, 0.15); transform: translateY(-2px); }
        .cursor-pointer { cursor: pointer; }
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
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-history me-2"></i>Riwayat Perjanjian</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Riwayat pembaruan perjanjian kerjasama.</p>
            </div>
        </div>
        <i class="ti tabler-history position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Histori Perjalanan PKS</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.index') }}">PKS</a></li>
                    <li class="breadcrumb-item active">Jejak Histori</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Form Filter Premium --}}
    <div class="card mb-5 border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('masterdata.agreement-histories.index') }}" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <label for="agreement_id" class="form-label fw-bold text-primary"><i class="ti tabler-search me-1"></i> Cari & Pilih Perjanjian</label>
                        <select name="agreement_id" id="agreement_id" class="form-select select2" data-allow-clear="true" required>
                            <option value="">Ketik No. PKS atau Nama Koordinator...</option>
                            @foreach ($agreementsForFilter as $item)
                                <option value="{{ $item->id }}" {{ $selectedAgreementId == $item->id ? 'selected' : '' }}>
                                    {{ $item->agreement_number }} — {{ $item->fieldCoordinator->user->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2 mt-4 mt-md-0 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary rounded-pill btn-action w-100 shadow-sm mt-4"><i class="ti tabler-history me-1"></i> Lacak Jejak</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Timeline --}}
    @if ($agreement)
        {{-- Kartu Ringkasan PKS (Tampil jika PKS terpilih) --}}
        <div class="card bg-primary bg-opacity-10 border-0 mb-5 rounded-3 shadow-none">
            <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md">
                        @php
                            $cName = $agreement->fieldCoordinator->user->name ?? 'N/A';
                            $cAvatar = ($agreement->fieldCoordinator->user && $agreement->fieldCoordinator->user->img)
                                ? asset('storage/'.$agreement->fieldCoordinator->user->img)
                                : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=40&rounded=true&bold=true";
                        @endphp
                        <img src="{{ $cAvatar }}" alt="Korlap" class="rounded-circle shadow-sm" style="object-fit:cover;">
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $agreement->agreement_number }}</h6>
                        <small class="text-muted">Korlap: <span class="fw-medium">{{ $cName }}</span></small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary rounded-pill mb-1">{{ $agreement->histories->count() }} Total Aktivitas</span><br>
                    <a href="{{ route('masterdata.agreements.show', $agreement->id) }}" class="small fw-bold text-primary text-decoration-underline">Lihat Detail PKS <i class="ti tabler-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row overflow-hidden">
            <div class="col-12">
                <ul class="timeline timeline-center mt-3">
                    @forelse ($agreement->histories as $history)
                        @php
                            $positionClass = $loop->odd ? 'timeline-item-left' : 'timeline-item-right';

                            // Styling Event
                            $icon = 'ti tabler-file-text'; $color = 'secondary'; $eventName = 'Aktivitas Sistem';
                            switch ($history->event_type) {
                                case 'agreement_created': $icon = 'ti tabler-file-add'; $color = 'primary'; $eventName = 'PKS Diterbitkan'; break;
                                case 'location_added': $icon = 'ti tabler-map-pin-plus'; $color = 'success'; $eventName = 'Lokasi Ditambahkan'; break;
                                case 'location_removed': $icon = 'ti tabler-map-pin-minus'; $color = 'danger'; $eventName = 'Lokasi Ditarik'; break;
                                case 'deposit_changed': $icon = 'ti tabler-currency-dollar'; $color = 'info'; $eventName = 'Perubahan Setoran'; break;
                                case 'status_changed': $icon = 'ti tabler-refresh'; $color = 'warning'; $eventName = 'Status Berubah'; break;
                                case 'agreement_renewed': $icon = 'ti tabler-refresh'; $color = 'success'; $eventName = 'PKS Diperpanjang'; break;
                                case 'agreement_terminated':
                                case 'agreement_expired': $icon = 'ti tabler-user-off'; $color = 'dark'; $eventName = 'PKS Berakhir/Diputus'; break;
                            }

                            // Avatar Pembuat
                            $uName = $history->changer->name ?? 'Sistem Otomatis';
                            $uAvatar = ($history->changer && $history->changer->img)
                                ? asset('storage/' . $history->changer->img)
                                : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=32&rounded=true&bold=true";

                            // Logika Teks Panjang (Sama seperti PDF History)
                            $notesList = array_filter(array_map('trim', explode(';', $history->notes)));
                            $hasMultiple = count($notesList) > 1;
                            $previewText = Str::limit($notesList[0] ?? $history->notes, 60);
                            $collapseId = 'collapseHistory_' . $history->id;
                        @endphp

                        <li class="timeline-item {{ $positionClass }}">
                            <span class="timeline-indicator timeline-indicator-{{ $color }} bg-white shadow-sm">
                                <i class="icon-base {{ $icon }}"></i>
                            </span>
                            <div class="timeline-event card p-0 border border-{{ $color }} border-opacity-25">
                                <div class="card-header border-bottom bg-{{ $color }} bg-opacity-10 d-flex justify-content-between align-items-center flex-wrap py-2 px-3 p-4">
                                    <h6 class="card-title mb-0 fw-bold text-{{ $color }}">{{ $eventName }}</h6>
                                    <div class="meta"><span class="badge bg-white text-dark shadow-sm">{{ $history->created_at->diffForHumans() }}</span></div>
                                </div>
                                <div class="card-body py-3 px-3 p-4">

                                    {{-- ✅ LOGIKA TEKS INTERAKTIF --}}
                                    <div class="mb-3">
                                        @if($hasMultiple || strlen($history->notes) > 60)
                                            <div class="d-flex flex-column align-items-start">
                                                <div class="d-flex justify-content-between align-items-center w-100 cursor-pointer"
                                                     data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                                    <span class="text-dark fw-medium">{{ $previewText }}</span>
                                                    <span class="badge bg-label-secondary rounded-pill ms-2"><i class="ti tabler-arrow-down"></i></span>
                                                </div>
                                                <div class="collapse w-100 mt-2" id="{{ $collapseId }}">
                                                    <div class="p-2 bg-lighter rounded-3">
                                                        @if($hasMultiple)
                                                            <ul class="list-unstyled mb-0 ps-1">
                                                                @foreach($notesList as $note)
                                                                    <li class="d-flex align-items-start mb-1 text-muted small">
                                                                        <i class="ti tabler-arrow-right-s-filled text-primary me-1"></i>
                                                                        <span class="text-wrap" style="white-space: normal;">{{ $note }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="mb-0 text-muted small text-wrap" style="white-space: normal;">{{ $history->notes }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-dark fw-medium text-wrap" style="white-space: normal;">{{ $history->notes }}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center border-top pt-2">
                                        <div class="avatar avatar-xs me-2">
                                            <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle shadow-sm" style="object-fit:cover;" />
                                        </div>
                                        <small class="text-muted">Oleh: <span class="fw-bold text-dark">{{ $uName }}</span></small>
                                    </div>
                                </div>
                                <div class="timeline-event-time fw-bold text-muted" style="font-size:0.75rem;">
                                    {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-indicator timeline-indicator-secondary"><i class="ri icon-base ti tabler-info-circle"></i></span>
                            <div class="timeline-event">
                                <p class="text-center text-muted">Belum ada riwayat tercatat untuk perjanjian ini.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-6 p-4">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti tabler-history ti-lg"></i></span>
                </div>
                <h5 class="fw-bold text-dark">Ruang Rekam Jejak</h5>
                <p class="mb-0 text-muted">Silakan cari dan pilih Nomor PKS pada filter di atas untuk melihat seluruh perjalanan sejarah kontrak.</p>
            </div>
        </div>
    @endif
@endsection

@section('vendor-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
    <script src="{{ asset('assets/js/extended-ui-timeline.js') }}"></script>
@endsection

@section('page-script')
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            const select2 = $('.select2');
            if (select2.length) {
                select2.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Ketik No PKS atau Nama Korlap...',
                        dropdownParent: $this.parent(),
                        language: { noResults: () => "Perjanjian tidak ditemukan" }
                    });
                });
            }

            // Aktifkan Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
