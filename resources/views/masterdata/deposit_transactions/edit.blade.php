@extends('layouts.app')

@section('title', 'Edit Transaksi Setoran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Transaksi Setoran</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masterdata.deposit-transactions.index') }}">Transaksi
                            Setoran</a></li>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detail Setoran untuk PKS: {{ $depositTransaction->agreement->agreement_number }}
            </h5>
            @if (!$depositTransaction->is_validated && (Auth::user()->isAdmin() || Auth::user()->isLeader()))
                <form action="{{ route('masterdata.deposit-transactions.validate', $depositTransaction->id) }}"
                    method="POST" class="form-validate">
                    @csrf
                    <button type="submit" class="btn btn-success"><i
                            class="icon-base ri ri-check-double-line me-2"></i>Validasi Setoran Ini</button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('masterdata.deposit-transactions.update', $depositTransaction->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="row g-6">
                    <div class="col-12">
                        <label for="agreement_id" class="form-label">Perjanjian Aktif</label>
                        <select name="agreement_id" id="agreement_id" class="form-select select2-agreements" required
                            {{ $depositTransaction->is_validated ? 'disabled' : '' }}>
                            @if ($depositTransaction->agreement)
                                <option value="{{ $depositTransaction->agreement->id }}" selected>
                                    {{ $depositTransaction->agreement->agreement_number }} (Korlap:
                                    {{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }})
                                </option>
                            @endif
                        </select>
                        @if ($depositTransaction->is_validated)
                            <div class="form-text">Perjanjian tidak dapat diubah karena setoran sudah divalidasi.</div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline"><input type="date" name="deposit_date"
                                id="deposit_date" class="form-control"
                                value="{{ old('deposit_date', $depositTransaction->deposit_date->format('Y-m-d')) }}"
                                required {{ $depositTransaction->is_validated ? 'readonly' : '' }} /><label
                                for="deposit_date">Tanggal Setoran</label></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline"><input type="number" name="amount" id="amount"
                                class="form-control" placeholder="Contoh: 50000"
                                value="{{ old('amount', $depositTransaction->amount) }}" min="0" required
                                {{ $depositTransaction->is_validated ? 'readonly' : '' }} /><label for="amount">Jumlah
                                Setoran (Rp)</label></div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea name="notes" id="notes" class="form-control" placeholder="Masukkan catatan tambahan jika ada..."
                                style="height: 80px;" {{ $depositTransaction->is_validated ? 'readonly' : '' }}>{{ old('notes', $depositTransaction->notes) }}</textarea><label for="notes">Catatan (Opsional)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Bukti Transfer</label>
                        <div class="card">
                            <div class="card-body text-center">
                                {{-- ✅ PERBAIKAN DI SINI: Tampilkan gambar lama --}}
                                @if ($depositTransaction->proof_of_transfer)
                                    <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}"
                                        alt="proof-placeholder" class="d-block rounded-3 mx-auto mb-4" id="proof-preview"
                                        style="max-height: 150px;" />
                                @else
                                    <img src="{{ asset('assets/img/illustrations/image-light.png') }}"
                                        alt="proof-placeholder" class="d-block rounded-3 mx-auto mb-4" id="proof-preview"
                                        style="max-height: 150px;" />
                                @endif

                                {{-- Tombol upload hanya muncul jika belum divalidasi --}}
                                @if (!$depositTransaction->is_validated)
                                    <label for="proof-upload" class="btn btn-primary mt-2">
                                        <i class="icon-base ri-upload-2-line me-2"></i>Ubah Gambar
                                        <input type="file" id="proof-upload" name="proof_of_transfer"
                                            class="account-file-input" hidden accept="image/png, image/jpeg" />
                                    </label>
                                    <div id="proof-error" class="mt-2 text-danger text-sm"></div>
                                @else
                                    <p class="text-muted mb-0">Bukti transfer tidak dapat diubah setelah divalidasi.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 text-end">
                    <a href="{{ route('masterdata.deposit-transactions.index') }}"
                        class="btn btn-outline-secondary">Batal</a>
                    @if (!$depositTransaction->is_validated)
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ... (Script untuk Select2 dan konfirmasi validasi tidak berubah) ...
            const agreementSelect = $("#agreement_id");
            if (agreementSelect.length && !agreementSelect.prop("disabled")) {
                agreementSelect.wrap('<div class="position-relative"></div>').select2({
                    placeholder: "Cari atau pilih perjanjian...",
                    dropdownParent: agreementSelect.parent(),
                    allowClear: !0,
                    ajax: {
                        url: "{{ route('masterdata.search-active-agreements') }}",
                        dataType: "json",
                        delay: 250,
                        data: e => ({
                            term: e.term
                        }),
                        processResults: e => ({
                            results: e.results
                        }),
                        cache: !0
                    },
                    minimumInputLength: 2
                })
            }
            $(".form-validate").on("submit", function(e) {
                e.preventDefault();
                const t = this;
                Swal.fire({
                    title: "Validasi Setoran Ini?",
                    text: "Tindakan ini tidak dapat dibatalkan.",
                    icon: "question",
                    showCancelButton: !0,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6f6b7d",
                    confirmButtonText: "Ya, Validasi!",
                    cancelButtonText: "Batal"
                }).then(e => {
                    e.isConfirmed && t.submit()
                })
            });

            // --- Logika Upload & Kompresi Gambar ---
            const fileInput = document.getElementById('proof-upload');
            if (fileInput) {
                const imagePreview = document.getElementById('proof-preview');
                const errorDiv = document.getElementById('proof-error');
                const defaultSrc = imagePreview.src;

                fileInput.addEventListener('change', async (e) => {
                    const imageFile = e.target.files[0];
                    if (!imageFile) {
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) {
                        errorDiv.textContent = 'Hanya file JPG atau PNG.';
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                        return;
                    }
                    const options = {
                        maxSizeMB: 0.3,
                        maxWidthOrHeight: 1024,
                        useWebWorker: true
                    };
                    try {
                        const compressedFile = await imageCompression(imageFile, options);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                            type: compressedFile.type
                        }));
                        fileInput.files = dataTransfer.files;
                        imagePreview.src = URL.createObjectURL(compressedFile);
                    } catch (error) {
                        errorDiv.textContent = "Gagal memproses gambar.";
                        fileInput.value = '';
                        imagePreview.src = defaultSrc;
                    }
                });
            }
        });
    </script>
@endpush
