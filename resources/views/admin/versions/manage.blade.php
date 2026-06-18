@extends('layouts.contentNavbarLayout')

@section('title', 'Manajemen Versi Aplikasi')



@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Versi Aplikasi</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Manajemen Versi</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-6">
        <!-- Kolom Kiri: Form Input -->
        <div class="col-lg-5">
            @if(Auth::user()->role !== 'leader')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0" id="formTitle">Tambah Versi Baru</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnCancelEdit" onclick="cancelEdit()">Batal Edit</button>
                </div>
                <div class="card-body">
                    <form id="versionForm" action="{{ route('admin.app-versions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="version" name="version"
                                        placeholder="Contoh: v1.0.1" value="{{ old('version') }}" required />
                                    <label for="version">Nomor Versi</label>
                                </div>
                                @error('version')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" id="release_date" name="release_date"
                                        value="{{ old('release_date', date('Y-m-d')) }}" required />
                                    <label for="release_date">Tanggal Rilis</label>
                                </div>
                                @error('release_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label class="fw-bold text-dark" for="changelog">Catatan Perubahan (Changelog)</label>
                                </div>
                                <textarea id="changelog" name="changelog" class="form-control" rows="8" required placeholder="Gunakan format Markdown (*Bold*, - List, dll) atau HTML biasa.">{{ old('changelog') }}</textarea>
                                <div class="form-text mt-2">Anda bisa menggunakan format <b>Markdown</b> (.md) secara langsung (seperti *Italic*, **Bold**, atau - List).</div>
                                @error('changelog')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="pt-6 text-end">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Versi</button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                     <i class="ti tabler-lock ti-xl text-muted mb-3"></i>
                     <h6>Akses Dibatasi</h6>
                     <p class="text-muted mb-0">Anda hanya dapat melihat riwayat versi aplikasi.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Daftar Versi -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">5 Versi Terakhir</h5>
                </div>
                <div class="card-body">
                    @forelse ($versions as $version)
                        <div class="mb-4 pb-4 @if (!$loop->last) border-bottom @endif">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1"><strong>Versi {{ $version->version }}</strong></h6>
                                <div>
                                    <small class="text-muted me-2">{{ $version->release_date->translatedFormat('d F Y') }}</small>
                                    @if(Auth::user()->role !== 'leader')
                                    <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill" onclick="editVersion({{ $version->id }}, '{{ $version->version }}', '{{ $version->release_date->format('Y-m-d') }}', `{{ str_replace('`', '\`', $version->changelog) }}`)" title="Edit Versi">
                                        <i class="ti tabler-edit-box ti tabler-18px"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 text-dark changelog-content" style="font-size: 0.95rem;">
                                {!! \Illuminate\Support\Str::markdown($version->changelog) !!}
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada data versi yang ditambahkan.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $versions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-style')
<style>
    .changelog-content ul {
        padding-left: 1.5rem;
        list-style-type: disc;
    }
    .changelog-content ol {
        padding-left: 1.5rem;
        list-style-type: decimal;
    }
</style>
@endsection

@section('page-script')
<script>
    const form = document.getElementById('versionForm');
    const formTitle = document.getElementById('formTitle');
    const formMethod = document.getElementById('formMethod');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const baseAction = "{{ route('admin.app-versions.store') }}";

    function editVersion(id, version, releaseDate, changelog) {
        form.action = baseAction.replace('store', id); // This will change to /manage/{id}
        
        // Use proper route generation for update if possible, but JS string replace is fine here
        // The route is admin.app-versions.update
        // baseAction: /admin/app-versions/manage
        form.action = `{{ url('admin/app-versions/manage') }}/${id}`;
        
        formMethod.value = "PUT";
        
        document.getElementById('version').value = version;
        document.getElementById('release_date').value = releaseDate;
        document.getElementById('changelog').value = changelog;
        
        formTitle.innerText = "Edit Versi " + version;
        btnSubmit.innerText = "Simpan Perubahan";
        btnCancelEdit.classList.remove('d-none');
        
        // Scroll to form
        document.getElementById('versionForm').scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEdit() {
        form.action = baseAction;
        formMethod.value = "POST";
        
        document.getElementById('version').value = "";
        document.getElementById('release_date').value = "{{ date('Y-m-d') }}";
        document.getElementById('changelog').value = "";
        
        formTitle.innerText = "Tambah Versi Baru";
        btnSubmit.innerText = "Simpan Versi";
        btnCancelEdit.classList.add('d-none');
    }
</script>
@endsection
