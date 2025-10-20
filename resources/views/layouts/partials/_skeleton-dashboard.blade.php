<div class="row g-6">
    {{-- Skeleton untuk Info Cards --}}
    @for ($i = 0; $i < 4; $i++)
        <div class="col-sm-6 col-lg-3">
            <div class="skeleton skeleton-card" style="height: 100px;"></div>
        </div>
    @endfor

    {{-- Skeleton untuk Grafik Utama --}}
    <div class="col-lg-8">
        <div class="skeleton skeleton-card" style="height: 400px;"></div>
    </div>

    {{-- Skeleton untuk Info Samping --}}
    <div class="col-lg-4">
        <div class="skeleton skeleton-card mb-6" style="height: 120px;"></div>
        <div class="skeleton skeleton-card" style="height: 260px;"></div>
    </div>
</div>
