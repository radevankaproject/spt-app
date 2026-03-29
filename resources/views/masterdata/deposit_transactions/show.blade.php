@extends('layouts.app')

@section('title', 'Detail Transaksi Setoran')

@section('skeleton')
    @include('layouts.partials._skeleton-deposit-transaction-show')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .invoice-preview-card { border: none; box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15); }
        .invoice-preview-header { background-color: #f8f9fa; border-radius: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
                <div class="card invoice-preview-card p-sm-12 p-6">
                    <div class="card-body invoice-preview-header p-6 mb-4">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column align-items-xl-center align-items-md-start align-items-sm-center flex-wrap gap-6">
                            <div>
                                <div class="d-flex align-items-center mb-4">
                                    <img src="{{ $uptProfile->logo ? asset($uptProfile->logo) : asset('assets/img/logo-spt.png') }}"
                                        alt="Logo" height="50" class="me-4 rounded">
                                    <div>
                                        <h5 class="mb-0 fw-bold text-primary">{{ $uptProfile->name }}</h5>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">{{ $uptProfile->address }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xl-end">
                                <h4 class="mb-2 fw-bold text-dark">BUKTI SETORAN</h4>
                                <p class="mb-2 fw-medium text-primary fs-5">{{ $depositTransaction->referral_code }}</p>
                                <div class="mb-1 text-muted">
                                    <span>Tanggal Setor:</span>
                                    <span class="fw-medium text-dark">{{ $depositTransaction->deposit_date->translatedFormat('d F Y') }}</span>
                                </div>
                                <div>
                                    <span>Status Validasi:</span>
                                    @if ($depositTransaction->is_validated)
                                        <span class="badge bg-label-success rounded-pill px-3 py-1"><i class="ri ri-check-double-line me-1"></i> Tervalidasi</span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill px-3 py-1"><i class="ri ri-time-line me-1"></i> Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body py-4 px-0">
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold text-primary mb-3"><i class="ri ri-file-paper-2-line me-1"></i> Informasi Perjanjian</h6>
                                <table class="table table-sm table-borderless m-0">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 py-1 text-muted" style="width: 130px;">No. PKS</td>
                                            <td class="py-1 fw-bold text-dark">: {{ $depositTransaction->agreement->agreement_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 text-muted">Mitra (Korlap)</td>
                                            <td class="py-1 fw-medium">: {{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 text-muted">Pimpinan UPT</td>
                                            <td class="py-1">: {{ $depositTransaction->agreement->leader->user->name ?? 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6 text-sm-end text-start">
                                <h6 class="fw-bold text-primary mb-3"><i class="ri ri-shield-check-line me-1"></i> Informasi Audit</h6>
                                <table class="table table-sm table-borderless m-0 text-sm-end text-start">
                                    <tbody>
                                        <tr>
                                            <td class="py-1 text-muted">Dicatat Oleh</td>
                                            <td class="pe-0 py-1 fw-medium text-dark">: {{ $depositTransaction->creator->name ?? 'Sistem' }} <br><small class="text-muted">({{ $depositTransaction->created_at->translatedFormat('d M Y, H:i') }})</small></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 text-muted pt-3">Bendahara Penerimaan</td>
                                            <td class="pe-0 py-1 pt-3 fw-medium text-primary">: {{ $depositTransaction->treasurer->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        @if ($depositTransaction->is_validated)
                                            <tr>
                                                <td class="py-1 text-muted pt-3">Divalidasi Oleh</td>
                                                <td class="pe-0 py-1 pt-3 fw-bold text-success">: <i class="ri ri-verified-badge-fill me-1"></i> {{ $depositTransaction->validator->name ?? 'N/A' }} <br><small class="text-muted">({{ \Carbon\Carbon::parse($depositTransaction->validation_date)->translatedFormat('d M Y, H:i') }})</small></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3 mb-4">
                        <table class="table table-striped m-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="fw-bold">Rincian Pembayaran</th>
                                    <th class="text-end fw-bold">Tarif Harian</th>
                                    <th class="text-center fw-bold">Durasi (Hari)</th>
                                    <th class="text-end fw-bold">Total Dibayarkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-nowrap text-heading fw-medium">Setoran Bulan {{ $monthName }} {{ $year }}</td>
                                    <td class="text-end">Rp {{ number_format($depositTransaction->agreement->daily_deposit_amount, 0, ',', '.') }}</td>
                                    <td class="text-center"><span class="badge bg-label-info">{{ $daysInMonth }} Hari</span></td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($depositTransaction->amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-7">
                            <div class="bg-lighter p-3 rounded-3 h-100">
                                <p class="mb-1 fw-bold text-dark"><i class="ri ri-sticky-note-line me-1"></i> Catatan Tambahan:</p>
                                <p class="mb-0 text-muted fst-italic">{{ $depositTransaction->notes ?? 'Tidak ada catatan.' }}</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 p-4 rounded-3 h-100">
                                <span class="fw-bold text-primary fs-5">TOTAL SETORAN</span>
                                <span class="fw-bold text-primary fs-4">Rp {{ number_format($depositTransaction->amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($depositTransaction->proof_of_transfer)
                        <hr class="my-5" />
                        <div>
                            <h6 class="fw-bold text-dark mb-3"><i class="ri ri-image-2-line me-1"></i> Lampiran Bukti Transfer</h6>
                            <a href="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" target="_blank" class="d-inline-block border rounded-3 p-1 shadow-sm" style="background: #f8f9fa;">
                                <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">

                        {{-- ✅ TOMBOL VALIDASI (Khusus Admin/Bendahara/Staff Keu, jika belum divalidasi) --}}
                        @if (!$depositTransaction->is_validated && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('bendahara') || Auth::user()->hasRole('staff_keu')))
                            <form action="{{ route('masterdata.deposit-transactions.validate', $depositTransaction->id) }}" method="POST" class="form-validate mb-4">
                                @csrf
                                <button type="submit" class="btn btn-success d-grid w-100 shadow-sm">
                                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                                        <i class="ri ri-check-double-line me-2 fs-5"></i> Validasi Setoran
                                    </span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('masterdata.deposit-transactions.pdf', $depositTransaction->id) }}" target="_blank" class="btn btn-primary d-grid w-100 mb-3 shadow-sm">
                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                <i class="ri ri-printer-line me-2"></i> Cetak Struk PDF
                            </span>
                        </a>

                        @if (!$depositTransaction->is_validated && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu')))
                            <a href="{{ route('masterdata.deposit-transactions.edit', $depositTransaction->id) }}" class="btn btn-outline-primary d-grid w-100 mb-3">
                                <i class="ri ri-pencil-line me-1"></i> Edit Data
                            </a>
                        @endif

                        <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-outline-secondary d-grid w-100">
                            <i class="ri ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.form-validate').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Uang Sudah Masuk Kas?',
                    text: "Tindakan validasi ini tidak dapat dibatalkan, dan akan mengaktifkan PKS secara otomatis jika ini adalah setoran pertama.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ri ri-check-double-line me-1"></i> Ya, Validasi!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 shadow-sm',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Aktifkan Tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(t => new bootstrap.Tooltip(t));
        });
    </script>
@endpush
