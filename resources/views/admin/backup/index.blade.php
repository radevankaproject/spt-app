@extends('layouts.app')

@section('title', 'Backup Database')

@section('skeleton')
    @include('layouts.partials._skeleton-backup-index')
@endsection

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Backup Database</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Backup</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Aksi Utama -->
    @if(Auth::user()->role !== 'leader')
    <div class="card mb-6">
        <div class="card-body text-center">
            <i class="icon-base ri ri-download-cloud-2-line text-primary" style="font-size: 80px;"></i>
            <h5 class="mt-4">Buat Cadangan Database Baru</h5>
            <p class="text-muted">
                Pilih jenis backup yang ingin Anda lakukan. Backup Database hanya mencadangkan data SQL, sedangkan Backup Full Aplikasi mencadangkan database beserta semua file source code.
            </p>
            <form action="{{ route('admin.backup.store') }}" method="POST" id="backup-form" class="d-flex justify-content-center gap-3 flex-wrap">
                @csrf
                <button type="submit" name="type" value="db" class="btn btn-primary btn-lg backup-btn">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="icon-base ri ri-database-2-line me-2"></i>Backup Database
                </button>
                <button type="submit" name="type" value="full" class="btn btn-warning btn-lg backup-btn">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="icon-base ri ri-folder-zip-line me-2"></i>Backup Full Aplikasi
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Card Riwayat Backup -->
    <div class="card">
        <h5 class="card-header">Riwayat Backup</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Tanggal Dibuat</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($backups as $backup)
                        <tr>
                            <td><i class="icon-base ri ri-file-zip-line text-primary me-3"></i><span
                                    class="fw-medium">{{ $backup->file_name }}</span></td>
                            <td>{{ $backup->readable_size }}</td>
                            <td>{{ $backup->created_at->translatedFormat('d F Y, H:i') }}</td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $backup->creator->name ?? 'Sistem' }}</span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('admin.backup.download', $backup->id) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-2"
                                        data-bs-toggle="tooltip" title="Download"><i
                                            class="icon-base ri ri-download-2-line icon-22px"></i></a>
                                    @if(Auth::user()->role !== 'leader')
                                    <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill delete-btn"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-url="{{ route('admin.backup.destroy', $backup->id) }}"
                                        data-filename="{{ $backup->file_name }}" data-bs-toggle="tooltip" title="Hapus">
                                        <i class="icon-base ri ri-delete-bin-7-line icon-22px"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada riwayat backup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus file backup <strong id="filename-to-delete"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat diurungkan dan akan menghapus file secara permanen.</p>
                </div>
                <div class="modal-footer">
                    <form id="delete-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk menampilkan spinner saat form backup di-submit
            const backupButtons = document.querySelectorAll('.backup-btn');
            backupButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const spinner = this.querySelector('.spinner-border');
                    
                    // Supaya nilai tombol (type) tetap terkirim, kita tidak disable buttonnya, 
                    // tapi set readonly atau pointer-events none dan ganti teks.
                    // Namun cara termudah: submit formnya langsung atau biarkan default event berjalan
                    // lalu tambahkan sedikit delay untuk visualnya.
                    setTimeout(() => {
                        backupButtons.forEach(btn => btn.classList.add('disabled'));
                        spinner.classList.remove('d-none');
                    }, 50);
                });
            });

            // Script untuk modal konfirmasi hapus
            const deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-url');
                const filename = button.getAttribute('data-filename');

                const form = deleteModal.querySelector('#delete-form');
                const filenameElement = deleteModal.querySelector('#filename-to-delete');

                form.action = url;
                filenameElement.textContent = filename;
            });
        });
    </script>
@endpush
