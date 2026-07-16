@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Perjanjian: ' . $agreement->agreement_number)



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

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
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control {{ $agreement->jenis != 'draft' ? 'bg-lighter text-muted' : '' }}"
                                        id="agreement_number" name="agreement_number"
                                        value="{{ old('agreement_number', $agreement->agreement_number) }}"
                                        {{ $agreement->jenis == 'draft' ? '' : 'readonly style=pointer-events:none;' }} />
                                    <label for="agreement_number">Nomor Perjanjian {!! $agreement->jenis != 'draft' ? '<i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Hanya bisa diubah pada jenis draft"></i>' : '' !!}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="jenis" id="jenis" class="form-select" required>
                                        <option value="draft" {{ old('jenis', $agreement->jenis) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="sementara" {{ old('jenis', $agreement->jenis) == 'sementara' ? 'selected' : '' }}>Sementara</option>
                                        <option value="rilis" {{ old('jenis', $agreement->jenis) == 'rilis' ? 'selected' : '' }}>Rilis</option>
                                    </select>
                                    <label for="jenis">Jenis Perjanjian</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    @if($agreement->jenis == 'draft')
                                        <select name="leader_id" id="leader_id" class="form-select select2" required>
                                            @foreach ($leaders as $leader)
                                                <option value="{{ $leader->id }}" {{ (old('leader_id', $agreement->leader_id) == $leader->id) ? 'selected' : '' }}>
                                                    {{ $leader->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="leader_id">Pimpinan (Pihak Pertama)</label>
                                    @else
                                        <input type="text" class="form-control bg-lighter text-muted"
                                            value="{{ $agreement->leader->user->name ?? 'N/A' }}"
                                            readonly style="pointer-events: none;" />
                                        <input type="hidden" name="leader_id" value="{{ $agreement->leader_id }}">
                                        <label>Pimpinan (Pihak Pertama) <i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted"
                                        value="{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}"
                                        readonly style="pointer-events: none;" />
                                    <input type="hidden" name="field_coordinator_id" value="{{ $agreement->field_coordinator_id }}">
                                    <label>Koordinator Lapangan <i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            {{-- ✅ FIX: Semua tanggal dikunci (readonly & pointer-events: none) --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="start_date" name="start_date"
                                        value="{{ old('start_date', $agreement->start_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="start_date">Tanggal Mulai Berlaku <i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="end_date" name="end_date"
                                        value="{{ old('end_date', $agreement->end_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="end_date">Tanggal Selesai Berlaku <i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bg-lighter text-muted" id="signed_date" name="signed_date"
                                        value="{{ old('signed_date', $agreement->signed_date->format('Y-m-d')) }}" readonly style="pointer-events: none;" />
                                    <label for="signed_date">Tanggal TTD <i class="ti tabler-lock text-danger ms-1" data-bs-toggle="tooltip" title="Dikunci oleh sistem"></i></label>
                                </div>
                            </div>

                            {{-- ✅ FIX: Status hanya memunculkan status saat ini dan opsi Diakhiri --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="{{ $agreement->status }}" selected>
                                            @if($agreement->status === 'pending_renewal')
                                                Menunggu Perpanjangan
                                            @elseif($agreement->status === 'terminated')
                                                Diakhiri (Terminated)
                                            @elseif($agreement->status === 'active')
                                                Aktif
                                            @else
                                                {{ ucwords(str_replace('_', ' ', $agreement->status)) }}
                                            @endif
                                        </option>
                                        @if($agreement->status === 'pending')
                                            <option value="active">Aktif</option>
                                        @endif
                                        @if($agreement->status !== 'pending_renewal')
                                            <option value="pending_renewal">Menunggu Perpanjangan</option>
                                        @endif
                                        @if($agreement->status !== 'terminated')
                                            <option value="terminated">Diakhiri (Terminated)</option>
                                        @endif
                                    </select>
                                    <label for="status">Status Perjanjian</label>
                                </div>
                                <div class="form-text text-warning"><i class="ti tabler-alert-triangle"></i> Status dapat diubah sesuai kondisi PKS.</div>
                            </div>

                            {{-- Kalkulasi Setoran --}}
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline"><input type="text" class="form-control"
                                        id="daily_deposit_amount" name="daily_deposit_amount"
                                        placeholder="Otomatis dari lokasi"
                                        value="{{ old('daily_deposit_amount', number_format($agreement->daily_deposit_amount, 0, '', '.')) }}"
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
                            // Terkunci JIKA status PKS bukan 'active' atau 'pending' ATAU jenis adalah rilis
                            // Namun jika jenis rilis, form input bisa memicu kunci
                            $isStatusLocked = !in_array($agreement->status, ['active', 'pending']);
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">Zona Pengelolaan</label>
                            <div class="d-flex pt-2">
                                <div class="form-check me-4">
                                    <input name="zone_filter" class="form-check-input" type="radio" value="Zona 2"
                                        id="zone2" {{ $initialZone == 'Zona 2' ? 'checked' : '' }} {{ $isStatusLocked ? 'disabled' : '' }} />
                                    <label class="form-check-label" for="zone2"> Zona 2</label>
                                </div>
                                <div class="form-check">
                                    <input name="zone_filter" class="form-check-input" type="radio" value="Zona 3"
                                        id="zone3" {{ $initialZone == 'Zona 3' ? 'checked' : '' }} {{ $isStatusLocked ? 'disabled' : '' }} />
                                    <label class="form-check-label" for="zone3"> Zona 3</label>
                                </div>
                            </div>

                            {{-- Keterangan dinamis di bawah radio button --}}
                            @if($isStatusLocked)
                                <div class="form-text text-danger"><i class="ti tabler-lock"></i> Zona terkunci karena status PKS sudah tidak aktif.</div>
                            @endif
                            <div id="zona_lock_info" class="form-text text-danger" style="display: none;"><i class="ti tabler-lock"></i> Zona terkunci per zona karena jenis Rilis.</div>
                        </div>

                        <div class="mb-3">
                            <label for="road_section_filter" class="form-label">Filter Ruas Jalan</label>
                            <select id="road_section_filter" class="form-select select2">
                                <option value="">Tampilkan Semua Lokasi</option>
                                @foreach ($allRoadSections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Container untuk Hasil Filter (Tempat Memilih) --}}
                        <label class="form-label">Pilih Lokasi Parkir dari Ruas Jalan</label>
                        <div class="mb-2">
                            <input type="text" id="search-parking" class="form-control form-control-sm" placeholder="Cari nama lokasi parkir (Ketik di sini)...">
                        </div>
                        <div id="parking-location-container" class="border rounded-3 p-4 mb-4"
                            style="overflow-y: auto; min-height: 200px; max-height: 300px;">
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

            <div class="col-12 text-end position-sticky bottom-0 bg-white p-3 border-top shadow z-3 rounded">
                <a href="{{ route('masterdata.agreements.show', $agreement->id) }}"
                    class="btn btn-outline-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </form>
@endsection

@section('vendor-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
@endsection

@section('page-script')
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
            let lastSelectedTotal = 0;
            const isStatusLocked = {{ $isStatusLocked ? 'true' : 'false' }};
            const jenisSelect = $('#jenis');
            const zonaLockInfo = $('#zona_lock_info');

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
                    summaryContainer.html('<p class="text-muted text-center mt-3">Belum ada lokasi yang dipilih.</p>');
                    return;
                }
                const grouped = {};
                locations.forEach(loc => {
                    const rs = loc.road_section_name || 'Tidak Diketahui';
                    if (!grouped[rs]) grouped[rs] = [];
                    grouped[rs].push(loc);
                });

                for (const rs in grouped) {
                    let rsTotal = 0;
                    let rowsHtml = '';
                    grouped[rs].forEach(loc => {
                        rsTotal += parseFloat(loc.daily_deposit) || 0;
                        rowsHtml += `
                            <tr>
                                <td class="px-0 py-1">
                                    <span class="fw-semibold d-block" style="line-height:1.2; font-size:0.9rem;">${loc.name}</span>
                                    <input type="hidden" name="parking_location_ids[]" value="${loc.id}" />
                                </td>
                                <td class="text-end px-0 py-1" style="width: 40px;">
                                    <button type="button" class="btn btn-icon btn-sm btn-text-danger rounded-pill remove-location-btn" data-id="${loc.id}">
                                        <i class="ti tabler-trash icon-16px"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    const formattedTotal = new Intl.NumberFormat('id-ID').format(rsTotal);
                    const groupHtml = `
                        <div class="mb-3 border rounded p-2 bg-lighter">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom border-secondary-subtle">
                                <div>
                                    <span class="fw-bold text-primary small text-uppercase">${rs}</span>
                                    <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">${grouped[rs].length} Lokasi</span>
                                </div>
                                <span class="badge bg-label-primary">Rp ${formattedTotal}</span>
                            </div>
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    ${rowsHtml}
                                </tbody>
                            </table>
                        </div>
                    `;
                    summaryContainer.append(groupHtml);
                }
            }

            function updateDailyDepositTotal(isManualAction = false) {
                const jenis = jenisSelect.val();
                let currentSelectedTotal = 0;
                for (const id in selectedLocations) {
                    currentSelectedTotal += parseFloat(selectedLocations[id].daily_deposit) || 0;
                }

                if (jenis === 'rilis') {
                    dailyDepositInput.value = currentSelectedTotal > 0 ? new Intl.NumberFormat('id-ID').format(currentSelectedTotal) : '';
                } else {
                    if (isManualAction) {
                        let difference = currentSelectedTotal - lastSelectedTotal;
                        if (difference !== 0) {
                            let cleanValue = dailyDepositInput.value.replace(/\./g, '');
                            let currentInputVal = parseFloat(cleanValue) || 0;
                            let newValue = currentInputVal + difference;
                            if (newValue < 0) newValue = 0;
                            dailyDepositInput.value = newValue > 0 ? new Intl.NumberFormat('id-ID').format(newValue) : '';
                        }
                    }
                }
                lastSelectedTotal = currentSelectedTotal;
                calculateTotals();
            }

            dailyDepositInput.addEventListener('input', function(e) {
                let cleanValue = this.value.replace(/[^0-9]/g, '');
                if (cleanValue) {
                    this.value = parseInt(cleanValue, 10).toLocaleString('id-ID');
                } else {
                    this.value = '';
                }
                calculateTotals();
            });

            $('form').on('submit', function() {
                dailyDepositInput.value = dailyDepositInput.value.replace(/\./g, '');
            });

            function toggleJenisLogic() {
                const jenis = jenisSelect.val();
                if (jenis === 'rilis') {
                    dailyDepositInput.readOnly = true;
                    if (!isStatusLocked) {
                        const selectedZone = $('input[name="zone_filter"]:checked').val();
                        if (selectedZone) {
                            $('input[name="zone_filter"]').not('input[name="zone_filter"]:checked').prop('disabled', true);
                            zonaLockInfo.show();
                        }
                    }
                } else {
                    dailyDepositInput.readOnly = false;
                    if (!isStatusLocked) {
                        $('input[name="zone_filter"]').prop('disabled', false);
                        zonaLockInfo.hide();
                    }
                }
                updateDailyDepositTotal();
            }

            jenisSelect.on('change', function() {
                toggleJenisLogic();
            });
            toggleJenisLogic();

            function calculateTotals() {
                let cleanValue = dailyDepositInput.value.replace(/\./g, '');
                const dailyAmount = parseFloat(cleanValue) || 0;

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
            // ✅ ZONA AJAX FETCH (Sama seperti halaman Create) ATAU LOKAL JIKA TERKUNCI
            // ========================================================

            if (!isStatusLocked) {
                $('input[name="zone_filter"]').on('change', function() {
                    const zone = $(this).val();
                    if (jenisSelect.val() === 'rilis') {
                        $('input[name="zone_filter"]').not(this).prop('disabled', true);
                        zonaLockInfo.show();
                    }
                    roadSectionFilter.html('<option value="">Memuat Ruas Jalan...</option>');
                    parkingContainer.html('<p class="text-muted text-center">Silakan pilih ruas jalan terlebih dahulu.</p>');

                    fetch(`/masterdata/agreements/get-road-sections/${zone}`)
                        .then(response => response.json())
                        .then(data => {
                            roadSectionFilter.empty().append('<option value="">Tampilkan Semua Lokasi</option>');
                            data.forEach(section => {
                                roadSectionFilter.append(`<option value="${section.id}">${section.name}</option>`);
                            });
                            roadSectionFilter.trigger('change');
                        });
                });

                roadSectionFilter.on('change', function() {
                    const roadSectionId = $(this).val();
                    $('#search-parking').val(''); // Reset search
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
                roadSectionFilter.on('change', function() {
                    const selectedSectionId = $(this).val();
                    $('#search-parking').val(''); // Reset search
                    $('#location-placeholder').remove();
                    parkingContainer.find('.location-item').each(function() {
                        // Jika memilih 'Tampilkan Semua Lokasi' (value kosong), 
                        // kita sembunyikan yang tidak dicentang agar kembali ke tampilan awal,
                        // KECUALI jika memang ingin melihat semuanya (bisa disesuaikan).
                        // Sesuai request, saat awal buka hanya tampil PKS. Saat pilih jalan, tampil jalan tsb.
                        // Jika kembali ke "Semua", tampilkan hanya yang dicentang saja.
                        if (!selectedSectionId) {
                            const isChecked = $(this).find('input[type="checkbox"]').is(':checked');
                            $(this).toggle(isChecked);
                        } else {
                            const show = $(this).data('road-section') == selectedSectionId;
                            $(this).toggle(show);
                        }
                    });

                    if (parkingContainer.find('.location-item:visible').length === 0) {
                        const message = selectedSectionId ? 'Tidak ada lokasi pada ruas jalan ini.' : 'Pilih ruas jalan untuk menambah lokasi baru.';
                        parkingContainer.append(`<p id="location-placeholder" class="text-muted text-center">${message}</p>`);
                    }
                });

                $('input[name="zone_filter"]').prop('disabled', true);
            }

            // --- Tampilan Awal: Hanya Tampilkan Lokasi yang Masuk PKS ---
            parkingContainer.find('.location-item').each(function() {
                const isChecked = $(this).find('input[type="checkbox"]').is(':checked');
                $(this).toggle(isChecked);
            });

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
                updateDailyDepositTotal(true);
                renderSummary();
            });

            summaryContainer.on('click', '.remove-location-btn', function() {
                const locationId = $(this).data('id');
                delete selectedLocations[locationId];
                $('#loc-' + locationId).prop('checked', false);
                updateDailyDepositTotal(true);
                renderSummary();
            });

            $('#search-parking').on('input', function() {
                let keyword = $(this).val().toLowerCase();
                const selectedSectionId = roadSectionFilter.val();
                
                $('#parking-location-container .location-item').each(function() {
                    // Cek filter ruas jalan jika aktif (khusus form yang di-lock vs tidak)
                    if (isStatusLocked && selectedSectionId && $(this).data('road-section') != selectedSectionId) {
                        return; // Jangan tampilkan yang beda jalan kalau filternya aktif
                    }

                    let labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.includes(keyword)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // --- Trigger Awal ---
            initializeSelectedLocations();
            renderSummary();
            updateDailyDepositTotal();
        });
    </script>
@endsection
