@extends('layouts.app')

@section('title', 'Histori PDF: ' . $agreement->agreement_number)

@section('skeleton')
    @include('layouts.partials._skeleton-pdf-history')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Histori Arsip Dokumen PKS</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.index') }}">PKS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.show', $agreement->id) }}">{{ $agreement->agreement_number }}</a></li>
                    <li class="breadcrumb-item active">Arsip PDF</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('masterdata.agreements.show', $agreement->id) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Detail
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
            <h5 class="card-title mb-0 fw-bold">Daftar Arsip Dokumen</h5>
            <span class="badge bg-label-primary rounded-pill px-3">{{ $histories->total() ?? 0 }} Arsip Tersimpan</span>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-3">
                @forelse ($histories as $history)
                    @php
                        $uName = $history->generator->name ?? 'Sistem Server';
                        $uAvatar = ($history->generator && $history->generator->img)
                            ? asset('storage/' . $history->generator->img)
                            : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=32&rounded=true&bold=true";
                            
                        $notesList = array_filter(array_map('trim', explode(';', $history->notes)));
                        $hasMultiple = count($notesList) > 1;
                        $collapseId = 'collapseNote_' . $history->id;
                    @endphp
                    
                    <div class="border rounded-4 p-3 bg-white shadow-sm position-relative overflow-hidden" style="transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            
                            {{-- Kiri: Ikon & Info Tanggal --}}
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md rounded-circle bg-label-danger d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="ri ri-file-pdf-2-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Arsip PKS - {{ $history->created_at->translatedFormat('d M Y') }}</h6>
                                    <div class="d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                        <i class="ri ri-time-line me-1"></i> {{ $history->created_at->format('H:i') }} WIB
                                        <span class="mx-2">•</span>
                                        <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle me-1" width="16" height="16" style="object-fit: cover;">
                                        Dibuat oleh {{ Str::limit($uName, 15) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Kanan: Aksi --}}
                            <div class="d-flex gap-2 align-items-center">
                                <a href="{{ asset('storage/' . $history->file_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info rounded-pill fw-medium">
                                    <i class="ri ri-eye-line me-1"></i> Lihat
                                </a>
                                <a href="{{ asset('storage/' . $history->file_path) }}" download
                                    class="btn btn-sm btn-primary rounded-pill fw-medium shadow-sm">
                                    <i class="ri ri-download-cloud-2-line me-1"></i> Unduh
                                </a>
                            </div>
                        </div>

                        {{-- Bawah: Catatan Perubahan (Premium Box) --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-start gap-2">
                                <i class="ri ri-message-3-line text-primary mt-1"></i>
                                <div class="w-100">
                                    @if($hasMultiple)
                                        <div class="d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                            <span class="text-dark fw-medium" style="font-size: 0.9rem;">Terdapat {{ count($notesList) }} catatan perubahan. <span class="text-primary">Lihat rincian</span></span>
                                            <i class="ri ri-arrow-down-s-line text-muted"></i>
                                        </div>
                                        <div class="collapse mt-2" id="{{ $collapseId }}">
                                            <div class="bg-lighter p-3 rounded-3">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($notesList as $note)
                                                        <li class="d-flex align-items-start mb-2 text-muted" style="font-size: 0.85rem;">
                                                            <i class="ri ri-check-line text-success me-2 fs-6"></i>
                                                            <span style="line-height: 1.4;">{{ $note }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @else
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.4;">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 border rounded-4 bg-lighter" style="border-style: dashed !important; border-width: 2px !important;">
                        <div class="avatar avatar-xl mx-auto mb-3 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri ri-folder-open-line text-muted fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Arsip Kosong</h6>
                        <p class="mb-0 text-muted">Belum ada dokumen PDF lama yang tersimpan.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @if ($histories->hasPages())
            <div class="card-footer border-top px-4 py-3 bg-transparent">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aktifkan Tooltips Bootstrap untuk text yang terpotong dan tombol aksi
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
