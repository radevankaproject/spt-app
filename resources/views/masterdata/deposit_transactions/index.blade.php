@extends('layouts.contentNavbarLayout')
@section('title', 'Daftar Transaksi Setoran')


@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

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

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <a href="{{ route('masterdata.deposit-transactions.index', array_merge(request()->query(), ['status' => 'jatuh_tempo'])) }}" class="nav-link {{ request('status', 'jatuh_tempo') === 'jatuh_tempo' ? 'active bg-danger text-white' : 'text-danger' }}">
                    <i class="ti tabler-alert-triangle me-1"></i> Jatuh Tempo
                    @php 
                        $jatuhTempoCount = isset($arrears) ? $arrears->count() : 0; 
                    @endphp
                    @if($jatuhTempoCount > 0)
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-white text-danger ms-1">{{ $jatuhTempoCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('masterdata.deposit-transactions.index', array_merge(request()->query(), ['status' => 'all'])) }}" class="nav-link {{ request('status') === 'all' ? 'active' : '' }}">
                    <i class="ti tabler-list-check me-1"></i> Semua Setoran
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('masterdata.deposit-transactions.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}">
                    <i class="ti tabler-clock me-1"></i> Menunggu Validasi
                    @php $pendingCount = \App\Models\DepositTransaction::where('is_validated', 0)->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('masterdata.deposit-transactions.index', array_merge(request()->query(), ['status' => 'validated'])) }}" class="nav-link {{ request('status') === 'validated' ? 'active' : '' }}">
                    <i class="ti tabler-circle-check me-1"></i> Tervalidasi
                </a>
            </li>
        </ul>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-bottom pb-2">
            <h6 class="card-title mb-0"><i class="ti tabler-filter me-1"></i> Filter Lanjutan</h6>
        </div>
        <div class="card-body pt-3">
            <form action="{{ route('masterdata.deposit-transactions.index') }}" method="GET">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                <div class="row g-3">
                    @if(request('status', 'jatuh_tempo') !== 'jatuh_tempo')
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Spesifik</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti tabler-calendar-event"></i></span>
                                <input type="text" name="search_date" id="search_date" class="form-control" placeholder="YYYY-MM-DD" value="{{ $searchDate ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bulan Tagihan</label>
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
                            <label class="form-label">Tahun Tagihan</label>
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
                    @endif
                    <div class="col-12 mt-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari No PKS, Nama Korlap..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="pt-3 text-end">
                    <a href="{{ route('masterdata.deposit-transactions.index') }}" class="btn btn-sm btn-outline-secondary me-2">Reset</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="ti tabler-filter-filled me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3">
            <div>
                @if(request('status', 'jatuh_tempo') === 'jatuh_tempo')
                    <h5 class="mb-0 text-danger"><i class="ti tabler-alert-triangle-filled me-1"></i> Daftar PKS Jatuh Tempo</h5>
                    <small class="text-muted">Total {{ $arrears->count() }} PKS memiliki tunggakan/jatuh tempo.</small>
                @else
                    <h5 class="mb-0">Daftar Transaksi</h5>
                    <small class="text-muted">Total {{ $depositTransactions->total() }} transaksi.</small>
                @endif
            </div>
            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu') || Auth::user()->hasRole('treasurer'))
                <a href="{{ route('masterdata.deposit-transactions.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-1"></i> Catat Setoran
                </a>
            @endif
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive text-nowrap">
                @if(request('status', 'jatuh_tempo') === 'jatuh_tempo')
                <table class="table table-hover">
                    <thead class="table-danger">
                        <tr>
                            <th>No. Perjanjian</th>
                            <th>Koordinator Lapangan</th>
                            <th>Tunggakan Terlama</th>
                            <th>Tagihan Berjalan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($arrears as $arr)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $arr->agreement->agreement_number }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-danger"><i class="ti tabler-user"></i></span>
                                        </div>
                                        <span class="fw-medium">{{ $arr->agreement->fieldCoordinator->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-danger"><i class="ti tabler-calendar me-1"></i> {{ $arr->month_label }}</span>
                                </td>
                                <td><span class="fw-bold text-danger">Rp {{ number_format($arr->amount, 0, ',', '.') }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('masterdata.deposit-transactions.create', ['target_agreement_id' => $arr->agreement->id]) }}" class="btn btn-sm btn-danger rounded-pill shadow-sm">
                                        <i class="ti tabler-currency-dollar me-1"></i> Bayar Sekarang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="ti tabler-circle-check-filled text-success ti-xl mb-2"></i>
                                        <h6 class="text-dark fw-bold">Tidak ada tunggakan!</h6>
                                        <p class="text-muted mb-0">Semua PKS sudah membayar tagihannya tepat waktu.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Perjanjian</th>
                            <th>Koordinator</th>
                            <th>Tanggal & Tagihan</th>
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
                                <td>
                                    <div class="fw-medium text-dark">{{ $transaction->deposit_date->format('d M Y') }}</div>
                                    <small class="text-muted"><i class="ti tabler-calendar-check align-bottom"></i> {{ $transaction->transaction_month ? $transaction->transaction_month->translatedFormat('F Y') : '-' }}</small>
                                </td>
                                <td><span class="fw-medium text-success">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span></td>
                                <td>
                                    @if ($transaction->is_validated) <span class="badge rounded-pill bg-label-success">Tervalidasi</span>
                                    @else <span class="badge rounded-pill bg-label-warning">Pending</span> @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill" href="{{ route('masterdata.deposit-transactions.show', $transaction->id) }}" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="ti tabler-eye icon-22px"></i>
                                        </a>

                                        @if (!$transaction->is_validated)
                                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff_keu') || Auth::user()->hasRole('treasurer'))
                                                <form action="{{ route('masterdata.deposit-transactions.validate', $transaction->id) }}" method="POST" class="form-validate d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-text-success rounded-pill" data-bs-toggle="tooltip" title="Validasi">
                                                        <i class="ti tabler-checks icon-22px"></i>
                                                    </button>
                                                </form>
                                                <a class="btn btn-sm btn-icon btn-text-primary rounded-pill" href="{{ route('masterdata.deposit-transactions.edit', $transaction->id) }}" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="ti tabler-pencil icon-22px"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('treasurer'))
                                            <form action="{{ route('masterdata.deposit-transactions.destroy', $transaction->id) }}" method="POST" class="form-delete d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ti tabler-trash icon-22px"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4"><i class="ti tabler-inbox me-1"></i> Belum ada data transaksi setoran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @endif
            </div>
        </div>
        @if(request('status', 'jatuh_tempo') !== 'jatuh_tempo' && $depositTransactions->hasPages())
            <div class="card-footer border-top pt-3 pb-2">
                {{ $depositTransactions->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
@endsection

@section('page-script')
    <script type="module">
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
@endsection
