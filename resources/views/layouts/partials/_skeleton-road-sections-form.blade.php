{{-- resources/views/layouts/partials/_skeleton-road-sections-form.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 250px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 220px;"></div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-6">
            {{-- 2. Kerangka untuk Input Nama --}}
            <div class="col-12">
                <div class="skeleton skeleton-input"></div>
            </div>

            {{-- 3. Kerangka untuk Pilihan Zona --}}
            <div class="col-12">
                <div class="skeleton skeleton-text skeleton-text-sm mb-3" style="width: 80px;"></div>
                <div class="d-flex">
                    <div class="skeleton skeleton-text me-4" style="width: 80px;"></div>
                    <div class="skeleton skeleton-text" style="width: 80px;"></div>
                </div>
            </div>
        </div>

        {{-- 4. Kerangka untuk Tombol Aksi --}}
        <div class="pt-6 text-end">
            <div class="skeleton skeleton-button d-inline-block me-2" style="width: 80px; height: 38px;"></div>
            <div class="skeleton skeleton-button d-inline-block" style="width: 170px; height: 38px;"></div>
        </div>
    </div>
</div>
