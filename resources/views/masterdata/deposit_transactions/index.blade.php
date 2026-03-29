@extends('layouts.app')
@section('title', 'Daftar Transaksi Setoran')
@section('skeleton')
    @include('layouts.partials._skeleton-deposit-transactions-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Transaksi Setoran PKS</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                <li class="breadcrumb-item active">Setoran</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-bottom pb-2">
            <h6 class="card-title mb-0"><i class="ri ri-filter-3-line me-1"></i> Filter Lanjutan</h6>
        </div>
        <div class="card-body pt-3">
            <form action="{{ route('masterdata.deposit-transactions.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Spesifik</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ri ri-calendar-event-line"></i></span>
                            <input type="text" name="search_date" id="search_date" class="form-control" placeholder="YYYY-MM-DD" value="{{ $searchDate ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="search_month" class="form-select">
                            <option value="">Semua Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ ($searchMonth ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="search_year" min="2020" max="{{ date('Y') + 5 }}" class="form-control" value="{{ $searchYear ?? date('Y') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Rentang Waktu</label>
                        <div class="input-group">
                            <input type="text" name="start_date_range" id="start_date_range" class="form-control" placeholder="YYYY-MM-DD" value="{{ $startDateRange ?? '' }}">
                            <span class="input-group-text">s/d</span>
                            <input type="text" name="end_date_range" id="end_date_range" class="form-control" placeholder="YYYY-MM-DD" value="{{ $endDateRange ?? '' }}">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari No PKS, Nama Korlap, Nominal..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="pt-3 text-end">
                    <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-sm btn-outline-secondary me-2">Reset</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="ri ri-filter-3-fill me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3">
            <div>
                <h5 class="mb-0">Daftar Transaksi</h5>
                <small class="text-muted">Total {{ $depositTransactions->total() }} transaksi.</small>
            </div>
            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu'))
                <a href="{{ route('masterdata.deposit-transactions.create') }}" class="btn btn-primary">
                    <i class="ri ri-add-line me-1"></i> Catat Setoran
                </a>
            @endif
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Perjanjian</th>
                            <th>Koordinator</th>
                            <th>Tgl Setor</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($depositTransactions as $transaction)
                            <tr>
                                <td><span class="fw-medium text-primary">{{ $transaction->agreement->agreement_number ?? 'N/A' }}</span></td>
                                <td>{{ $transaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->deposit_date->format('d M Y') }}</td>
                                <td><span class="fw-medium text-success">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span></td>
                                <td>
                                    @if ($transaction->is_validated) <span class="badge rounded-pill bg-label-success">Tervalidasi</span>
                                    @else <span class="badge rounded-pill bg-label-warning">Pending</span> @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill" href="{{ route('masterdata.deposit-transactions.show', $transaction->id) }}" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="ri ri-eye-line ri-20px"></i>
                                        </a>

                                        @if (!$transaction->is_validated)
                                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu'))
                                                <form action="{{ route('masterdata.deposit-transactions.validate', $transaction->id) }}" method="POST" class="form-validate d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-text-success rounded-pill" data-bs-toggle="tooltip" title="Validasi">
                                                        <i class="ri ri-check-double-line ri-20px"></i>
                                                    </button>
                                                </form>
                                                <a class="btn btn-sm btn-icon btn-text-primary rounded-pill" href="{{ route('masterdata.deposit-transactions.edit', $transaction->id) }}" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="ri ri-pencil-line ri-20px"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if (Auth::user()->hasRole('admin'))
                                            <form action="{{ route('masterdata.deposit-transactions.destroy', $transaction->id) }}" method="POST" class="form-delete d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ri ri-delete-bin-line ri-20px"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $depositTransactions->appends(request()->query())->links() }}</div>
        </div>
    </div>
@endsection

@push('vendors-js')
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#search_date", { dateFormat: "Y-m-d" });
            flatpickr("#start_date_range", { dateFormat: "Y-m-d" });
            flatpickr("#end_date_range", { dateFormat: "Y-m-d" });

            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Setoran?', text: "Data tidak dapat dikembalikan!", icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#d33', cancelButtonColor: '#6f6b7d', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            document.querySelectorAll('.form-validate').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Uang Sudah Masuk Kas?', text: "Tindakan validasi ini tidak dapat dibatalkan.", icon: 'question', showCancelButton: true,
                        confirmButtonColor: '#28a745', cancelButtonColor: '#6f6b7d', confirmButtonText: 'Ya, Validasi!', cancelButtonText: 'Batal'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(t => new bootstrap.Tooltip(t));
        });
    </script>
@endpush
