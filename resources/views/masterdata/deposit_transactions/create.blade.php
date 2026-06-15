@extends('layouts.app')
@section('title', 'Catat Setoran Baru')

@section('skeleton')
    @include('layouts.partials._skeleton-deposit-transaction-create')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .select2-container .select2-selection--single { height: 58px !important; padding: 0.5rem 0.75rem; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 40px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 56px !important; }
        #payment-exists-modal { visibility: hidden; opacity: 0; transition: opacity 0.3s ease-in-out; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.98); z-index: 1050; border-radius: 0.5rem; backdrop-filter: blur(2px); }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Catat Setoran Baru</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                <li class="breadcrumb-item"><a href="{{ route('masterdata.deposit-transactions.index') }}">Transaksi Setoran</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Oops! Terjadi kesalahan:</strong></p>
            <ul class="mt-2 mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card position-relative border-0 shadow-sm">
        <div id="payment-exists-modal" class="d-flex justify-content-center align-items-center text-center p-4">
            <div>
                <i class="ri ri-time-line ri-5x text-warning mb-4 d-block"></i>
                <h4 class="mb-2">Belum Saatnya Membayar</h4>
                <div class="mb-4 text-muted" id="modal-message"></div>
                <button type="button" id="change-agreement-btn" class="btn btn-outline-secondary"><i class="ri ri-arrow-go-back-line me-1"></i>Pilih PKS Lain</button>
            </div>
        </div>

        <div class="card-header border-bottom pb-3">
            <h5 class="card-title mb-0">Formulir Setoran Bulanan</h5>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('masterdata.deposit-transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-5">

                    {{-- ✅ INFO BENDAHARA DIPINDAH KE LUAR FIELDSET AGAR SELALU MUNCUL --}}
                    <div class="col-12 mb-2">
                        <label class="form-label fw-bold">Penanggung Jawab Validasi Saat Ini</label>
                        <div class="alert alert-primary d-flex align-items-center p-3 mb-0 shadow-sm border border-primary" role="alert">
                            <div class="avatar avatar-sm me-3">
                                <img src="{{ ($activeTreasurer->user && $activeTreasurer->user->img) ? asset('storage/' . $activeTreasurer->user->img) : 'https://ui-avatars.com/api/?name='.urlencode($activeTreasurer->user->name).'&background=auto&color=fff' }}" alt="Avatar" class="rounded-circle" style="object-fit:cover;">
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-primary">Bendahara Penerimaan: {{ $activeTreasurer->user->name }}</h6>
                                <small class="text-muted">NIP. {{ formatNip($activeTreasurer->employee_number) }}</small>
                            </div>
                            <div class="ms-auto">
                                <i class="ri ri-shield-check-fill text-success ri-2x" data-bs-toggle="tooltip" title="Sistem Keamanan & Audit Aktif"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="agreement_id" class="form-label fw-bold">1. Pilih PKS (Ketik No PKS / Nama Korlap)</label>
                        <select name="agreement_id" id="agreement_id" class="form-select select2-agreements" required></select>
                    </div>

                    <fieldset id="deposit-form-fields" disabled>
                        <div class="row g-4 mt-2">
                            <div class="col-12"><hr class="mt-0 mb-2"> <p class="form-label mb-0 fw-bold">2. Detail Setoran Otomatis</p></div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="referral_code" class="form-control" readonly />
                                    <label for="referral_code">Kode Referensi</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" name="deposit_date" id="deposit_date" class="form-control" required />
                                    <label for="deposit_date">Tanggal Setor</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="original_amount_display" class="form-control fw-bold" readonly />
                                    <label for="original_amount_display">Tagihan Asli (Rp)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="discount_amount_display" class="form-control fw-bold text-danger" value="{{ old('discount_amount', 0) }}" />
                                    <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" />
                                    <label for="discount_amount_display">Potongan/Keringanan (Rp)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="amount_display" id="amount_display" class="form-control fw-bold text-success" readonly />
                                    <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}" />
                                    <label for="amount_display">Tagihan Bersih/Setoran (Rp)</label>
                                </div>
                            </div>
                            <div class="col-12" id="amount-calculation-info-container" style="display: none;">
                                <div id="amount-calculation-info" class="alert alert-info p-2 mb-0" role="alert" style="font-size: 0.85rem;"></div>
                            </div>
                            <div class="col-12" id="discount_notes_container" style="display: none;">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="discount_notes" id="discount_notes" class="form-control" placeholder="Alasan pemberian potongan...">{{ old('discount_notes') }}</textarea>
                                    <label for="discount_notes">Alasan Potongan / Diskon (Wajib jika ada potongan)</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="notes" id="notes" class="form-control" placeholder="Catatan tambahan (opsional)" style="height: 80px;"></textarea>
                                    <label for="notes">Catatan Tambahan</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">3. Bukti Transfer (Wajib)</label>
                                <div class="card border border-dashed shadow-none">
                                    <div class="card-body text-center py-4">
                                        <img src="{{ asset('assets/img/transaksi.png') }}" class="d-block rounded-3 mx-auto mb-3" id="proof-preview" style="height: 120px; object-fit:contain;" />
                                        <label for="proof-upload" class="btn btn-sm btn-primary rounded-pill"><i class="ri ri-upload-2-line me-1"></i>Pilih Gambar
                                            <input type="file" id="proof-upload" name="proof_of_transfer" hidden accept="image/png, image/jpeg" required/>
                                        </label>
                                        <div id="proof-error" class="mt-2 text-danger small"></div>
                                        <p class="text-muted mt-2 mb-0 small">Otomatis dikompres &lt; 300KB (JPG/PNG)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-5 text-end border-top mt-5">
                            <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="ri ri-save-3-line me-1"></i> Simpan Setoran</button>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const agreementSelect = $('#agreement_id');
            const formFields = $('#deposit-form-fields');
            const modal = $('#payment-exists-modal');
            const amountCalculationInfo = $('#amount-calculation-info');
            const amountCalculationInfoContainer = $('#amount-calculation-info-container');
            let originalTagihan = 0;

            agreementSelect.select2({
                placeholder: 'Ketik No. PKS atau Nama Korlap...',
                allowClear: true,
                ajax: {
                    url: '{{ route('masterdata.search-active-agreements') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) { return { results: data.results }; }
                }
            });

            function resetAndDisableForm() {
                formFields.prop('disabled', true);
                formFields.find('input[type="text"], input[type="date"], textarea').val('');
                $('#discount_amount').val(0);
                $('#discount_amount_display').val('');
                $('#proof-preview').attr('src', "{{ asset('assets/img/illustrations/image-light.png') }}");
                $('#proof-upload').val('');
                amountCalculationInfo.html('');
                amountCalculationInfoContainer.hide();
                $('#discount_notes_container').hide();
                $('#discount_notes').removeAttr('required');
                modal.css({'visibility': 'hidden', 'opacity': '0'});
                originalTagihan = 0;
            }

            agreementSelect.on('select2:select', function(e) {
                const data = e.params.data;
                if (!data.id) return;

                $.ajax({
                    url: `/masterdata/check-transaction/${data.id}`,
                    type: 'GET',
                    success: function(response) {
                        if (!response.can_pay) {
                            $('#modal-message').html(response.message);
                            modal.css({'visibility': 'visible', 'opacity': '1'});
                        } else {
                            formFields.prop('disabled', false);

                            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

                            const infoText = `<i class="ri ri-information-line me-1"></i> Setoran bulan <strong>${response.target_month_name}</strong> (Tarif Harian ${formatRupiah(response.daily_amount)} &times; ${response.days_in_month} hari).`;

                            amountCalculationInfo.html(infoText);
                            amountCalculationInfoContainer.show();
                            
                            originalTagihan = response.total_amount;
                            $('#original_amount_display').val(formatRupiah(originalTagihan));
                            calculateNetTagihan();

                            const now = new Date();
                            const dateCode = `${now.getFullYear()}${(now.getMonth() + 1).toString().padStart(2, '0')}${now.getDate().toString().padStart(2, '0')}`;
                            const randomCode = (Math.random().toString(36) + '00000000').slice(2, 8).toUpperCase();
                            $('#referral_code').val(`TRXPRK-${dateCode}-${randomCode}`);

                            const today = now.toISOString().split('T')[0];
                            $('#deposit_date').attr('max', today).val(today);
                        }
                    }
                });
            });

            agreementSelect.on('select2:unselect', resetAndDisableForm);
            $('#change-agreement-btn').on('click', function() {
                agreementSelect.val(null).trigger('change');
                resetAndDisableForm();
            });

            $('#discount_amount_display').on('input', function() {
                // Hapus karakter selain angka
                let rawValue = $(this).val().replace(/\D/g, '');
                let discount = parseFloat(rawValue) || 0;
                
                if (discount < 0) discount = 0;
                if (discount > originalTagihan) discount = originalTagihan;
                
                $('#discount_amount').val(discount);
                
                if (discount === 0 && rawValue === '') {
                    $(this).val('');
                } else {
                    $(this).val(new Intl.NumberFormat('id-ID').format(discount));
                }

                calculateNetTagihan();
            });

            function calculateNetTagihan() {
                let discount = parseFloat($('#discount_amount').val()) || 0;
                let net = originalTagihan - discount;
                
                const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                
                $('#amount_display').val(formatRupiah(net));
                $('#amount').val(net);

                if (discount > 0) {
                    $('#discount_notes_container').slideDown();
                    $('#discount_notes').attr('required', true);
                } else {
                    $('#discount_notes_container').slideUp();
                    $('#discount_notes').removeAttr('required');
                }
            }

            // --- Kompresi Gambar ---
            const fileInput = document.getElementById('proof-upload');
            if (fileInput) {
                fileInput.addEventListener('change', async (e) => {
                    const imagePreview = document.getElementById('proof-preview');
                    const errorDiv = document.getElementById('proof-error');
                    const defaultSrc = "{{ asset('assets/img/illustrations/image-light.png') }}";
                    const imageFile = e.target.files[0];

                    if (!imageFile) { imagePreview.src = defaultSrc; return; }
                    errorDiv.textContent = '';

                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya file JPG atau PNG.'; fileInput.value = ''; imagePreview.src = defaultSrc; return;
                    }

                    if(imageFile.size / 1024 <= 300){
                        imagePreview.src = URL.createObjectURL(imageFile); return;
                    }

                    try {
                        const options = { maxSizeMB: 0.3, maxWidthOrHeight: 1024, useWebWorker: true };
                        const compressedFile = await imageCompression(imageFile, options);
                        const dt = new DataTransfer(); dt.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        fileInput.files = dt.files;
                        imagePreview.src = URL.createObjectURL(compressedFile);
                    } catch (error) { errorDiv.textContent = "Gagal memproses gambar."; fileInput.value = ''; imagePreview.src = defaultSrc; }
                });
            }
        });
    </script>
@endpush
