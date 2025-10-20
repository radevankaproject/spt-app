@extends('layouts.app')

@section('title', 'Daftar Transaksi Setoran')

@section('skeleton')
    @include('layouts.partials._skeleton-deposit-transactions-index')
@endsection

@push('styles')
    {{-- ✅ CSS UNTUK FITUR BARU --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Transaksi Setoran</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Transaksi Setoran</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Pencarian</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('masterdata.deposit-transactions.index') }}" method="GET">
                <div class="row g-4">
                    {{-- ✅ INPUT TANGGAL DIUBAH --}}
                    <div class="col-md-3">
                        <label for="search_date" class="form-label">Tanggal Spesifik</label>
                        <input type="text" name="search_date" id="search_date" class="form-control"
                            placeholder="YYYY-MM-DD" value="{{ $searchDate ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search_month" class="form-label">Bulan</label>
                        <select name="search_month" id="search_month" class="form-select">
                            <option value="">Semua Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}"
                                    {{ ($searchMonth ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="search_year" class="form-label">Tahun</label>
                        <input type="number" name="search_year" id="search_year" min="2020" max="{{ date('Y') + 5 }}"
                            class="form-control" value="{{ $searchYear ?? date('Y') }}">
                    </div>
                    {{-- ✅ INPUT TANGGAL DIUBAH --}}
                    <div class="col-md-4">
                        <label class="form-label">Rentang Waktu</label>
                        <div class="input-group">
                            <input type="text" name="start_date_range" id="start_date_range" class="form-control"
                                placeholder="YYYY-MM-DD" value="{{ $startDateRange ?? '' }}">
                            <span class="input-group-text">s/d</span>
                            <input type="text" name="end_date_range" id="end_date_range" class="form-control"
                                placeholder="YYYY-MM-DD" value="{{ $endDateRange ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <label for="search" class="form-label">Pencarian Umum</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Cari no. PKS, nama korlap, atau nominal..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="pt-4 text-end">
                    <a href="{{ route('masterdata.deposit-transactions.index') }}"
                        class="btn btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Cari Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Transaksi --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Transaksi Setoran</h5>
                <p class="text-muted mb-0">Total {{ $depositTransactions->total() }} transaksi ditemukan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center">
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu'))
                    <a href="{{ route('masterdata.deposit-transactions.create') }}" class="btn btn-primary">
                        <i class="icon-base ri ri-add-line me-2"></i>Catat Setoran
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
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
                                <td><span class="fw-medium">{{ $transaction->agreement->agreement_number ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $transaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->deposit_date->format('d M Y') }}</td>
                                <td><span class="fw-medium">Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}</span></td>
                                <td>
                                    @if ($transaction->is_validated)
                                        <span class="badge rounded-pill bg-label-success">Tervalidasi</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        {{-- 1. Tombol Detail: Selalu bisa diakses oleh admin dan staff_keu --}}
                                        <a class="btn btn-sm btn-icon"
                                            href="{{ route('masterdata.deposit-transactions.show', $transaction->id) }}"
                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="icon-base ri ri-eye-line"></i>
                                        </a>

                                        {{-- 2. Tombol Aksi jika status masih PENDING --}}
                                        @if (!$transaction->is_validated)
                                            {{-- Tombol Validasi & Edit (untuk admin & staff_keu) --}}
                                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu'))
                                                <form
                                                    action="{{ route('masterdata.deposit-transactions.validate', $transaction->id) }}"
                                                    method="POST" class="form-validate">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon"
                                                        data-bs-toggle="tooltip" title="Validasi Setoran">
                                                        <i class="icon-base ri ri-check-double-line text-success"></i>
                                                    </button>
                                                </form>

                                                <a class="btn btn-sm btn-icon"
                                                    href="{{ route('masterdata.deposit-transactions.edit', $transaction->id) }}"
                                                    data-bs-toggle="tooltip" title="Edit">
                                                    <i class="icon-base ri ri-pencil-line"></i>
                                                </a>
                                            @endif
                                        @endif

                                        {{-- ✅ FIX: Tombol Hapus hanya akan muncul jika role pengguna adalah 'admin' --}}
                                        @if (Auth::user()->hasRole('admin'))
                                            <form
                                                action="{{ route('masterdata.deposit-transactions.destroy', $transaction->id) }}"
                                                method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="icon-base ri ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data transaksi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $depositTransactions->appends(request()->query())->links() }}
            </div>
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
            // Inisialisasi Flatpickr
            flatpickr("#search_date", {
                dateFormat: "Y-m-d"
            });
            flatpickr("#start_date_range", {
                dateFormat: "Y-m-d"
            });
            flatpickr("#end_date_range", {
                dateFormat: "Y-m-d"
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Konfirmasi Hapus
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data setoran yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6f6b7d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Konfirmasi Validasi
            document.querySelectorAll('.form-validate').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Validasi Setoran Ini?',
                        text: "Tindakan ini tidak dapat dibatalkan.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6f6b7d',
                        confirmButtonText: 'Ya, Validasi!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
