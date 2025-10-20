{{-- resources/views/layouts/partials/_skeleton-leaders-form.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 250px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 280px;"></div>
</div>

<div class="row g-6">
    {{-- 2. Kerangka Kolom Kiri (2 Kartu) --}}
    <div class="col-lg-8">
        {{-- Kartu Pertama: Informasi Akun --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 200px;"></div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="skeleton skeleton-input flex-grow-1 me-2"></div>
                            <div class="skeleton skeleton-button" style="width: 100px; height: 54px;"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Kedua: Detail Pimpinan --}}
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 150px;"></div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Kerangka Kolom Kanan (Foto Profil) --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 120px;"></div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center">
                    <div class="skeleton skeleton-avatar-lg mb-4"></div>
                    <div class="d-flex justify-content-center">
                        <div class="skeleton skeleton-button me-3" style="width: 100px; height: 38px;"></div>
                        <div class="skeleton skeleton-button" style="width: 80px; height: 38px;"></div>
                    </div>
                    <div class="skeleton skeleton-text skeleton-text-sm mt-3" style="width: 90%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Kerangka Tombol Aksi Bawah --}}
    <div class="col-12 text-end">
        <div class="skeleton skeleton-button d-inline-block me-2" style="width: 80px; height: 38px;"></div>
        <div class="skeleton skeleton-button d-inline-block" style="width: 150px; height: 38px;"></div>
    </div>
</div>
