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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0 fw-bold">Daftar Arsip Dokumen</h5>
            <span class="badge bg-label-primary rounded-pill px-3">{{ $histories->total() ?? 0 }} Arsip Tersimpan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="20%">Tanggal Arsip</th>
                            <th width="40%">Catatan Perubahan</th>
                            <th width="25%">Dibuat Oleh</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($histories as $history)
                            @php
                                // Setup Avatar Pembuat
                                $uName = $history->generator->name ?? 'Sistem Server';
                                $uAvatar = ($history->generator && $history->generator->img)
                                    ? asset('storage/' . $history->generator->img)
                                    : "https://ui-avatars.com/api/?name=" . urlencode($uName) . "&background=random&color=fff&size=32&rounded=true&bold=true";
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded bg-label-danger"><i class="ri ri-file-pdf-2-line ri-20px"></i></span>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $history->created_at->translatedFormat('d M Y') }}</span>
                                            <small class="text-muted">{{ $history->created_at->format('H:i') }} WIB</small>
                                        </div>
                                    </div>
                                </td>
                               <td>
                                    @php
                                        // 1. Pecah teks berdasarkan titik koma (;)
                                        // 2. array_map('trim') untuk membuang spasi berlebih
                                        // 3. array_filter untuk membuang array kosong (jika ada titik koma di akhir kalimat)
                                        $notesList = array_filter(array_map('trim', explode(';', $history->notes)));
                                        $hasMultiple = count($notesList) > 1;

                                        // Ambil kalimat pertama sebagai preview, batasi maksimal 50 karakter
                                        $previewText = Str::limit($notesList[0] ?? $history->notes, 50);
                                        $collapseId = 'collapseNote_' . $history->id;
                                    @endphp

                                    @if($hasMultiple || strlen($history->notes) > 50)
                                        {{-- ✅ JIKA TEKS PANJANG ATAU PUNYA TITIK KOMA (Bisa di-klik) --}}
                                        <div class="d-flex flex-column align-items-start">
                                            <div class="d-flex align-items-center gap-2 cursor-pointer"
                                                 data-bs-toggle="collapse"
                                                 data-bs-target="#{{ $collapseId }}"
                                                 aria-expanded="false"
                                                 aria-controls="{{ $collapseId }}">
                                                <span class="text-body fw-medium">{{ $previewText }}</span>
                                                <span class="badge bg-label-primary rounded-pill px-2 py-1" data-bs-toggle="tooltip" title="Klik untuk lihat detail">
                                                    <i class="ri ri-arrow-down-s-line"></i> Detail
                                                </span>
                                            </div>

                                            {{-- Area Ekspansi (Tersembunyi secara default) --}}
                                            <div class="collapse w-100 mt-2" id="{{ $collapseId }}">
                                                <div class="p-3 bg-lighter rounded-3 border">
                                                    @if($hasMultiple)
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($notesList as $note)
                                                                <li class="d-flex align-items-start mb-2">
                                                                    <i class="ri ri-checkbox-circle-fill text-primary me-2 mt-1 ri-14px"></i>
                                                                    <span class="text-wrap" style="white-space: normal; line-height: 1.4;">{{ $note }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="mb-0 text-wrap" style="white-space: normal; line-height: 1.4;">{{ $history->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ✅ JIKA TEKS PENDEK DAN TIDAK ADA TITIK KOMA (Tampil biasa) --}}
                                        <span class="text-wrap" style="white-space: normal;">{{ $history->notes }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $uAvatar }}" alt="Avatar" class="rounded-circle me-2 shadow-sm" width="28" height="28" style="object-fit: cover;">
                                        <span class="fw-medium text-dark">{{ $uName }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ asset('storage/' . $history->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-icon btn-text-info rounded-pill" data-bs-toggle="tooltip"
                                            title="Buka PDF Baru">
                                            <i class="icon-base ri ri-external-link-line ri-20px"></i>
                                        </a>
                                        <a href="{{ asset('storage/' . $history->file_path) }}" download
                                            class="btn btn-sm btn-icon btn-text-primary rounded-pill" data-bs-toggle="tooltip"
                                            title="Unduh PDF">
                                            <i class="icon-base ri ri-download-cloud-2-line ri-20px"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="ri ri-folder-open-line text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                                    <p class="mb-0 text-muted">Belum ada arsip PDF yang tersimpan untuk perjanjian ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($histories->hasPages())
            <div class="card-footer border-top px-4 py-3">
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
