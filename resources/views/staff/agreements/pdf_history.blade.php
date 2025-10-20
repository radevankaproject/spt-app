@extends('layouts.app')

@section('title', 'Histori PDF: ' . $agreement->agreement_number)

@section('skeleton')
    @include('layouts.partials._skeleton-pdf-history')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Histori Arsip PDF</h4>
            <p class="text-muted mb-0">Perjanjian Nomor: <strong>{{ $agreement->agreement_number }}</strong></p>
        </div>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.index') }}">PKS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.show', $agreement->id) }}">Detail</a>
                    </li>
                    <li class="breadcrumb-item active">Histori PDF</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Arsip Dokumen</h5>
            <a href="{{ route('masterdata.agreements.show', $agreement->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i>Kembali ke Detail
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <th>Catatan Perubahan</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $history->created_at->translatedFormat('d F Y, H:i') }}</span>
                            </td>
                            <td>{{ $history->notes }}</td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $history->generator->name ?? 'Sistem' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ asset('storage/' . $history->file_path) }}" target="_blank"
                                    class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip"
                                    title="Lihat/Unduh PDF">
                                    <i class="icon-base ri ri-download-2-line"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6">
                                <p class="mb-0">Belum ada arsip PDF yang tersimpan untuk perjanjian ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($histories->hasPages())
            <div class="card-footer">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
@endsection
