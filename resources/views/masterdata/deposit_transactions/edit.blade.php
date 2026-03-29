@extends('layouts.app')
@section('title', 'Edit Transaksi Setoran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Transaksi Setoran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                <li class="breadcrumb-item"><a href="{{ route('masterdata.deposit-transactions.index') }}">Transaksi Setoran</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom pb-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">No PKS: <span class="text-primary">{{ $depositTransaction->agreement->agreement_number }}</span></h5>
            @if (!$depositTransaction->is_validated && (Auth::user()->isAdmin() || Auth::user()->isStaffKeu()))
                <form action="{{ route('masterdata.deposit-transactions.validate', $depositTransaction->id) }}" method="POST" class="form-validate">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="ri ri-check-double-line me-1"></i> Validasi Setoran Ini</button>
                </form>
            @endif
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('masterdata.deposit-transactions.update', $depositTransaction->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="row g-4">
                    <div class="col-12">
                        <label for="agreement_id" class="form-label">Perjanjian Terkait</label>
                        <select name="agreement_id" id="agreement_id" class="form-select select2-agreements" required {{ $depositTransaction->is_validated ? 'disabled' : '' }}>
                            @if ($depositTransaction->agreement)
                                <option value="{{ $depositTransaction->agreement->id }}" selected>
                                    {{ $depositTransaction->agreement->agreement_number }} (Korlap: {{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }})
                                </option>
                            @endif
                        </select>
                        @if ($depositTransaction->is_validated) <div class="form-text text-warning"><i class="ri ri-lock-2-line me-1"></i> Setoran terkunci karena sudah divalidasi.</div> @endif
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="date" name="deposit_date" id="deposit_date" class="form-control" value="{{ old('deposit_date', $depositTransaction->deposit_date->format('Y-m-d')) }}" required {{ $depositTransaction->is_validated ? 'readonly' : '' }} />
                            <label for="deposit_date">Tanggal Setoran</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $depositTransaction->amount) }}" min="0" required {{ $depositTransaction->is_validated ? 'readonly' : '' }} />
                            <label for="amount">Jumlah Setoran (Rp)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea name="notes" id="notes" class="form-control" style="height: 80px;" {{ $depositTransaction->is_validated ? 'readonly' : '' }}>{{ old('notes', $depositTransaction->notes) }}</textarea>
                            <label for="notes">Catatan Tambahan</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Bukti Transfer</label>
                        <div class="card border border-dashed shadow-none">
                            <div class="card-body text-center py-4">
                                @if ($depositTransaction->proof_of_transfer)
                                    <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" class="d-block rounded-3 mx-auto mb-3 shadow-sm" id="proof-preview" style="height: 150px; object-fit:contain;" />
                                @else
                                    <img src="{{ asset('assets/img/illustrations/image-light.png') }}" class="d-block rounded-3 mx-auto mb-3" id="proof-preview" style="height: 150px; object-fit:contain;" />
                                @endif

                                @if (!$depositTransaction->is_validated)
                                    <label for="proof-upload" class="btn btn-sm btn-primary rounded-pill">
                                        <i class="ri ri-upload-2-line me-1"></i> Ubah Gambar
                                        <input type="file" id="proof-upload" name="proof_of_transfer" class="account-file-input" hidden accept="image/png, image/jpeg" />
                                    </label>
                                    <div id="proof-error" class="mt-2 text-danger small"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 text-end border-top mt-5">
                    <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-outline-secondary me-2">Kembali</a>
                    @if (!$depositTransaction->is_validated)
                        <button type="submit" class="btn btn-primary"><i class="ri ri-save-3-line me-1"></i> Simpan Perubahan</button>
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
            $(".form-validate").on("submit", function(e) {
                e.preventDefault(); const t = this;
                Swal.fire({
                    title: "Validasi Setoran?", text: "Uang sudah masuk ke rekening Kas Daerah?", icon: "question", showCancelButton: !0,
                    confirmButtonColor: "#28a745", cancelButtonColor: "#6f6b7d", confirmButtonText: "Ya, Validasi!", cancelButtonText: "Batal"
                }).then(e => { if(e.isConfirmed) t.submit(); })
            });

            // --- Kompresi Gambar ---
            const fileInput = document.getElementById('proof-upload');
            if (fileInput) {
                fileInput.addEventListener('change', async (e) => {
                    const imagePreview = document.getElementById('proof-preview');
                    const errorDiv = document.getElementById('proof-error');
                    const imageFile = e.target.files[0];
                    if (!imageFile) return;

                    errorDiv.textContent = '';
                    if (!['image/jpeg', 'image/png'].includes(imageFile.type)) { errorDiv.textContent = 'Hanya JPG/PNG.'; fileInput.value = ''; return; }

                    if(imageFile.size / 1024 <= 300) { imagePreview.src = URL.createObjectURL(imageFile); return; }

                    try {
                        const options = { maxSizeMB: 0.3, maxWidthOrHeight: 1024, useWebWorker: true };
                        const compressedFile = await imageCompression(imageFile, options);
                        const dt = new DataTransfer(); dt.items.add(new File([compressedFile], imageFile.name, { type: compressedFile.type }));
                        fileInput.files = dt.files;
                        imagePreview.src = URL.createObjectURL(compressedFile);
                    } catch (error) { errorDiv.textContent = "Gagal memproses."; fileInput.value = ''; }
                });
            }
        });
    </script>
@endpush
