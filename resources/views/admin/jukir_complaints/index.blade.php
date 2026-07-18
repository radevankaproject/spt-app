@extends('layouts.contentNavbarLayout')

@section('title', 'Data Pengaduan Jukir')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Pengaduan Juru Parkir</h5>
    </div>
    
    <div class="card-body pb-0">
        <form method="GET" action="{{ route('admin.jukir-complaints.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama/HP Pelapor atau Jukir..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="valid" {{ $status == 'valid' ? 'selected' : '' }}>Valid (Diterima)</option>
                    <option value="invalid" {{ $status == 'invalid' ? 'selected' : '' }}>Tidak Valid (Ditolak)</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="ti tabler-search me-1"></i> Cari</button>
                <a href="{{ route('admin.jukir-complaints.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive text-nowrap mt-3">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pelapor</th>
                    <th>Jukir Terlapor</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($complaints as $complaint)
                <tr>
                    <td>{{ $complaints->firstItem() + $loop->index }}</td>
                    <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $complaint->reporter_name }}</strong><br>
                        <small class="text-muted">{{ $complaint->reporter_phone }}</small>
                    </td>
                    <td>
                        {{ $complaint->jukir->nama_jukir ?? '-' }}<br>
                        <small class="text-muted">{{ $complaint->jukir->id_jukir ?? '-' }}</small>
                    </td>
                    <td>{{ ucfirst($complaint->category) }}</td>
                    <td>
                        @if($complaint->status == 'pending')
                            <span class="badge bg-warning">Menunggu</span>
                        @elseif($complaint->status == 'valid')
                            <span class="badge bg-success">Valid</span>
                        @else
                            <span class="badge bg-danger">Tidak Valid</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.jukir-complaints.show', $complaint) }}" class="btn btn-sm btn-info">
                            <i class="ti tabler-eye me-1"></i> Detail & Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pengaduan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer d-flex justify-content-center pb-0">
        {{ $complaints->links() }}
    </div>
</div>
@endsection
