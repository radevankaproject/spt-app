    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Juru Parkir Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.jukirs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Ruas Jalan</label>
                            <select id="create_road_section_id" class="form-select select2-road">
                                <option value="">Pilih Ruas Jalan</option>
                                @foreach($roadSections as $rs)
                                    <option value="{{ $rs->id }}">{{ $rs->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titik Parkir <span class="text-danger">*</span></label>
                            <select name="parking_location_id" id="create_parking_location_id" class="form-select select2-parking" required disabled>
                                <option value="">Pilih Titik Parkir</option>
                                @foreach($parkingLocations as $pl)
                                    @php
                                        $activeAgreement = $pl->agreements->first();
                                        $korlap = $activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user ? $activeAgreement->fieldCoordinator->user->name : '-';
                                        $zona = $pl->roadSection->zone ?? '-';
                                        $alamat = $pl->roadSection->name ?? '-';
                                    @endphp
                                    <option value="{{ $pl->id }}" data-road-section-id="{{ $pl->road_section_id }}" data-korlap="{{ $korlap }}" data-zona="{{ $zona }}" data-alamat="{{ $alamat }}">{{ $pl->name }}</option>
                                @endforeach
                            </select>
                            <div class="parking-info-box mt-2 p-3 bg-light rounded border border-primary border-dashed" style="display: none; font-size: 0.85rem;">
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-user me-1"></i> Korlap</span><span class="fw-bold text-dark info-korlap">-</span></div>
                                <div class="d-flex mb-1"><span class="text-muted w-px-100"><i class="ti tabler-map-pin me-1"></i> Zona</span><span class="fw-bold text-dark info-zona">-</span></div>
                                <div class="d-flex"><span class="text-muted w-px-100"><i class="ti tabler-road me-1"></i> Ruas Jalan</span><span class="fw-bold text-dark info-alamat">-</span></div>
                            </div>
                        </div>

                        <!-- KTA Fields (Hidden by default) -->
                        <div id="kta_section_create" class="row d-none bg-label-primary p-3 rounded mb-3 mx-0">
                            <h6 class="fw-bold text-primary mb-2 px-0"><i class="ti tabler-id-badge"></i> Informasi KTA</h6>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Jenis KTA</label>
                                <select name="kta_type" id="create_kta_type" class="form-select">
                                    <option value="">Pilih Jenis (Opsional)</option>
                                    <option value="baru">Baru</option>
                                    <option value="perpanjangan">Perpanjangan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 px-1">
                                <label class="form-label fw-bold">Tanggal Mulai KTA</label>
                                <input type="date" name="kta_start_date" id="create_kta_start_date" class="form-control">
                                <small class="text-muted">Masa berlaku +3 bulan</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Jukir</label>
                            <input type="text" class="form-control text-muted" value="{{ $nextIdJukir }}" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Jukir <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jukir" class="form-control" placeholder="Nama lengkap jukir" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. KTP</label>
                            <input type="text" name="no_ktp" class="form-control" placeholder="Nomor KTP (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Handphone</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="Nomor HP (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_preview_create" src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle border border-2 border-primary" width="60" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_progress_container_create" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_progress_bar_create" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image" id="image_input_create" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis crop 1:1 (lingkaran) & kompres dibawah 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto KTP</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="position-relative">
                                    <img id="image_ktp_preview_create" src="" class="rounded border border-2 border-primary" width="90" height="60" style="object-fit: cover; display: none; padding: 2px;">
                                    <div id="image_ktp_progress_container_create" class="position-absolute top-50 start-50 translate-middle w-100 px-1" style="display: none; z-index: 5;">
                                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.8);">
                                            <div id="image_ktp_progress_bar_create" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image_ktp" id="image_ktp_input_create" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1"><i class="ti tabler-info-circle"></i> Otomatis kompres dibawah 50KB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_create" value="1" checked>
                            <label class="form-check-label fw-bold" for="is_active_create">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-label-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
