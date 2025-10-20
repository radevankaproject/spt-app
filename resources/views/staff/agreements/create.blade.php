@extends('layouts.app')

@section('title', 'Tambah Perjanjian Baru')

@section('skeleton')
    @include('layouts.partials._skeleton-agreements-form')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Tambah Perjanjian Kerjasama Baru</h4>
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

    <form action="{{ route('masterdata.agreements.store') }}" method="POST">
        @csrf
        <div class="row g-6">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detail Perjanjian</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                        id="agreement_number" name="agreement_number" placeholder="Contoh: PKS/2025/001"
                                        value="{{ old('agreement_number') }}" required /><label for="agreement_number">Nomor
                                        Perjanjian</label></div>
                            </div>
                            <div class="col-md-6"><label for="field_coordinator_id" class="form-label">Koordinator
                                    Lapangan</label><select class="form-select select2" id="field_coordinator_id"
                                    name="field_coordinator_id" required>
                                    <option value=""></option>
                                    @foreach ($fieldCoordinators as $fc)
                                        <option value="{{ $fc->id }}"
                                            {{ old('field_coordinator_id') == $fc->id ? 'selected' : '' }}>
                                            {{ $fc->user->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6"><label for="leader_id" class="form-label">Pimpinan (Pihak
                                    Pertama)</label><select class="form-select select2" id="leader_id" name="leader_id"
                                    required>
                                    <option value="">Pilih Pimpinan</option>
                                    @foreach ($leaders as $leader)
                                        <option value="{{ $leader->id }}"
                                            {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                            {{ $leader->user->name }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                        id="start_date" name="start_date" placeholder="YYYY-MM-DD"
                                        value="{{ old('start_date') }}" required /><label for="start_date">Tanggal Mulai
                                        Berlaku</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                        id="end_date" name="end_date" placeholder="YYYY-MM-DD"
                                        value="{{ old('end_date') }}" required /><label for="end_date">Tanggal Selesai
                                        Berlaku</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                        id="signed_date" name="signed_date" value="{{ old('signed_date', date('Y-m-d')) }}"
                                        required /><label for="signed_date">Tanggal TTD</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline"><select name="status" id="status"
                                        class="form-select" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="pending_renewal"
                                            {{ old('status') == 'pending_renewal' ? 'selected' : '' }}>Menunggu
                                            Perpanjangan</option>
                                    </select><label for="status">Status Perjanjian</label></div>
                            </div>

                            {{-- ✅ PERUBAHAN DI SINI: Input Setoran --}}
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="daily_deposit_amount"
                                        name="daily_deposit_amount" placeholder="Otomatis dari lokasi"
                                        value="{{ old('daily_deposit_amount', 0) }}" required />
                                    <label for="daily_deposit_amount">Setoran Harian (Rp)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="monthly_deposit"
                                        placeholder="Akan terisi otomatis" readonly />
                                    <label for="monthly_deposit">Estimasi Setoran Bulanan</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="total_deposit"
                                        placeholder="Akan terisi otomatis" readonly />
                                    <label for="total_deposit">Total Setoran Kontrak</label>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Lokasi Parkir Terkait</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="col-md-12">
                            <label class="form-label">Zona Pengelolaan</label>
                            <div class="d-flex align-items-center pt-2">
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
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-4" id="reset-zone-btn"
                                    style="display: none;">
                                    Ganti Zona
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="road_section_id" class="form-label">Filter Ruas Jalan</label>
                            <select class="form-select select2" id="road_section_id" name="road_section_id_filter"
                                disabled>
                                <option value="">Pilih Zona terlebih dahulu</option>
                            </select>
                        </div>

                        <label class="form-label">Pilih Lokasi Parkir dari Ruas Jalan</label>
                        <div class="border rounded-3 p-4 mb-4" style="overflow-y: auto; min-height: 150px;">
                            <div id="parking-location-container">
                                <p class="text-muted text-center" id="parking-location-placeholder">Pilih Ruas Jalan
                                    terlebih dahulu.</p>
                            </div>
                        </div>

                        <hr class="mx-n4">
                        <h6 class="mb-3">Total Lokasi Terpilih: <span id="selected-count" class="fw-bold">0</span></h6>
                        <div id="selected-locations-summary" class="flex-grow-1" style="overflow-y: auto;">
                            <p class="text-muted text-center">Belum ada lokasi yang dipilih.</p>
                        </div>

                        @error('parking_location_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 text-end">
                <a href="{{ route('masterdata.agreements.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perjanjian</button>
            </div>
        </div>
    </form>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.select2').select2({
                placeholder: "Pilih atau cari...",
                allowClear: true
            });
            const startDatePicker = flatpickr("#start_date", {
                dateFormat: 'Y-m-d',
                onChange: (selectedDates, dateStr) => {
                    if (dateStr) {
                        endDatePicker.set('minDate', dateStr);
                    }
                    calculateTotals();
                }
            });
            const endDatePicker = flatpickr("#end_date", {
                dateFormat: 'Y-m-d',
                onChange: () => calculateTotals()
            });
            flatpickr("#signed_date", {
                dateFormat: 'Y-m-d'
            });

            const dailyDepositInput = document.getElementById('daily_deposit_amount');
            const monthlyDepositInput = document.getElementById('monthly_deposit');
            const totalDepositInput = document.getElementById('total_deposit');
            const roadSectionSelect = $('#road_section_id');
            const parkingContainer = $('#parking-location-container');
            const summaryContainer = $('#selected-locations-summary');
            const selectedCountEl = $('#selected-count');
            const resetZoneBtn = $('#reset-zone-btn');

            let selectedLocations = {};

            function renderSummary() {
                summaryContainer.empty();
                const locations = Object.values(selectedLocations);
                selectedCountEl.text(locations.length);

                if (locations.length === 0) {
                    summaryContainer.html('<p class="text-muted text-center">Belum ada lokasi yang dipilih.</p>');
                    return;
                }

                locations.forEach(loc => {
                    const itemHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="mb-0 fw-semibold">${loc.name}</p>
                        <small class="text-muted">${loc.road_section_name}</small>
                        <input type="hidden" name="parking_location_ids[]" value="${loc.id}" />
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-danger remove-location-btn" data-id="${loc.id}">
                        <i class="ri ri-close-line"></i>
                    </button>
                </div>
            `;
                    summaryContainer.append(itemHtml);
                });
            }

            function updateDailyDepositTotal() {
                let total = 0;
                for (const id in selectedLocations) {
                    total += parseFloat(selectedLocations[id].daily_deposit) || 0;
                }
                dailyDepositInput.value = total;
                calculateTotals();
            }

            function calculateTotals() {
                const dailyAmount = parseFloat(dailyDepositInput.value) || 0;
                const startDate = startDatePicker.selectedDates[0];
                const endDate = endDatePicker.selectedDates[0];
                const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
                monthlyDepositInput.value = dailyAmount > 0 ? formatRupiah(dailyAmount * 30) : '';
                if (!startDate || !endDate || endDate < startDate) {
                    totalDepositInput.value = '';
                    return;
                }
                const durationInDays = moment(endDate).diff(moment(startDate), 'days') + 1;
                totalDepositInput.value = (dailyAmount > 0 && durationInDays > 0) ? formatRupiah(dailyAmount *
                    durationInDays) : '';
            }

            $('input[name="zone_filter"]').on('change', function() {
                const selectedZone = $(this).val();
                $('input[name="zone_filter"]').not(this).prop('disabled', true);
                resetZoneBtn.show();
                roadSectionSelect.empty().append('<option value="">Memuat ruas jalan...</option>').prop(
                    'disabled', true).trigger('change');
                parkingContainer.html(
                    '<p class="text-muted text-center">Pilih Ruas Jalan terlebih dahulu.</p>');

                const url = `{{ url('masterdata/get-road-sections-by-zone') }}/${selectedZone}`;
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        roadSectionSelect.empty().append(
                            '<option value="">Pilih Ruas Jalan</option>').prop('disabled',
                            false);
                        if (data && data.length > 0) {
                            $.each(data, function(key, value) {
                                roadSectionSelect.append($('<option></option>').attr(
                                    'value', value.id).text(value.name));
                            });
                        } else {
                            roadSectionSelect.empty().append(
                                '<option value="">Tidak ada ruas jalan</option>').prop(
                                'disabled', true);
                        }
                        roadSectionSelect.trigger('change');
                    },
                    error: function() {
                        roadSectionSelect.empty().append(
                                '<option value="">Gagal memuat</option>').prop('disabled', true)
                            .trigger('change');
                    }
                });
            });

            resetZoneBtn.on('click', function() {
                $('input[name="zone_filter"]').prop('checked', false).prop('disabled', false);
                $(this).hide();
                roadSectionSelect.empty().append('<option value="">Pilih Zona terlebih dahulu</option>')
                    .prop('disabled', true).trigger('change');
                parkingContainer.html(
                    '<p class="text-muted text-center">Pilih Ruas Jalan terlebih dahulu.</p>');
                selectedLocations = {};
                renderSummary();
                updateDailyDepositTotal();
            });

            roadSectionSelect.on('change', function() {
                const selectedRoadSectionId = $(this).val();
                const roadSectionName = $(this).find('option:selected').text();
                parkingContainer.html('<p class="text-muted text-center">Memuat lokasi...</p>');

                if (!selectedRoadSectionId) {
                    parkingContainer.html(
                        '<p class="text-muted text-center">Pilih Ruas Jalan terlebih dahulu.</p>');
                    return;
                }

                const url =
                    `{{ url('masterdata/get-parking-locations-by-road-section') }}/${selectedRoadSectionId}`;
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        parkingContainer.empty();
                        if (data && data.length > 0) {
                            let html = '<div class="row">';
                            data.forEach(location => {
                                const isChecked = selectedLocations.hasOwnProperty(
                                    location.id) ? 'checked' : '';
                                html += `
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="parking_location_ids_filter[]" value="${location.id}" id="loc-${location.id}"
                                        data-daily-deposit="${location.daily_deposit}"
                                        data-name="${location.name}"
                                        data-road-section-name="${roadSectionName}" ${isChecked}>
                                    <label class="form-check-label" for="loc-${location.id}">${location.name}</label>
                                </div>
                            </div>`;
                            });
                            html += '</div>';
                            parkingContainer.html(html);
                        } else {
                            parkingContainer.html(
                                '<p class="text-muted text-center">Tidak ada lokasi parkir tersedia.</p>'
                            );
                        }
                    },
                    error: function() {
                        parkingContainer.html(
                            '<p class="text-danger text-center">Gagal memuat lokasi.</p>');
                    }
                });
            });

            parkingContainer.on('change', 'input[type="checkbox"]', function() {
                const checkbox = $(this);
                const locationId = checkbox.val();

                if (checkbox.is(':checked')) {
                    selectedLocations[locationId] = {
                        id: locationId,
                        name: checkbox.data('name'),
                        daily_deposit: checkbox.data('daily-deposit'),
                        road_section_name: checkbox.data('road-section-name')
                    };
                } else {
                    delete selectedLocations[locationId];
                }

                updateDailyDepositTotal();
                renderSummary();
            });

            summaryContainer.on('click', '.remove-location-btn', function() {
                const locationId = $(this).data('id');
                delete selectedLocations[locationId];
                $('#loc-' + locationId).prop('checked', false);
                updateDailyDepositTotal();
                renderSummary();
            });
        });
    </script>
@endpush
