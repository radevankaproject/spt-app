@extends('layouts.app')

@section('title', 'Edit Lokasi Parkir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Lokasi Parkir</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.parking-locations.index') }}">Lokasi Parkir</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

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
            <form action="{{ route('masterdata.parking-locations.update', $parkingLocation->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row g-6">
                    <div class="col-12">
                        <label class="form-label">1. Pilih Zona</label>
                        <div class="d-flex pt-2">
                            <div class="form-check me-4">
                                <input name="zone" class="form-check-input" type="radio" value="Zona 2" id="zone2"
                                    {{ old('zone', $parkingLocation->roadSection->zone) == 'Zona 2' ? 'checked' : '' }} />
                                <label class="form-check-label" for="zone2"> Zona 2 </label>
                            </div>
                            <div class="form-check">
                                <input name="zone" class="form-check-input" type="radio" value="Zona 3" id="zone3"
                                    {{ old('zone', $parkingLocation->roadSection->zone) == 'Zona 3' ? 'checked' : '' }} />
                                <label class="form-check-label" for="zone3"> Zona 3 </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select select2" id="road_section_id" name="road_section_id" required>
                                {{-- Options akan diisi oleh JavaScript --}}
                            </select>
                            <label for="road_section_id">2. Pilih Ruas Jalan</label>
                        </div>
                        @error('road_section_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Contoh: Depan Toko ABC" value="{{ old('name', $parkingLocation->name) }}"
                                required />
                            <label for="name">3. Masukkan Nama Lokasi Parkir</label>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="pt-6 text-end">
                    <a href="{{ route('masterdata.parking-locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const roadSectionSelect = $('#road_section_id');
            const radios = $('input[name="zone"]');

            // Data dari PHP untuk JavaScript
            const initialZone = '{{ $parkingLocation->roadSection->zone }}';
            const initialRoadSectionId = '{{ $parkingLocation->road_section_id }}';

            // Inisialisasi Select2
            roadSectionSelect.select2({
                placeholder: 'Pilih Ruas Jalan',
                allowClear: true
            });

            // Fungsi untuk memuat ruas jalan berdasarkan zona
            function loadRoadSections(zone, selectedId = null) {
                roadSectionSelect.empty().append($('<option></option>').text('Memuat data...')).prop('disabled',
                    true);
                roadSectionSelect.val(null).trigger('change');

                if (zone) {
                    const url = `{{ url('masterdata/get-road-sections-by-zone') }}/${zone}`;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            roadSectionSelect.empty().append($('<option></option>').attr('value', '')
                                .text('Pilih Ruas Jalan'));
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    roadSectionSelect.append($('<option></option>').attr(
                                        'value', value.id).text(value.name));
                                });
                                roadSectionSelect.prop('disabled', false);

                                // Jika ada ID yang harus dipilih, pilih ID tersebut
                                if (selectedId) {
                                    roadSectionSelect.val(selectedId).trigger('change');
                                }
                            } else {
                                roadSectionSelect.empty().append($('<option></option>').attr('value',
                                    '').text('Tidak ada ruas jalan di zona ini')).prop('disabled',
                                    true);
                            }
                        },
                        error: function() {
                            roadSectionSelect.empty().append($('<option></option>').attr('value', '')
                                .text('Gagal memuat data')).prop('disabled', true);
                        }
                    });
                } else {
                    roadSectionSelect.empty().append($('<option></option>').attr('value', '').text(
                        'Pilih Zona terlebih dahulu')).prop('disabled', true);
                }
            }

            // Panggil fungsi saat halaman pertama kali dimuat
            if (initialZone) {
                loadRoadSections(initialZone, initialRoadSectionId);
            }

            // Event listener untuk perubahan pada radio button zona
            radios.on('change', function() {
                const selectedZone = $(this).val();
                loadRoadSections(selectedZone);
            });
        });
    </script>
@endpush
