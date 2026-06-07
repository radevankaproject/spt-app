@extends('layouts.app')

@section('title', 'Manajemen Ruas Jalan')

@section('skeleton')
    @include('layouts.partials._skeleton-road-sections-index')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manajemen Ruas Jalan</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Ruas Jalan</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Daftar Ruas Jalan --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4 border-bottom pb-3">
            <div class="card-title mb-0">
                <h5 class="mb-1">Daftar Semua Ruas Jalan</h5>
                <p class="text-muted mb-0">Total {{ $roadSections->total() }} ruas jalan terdaftar.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3">
                <form action="{{ route('masterdata.road-sections.index') }}" method="GET"
                    class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama ruas..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit"><i
                                class="ri icon-base ri-search-line"></i></button>
                    </div>
                </form>
                @if(Auth::user()->role !== 'leader')
                {{-- Tombol Panggil Modal Create --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createRoadSectionModal">
                    <i class="ri icon-base ri-add-line me-1"></i> Tambah
                </button>
                @endif
            </div>
        </div>

        <div class="card-body pt-3">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fw-bold" role="alert">
                    <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <strong>Oops! Ada input yang salah:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="5%">No</th>
                            <th scope="col" width="40%">Nama Ruas Jalan</th>
                            <th scope="col" width="15%">Zona</th>
                            <th scope="col" width="20%" class="text-center">Total Titik Parkir</th>
                            <th scope="col" width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($roadSections as $index => $roadSection)
                            @php
                                $inUse = $roadSection->parking_locations_count > 0;
                            @endphp
                            <tr>
                                <td>{{ $roadSections->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $roadSection->name }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-label-info fw-bold">{{ $roadSection->zone }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- ✅ Tampilan Count Parking Location --}}
                                    @if ($inUse)
                                        <span class="badge bg-primary">{{ $roadSection->parking_locations_count }}
                                            Titik</span>
                                    @else
                                        <span class="badge bg-label-secondary">0 Titik</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if(Auth::user()->role !== 'leader')
                                        {{-- Tombol Edit Panggil Modal --}}
                                        <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                            data-bs-toggle="modal" data-bs-target="#editModal{{ $roadSection->id }}"
                                            data-bs-placement="top" title="Edit">
                                            <i class="ri icon-base ri-pencil-line ri-20px"></i>
                                        </button>

                                        {{-- ✅ Logika Disable Hapus --}}
                                        @if ($inUse)
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill" disabled
                                                data-bs-toggle="tooltip" title="Tidak dapat dihapus, sedang digunakan!">
                                                <i class="ri icon-base ri-delete-bin-7-line ri-20px opacity-50"></i>
                                            </button>
                                        @else
                                            <form
                                                action="{{ route('masterdata.road-sections.destroy', $roadSection->id) }}"
                                                method="POST" class="d-inline" id="deleteForm{{ $roadSection->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-text-danger rounded-pill"
                                                    onclick="confirmDelete({{ $roadSection->id }}, '{{ addslashes($roadSection->name) }}')"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ri icon-base ri-delete-bin-7-line ri-20px"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- =============================================== --}}
                            {{-- ✅ MODAL EDIT (Di-generate per baris data) --}}
                            {{-- =============================================== --}}
                            <div class="modal fade" id="editModal{{ $roadSection->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-label-primary border-bottom">
                                            <h5 class="modal-title fw-bold" id="exampleModalLabel1">Edit Ruas Jalan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('masterdata.road-sections.update', $roadSection->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                @if ($inUse)
                                                    <div class="alert alert-warning d-flex align-items-center p-2 mb-4"
                                                        role="alert">
                                                        <i class="ri-information-line me-2"></i>
                                                        <small>Zona tidak dapat diubah karena sudah memiliki titik parkir
                                                            terdaftar.</small>
                                                    </div>
                                                @endif

                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" class="form-control" name="name"
                                                                placeholder="Nama Ruas" value="{{ $roadSection->name }}"
                                                                required />
                                                            <label>Nama Ruas Jalan</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label d-block mb-2">Pilih Zona</label>
                                                        <div class="d-flex align-items-center gap-4">
                                                            <div class="form-check">
                                                                <input name="zone" class="form-check-input"
                                                                    type="radio" value="Zona 2"
                                                                    id="editZone2{{ $roadSection->id }}"
                                                                    {{ $roadSection->zone == 'Zona 2' ? 'checked' : '' }}
                                                                    {{ $inUse ? 'disabled' : '' }} />
                                                                <label class="form-check-label"
                                                                    for="editZone2{{ $roadSection->id }}"> Zona 2 </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input name="zone" class="form-check-input"
                                                                    type="radio" value="Zona 3"
                                                                    id="editZone3{{ $roadSection->id }}"
                                                                    {{ $roadSection->zone == 'Zona 3' ? 'checked' : '' }}
                                                                    {{ $inUse ? 'disabled' : '' }} />
                                                                <label class="form-check-label"
                                                                    for="editZone3{{ $roadSection->id }}"> Zona 3 </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="text" class="form-control" name="coordinates"
                                                                placeholder="Contoh: 0.5333, 101.4500" value="{{ $roadSection->latitude && $roadSection->longitude ? $roadSection->latitude . ', ' . $roadSection->longitude : '' }}" />
                                                            <label>Koordinat Titik Tengah (Latitude, Longitude)</label>
                                                        </div>
                                                        <small class="text-muted"><i class="ri-information-line"></i> Opsional. Dapat di-copy langsung dari Google Maps.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer pt-3 border-top">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}"
                                        width="120" class="mb-3 opacity-50" alt="No Data">
                                    <p class="text-muted">Tidak ada data ruas jalan ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-3">
                {{ $roadSections->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>

    {{-- =============================================== --}}
    {{-- ✅ MODAL CREATE (Satu saja untuk halaman ini) --}}
    {{-- =============================================== --}}
    <div class="modal fade" id="createRoadSectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white fw-bold"><i class="ri-add-circle-line me-1"></i> Tambah Ruas Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('masterdata.road-sections.store') }}" method="POST">
                    @csrf
                    <div class="modal-body mt-2">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Contoh: Jl. Jend. Sudirman" value="{{ old('name') }}" required />
                                    <label>Nama Ruas Jalan</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block mb-2">Pilih Zona</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input name="zone" class="form-check-input" type="radio" value="Zona 2"
                                            id="createZone2" {{ old('zone', 'Zona 2') == 'Zona 2' ? 'checked' : '' }} />
                                        <label class="form-check-label" for="createZone2"> Zona 2 </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="zone" class="form-check-input" type="radio" value="Zona 3"
                                            id="createZone3" {{ old('zone') == 'Zona 3' ? 'checked' : '' }} />
                                        <label class="form-check-label" for="createZone3"> Zona 3 </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="coordinates"
                                        placeholder="Contoh: 0.5333, 101.4500" value="{{ old('coordinates') }}" />
                                    <label>Koordinat Titik Tengah (Latitude, Longitude)</label>
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> Opsional. Dapat di-copy langsung dari Google Maps.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Ruas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Aktifkan Tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // (Notifikasi Sukses sudah otomatis ditangani oleh Global Dynamic Island di _alerts.blade.php)

            // 2. Jika ada error validasi saat Create, buka kembali Modal Create secara otomatis
            @if($errors->any() && !old('_method'))
                var createModal = new bootstrap.Modal(document.getElementById('createRoadSectionModal'));
                createModal.show();
            @endif
        });

        // ✅ 3. Fungsi Konfirmasi Delete via SweetAlert (Hanya untuk aksi krusial)
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: `Anda akan menghapus ruas jalan <strong>${name}</strong>.<br>Data tidak dapat dikembalikan!`,
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
@endpush
