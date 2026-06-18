@extends('layouts.contentNavbarLayout')
@section('title', 'Catat Setoran Baru')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .select2-container .select2-selection--single { height: 58px !important; padding: 0.5rem 0.75rem; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 40px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 56px !important; }
        .disabled-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 0.5rem;
        }
        
        .premium-lock-modal {
            background: #ffffff;
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.05);
            border: none;
            max-width: 420px;
            width: 90%;
            transform: translateY(30px) scale(0.9);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .disabled-overlay.active .premium-lock-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-icon-container {
            width: 88px;
            height: 88px;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: inset 0 0 0 2px rgba(255, 193, 7, 0.3), 0 15px 25px -5px rgba(255, 193, 7, 0.2);
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: inset 0 0 0 2px rgba(255, 193, 7, 0.3), 0 0 0 0 rgba(255, 193, 7, 0.4); }
            70% { box-shadow: inset 0 0 0 2px rgba(255, 193, 7, 0.3), 0 0 0 20px rgba(255, 193, 7, 0); }
            100% { box-shadow: inset 0 0 0 2px rgba(255, 193, 7, 0.3), 0 0 0 0 rgba(255, 193, 7, 0); }
        }

        .modal-icon-container i {
            font-size: 3.5rem !important;
            color: #ff9800 !important;
            line-height: 1;
            filter: drop-shadow(0 4px 6px rgba(255, 152, 0, 0.3));
        }

        .modal-title {
            font-weight: 700;
            color: #2b3445;
            margin-bottom: 12px;
            font-size: 1.25rem;
        }

        .modal-message {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .btn-premium {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
        }
    </style>
@endsection

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
        <div id="payment-exists-modal" class="disabled-overlay d-flex justify-content-center align-items-center">
            <div class="premium-lock-modal">
                <div class="modal-icon-container">
                    <i class="tf-icons ti tabler-clock-filled" id="modal-icon"></i>
                </div>
                <h4 class="modal-title" id="modal-title">Belum Saatnya Membayar</h4>
                <p class="modal-message" id="modal-message"></p>
                <button type="button" class="btn btn-primary btn-premium mt-2" id="modal-action-btn">
                    <i class="tf-icons ti tabler-refresh me-2"></i> Pilih PKS Lain
                </button>
            </div>
        </div>

        <div class="card-header border-bottom pb-3">
            <h5 class="card-title mb-0">Formulir Setoran Bulanan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('masterdata.deposit-transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="target_agreement_id" id="target_agreement_id">
                
                <div class="row g-4">

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
                                <i class="ti tabler-shield-check-filled text-success ti-lg" data-bs-toggle="tooltip" title="Sistem Keamanan & Audit Aktif"></i>
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

                            <div class="col-md-12 mb-3">
                                <label for="transaction_month" class="form-label fw-bold">Bulan Tagihan</label>
                                <select name="transaction_month" id="transaction_month" class="form-select" required>
                                    <option value="">-- Pilih Bulan Tagihan --</option>
                                </select>
                            </div>

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
                                        <label for="proof-upload" class="btn btn-sm btn-primary rounded-pill"><i class="ti tabler-upload me-1"></i>Pilih Gambar
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
                            <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i> Simpan Setoran</button>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js'])
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
@endsection

@section('page-script')
    <script type="module">
        $(document).ready(function() {
            const agreementSelect = $('#agreement_id');
            const formFields = $('#deposit-form-fields');
            const modal = $('#payment-exists-modal');
            const amountCalculationInfo = $('#amount-calculation-info');
            const amountCalculationInfoContainer = $('#amount-calculation-info-container');
            const transactionMonthSelect = $('#transaction_month');
            let availableMonthsData = [];
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
                transactionMonthSelect.empty().append('<option value="">-- Pilih Bulan Tagihan --</option>');
                $('#target_agreement_id').val('');
                $('#discount_amount').val(0);
                $('#discount_amount_display').val('');
                $('#proof-preview').attr('src', "{{ asset('/assets/img/transaksi.png') }}");
                $('#proof-upload').val('');
                amountCalculationInfo.html('');
                amountCalculationInfoContainer.hide();
                $('#discount_notes_container').hide();
                $('#discount_notes').removeAttr('required');
                let modal = $('#payment-exists-modal');
                modal.removeClass('active');
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
                            $('#modal-title').html('Belum Saatnya Membayar');
                            $('#modal-icon').attr('class', 'tf-icons ti tabler-clock-filled');
                            $('#modal-message').html(response.message);
                            $('#modal-action-btn').html('<i class="tf-icons ti tabler-refresh me-2"></i> Pilih PKS Lain')
                                .removeClass('btn-danger').addClass('btn-primary')
                                .off('click').on('click', function() { location.reload(); });
                            
                            modal.addClass('active').css({'visibility': 'visible', 'opacity': '1'});
                        } else {
                            modal.removeClass('active').css({'visibility': 'hidden', 'opacity': '0'});
                            formFields.prop('disabled', false);

                            availableMonthsData = response.available_months;
                            transactionMonthSelect.empty().append('<option value="">-- Pilih Bulan Tagihan --</option>');
                            
                            $.each(availableMonthsData, function(index, month) {
                                let opt = new Option(month.label, month.date);
                                $(opt).attr('data-amount', month.amount);
                                $(opt).attr('data-agreement-id', month.agreement_id);
                                transactionMonthSelect.append(opt);
                            });

                            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

                            // Saat bulan tagihan dipilih
                            transactionMonthSelect.off('change').on('change', function() {
                                const selectedOption = $(this).find('option:selected');
                                const selectedMonthVal = selectedOption.val();
                                
                                // Validasi: Hanya boleh bayar tagihan tertua (urutan pertama)
                                if (selectedMonthVal && availableMonthsData.length > 0) {
                                    const firstAvailableMonthVal = availableMonthsData[0].date;
                                    if (selectedMonthVal !== firstAvailableMonthVal) {
                                        const firstMonthLabel = availableMonthsData[0].label;
                                        $('#modal-title').html('Tunggakan Harus Dilunasi');
                                        $('#modal-icon').attr('class', 'tf-icons ti tabler-alert-triangle-filled text-danger');
                                        $('#modal-message').html(`Sistem mendeteksi adanya tagihan tertunggak.<br><br>Anda <strong>wajib</strong> melunasi tagihan <strong>${firstMonthLabel}</strong> terlebih dahulu sebelum dapat membayar tagihan yang baru.`);
                                        
                                        $('#modal-action-btn').html('<i class="tf-icons ti tabler-check me-2"></i> Mengerti, Saya Bayar Itu Dulu')
                                            .removeClass('btn-primary')
                                            .addClass('btn-danger')
                                            .off('click').on('click', function() {
                                                modal.removeClass('active').css({'visibility': 'hidden', 'opacity': '0'});
                                                transactionMonthSelect.val(firstAvailableMonthVal).trigger('change');
                                            });
                                            
                                        modal.addClass('active').css({'visibility': 'visible', 'opacity': '1'});
                                        return;
                                    }
                                }

                                const targetAmount = selectedOption.data('amount');
                                const targetAgreementId = selectedOption.data('agreement-id');

                                if (targetAgreementId) {
                                    $('#target_agreement_id').val(targetAgreementId);
                                } else {
                                    $('#target_agreement_id').val('');
                                }

                                if (!selectedOption.val()) {
                                    amountCalculationInfoContainer.hide();
                                    $('#original_amount_display').val('');
                                    originalTagihan = 0;
                                    calculateNetTagihan();
                                    return;
                                }

                                const selectedMonth = availableMonthsData.find(m => m.date === selectedOption.val());
                                if (selectedMonth) {
                                    const dAmount = selectedMonth.daily_amount || response.daily_amount;
                                    const infoText = `<i class="ti tabler-info-circle me-1"></i> Setoran bulan <strong>${selectedMonth.label}</strong> (Tarif Harian ${formatRupiah(dAmount)} &times; ${selectedMonth.days} hari).`;
                                    
                                    amountCalculationInfo.html(infoText);
                                    amountCalculationInfoContainer.show();
                                    
                                    originalTagihan = targetAmount || selectedMonth.amount;
                                    $('#original_amount_display').val(formatRupiah(originalTagihan));
                                    calculateNetTagihan();
                                }
                            });

                            // Select the first available month by default
                            if (availableMonthsData.length > 0) {
                                transactionMonthSelect.val(availableMonthsData[0].date).trigger('change');
                            }

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

            // Auto-trigger if targetAgreement is passed from server
            @if(isset($targetAgreement))
                var newOption = new Option("{{ $targetAgreement->agreement_number }} (Korlap: {{ $targetAgreement->fieldCoordinator->user->name ?? 'N/A' }})", "{{ $targetAgreement->id }}", true, true);
                agreementSelect.append(newOption).trigger('change');
                agreementSelect.trigger({
                    type: 'select2:select',
                    params: {
                        data: { id: "{{ $targetAgreement->id }}" }
                    }
                });
            @endif
        });
    </script>
@endsection
