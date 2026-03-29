@extends('layouts.app')

@section('title', 'Edit Perjanjian: ' . $agreement->agreement_number)

@section('skeleton')
    @include('layouts.partials._skeleton-agreements-form')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Perjanjian</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.agreements.index') }}">PKS</a></li>
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

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('masterdata.agreements.update', $agreement->id) }}" method="POST">
        @csrf
        @method('PATCH')
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
                                        id="agreement_number" name="agreement_number"
                                        value="{{ old('agreement_number', $agreement->agreement_number) }}"
                                        readonly /><label for="agreement_number">Nomor Perjanjian</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted"
                                        value="{{ $agreement->leader->user->name ?? 'N/A' }}"
                                        readonly style="pointer-events: none;" />
                                    {{-- ✅ Input hidden untuk mengirimkan data leader_id saat form disubmit --}}
                                    <input type="hidden" name="leader_id" value="{{ $agreement->leader_id }}">
                                    <label>Pimpinan (Pihak Pertama) <i class="ri ri-lock-line text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted"
                                        value="{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}"
                                        readonly style="pointer-events: none;" />
                                    <input type="hidden" name="field_coordinator_id" value="{{ $agreement->field_coordinator_id }}">
                                    <label>Koordinator Lapangan <i class="ri ri-lock-line text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            {{-- ✅ FIX: Semua tanggal dikunci (readonly & pointer-events: none) --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="start_date" name="start_date"
                                        value="{{ old('start_date', $agreement->start_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="start_date">Tanggal Mulai Berlaku <i class="ri ri-lock-line text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="end_date" name="end_date"
                                        value="{{ old('end_date', $agreement->end_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="end_date">Tanggal Selesai Berlaku <i class="ri ri-lock-line text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="signed_date" name="signed_date"
                                        value="{{ old('signed_date', $agreement->signed_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="signed_date">Tanggal TTD <i class="ri ri-lock-line text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>

                            {{-- ✅ FIX: Status hanya memunculkan status saat ini dan opsi Diakhiri --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select" required>
                                        {{-- Tampilkan status saat ini sebagai opsi pertama --}}
                                        <option value="{{ $agreement->status }}" selected>
                                            {{ ucwords(str_replace('_', ' ', $agreement->status)) }}
                                        </option>

                                        {{-- Jika status belum terminated, berikan opsi untuk memutus kontrak --}}
                                        @if($agreement->status !== 'terminated')
                                            <option value="terminated">Diakhiri (Terminated)</option>
                                        @endif
                                    </select>
                                    <label for="status">Status Perjanjian</label>
                                </div>
                                <div class="form-text text-warning"><i class="ri ri-error-warning-line"></i> Status hanya bisa diubah ke diakhiri (terminated).</div>
                            </div>

                            {{-- Kalkulasi Setoran --}}
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline"><input type="number" class="form-control"
                                        id="daily_deposit_amount" name="daily_deposit_amount"
                                        placeholder="Otomatis dari lokasi"
                                        value="{{ old('daily_deposit_amount', $agreement->daily_deposit_amount) }}"
                                        required /><label for="daily_deposit_amount">Setoran Harian (Rp)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline"><input type="text"
                                        class="form-control" id="monthly_deposit" placeholder="Otomatis"
                                        readonly /><label for="monthly_deposit">Estimasi Bulanan</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline"><input type="text"
                                        class="form-control" id="total_deposit" placeholder="Otomatis" readonly /><label
                                        for="total_deposit">Total Kontrak</label></div>
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
                        @php
                            // ✅ LOGIKA KUNCI ZONA
                            // Terkunci JIKA: Ada lokasi yg terikat ATAU status PKS bukan 'active'
                            $isZoneLocked = !is_null($initialZone) || $agreement->status !== 'active';
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">Zona Pengelolaan</label>
                            <div class="d-flex pt-2">
                                <div class="form-check me-4">
                                    <input name="zone_filter" class="form-check-input" type="radio" value="Zona 2"
                                        id="zone2" {{ $initialZone == 'Zona 2' ? 'checked' : '' }} {{ $isZoneLocked ? 'disabled' : '' }} />
                                    <label class="form-check-label" for="zone2"> Zona 2</label>
                                </div>
                                <div class="form-check">
                                    <input name="zone_filter" class="form-check-input" type="radio" value="Zona 3"
                                        id="zone3" {{ $initialZone == 'Zona 3' ? 'checked' : '' }} {{ $isZoneLocked ? 'disabled' : '' }} />
                                    <label class="form-check-label" for="zone3"> Zona 3</label>
                                </div>
                            </div>

                            {{-- Keterangan dinamis di bawah radio button --}}
                            @if($isZoneLocked)
                                @if($agreement->status !== 'active')
                                    <div class="form-text text-danger"><i class="ri-lock-line"></i> Zona terkunci karena status PKS sudah tidak aktif.</div>
                                @else
                                    <div class="form-text text-danger"><i class="ri-lock-line"></i> Zona terkunci berdasarkan lokasi yang sedang terikat.</div>
                                @endif
                            @else
                                <div class="form-text text-success fw-bold"><i class="ri-lock-unlock-line"></i> Silakan pilih zona baru untuk PKS ini.</div>
                            @endif
                        </div>

                        {{-- Container untuk Hasil Filter (Tempat Memilih) --}}
                        <label class="form-label">Pilih Lokasi Parkir dari Ruas Jalan</label>
                        <div id="parking-location-container" class="border rounded-3 p-4 mb-4"
                            style="overflow-y: auto; min-height: 200px;">
                            @forelse($parkingLocationsForCheckboxes as $location)
                                <div class="form-check mb-3 location-item"
                                    data-road-section="{{ $location->road_section_id }}">
                                    <input class="form-check-input" type="checkbox" value="{{ $location->id }}"
                                        id="loc-{{ $location->id }}" data-daily-deposit="{{ $location->daily_deposit }}"
                                        data-name="{{ $location->name }}"
                                        data-road-section-name="{{ $location->roadSection->name ?? 'N/A' }}"
                                        {{ in_array($location->id, $currentParkingLocationIds) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="loc-{{ $location->id }}">{{ $location->name }}</label>
                                </div>
                            @empty
                                <p class="text-muted text-center">Tidak ada lokasi parkir tersedia di zona ini.</p>
                            @endforelse
                        </div>

                        {{-- Ringkasan Lokasi yang Dipilih --}}
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
                <a href="{{ route('masterdata.agreements.show', $agreement->id) }}"
                    class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
            // --- Inisialisasi Awal ---
            $('.select2').select2({
                placeholder: "Pilih atau cari...",
                allowClear: true
            });

            // ✅ FIX: Flatpickr dihapus karena tanggal sudah dikunci permanen.

            // --- Elemen Penting ---
            const dailyDepositInput = document.getElementById('daily_deposit_amount');
            const monthlyDepositInput = document.getElementById('monthly_deposit');
            const totalDepositInput = document.getElementById('total_deposit');
            const roadSectionFilter = $('#road_section_filter');
            const parkingContainer = $('#parking-location-container');
            const summaryContainer = $('#selected-locations-summary');
            const selectedCountEl = $('#selected-count');

            // --- State Management ---
            let selectedLocations = {};
            const isZoneLocked = {{ $isZoneLocked ? 'true' : 'false' }};

            // --- Definisi Fungsi ---
            function initializeSelectedLocations() {
                $('input[type="checkbox"]:checked', parkingContainer).each(function() {
                    const checkbox = $(this);
                    const locationId = checkbox.val();
                    selectedLocations[locationId] = {
                        id: locationId,
                        name: checkbox.data('name'),
                        daily_deposit: checkbox.data('daily-deposit'),
                        road_section_name: checkbox.data('road-section-name')
                    };
                });
            }

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
                    </div>`;
                    summaryContainer.append(itemHtml);
                });
            }

            function updateDailyDepositTotal() {
                let total = 0;
                for (const id in selectedLocations) {
                    total += parseFloat(selectedLocations[id].daily_deposit) || 0;
                }
                dailyDepositInput.value = total.toFixed(0);
                calculateTotals();
            }

            function calculateTotals() {
                const dailyAmount = parseFloat(dailyDepositInput.value) || 0;

                // Karena flatpickr dihapus, kita ambil nilai langsung dari input hidden/readonly
                const startDateStr = document.getElementById('start_date').value;
                const endDateStr = document.getElementById('end_date').value;

                const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(number);

                monthlyDepositInput.value = dailyAmount > 0 ? formatRupiah(dailyAmount * 30) : '';

                if (dailyAmount > 0 && startDateStr && endDateStr) {
                    const startDate = new Date(startDateStr);
                    const endDate = new Date(endDateStr);
                    if (endDate >= startDate) {
                        // Hitung selisih hari
                        const diffTime = Math.abs(endDate - startDate);
                        const durationInDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        totalDepositInput.value = durationInDays > 0 ? formatRupiah(dailyAmount * durationInDays) : '';
                    } else {
                        totalDepositInput.value = '';
                    }
                } else {
                    totalDepositInput.value = '';
                }
            }

            // ========================================================
            // ✅ LOGIKA PERCABANGAN (ZONA TERBUKA vs ZONA TERKUNCI)
            // ========================================================

            if (!isZoneLocked) {
                // 1. ZONA TERBUKA: Gunakan AJAX Fetch (Sama seperti halaman Create)
                $('input[name="zone_filter"]').on('change', function() {
                    const zone = $(this).val();
                    roadSectionFilter.html('<option value="">Memuat Ruas Jalan...</option>');
                    parkingContainer.html('<p class="text-muted text-center">Silakan pilih ruas jalan terlebih dahulu.</p>');

                    fetch(`/masterdata/agreements/get-road-sections/${zone}`)
                        .then(response => response.json())
                        .then(data => {
                            roadSectionFilter.empty().append('<option value="">Tampilkan Semua Lokasi</option>');
                            data.forEach(section => {
                                roadSectionFilter.append(`<option value="${section.id}">${section.name}</option>`);
                            });
                        });
                });

                roadSectionFilter.on('change', function() {
                    const roadSectionId = $(this).val();
                    parkingContainer.html('<p class="text-muted text-center">Memuat data lokasi...</p>');

                    if (!roadSectionId) {
                        parkingContainer.html('<p class="text-muted text-center">Silakan pilih ruas jalan untuk menampilkan lokasi.</p>');
                        return;
                    }

                    fetch(`/masterdata/agreements/get-parking-locations/${roadSectionId}`)
                        .then(response => response.json())
                        .then(data => {
                            parkingContainer.empty();
                            if (data.length === 0) {
                                parkingContainer.append('<p class="text-muted text-center">Tidak ada lokasi tersedia di ruas jalan ini.</p>');
                                return;
                            }

                            const roadName = roadSectionFilter.find('option:selected').text();
                            data.forEach(location => {
                                const isChecked = selectedLocations[location.id] ? 'checked' : '';
                                parkingContainer.append(`
                                    <div class="form-check mb-3 location-item" data-road-section="${location.road_section_id}">
                                        <input class="form-check-input" type="checkbox" value="${location.id}"
                                            id="loc-${location.id}" data-daily-deposit="${location.daily_deposit}"
                                            data-name="${location.name}" data-road-section-name="${roadName}" ${isChecked}>
                                        <label class="form-check-label" for="loc-${location.id}">
                                            ${location.name} <span class="text-success fw-bold">(Rp ${new Intl.NumberFormat('id-ID').format(location.daily_deposit)})</span>
                                        </label>
                                    </div>
                                `);
                            });
                        });
                });

            } else {
                // 2. ZONA TERKUNCI: Gunakan DOM Filter Element yang sudah ada di HTML
                roadSectionFilter.on('change', function() {
                    const selectedSectionId = $(this).val();
                    $('#location-placeholder').remove();
                    parkingContainer.find('.location-item').each(function() {
                        const show = !selectedSectionId || $(this).data('road-section') == selectedSectionId;
                        $(this).toggle(show);
                    });

                    if (parkingContainer.find('.location-item:visible').length === 0) {
                        const message = selectedSectionId ? 'Tidak ada lokasi pada ruas jalan ini.' : 'Silakan pilih ruas jalan untuk menampilkan lokasi.';
                        parkingContainer.append(`<p id="location-placeholder" class="text-muted text-center">${message}</p>`);
                    }
                });

                // Sembunyikan semua item lokasi di awal saat halaman dimuat
                parkingContainer.find('.location-item').hide();
                parkingContainer.append('<p id="location-placeholder" class="text-muted text-center">Silakan pilih ruas jalan untuk menampilkan lokasi.</p>');
            }

            // --- Event Listeners Global ---
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

            // --- Trigger Awal ---
            initializeSelectedLocations();
            renderSummary();
            updateDailyDepositTotal();
        });
    </script>
@endpush
