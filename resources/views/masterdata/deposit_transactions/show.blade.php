@extends('layouts.app')

@section('title', 'Detail Transaksi Setoran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
                <div class="card invoice-preview-card p-sm-12 p-6">
                    <div class="card-body invoice-preview-header rounded-4 p-6"
                        style="background-color: rgba(38, 43, 64, .03);">
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column text-heading align-items-xl-center align-items-md-start align-items-sm-center flex-wrap gap-6">
                            <div>
                                <div class="d-flex align-items-center mb-4">
                                    <img src="{{ $uptProfile->logo ? asset($uptProfile->logo) : asset('assets/img/logo-spt.png') }}"
                                        alt="Logo" height="40" class="me-3">
                                    <div>
                                        <h5 class="mb-0 fw-semibold">{{ $uptProfile->name }}</h5>
                                        <p class="mb-0" style="font-size: 0.85rem;">{{ $uptProfile->address }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xl">
                                <h5 class="mb-2 text-nowrap">BUKTI SETORAN</h5>
                                <p class="mb-2">{{ $depositTransaction->referral_code }}</p>
                                <div class="mb-1">
                                    <span>Tanggal Setor:</span>
                                    <span
                                        class="fw-medium">{{ $depositTransaction->deposit_date->translatedFormat('d F Y') }}</span>
                                </div>
                                <div>
                                    <span>Status:</span>
                                    @if ($depositTransaction->is_validated)
                                        <span class="badge bg-label-success rounded-pill">Tervalidasi</span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-6 px-0">
                        <div class="d-flex justify-content-between flex-wrap gap-6">
                            <div>
                                <h6>Informasi Perjanjian:</h6>
                                <p class="mb-1 fw-medium">{{ $depositTransaction->agreement->agreement_number }}</p>
                                <p class="mb-1">Korlap:
                                    {{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</p>
                                <p class="mb-0">Pimpinan:
                                    {{ $depositTransaction->agreement->leader->user->name ?? 'N/A' }}</p>
                            </div>
                            <div class="text-end">
                                <h6>Dicatat Oleh:</h6>
                                <p class="mb-1">{{ $depositTransaction->creator->name ?? 'N/A' }}</p>
                                <p class="mb-0"><small>Pada:
                                        {{ $depositTransaction->created_at->translatedFormat('d M Y, H:i') }}</small></p>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ PERUBAHAN UTAMA PADA TABEL INI --}}
                    <div class="table-responsive border rounded-4">
                        <table class="table m-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Setoran Harian</th>
                                    <th class="text-center">Jumlah Hari</th>
                                    <th class="text-end">Jumlah Dibayarkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-nowrap text-heading">Setoran Bulan {{ $monthName }}
                                        {{ $year }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($depositTransaction->agreement->daily_deposit_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">{{ $daysInMonth }} Hari</td>
                                    <td class="text-end fw-medium">Rp
                                        {{ number_format($depositTransaction->amount, 0, ',', '.') }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table m-0 table-borderless">
                            <tbody>
                                <tr>
                                    <td class="align-top px-0 py-6">
                                        <p class="mb-1"><span class="me-2 fw-medium text-heading">Catatan:</span></p>
                                        <span>{{ $depositTransaction->notes ?? 'Tidak ada catatan.' }}</span>
                                    </td>
                                    <td class="text-end pe-0 py-6 w-px-100">
                                        <p class="mb-0 pt-2 fw-bold text-heading">Total:</p>
                                    </td>
                                    <td class="text-end px-0 py-6 w-px-100">
                                        <p class="fw-bold mb-0 pt-2">Rp
                                            {{ number_format($depositTransaction->amount, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($depositTransaction->proof_of_transfer)
                        <hr class="mt-6 mb-6" />
                        <div class="card-body p-0">
                            <h6 class="mb-4">Bukti Transfer:</h6>
                            <a href="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" target="_blank">
                                <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}"
                                    alt="Bukti Transfer" class="img-fluid rounded-3 border" style="max-width: 400px;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Invoice -->

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        @if (!$depositTransaction->is_validated && Auth::user()->isAdmin())
                            <form action="{{ route('masterdata.deposit-transactions.validate', $depositTransaction->id) }}"
                                method="POST" class="form-validate">
                                @csrf
                                <button type="submit" class="btn btn-success d-grid w-100 mb-4">
                                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                                        <i class="icon-base ri ri-check-double-line me-2"></i>Validasi Setoran
                                    </span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('masterdata.deposit-transactions.pdf', $depositTransaction->id) }}"
                            target="_blank" class="btn btn-primary d-grid w-100 mb-4">
                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                <i class="icon-base ri ri-printer-line me-2"></i>Cetak Bukti
                            </span>
                        </a>

                        @if (!$depositTransaction->is_validated)
                            <a href="{{ route('masterdata.deposit-transactions.edit', $depositTransaction->id) }}"
                                class="btn btn-outline-secondary d-grid w-100 mb-4">Edit</a>
                        @endif

                        <a href="{{ route('masterdata.deposit-transactions.index') }}"
                            class="btn btn-outline-secondary d-grid w-100">Kembali</a>
                    </div>
                </div>
            </div>
            <!-- /Invoice Actions -->
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
                    title: 'Anda Yakin?',
                    text: "Tindakan validasi ini tidak dapat dibatalkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Validasi!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success me-3',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
