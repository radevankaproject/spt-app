@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Transaksi Setoran')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .premium-card {
            border: 0;
            box-shadow: 0 0.5rem 1.5rem rgba(22, 28, 36, 0.05);
            border-radius: 1rem;
            background-color: #ffffff;
        }
        .premium-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(22, 28, 36, 0.05);
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 2rem 2.5rem;
        }
        .premium-body {
            padding: 2.5rem;
        }
        .text-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 130px 10px 1fr;
            row-gap: 0.75rem;
            align-items: start;
        }
        .info-grid .label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .info-grid .colon {
            color: #495057;
            font-weight: 500;
        }
        .info-grid .value {
            color: #212529;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .info-section {
            background-color: #ffffff;
            border: 1px solid rgba(22, 28, 36, 0.08);
            border-radius: 0.75rem;
            padding: 1.5rem;
            height: 100%;
        }
        .table-premium {
            margin-bottom: 0;
        }
        .table-premium thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(22, 28, 36, 0.08);
            padding: 1rem 1.25rem;
        }
        .table-premium tbody td {
            padding: 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(22, 28, 36, 0.04);
            font-size: 0.95rem;
        }
        .table-premium tbody tr:last-child td {
            border-bottom: 0;
        }
        .total-box {
            background-color: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid rgba(22, 28, 36, 0.08);
        }
        .total-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .total-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0d6efd;
            letter-spacing: -0.5px;
        }
        .proof-img {
            border-radius: 0.5rem;
            border: 1px solid rgba(22, 28, 36, 0.1);
            max-height: 160px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.2s;
        }
        .proof-img:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .action-card {
            border: 1px solid rgba(22, 28, 36, 0.08);
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(22, 28, 36, 0.03);
        }
        .btn-premium {
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .badge-premium {
            padding: 0.5em 1em;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 0.375rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-4">
            {{-- Kiri: Detail Kuitansi --}}
            <div class="col-xl-9 col-lg-8">
                <div class="premium-card">
                    {{-- Header --}}
                    <div class="premium-header">
                        <div class="row align-items-center justify-content-between g-4">
                            <div class="col-md-7 d-flex align-items-center">
                                <img src="{{ $uptProfile->logo ? asset($uptProfile->logo) : asset('assets/img/logo-spt.png') }}"
                                    alt="Logo UPT" height="64" class="me-3 rounded bg-white p-1 border">
                                <div>
                                    <h4 class="mb-1 fw-bold text-dark">{{ $uptProfile->name }}</h4>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;">{{ $uptProfile->address }}</p>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <h5 class="fw-bold text-uppercase text-primary mb-2" style="letter-spacing: 1px;">Bukti Setoran</h5>
                                <div class="fs-5 fw-bold text-dark mb-2">{{ $depositTransaction->referral_code }}</div>
                                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3">
                                    <div class="text-muted" style="font-size: 0.9rem;">
                                        <i class="ti tabler-calendar text-primary"></i> {{ $depositTransaction->deposit_date->translatedFormat('d F Y') }}
                                    </div>
                                    <div>
                                        @if ($depositTransaction->is_validated)
                                            <span class="badge bg-success bg-opacity-10 text-success badge-premium border border-success border-opacity-25">
                                                <i class="ti tabler-checks me-1"></i> Tervalidasi
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning badge-premium border border-warning border-opacity-50 text-dark">
                                                <i class="ti tabler-clock me-1"></i> Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="premium-body">
                        {{-- Grid Info --}}
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="info-section">
                                    <div class="text-label mb-3"><i class="ti tabler-file-description text-primary me-1"></i> Informasi Perjanjian</div>
                                    <div class="info-grid">
                                        <div class="label">No. PKS</div>
                                        <div class="colon">:</div>
                                        <div class="value">{{ $depositTransaction->agreement->agreement_number }}</div>

                                        <div class="label">Mitra (Korlap)</div>
                                        <div class="colon">:</div>
                                        <div class="value">{{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</div>

                                        <div class="label">Pimpinan UPT</div>
                                        <div class="colon">:</div>
                                        <div class="value">{{ $depositTransaction->agreement->leader->user->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-section">
                                    <div class="text-label mb-3"><i class="ti tabler-shield-check text-primary me-1"></i> Jejak Audit</div>
                                    <div class="info-grid">
                                        <div class="label">Dicatat Oleh</div>
                                        <div class="colon">:</div>
                                        <div class="value">
                                            {{ $depositTransaction->creator->name ?? 'Sistem' }}
                                            <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">{{ $depositTransaction->created_at->translatedFormat('d M Y, H:i') }}</div>
                                        </div>

                                        <div class="label">Bendahara</div>
                                        <div class="colon">:</div>
                                        <div class="value text-primary">{{ $depositTransaction->treasurer->user->name ?? 'N/A' }}</div>

                                        @if ($depositTransaction->is_validated)
                                            <div class="label pt-2">Divalidasi Oleh</div>
                                            <div class="colon pt-2">:</div>
                                            <div class="value pt-2 text-success">
                                                <i class="ti tabler-verified-badge-filled me-1"></i> {{ $depositTransaction->validator->name ?? 'N/A' }}
                                                <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($depositTransaction->validation_date)->translatedFormat('d M Y, H:i') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if ($depositTransaction->discount_amount > 0)
                                            <div class="label pt-2">Diskon Oleh</div>
                                            <div class="colon pt-2">:</div>
                                            <div class="value pt-2 text-danger">
                                                <i class="ti tabler-shield-star-filled me-1"></i> {{ $depositTransaction->discountApprover->name ?? 'N/A' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Rincian --}}
                        <div class="border rounded-4 overflow-hidden mb-5">
                            <div class="table-responsive">
                                <table class="table table-premium">
                                    <thead>
                                        <tr>
                                            <th>Rincian Tagihan</th>
                                            <th class="text-end">Tarif Harian</th>
                                            <th class="text-center">Durasi</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">Setoran Bulan {{ $monthName }}</div>
                                                <div class="text-muted small">Tahun {{ $year }}</div>
                                            </td>
                                            <td class="text-end fw-medium text-dark">Rp {{ number_format($depositTransaction->agreement->daily_deposit_amount, 0, ',', '.') }}</td>
                                            <td class="text-center"><span class="badge bg-light text-dark border px-2 py-1">{{ $daysInMonth }} Hari</span></td>
                                            <td class="text-end fw-bold text-dark fs-6">Rp {{ number_format($depositTransaction->amount + $depositTransaction->discount_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @if ($depositTransaction->discount_amount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end text-danger fw-bold">
                                                <i class="ti tabler-price-tag-3 me-1"></i> Potongan / Keringanan
                                            </td>
                                            <td class="text-end fw-bold text-danger fs-6">- Rp {{ number_format($depositTransaction->discount_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Section Bawah: Catatan & Total --}}
                        <div class="row g-4 align-items-stretch">
                            <div class="col-md-7 d-flex flex-column gap-4">
                                {{-- Notes --}}
                                <div>
                                    <div class="text-label mb-2"><i class="ti tabler-sticky-note me-1"></i> Catatan Tambahan</div>
                                    <div class="bg-light rounded p-3 text-muted fst-italic" style="font-size: 0.9rem; border-left: 3px solid #dee2e6;">
                                        {{ $depositTransaction->notes ?? 'Tidak ada catatan yang dilampirkan.' }}
                                    </div>
                                </div>
                                
                                @if ($depositTransaction->discount_amount > 0)
                                <div>
                                    <div class="text-label text-danger mb-2"><i class="ti tabler-alert-triangle me-1"></i> Alasan Potongan</div>
                                    <div class="bg-danger bg-opacity-10 text-danger rounded p-3 fst-italic" style="font-size: 0.9rem; border-left: 3px solid #dc3545;">
                                        {{ $depositTransaction->discount_notes ?? '-' }}
                                    </div>
                                </div>
                                @endif

                                {{-- Image --}}
                                @if ($depositTransaction->proof_of_transfer)
                                <div>
                                    <div class="text-label mb-2"><i class="ti tabler-image me-1"></i> Bukti Transfer</div>
                                    <div class="d-inline-block position-relative" data-bs-toggle="modal" data-bs-target="#proofModal">
                                        <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" alt="Bukti Transfer" class="proof-img img-fluid bg-light p-1">
                                        <div class="position-absolute bottom-0 start-0 w-100 p-2 text-center" style="background: linear-gradient(transparent, rgba(0,0,0,0.7)); border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                                            <small class="text-white"><i class="ti tabler-zoom-in"></i> Perbesar</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-5 d-flex flex-column justify-content-end">
                                <div class="total-box text-end shadow-sm">
                                    <div class="total-label mb-1">Total Bersih Setoran</div>
                                    <div class="total-value">
                                        <span class="fs-5 text-muted fw-normal">Rp</span> {{ number_format($depositTransaction->amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Aksi --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card action-card sticky-top" style="top: 1.5rem;">
                    <div class="card-body p-4">
                        <div class="text-label mb-4 text-center">Tindakan</div>
                        
                        @if (!$depositTransaction->is_validated && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('treasurer') || Auth::user()->hasRole('staff_keu')))
                            <form action="{{ route('masterdata.deposit-transactions.validate', $depositTransaction->id) }}" method="POST" class="form-validate mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success btn-premium w-100 shadow-sm">
                                    <i class="ti tabler-checks fs-5"></i> Validasi Setoran
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('masterdata.deposit-transactions.pdf', $depositTransaction->id) }}" target="_blank" class="btn btn-primary btn-premium w-100 mb-3 shadow-sm">
                            <i class="ti tabler-printer fs-5"></i> Cetak Struk
                        </a>

                        @if (!$depositTransaction->is_validated && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu') || Auth::user()->hasRole('treasurer')))
                            <a href="{{ route('masterdata.deposit-transactions.edit', $depositTransaction->id) }}" class="btn btn-outline-primary btn-premium w-100 mb-4 bg-white">
                                <i class="ti tabler-pencil"></i> Edit Data
                            </a>
                        @endif

                        <hr class="my-4 border-light">

                        <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-light btn-premium w-100 text-dark border">
                            <i class="ti tabler-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bukti Transfer --}}
    @if ($depositTransaction->proof_of_transfer)
    <div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white fs-4" data-bs-dismiss="modal" aria-label="Close" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));"></button>
                </div>
                <div class="modal-body text-center pt-0 px-0">
                    <img src="{{ asset('storage/' . $depositTransaction->proof_of_transfer) }}" alt="Bukti Transfer Zoom" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; border: 4px solid white;">
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            $('.form-validate').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Validasi Setoran?',
                    text: "Tindakan ini akan mengesahkan setoran ke sistem kas.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti tabler-checks me-1"></i> Ya, Validasi!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 shadow-sm px-4',
                        cancelButton: 'btn btn-outline-secondary px-4'
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
@endsection
