@extends('layouts.contentNavbarLayout')

@section('title', 'Kontak Masyarakat')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kontak Masyarakat (Pelapor)</h5>
    </div>
    
    <div class="card-body pb-0">
        <form method="GET" action="{{ route('admin.kontak-masyarakat.index') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama / No. WhatsApp..." value="{{ $search }}">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary"><i class="ti tabler-search me-1"></i> Cari</button>
                <a href="{{ route('admin.kontak-masyarakat.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive text-nowrap mt-3">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelapor</th>
                    <th>No. WhatsApp</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($contacts as $contact)
                <tr>
                    <td>{{ $contacts->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $contact->reporter_name }}</strong></td>
                    <td>{{ $contact->reporter_phone }}</td>
                    <td>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->reporter_phone) }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="ti tabler-brand-whatsapp me-1"></i> Hubungi WA
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data kontak masyarakat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer d-flex justify-content-center pb-0">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
