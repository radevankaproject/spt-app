{{-- resources/views/layouts/partials/_skeleton-parking-locations-form.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 280px;"></div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-6">

            {{-- Bagian Informasi Dasar --}}
            <div class="col-12">
                <div class="skeleton skeleton-text" style="width: 150px;"></div>
                <hr class="mt-2 mb-4">
            </div>
            <div class="col-md-6">
                <div class="skeleton skeleton-text skeleton-text-sm mb-3" style="width: 80px;"></div>
                <div class="d-flex">
                    <div class="skeleton skeleton-text me-4" style="width: 80px;"></div>
                    <div class="skeleton skeleton-text" style="width: 80px;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 120px;"></div>
                <div class="skeleton skeleton-input"></div>
            </div>

            {{-- Bagian Detail Lokasi --}}
            <div class="col-12 mt-4">
                <div class="skeleton skeleton-text" style="width: 120px;"></div>
                <hr class="mt-2 mb-4">
            </div>
            <div class="col-12">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-input"></div>
            </div>

            {{-- Bagian Dokumen Pendukung --}}
            <div class="col-12 mt-4">
                <div class="skeleton skeleton-text" style="width: 200px;"></div>
                <hr class="mt-2 mb-4">
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 80px;"></div>
                <div class="card">
                    <div class="card-body text-center p-3">
                        <div class="skeleton rounded-3 mx-auto mb-3" style="height: 120px; width: 85%;"></div>
                        <div class="skeleton skeleton-button mx-auto" style="width: 120px; height: 32px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 150px;"></div>
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 160px;"></div>
                <div class="skeleton skeleton-input"></div>
            </div>
        </div>

        {{-- Kerangka Tombol Aksi --}}
        <div class="pt-6 text-end">
            <div class="skeleton skeleton-button d-inline-block me-2" style="width: 80px; height: 38px;"></div>
            <div class="skeleton skeleton-button d-inline-block" style="width: 150px; height: 38px;"></div>
        </div>
    </div>
</div>
