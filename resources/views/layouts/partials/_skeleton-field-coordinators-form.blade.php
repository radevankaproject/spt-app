{{-- resources/views/layouts/partials/_skeleton-field-coordinators-form.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 250px;"></div>
</div>

<div class="row g-6">
    {{-- 2. Kerangka Kolom Kiri (Informasi Pribadi) --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 280px;"></div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-12">
                        <div class="skeleton skeleton-input" style="height: 100px;"></div>
                    </div>
                    <div class="col-12">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Kerangka Kolom Kanan (2 Kartu Foto) --}}
    <div class="col-lg-4">
        {{-- Kartu Foto Profil --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 120px;"></div>
            </div>
            <div class="card-body text-center">
                <div class="skeleton skeleton-avatar-lg mx-auto mb-4"></div>
                <div class="skeleton skeleton-button mx-auto" style="width: 120px; height: 38px;"></div>
            </div>
        </div>
        {{-- Kartu Foto KTP --}}
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 100px;"></div>
            </div>
            <div class="card-body text-center">
                <div class="skeleton rounded-3 mx-auto mb-4" style="height: 120px; width: 85%;"></div>
                <div class="skeleton skeleton-button mx-auto" style="width: 150px; height: 38px;"></div>
            </div>
        </div>
    </div>

    {{-- 4. Kerangka Tombol Aksi Bawah --}}
    <div class="col-12 text-end">
        <div class="skeleton skeleton-button d-inline-block me-2" style="width: 80px; height: 38px;"></div>
        <div class="skeleton skeleton-button d-inline-block" style="width: 180px; height: 38px;"></div>
    </div>
</div>
