{{-- resources/views/layouts/partials/_skeleton-blud-bank-form.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 250px;"></div>
</div>

<div class="card">
    <div class="card-body">
        {{-- 2. Kerangka untuk Form --}}
        <div class="row g-6">
            {{-- Baris pertama input --}}
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
            {{-- Baris kedua input --}}
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
        </div>

        {{-- 3. Kerangka untuk Tombol Aksi --}}
        <div class="pt-6 text-end">
            <div class="skeleton skeleton-button d-inline-block me-2" style="width: 80px; height: 38px;"></div>
            <div class="skeleton skeleton-button d-inline-block" style="width: 150px; height: 38px;"></div>
        </div>
    </div>
</div>
