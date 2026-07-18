@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Pengaduan Jukir')

@section('page-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
<style>
    .evidence-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
        transition: transform 0.2s;
    }
    .evidence-img:hover {
        transform: scale(1.05);
        border-color: #0b78a9;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('admin.jukir-complaints.index') }}" class="btn btn-secondary mb-3"><i class="ti tabler-arrow-left me-1"></i> Kembali</a>
    </div>

    <!-- Informasi Pengaduan -->
    <div class="col-md-7">
        <div class="card mb-4">
            <h5 class="card-header border-bottom">Detail Pengaduan</h5>
            <div class="card-body mt-3">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Nama Pelapor</th>
                        <td>: {{ $jukirComplaint->reporter_name }}</td>
                    </tr>
                    <tr>
                        <th>No. HP / WhatsApp</th>
                        <td>: {{ $jukirComplaint->reporter_phone }}
                            @if($jukirComplaint->reporter_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $jukirComplaint->reporter_phone) }}" target="_blank" class="btn btn-sm btn-success py-0 px-2 ms-2"><i class="ti tabler-brand-whatsapp me-1"></i> Chat WA</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Lapor</th>
                        <td>: {{ $jukirComplaint->created_at->format('d F Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>: <span class="badge bg-primary">{{ ucfirst($jukirComplaint->category) }}</span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @if($jukirComplaint->status == 'pending')
                                <span class="badge bg-warning">Menunggu Review</span>
                            @elseif($jukirComplaint->status == 'valid')
                                <span class="badge bg-success">Valid (Diterima)</span>
                            @else
                                <span class="badge bg-danger">Tidak Valid (Ditolak)</span>
                            @endif
                        </td>
                    </tr>
                    @if($jukirComplaint->user)
                    <tr>
                        <th>Ditanggapi Oleh</th>
                        <td>: {{ $jukirComplaint->user->name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Deskripsi</th>
                        <td>:</td>
                    </tr>
                </table>
                <div class="p-3 bg-light rounded border mt-2">
                    {{ $jukirComplaint->description }}
                </div>

                <h6 class="mt-4 fw-bold mb-3">Bukti Pelanggaran (Foto)</h6>
                <div class="d-flex flex-wrap gap-3">
                    @if($jukirComplaint->evidence && is_array($jukirComplaint->evidence) && count($jukirComplaint->evidence) > 0)
                        @foreach($jukirComplaint->evidence as $img)
                            <a href="{{ asset('storage/' . $img) }}" data-fancybox="gallery" data-caption="Bukti Pengaduan">
                                <img src="{{ asset('storage/' . $img) }}" class="evidence-img" alt="Bukti">
                            </a>
                        @endforeach
                    @elseif($jukirComplaint->evidence_urls && is_array($jukirComplaint->evidence_urls) && count($jukirComplaint->evidence_urls) > 0)
                        @foreach($jukirComplaint->evidence_urls as $img_url)
                            <a href="{{ $img_url }}" data-fancybox="gallery" data-caption="Bukti Pengaduan (Web)">
                                <img src="{{ $img_url }}" class="evidence-img" alt="Bukti Web">
                            </a>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada bukti foto yang dilampirkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Jukir & Review -->
    <div class="col-md-5">
        <div class="card mb-4">
            <h5 class="card-header border-bottom">Informasi Jukir Terlapor</h5>
            <div class="card-body mt-3 text-center">
                @if($jukirComplaint->jukir->image)
                    <img src="{{ asset('storage/' . $jukirComplaint->jukir->image) }}" class="rounded-circle mb-3 border" width="100" height="100" style="object-fit: cover;" alt="Foto">
                @else
                    <div class="rounded-circle mb-3 border bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ strtoupper(substr($jukirComplaint->jukir->nama_jukir, 0, 1)) }}
                    </div>
                @endif
                <h5 class="mb-1">{{ $jukirComplaint->jukir->nama_jukir }}</h5>
                <p class="text-muted mb-2">ID: {{ $jukirComplaint->jukir->id_jukir }}</p>
                <div class="bg-light p-2 rounded text-start">
                    <small class="text-muted d-block">Lokasi Parkir:</small>
                    <span class="fw-bold">{{ $jukirComplaint->jukir->parkingLocation->name ?? '-' }}</span><br>
                    <small>{{ $jukirComplaint->jukir->parkingLocation->roadSection->name ?? '-' }}</small>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.jukirs.show', $jukirComplaint->jukir) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="ti tabler-user me-1"></i> Lihat Detail Jukir
                    </a>
                </div>
            </div>
        </div>


    </div>

    <!-- Informasi Tindak Lanjut -->
    @if($jukirComplaint->field_officer_name || $jukirComplaint->follow_up_description || $jukirComplaint->follow_up_evidence_urls)
    <div class="col-md-12 mt-4">
        <div class="card mb-4 border-success">
            <h5 class="card-header border-bottom bg-success text-white">Laporan Selesai - Hasil Tindak Lanjut</h5>
            <div class="card-body mt-3">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Nama Petugas Lapangan</th>
                        <td>: {{ $jukirComplaint->field_officer_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Pelanggaran</th>
                        <td>: 
                            @if($jukirComplaint->is_violation_proven)
                                <span class="badge bg-danger">Terbukti Melanggar (+1 Poin)</span>
                            @else
                                <span class="badge bg-secondary">Tidak Terbukti</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi Penyelesaian</th>
                        <td>:</td>
                    </tr>
                </table>
                <div class="p-3 bg-light rounded border mt-2">
                    {{ $jukirComplaint->follow_up_description ?? '-' }}
                </div>

                <h6 class="mt-4 fw-bold mb-3">Bukti Turun Lapangan</h6>
                <div class="d-flex flex-wrap gap-3">
                    @if($jukirComplaint->follow_up_evidence_urls && is_array($jukirComplaint->follow_up_evidence_urls) && count($jukirComplaint->follow_up_evidence_urls) > 0)
                        @foreach($jukirComplaint->follow_up_evidence_urls as $img_url)
                            <a href="{{ $img_url }}" data-fancybox="followup-gallery" data-caption="Bukti Turun Lapangan">
                                <img src="{{ $img_url }}" class="evidence-img" alt="Bukti Tindak Lanjut">
                            </a>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada bukti foto tindak lanjut yang dilampirkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('page-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
@endsection
