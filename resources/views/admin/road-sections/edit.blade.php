@extends('layouts.app')

@section('title', 'Edit Ruas Jalan')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-form')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Ruas Jalan</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.road-sections.index') }}">Ruas Jalan</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Menampilkan Pesan Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
            <ul class="mt-2 mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('masterdata.road-sections.update', $roadSection->id) }}" method="POST">
                @csrf
                @method('PATCH') {{-- Gunakan PATCH atau PUT untuk update --}}
                <div class="row g-6">
                    {{-- Nama Ruas Jalan --}}
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Contoh: Jl. Jend. Sudirman" value="{{ old('name', $roadSection->name) }}"
                                required />
                            <label for="name">Nama Ruas Jalan</label>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pilihan Zona --}}
                    <div class="col-12">
                        <label class="form-label">Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4">
                                <input name="zone" class="form-check-input" type="radio" value="Zona 2" id="zone2"
                                    {{ old('zone', $roadSection->zone) == 'Zona 2' ? 'checked' : '' }} />
                                <label class="form-check-label" for="zone2"> Zona 2 </label>
                            </div>
                            <div class="form-check">
                                <input name="zone" class="form-check-input" type="radio" value="Zona 3" id="zone3"
                                    {{ old('zone', $roadSection->zone) == 'Zona 3' ? 'checked' : '' }} />
                                <label class="form-check-label" for="zone3"> Zona 3 </label>
                            </div>
                        </div>
                        @error('zone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-6 text-end">
                    <a href="{{ route('masterdata.road-sections.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
