@extends('layouts.contentNavbarLayout')

@section('title', 'Data Jukir')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
    <div class="page-hero text-white mb-4 shadow-lg anim-1 hero-mesh-primary" style="padding: 2.5rem; border-radius: 1.5rem; position: relative; overflow: hidden;">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div>
                <h4 class="fw-bold text-white mb-1"><i class="ti tabler-users me-2"></i>Data Juru Parkir (Jukir)</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Kelola data jukir yang terdaftar di masing-masing titik lokasi parkir.</p>
            </div>
        </div>
        <i class="ti tabler-users position-absolute text-white" style="font-size: 160px; right: -15px; bottom: -30px; opacity: 0.08; transform: rotate(-10deg); z-index: 1;"></i>
    </div>

    <div class="glass-card anim-2 border-0 overflow-hidden mb-4">
        <div class="card-header border-bottom pb-3 p-4">
            <h5 class="mb-1">Daftar Jukir Terdaftar</h5>
            <p class="text-muted mb-0">Total {{ count($jukirs) }} jukir.</p>
        </div>

        <div class="card-body pt-3 p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fw-bold" role="alert">
                    <i class="ti tabler-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th scope="col" class="text-uppercase fw-bold text-primary" width="5%">No</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Foto</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Nama Jukir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Titik Parkir</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary">Kontak / KTP</th>
                            <th scope="col" class="text-uppercase fw-bold text-primary" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($jukirs as $index => $jukir)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($jukir->image)
                                        <img src="{{ Storage::url($jukir->image) }}" alt="Foto Jukir" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="avatar avatar-sm rounded-circle bg-label-secondary d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($jukir->nama_jukir, 0, 2)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $jukir->nama_jukir }}</span>
                                    @if(!$jukir->is_active)
                                        <span class="badge bg-label-danger ms-1" style="font-size: 0.65rem;">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $jukir->parkingLocation->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted"><i class="ti tabler-phone"></i> {{ $jukir->phone_number ?? '-' }}</span>
                                        <span class="small text-muted"><i class="ti tabler-id"></i> {{ $jukir->no_ktp ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            href="{{ route('admin.jukirs.edit', $jukir->id) }}"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="ri icon-base ti tabler-pencil icon-22px"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.jukirs.destroy', $jukir->id) }}"
                                            method="POST" class="d-inline" id="deleteForm{{ $jukir->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-text-danger rounded-pill"
                                                onclick="confirmDelete({{ $jukir->id }}, '{{ addslashes($jukir->nama_jukir) }}')"
                                                data-bs-toggle="tooltip" title="Hapus">
                                                <i class="ri icon-base ti tabler-trash icon-22px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-center py-5">
                                        <div class="icon-glass bg-label-secondary mx-auto mb-3">
                                            <i class="ti tabler-user-off fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">Tidak Ada Data</h5>
                                        <p class="text-muted mb-0">Belum ada data Jukir yang terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @vite(["resources/assets/vendor/libs/sweetalert2/sweetalert2.js"])
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        window.confirmDelete = function(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: `Anda akan menghapus data jukir <strong>${name}</strong>.<br>Data ini akan terhapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>
@endsection
