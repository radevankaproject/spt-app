{{-- resources/views/layouts/partials/_skeleton-admin-dashboard.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">

    {{-- ✅ 1. HERO GREETING SKELETON --}}
    <div class="card mb-4 border-0 shadow-sm placeholder-glow" style="height: 220px; background: #eef0f8;">
        <div class="card-body p-4 p-md-5 d-flex align-items-center">
            <div class="row w-100 align-items-center">
                <div class="col-md-8 text-md-start text-center">
                    <span class="placeholder col-3 rounded-pill mb-3 bg-secondary opacity-50" style="height: 30px;"></span>
                    <span class="placeholder col-8 rounded-pill mb-2 bg-secondary opacity-50 d-block" style="height: 40px;"></span>
                    <span class="placeholder col-6 rounded-pill bg-secondary opacity-50" style="height: 18px;"></span>
                </div>
                <div class="col-md-4 text-center text-md-end mt-4 mt-md-0 d-flex justify-content-md-end justify-content-center">
                    <span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 120px; height: 120px;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 2. TOP METRICS SKELETON (4 Card Row) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-body d-flex align-items-center">
                    {{-- Avatar/Icon Placeholder --}}
                    <span class="placeholder rounded-circle me-3 bg-secondary opacity-25" style="width: 56px; height: 56px;"></span>
                    <div class="d-flex flex-column w-100">
                        <span class="placeholder col-6 rounded-pill mb-2 bg-secondary opacity-25" style="height: 14px;"></span>
                        <span class="placeholder col-8 rounded-pill bg-secondary opacity-25" style="height: 22px;"></span>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- ✅ 3. MAIN CHARTS SKELETON (8 Col & 4 Col) --}}
    <div class="row g-4 mb-4">
        {{-- Mixed Chart Skeleton --}}
        <div class="col-xl-8">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom pb-3 d-flex justify-content-between align-items-center">
                    <span class="placeholder col-4 rounded-pill bg-secondary opacity-25" style="height: 22px;"></span>
                    <span class="placeholder col-2 rounded-pill bg-secondary opacity-25" style="height: 22px;"></span>
                </div>
                <div class="card-body pt-4">
                    <span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 350px;"></span>
                </div>
            </div>
        </div>

        {{-- Bar Chart Skeleton --}}
        <div class="col-xl-4">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom pb-3">
                    <span class="placeholder col-7 rounded-pill mb-2 bg-secondary opacity-25" style="height: 22px;"></span>
                    <br>
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 12px;"></span>
                </div>
                <div class="card-body pt-4">
                    <span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 350px;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ 4. POLAR CHARTS SKELETON (2 Zona) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 2; $i++)
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header text-center pb-0 mt-2">
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center pt-4">
                    {{-- Circle Placeholder for Polar Area --}}
                    <span class="placeholder rounded-circle bg-secondary opacity-25" style="width: 250px; height: 250px;"></span>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- ✅ 5. TABLES LIST SKELETON (3 Tables Row) --}}
    <div class="row g-4">
        @for ($i = 0; $i < 3; $i++)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span>
                    <span class="placeholder col-2 rounded-pill bg-secondary opacity-25" style="height: 24px;"></span>
                </div>
                <div class="card-body p-2 mt-3">
                    {{-- 4 Dummy Table Rows --}}
                    @for ($j = 0; $j < 4; $j++)
                    <div class="d-flex align-items-center mb-4 px-3">
                        <span class="placeholder rounded-circle me-3 bg-secondary opacity-25" style="width: 40px; height: 40px;"></span>
                        <div class="d-flex flex-column w-100">
                            <span class="placeholder col-8 rounded-pill mb-2 bg-secondary opacity-25" style="height: 14px;"></span>
                            <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 10px;"></span>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
