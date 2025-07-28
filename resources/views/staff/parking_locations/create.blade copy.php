@extends('layouts.app')

@section('title', 'Tambah Lokasi Parkir Baru')

@push('styles')
    {{-- CSS untuk Select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Tambah Lokasi Parkir Baru</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}">Lokasi Parkir</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
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
            <form action="{{ route('masterdata.parking-locations.store') }}" method="POST">
                @csrf
                <div class="row g-6">
                    <!-- Pilihan Zona -->
                    <div class="col-md-6">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4">
                                <input name="zone_filter" class="form-check-input" type="radio" value="Zona 2"
                                    id="zone2" />
                                <label class="form-check-label" for="zone2"> Zona 2 </label>
                            </div>
                            <div class="form-check">
                                <input name="zone_filter" class="form-check-input" type="radio" value="Zona 3"
                                    id="zone3" />
                                <label class="form-check-label" for="zone3"> Zona 3 </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Ruas Jalan -->
                    <div class="col-md-6">
                        <label for="road_section_id" class="form-label">2. Pilih Ruas Jalan</label>
                        {{-- Dropdown ini akan diisi oleh JavaScript --}}
                        <select class="form-select select2" id="road_section_id" name="road_section_id" required disabled>
                            <option value="">Pilih Zona terlebih dahulu</option>
                        </select>
                        @error('road_section_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Lokasi Parkir -->
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Contoh: Depan Toko ABC" value="{{ old('name') }}" required />
                            <label for="name">3. Masukkan Nama Lokasi Parkir</label>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-6 text-end">
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vendors-js')
    {{-- Pastikan jQuery dimuat sebelum Select2 --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endpush

@push('scripts')
    <script>
        // Gunakan $(function() { ... }) yang merupakan shortcut untuk $(document).ready()
        $(function() {
            const roadSectionSelect = $('#road_section_id');

            // Inisialisasi Select2
            if (roadSectionSelect.length) {
                roadSectionSelect.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Ruas Jalan',
                    dropdownParent: roadSectionSelect.parent()
                });
            }

            // Event listener untuk radio button zona
            $('input[name="zone_filter"]').on('change', function() {
                const selectedZone = $(this).val();

                roadSectionSelect.empty().append('<option value="">Memuat...</option>').prop('disabled',
                    true).trigger('change');

                if (selectedZone) {
                    // Gunakan nama route yang benar dari file web.php Anda
                    const url = `{{ route('masterdata.road-sections.getByZone', ':zone') }}`.replace(
                        ':zone', selectedZone);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            roadSectionSelect.empty().append(
                                '<option value="">Pilih Ruas Jalan</option>').prop(
                                'disabled', false);
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    roadSectionSelect.append($('<option></option>')
                                        .attr('value', value.id).text(value.name));
                                });
                            } else {
                                roadSectionSelect.empty().append(
                                    '<option value="">Tidak ada ruas jalan di zona ini</option>'
                                    ).prop('disabled', true);
                            }
                            roadSectionSelect.trigger('change');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("AJAX Error:", textStatus, errorThrown);
                            roadSectionSelect.empty().append(
                                '<option value="">Gagal memuat data</option>').prop(
                                'disabled', true).trigger('change');
                        }
                    });
                }
            });
        });
    </script>
@endpush
