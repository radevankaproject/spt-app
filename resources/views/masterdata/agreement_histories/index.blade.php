@extends('layouts.app')

@section('title', 'Histori Perjanjian Kerjasama')

@section('skeleton')
    @include('layouts.partials._skeleton-agreement-history-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    {{-- CSS untuk timeline bawaan template --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/timeline.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Histori Perjanjian Kerjasama</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Histori PKS</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Form Filter --}}
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Histori</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('masterdata.agreement-histories.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label for="agreement_id" class="form-label">Pilih Perjanjian (No. PKS atau Nama Korlap)</label>
                        <select name="agreement_id" id="agreement_id" class="form-select select2" data-allow-clear="true"
                            required>
                            <option value="">Cari dan Pilih Perjanjian...</option>
                            @foreach ($agreementsForFilter as $item)
                                <option value="{{ $item->id }}"
                                    {{ $selectedAgreementId == $item->id ? 'selected' : '' }}>
                                    {{ $item->agreement_number }} - {{ $item->fieldCoordinator->user->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Timeline --}}
    @if ($agreement)
        <div class="row overflow-hidden">
            <div class="col-12">
                <h5 class="text-center mb-6">Timeline untuk: <span class="fw-bold">{{ $agreement->agreement_number }}</span>
                </h5>
                <ul class="timeline timeline-center">
                    @forelse ($agreement->histories as $key => $history)
                        @php
                            // Menentukan posisi event (kiri/kanan) secara bergantian
                            $positionClass = $loop->odd ? 'timeline-item-left' : 'timeline-item-right';

                            // Menentukan ikon dan warna berdasarkan tipe event
                            $icon = 'ri-file-text-line';
                            $color = 'secondary';
                            switch ($history->event_type) {
                                case 'agreement_created':
                                    $icon = 'ri-file-add-line';
                                    $color = 'primary';
                                    break;
                                case 'location_added':
                                    $icon = 'ri-map-pin-add-line';
                                    $color = 'success';
                                    break;
                                case 'location_removed':
                                    $icon = 'ri-map-pin-user-line';
                                    $color = 'warning';
                                    break;
                                case 'deposit_changed':
                                    $icon = 'ri-money-dollar-circle-line';
                                    $color = 'info';
                                    break;
                                case 'status_changed':
                                case 'agreement_renewed':
                                    $icon = 'ri-refresh-line';
                                    $color = 'success';
                                    break;
                                case 'agreement_terminated':
                                case 'agreement_expired':
                                    $icon = 'ri-shield-x-line';
                                    $color = 'danger';
                                    break;
                            }
                        @endphp
                        <li class="timeline-item {{ $positionClass }}">
                            <span class="timeline-indicator timeline-indicator-{{ $color }}">
                                <i class="icon-base {{ $icon }}"></i>
                            </span>
                            <div class="timeline-event card p-0">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="card-title mb-0">{{ $history->notes }}</h6>
                                    <div class="meta"><small
                                            class="text-muted">{{ $history->created_at->diffForHumans() }}</small></div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <img src="{{ $history->changer && $history->changer->img ? asset('storage/' . $history->changer->img) : strtoupper(substr($history->changer->name ?? 'S', 0, 1)) }}"
                                                alt="Avatar" class="rounded-circle" />
                                            {{-- @else
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-secondary">{{ strtoupper(substr($history->creator->name ?? 'S', 0, 1)) }}</span>
                                            @endif --}}
                                        </div>
                                        <span>Oleh: <span
                                                class="fw-medium">{{ $history->changer->name ?? 'Sistem' }}</span></span>
                                    </div>
                                </div>
                                <div class="timeline-event-time">
                                    {{ $history->created_at->translatedFormat('d M y, H:i') }}
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-indicator timeline-indicator-secondary"><i
                                    class="icon-base ri-information-line"></i></span>
                            <div class="timeline-event">
                                <p class="text-center text-muted">Belum ada riwayat tercatat untuk perjanjian ini.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-6">
                <i class="ri-history-line ri-48px text-muted"></i>
                <p class="mt-4 text-muted">Silakan pilih sebuah perjanjian untuk melihat timeline historinya.</p>
            </div>
        </div>
    @endif
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/extended-ui-timeline.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select2 = $('.select2');
            if (select2.length) {
                select2.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Cari berdasarkan No. PKS atau Nama Korlap...',
                        dropdownParent: $this.parent()
                    });
                });
            }
        });
    </script>
@endpush
