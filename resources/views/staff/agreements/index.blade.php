@extends('layouts.contentNavbarLayout')

@section('title', 'Manajemen Perjanjian Kerjasama')



@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.92);
            --glass-border: rgba(255, 255, 255, 0.6);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            box-shadow: var(--glass-shadow);
        }
        
        /* ===== HERO HEADER ===== */
        .page-hero {
            background: linear-gradient(135deg, #696cff 0%, #8b8eff 100%);
            border-radius: 1.25rem;
            position: relative;
            overflow: hidden;
            padding: 2rem 2.5rem;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* ===== TABLE PREMIUM ===== */
        .premium-table { border-collapse: separate; border-spacing: 0; }
        .premium-table thead th {
            background: linear-gradient(135deg, #f8f7fa 0%, #f1f0f4 100%) !important;
            border-bottom: 2px solid rgba(105, 108, 255, 0.1) !important;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #697a8d !important;
            padding: 0.875rem 1rem !important;
            white-space: nowrap;
        }
        .premium-table thead th a { color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .premium-table thead th a:hover { color: #696cff !important; }
        
        .premium-table tbody tr {
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
        }
        .premium-table tbody tr:hover {
            background: rgba(105, 108, 255, 0.03) !important;
            border-left-color: #696cff;
        }
        .premium-table tbody td {
            padding: 0.875rem 1rem !important;
            vertical-align: middle !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        /* ===== BADGE ===== */
        .badge-modern {
            padding: 0.45em 0.75em;
            font-weight: 600;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
        }

        /* ===== BUTTON ACTIONS ===== */
        .btn-action { transition: all 0.2s ease; border-radius: 0.5rem; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-1 { animation: fadeInUp 0.5s ease 0.1s both; }
        .anim-2 { animation: fadeInUp 0.5s ease 0.2s both; }
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #696cff;
            border-bottom: 3px solid #696cff;
        }
        /* Highlight baris masa tenggang & expired */
        .row-grace-period {
            background-color: rgba(255, 171, 0, 0.04) !important;
            border-left-color: #ffab00 !important;
        }
        .row-expired {
            background-color: rgba(255, 62, 29, 0.04) !important;
            border-left-color: #ff3e1d !important;
        }
    </style>
@endsection

@section('content')
    {{-- ============================================= --}}
    {{-- HERO HEADER --}}
    {{-- ============================================= --}}
    <div class="page-hero text-white mb-4 shadow-lg anim-1">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="ti tabler-calendar-event me-1 align-middle"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="backdrop-filter: blur(5px);">
                        Total: {{ $agreements->total() }} Data
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-file-type-doc me-2"></i>Manajemen Perjanjian Kerjasama</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola seluruh dokumen PKS aktif, tidak aktif, maupun yang sedang masa tenggang.</p>
            </div>
            <!-- <div class="mt-3 mt-md-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-0 p-0" style="background: transparent;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white-50 text-decoration-none">Master Data</a></li>
                        <li class="breadcrumb-item text-white fw-bold active" aria-current="page">PKS</li>
                    </ol>
                </nav>
            </div> -->
        </div>
        <i class="ti tabler-file-type-doc position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    {{-- Tabs Navigasi --}}
    <ul class="nav nav-tabs mb-4 border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'all' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'all', 'search' => request('search'), 'year' => request('year')]) }}">
               Semua PKS <span class="badge bg-{{ $tab == 'all' ? 'primary' : 'label-secondary' }} ms-1 rounded-pill">{{ $countAll }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'active' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'active', 'search' => request('search'), 'year' => request('year')]) }}">
               Aktif <span class="badge bg-{{ $tab == 'active' ? 'success' : 'label-secondary' }} ms-1 rounded-pill">{{ $countActive }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 {{ $tab == 'inactive' ? 'active' : '' }}"
               href="{{ route('masterdata.agreements.index', ['tab' => 'inactive', 'search' => request('search'), 'year' => request('year')]) }}">
               Tidak Aktif <span class="badge bg-{{ $tab == 'inactive' ? 'danger' : 'label-secondary' }} ms-1 rounded-pill">{{ $countInactive }}</span>
            </a>
        </li>
    </ul>

    {{-- Daftar Perjanjian --}}
    <div class="glass-card overflow-hidden anim-2">
        <div class="card-header p-4 d-flex flex-wrap justify-content-between align-items-center gap-4 border-bottom">
            <div class="card-title mb-0">
                <h5 class="mb-1">
                    @if($tab == 'active') Daftar PKS Aktif
                    @elseif($tab == 'inactive') Daftar PKS Tidak Aktif (Kedaluwarsa/Diputus)
                    @else Daftar Semua PKS
                    @endif
                </h5>
                <p class="text-muted mb-0">Total {{ $agreements->total() }} data ditampilkan.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                {{-- Form Pencarian & Filter Tahun --}}
                <form action="{{ route('masterdata.agreements.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    {{-- Dropdown Filter Tahun --}}
                    <select name="year" class="form-select form-select-sm shadow-sm" style="width: auto; height: 38px; border-radius: 50rem;" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>

                    {{-- Dropdown Filter Korlap --}}
                    <select name="korlap_id" class="form-select select2 shadow-sm" style="width: 200px;">
                        <option value="">Semua Korlap</option>
                        @foreach($fieldCoordinators as $fc)
                            <option value="{{ $fc->id }}" {{ request('korlap_id') == $fc->id ? 'selected' : '' }}>{{ $fc->user->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>

                    <div class="input-group input-group-merge shadow-sm" style="border-radius: 50rem; overflow: hidden;">
                        <input type="search" name="search" class="form-control border-0 px-3 bg-white" placeholder="Cari No PKS/Korlap..." value="{{ request('search') }}">
                        <button class="btn btn-primary border-0 rounded-pill" type="submit"><i class="icon-base ti tabler-search icon-20px"></i></button>
                    </div>
                </form>
                {{-- Tombol Tambah --}}
                @if(Auth::user()->role !== 'leader')
                <a href="{{ route('masterdata.agreements.create') }}" class="btn btn-primary rounded-pill shadow-sm btn-action">
                    <i class="icon-base ti tabler-plus me-1"></i> Tambah PKS
                </a>
                @endif
            </div>
        </div>
        <div class="card-body pt-3 p-4">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                    <i class="ti tabler-alert-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table premium-table mb-0">
                    <thead>
                        <tr>
                            <th width="20%" class="ps-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'agreement_number', 'sort_dir' => ($sortBy == 'agreement_number' && $sortDir == 'asc') ? 'desc' : 'asc']) }}">
                                    Nomor PKS
                                    @if($sortBy == 'agreement_number')
                                        <i class="ti tabler-chevron-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="ti tabler-selector text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th width="25%">Koordinator Lapangan</th>
                            <th width="25%">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'end_date', 'sort_dir' => ($sortBy == 'end_date' && $sortDir == 'asc') ? 'desc' : 'asc']) }}">
                                    Masa Berlaku
                                    @if($sortBy == 'end_date')
                                        <i class="ti tabler-chevron-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="ti tabler-selector text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th width="15%">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => ($sortBy == 'status' && $sortDir == 'asc') ? 'desc' : 'asc']) }}">
                                    Status
                                    @if($sortBy == 'status')
                                        <i class="ti tabler-chevron-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="ti tabler-selector text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th width="15%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($agreements as $agreement)
                                @php
                                    // ✅ LOGIKA MASA TENGGANG (10 Hari) & EXPIRED
                                    $isGracePeriod = false;
                                    $isExpired = false;
                                    $daysRemaining = null;
                                    
                                    if ($agreement->status === 'active') {
                                        $daysRemaining = (int) now()->diffInDays($agreement->end_date, false);
                                        if ($daysRemaining >= 0 && $daysRemaining <= 10) {
                                            $isGracePeriod = true;
                                        }
                                    }
                                    
                                    if ($agreement->end_date->isPast()) {
                                        $isExpired = true;
                                    }

                                    // Setup Avatar
                                    $cName = $agreement->fieldCoordinator->user->name ?? 'N/A';
                                    $cAvatar = ($agreement->fieldCoordinator->user && $agreement->fieldCoordinator->user->img)
                                        ? asset('storage/'.$agreement->fieldCoordinator->user->img)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=32&rounded=true&bold=true";
                                @endphp

                                <tr class="{{ $isExpired ? 'row-expired' : ($isGracePeriod ? 'row-grace-period' : '') }}">
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">{{ $agreement->agreement_number }}</span>
                                    <small class="d-block text-muted">Pimpinan: {{ Str::limit($agreement->leader->user->name ?? 'N/A', 15) }}</small>
                                    <span class="badge bg-label-info rounded-pill mt-1" style="font-size: 0.7rem;">{{ ucfirst($agreement->jenis) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle" style="object-fit: cover;">
                                        </div>
                                        <span class="fw-medium text-dark">{{ $cName }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="d-block text-dark">{{ $agreement->start_date->translatedFormat('d M Y') }}</span>
                                            <small class="text-muted">s/d <span class="{{ $isGracePeriod ? 'text-danger fw-bold' : '' }}">{{ $agreement->end_date->translatedFormat('d M Y') }}</span></small>
                                        </div>
                                        {{-- ✅ INDIKATOR MASA TENGGANG --}}
                                        @if($isGracePeriod)
                                            <span class="badge bg-label-warning bg-opacity-20 text-warning ms-3 rounded-pill" data-bs-toggle="tooltip" title="Tersisa {{ $daysRemaining }} hari lagi!">
                                                <i class="ti tabler-alert-octagon me-1"></i> PKS dalam masa Tenggang
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-label-secondary';
                                        if ($agreement->status == 'active') $statusClass = 'bg-label-success';
                                        if ($agreement->status == 'expired') $statusClass = 'bg-label-danger';
                                        if ($agreement->status == 'terminated') $statusClass = 'bg-label-dark';
                                        if ($agreement->status == 'pending_renewal') $statusClass = 'bg-label-warning';
                                    @endphp
                                    <span class="badge badge-modern {{ $statusClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $agreement->status)) }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a class="btn btn-sm btn-icon btn-text-info rounded-pill"
                                            href="{{ route('masterdata.agreements.show', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="icon-base ti tabler-eye icon-20px"></i>
                                        </a>
                                        @if(Auth::user()->role !== 'leader' && $agreement->status !== 'expired')
                                        <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            href="{{ route('masterdata.agreements.edit', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Edit PKS">
                                            <i class="icon-base ti tabler-pencil icon-20px"></i>
                                        </a>
                                        @endif
                                        
                                        @if($agreement->status !== 'expired')
                                        <a class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                            href="{{ route('masterdata.agreements.pdf', $agreement->id) }}" target="_blank"
                                            data-bs-toggle="tooltip" title="Cetak Dokumen PKS">
                                            <i class="icon-base ti tabler-printer icon-20px"></i>
                                        </a>
                                        @endif
                                        @if(Auth::user()->role !== 'leader')
                                        @if($agreement->status === 'expired' && $agreement->signed_document_path)
                                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" disabled
                                            data-bs-toggle="tooltip" title="PKS Expired dan sudah memiliki file scan">
                                            <i class="icon-base ti tabler-file-check icon-20px opacity-50"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-icon {{ $agreement->signed_document_path ? 'btn-text-success' : 'btn-text-warning' }} rounded-pill btn-upload-scan"
                                            data-id="{{ $agreement->id }}"
                                            data-number="{{ $agreement->agreement_number }}"
                                            data-has-scan="{{ $agreement->signed_document_path ? '1' : '0' }}"
                                            data-bs-toggle="tooltip" title="{{ $agreement->signed_document_path ? 'Update File Scan (Sudah Ada)' : 'Upload File Scan PKS' }}">
                                            <i class="icon-base {{ $agreement->signed_document_path ? 'ti tabler-file-check' : 'ti tabler-file-upload' }} icon-20px"></i>
                                        </button>
                                        @endif
                                        @endif
                                        @if(Auth::user()->role !== 'leader')
                                        @if(($agreement->status == 'active' && $agreement->end_date->isPast()) || $agreement->status == 'pending_renewal' || ($agreement->status == 'expired' && $agreement->activeParkingLocations->count() > 0))
                                        <a class="btn btn-sm btn-icon btn-text-warning rounded-pill"
                                            href="{{ route('masterdata.agreements.renew', $agreement->id) }}"
                                            data-bs-toggle="tooltip" title="Perpanjang PKS">
                                            <i class="icon-base ti tabler-refresh icon-20px"></i>
                                        </a>
                                        @endif
                                        <form action="{{ route('masterdata.agreements.destroy', $agreement->id) }}"
                                            method="POST" class="form-delete d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" data-bs-toggle="tooltip"
                                                title="Hapus PKS">
                                                <i class="icon-base ti tabler-trash icon-20px"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}" width="120" class="mb-3 opacity-50" alt="No Data">
                                    <p class="text-muted">Tidak ada data perjanjian ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{-- Memastikan query search dan tab dibawa saat pindah halaman --}}
                {{ $agreements->appends(['search' => request('search'), 'tab' => $tab, 'year' => request('year'), 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL UPLOAD SCAN --}}
    <div class="modal fade" id="uploadScanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-primary"><i class="ti tabler-file-upload me-1"></i> Upload Scan PKS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="text-dark mb-4">Upload dokumen PKS yang telah ditandatangani untuk nomor: <strong id="scanAgreementNumber"></strong></p>
                    
                    <form id="formUploadScan" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="scanAgreementId" name="agreement_id">
                        
                        {{-- DRAG & DROP AREA --}}
                        <div id="drop-area" class="border rounded-3 p-4 text-center cursor-pointer mb-3" style="border: 2px dashed #696cff !important; background-color: rgba(105, 108, 255, 0.05); transition: all 0.3s ease;">
                            <i class="ti tabler-upload-cloud-2 text-primary mb-2" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold mb-1">Drag & Drop file PDF di sini</h6>
                            <p class="text-muted small mb-3">Atau klik untuk memilih file dari komputer</p>
                            <input type="file" id="signed_document" name="signed_document" class="d-none" accept=".pdf" required>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" onclick="document.getElementById('signed_document').click()">Pilih File</button>
                        </div>
                        <div id="file-info" class="d-none mb-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                            <div class="d-flex align-items-center">
                                <i class="ti tabler-file-pdf text-danger fs-3 me-2"></i>
                                <div>
                                    <span id="file-name" class="fw-bold text-dark d-block" style="font-size: 0.9rem;"></span>
                                    <small id="file-size" class="text-muted"></small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill" id="btn-remove-file">
                                <i class="ti tabler-x"></i>
                            </button>
                        </div>

                        {{-- PROGRESS BAR --}}
                        <div id="progress-container" class="d-none mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span id="progress-text" class="text-primary fw-bold" style="font-size: 0.85rem;">Mengupload...</span>
                                <span id="progress-percentage" class="text-primary fw-bold" style="font-size: 0.85rem;">0%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <small class="text-muted d-block mt-2 text-center" id="progress-helper-text">Mohon tunggu, jangan tutup halaman ini.</small>
                        </div>
                        
                        <div id="upload-error" class="text-danger small mt-2 d-none"></div>
                    </form>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal" id="btn-cancel-upload">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill" id="btn-submit-upload" disabled>
                        <i class="ti tabler-upload me-1"></i> Upload & Kompresi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    @vite(["resources/assets/vendor/libs/select2/select2.js"])
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            // Aktifkan Tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inisialisasi Select2 untuk Filter Korlap
            $('.select2').select2({
                placeholder: "Semua Korlap"
            }).on('change', function() {
                // Submit form otomatis saat dropdown korlap (select2) berubah
                $(this).closest('form').submit();
            });

            // ✅ HANYA Modal Konfirmasi Hapus
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Data perjanjian yang dihapus akan otomatis melepaskan lokasi parkir terkait (kembali menjadi Tersedia).",
                        icon: 'warning',
                        showCancelButton: true,
                        customClass: {
                            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-outline-secondary waves-effect'
                        },
                        buttonsStyling: false,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        // ==========================================
        // LOGIKA UPLOAD & KOMPRESI PDF (PREMIUM UI)
        // ==========================================
        document.addEventListener("DOMContentLoaded", function() {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('signed_document');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const btnRemoveFile = document.getElementById('btn-remove-file');
            const btnSubmitUpload = document.getElementById('btn-submit-upload');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('upload-progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressPercentage = document.getElementById('progress-percentage');
            const progressHelperText = document.getElementById('progress-helper-text');
            const uploadError = document.getElementById('upload-error');
            const btnCancelUpload = document.getElementById('btn-cancel-upload');

            let selectedFile = null;

            // Format ukuran file
            function formatBytes(bytes, decimals = 2) {
                if (!+bytes) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
            }

            // Fungsi reset UI
            function resetUploadUI() {
                selectedFile = null;
                fileInput.value = '';
                dropArea.classList.remove('d-none');
                fileInfo.classList.add('d-none');
                btnSubmitUpload.disabled = true;
                progressContainer.classList.add('d-none');
                uploadError.classList.add('d-none');
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-success', 'bg-warning');
                progressBar.classList.add('bg-primary');
            }

            // Buka Modal & Set Data
            document.querySelectorAll('.btn-upload-scan').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const id = this.dataset.id;
                    const number = this.dataset.number;
                    const hasScan = this.dataset.hasScan === '1';

                    if (hasScan) {
                        Swal.fire({
                            title: 'Ubah File Scan?',
                            text: 'File Scan sudah ada (silahkan lihat di details PKS) apakah anda ingin mengubah file scan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Ubah',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'btn btn-primary me-3',
                                cancelButton: 'btn btn-outline-secondary'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                openUploadModal(id, number);
                            }
                        });
                    } else {
                        openUploadModal(id, number);
                    }
                });
            });

            function openUploadModal(id, number) {
                resetUploadUI();
                document.getElementById('scanAgreementId').value = id;
                document.getElementById('scanAgreementNumber').innerText = number;
                
                // Sembunyikan semua tooltip yang sedang aktif agar tidak nyangkut
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(t => {
                    const tooltipInstance = bootstrap.Tooltip.getInstance(t);
                    if (tooltipInstance) {
                        tooltipInstance.hide();
                    }
                });
                
                $('#uploadScanModal').modal('show');
            }

            // Handle Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.backgroundColor = 'rgba(105, 108, 255, 0.1)';
                    dropArea.style.borderColor = '#696cff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.backgroundColor = 'rgba(105, 108, 255, 0.05)';
                }, false);
            });

            dropArea.addEventListener('drop', (e) => {
                let dt = e.dataTransfer;
                let files = dt.files;
                handleFiles(files);
            }, false);

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            btnRemoveFile.addEventListener('click', resetUploadUI);

            function handleFiles(files) {
                if(files.length > 0) {
                    const file = files[0];
                    if(file.type !== 'application/pdf') {
                        uploadError.innerText = "Error: File harus berformat PDF.";
                        uploadError.classList.remove('d-none');
                        return;
                    }
                    if(file.size > 10 * 1024 * 1024) { // 10MB
                        uploadError.innerText = "Error: Ukuran maksimal file 10MB.";
                        uploadError.classList.remove('d-none');
                        return;
                    }

                    selectedFile = file;
                    uploadError.classList.add('d-none');
                    dropArea.classList.add('d-none');
                    fileInfo.classList.remove('d-none');
                    fileName.innerText = file.name;
                    fileSize.innerText = formatBytes(file.size);
                    btnSubmitUpload.disabled = false;
                }
            }

            // Eksekusi Upload
            btnSubmitUpload.addEventListener('click', function() {
                if(!selectedFile) return;

                const agreementId = document.getElementById('scanAgreementId').value;
                const formData = new FormData();
                formData.append('signed_document', selectedFile);
                formData.append('_token', '{{ csrf_token() }}');

                // Update UI state
                btnSubmitUpload.disabled = true;
                btnCancelUpload.disabled = true;
                btnRemoveFile.disabled = true;
                progressContainer.classList.remove('d-none');
                uploadError.classList.add('d-none');

                const xhr = new XMLHttpRequest();
                let uploadUrl = "{{ url('masterdata/agreements') }}/" + agreementId + "/upload-scan";
                
                xhr.open('POST', uploadUrl, true);

                // Progress Upload ke Server
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        let percentComplete = Math.floor((e.loaded / e.total) * 100);
                        // Tahan di 90% karena 10% sisa adalah waktu proses kompresi di server
                        let displayPercent = Math.min(percentComplete, 90); 
                        
                        progressBar.style.width = displayPercent + '%';
                        progressPercentage.innerText = displayPercent + '%';

                        if(displayPercent === 90) {
                            progressBar.classList.remove('bg-primary');
                            progressBar.classList.add('bg-warning');
                            progressText.innerText = 'Mengkompresi Dokumen via Ghostscript...';
                            progressText.classList.remove('text-primary');
                            progressText.classList.add('text-warning');
                            progressPercentage.classList.remove('text-primary');
                            progressPercentage.classList.add('text-warning');
                            progressHelperText.innerText = 'Proses kompresi memakan waktu beberapa detik. Harap bersabar...';
                        }
                    }
                };

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if(response.success) {
                            progressBar.style.width = '100%';
                            progressBar.classList.remove('bg-warning', 'progress-bar-animated', 'progress-bar-striped');
                            progressBar.classList.add('bg-success');
                            progressText.innerText = 'Selesai!';
                            progressText.classList.remove('text-warning');
                            progressText.classList.add('text-success');
                            progressPercentage.innerText = '100%';
                            progressPercentage.classList.remove('text-warning');
                            progressPercentage.classList.add('text-success');
                            progressHelperText.innerText = response.message;
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    } else {
                        handleError('Terjadi kesalahan pada server. Kode: ' + xhr.status);
                    }
                };

                xhr.onerror = function() {
                    handleError('Gagal menghubungi server.');
                };

                xhr.send(formData);

                function handleError(msg) {
                    uploadError.innerText = msg;
                    uploadError.classList.remove('d-none');
                    btnSubmitUpload.disabled = false;
                    btnCancelUpload.disabled = false;
                    btnRemoveFile.disabled = false;
                    progressContainer.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
