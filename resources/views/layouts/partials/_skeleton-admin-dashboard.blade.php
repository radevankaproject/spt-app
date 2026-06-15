{{-- resources/views/layouts/partials/_skeleton-admin-dashboard.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">
    {{-- 1. HERO --}}
    <div class="card mb-4 border-0 shadow-sm placeholder-glow" style="height: 200px; background: #eef0f8;">
        <div class="card-body p-4 d-flex align-items-center">
            <div class="row w-100 align-items-center">
                <div class="col-md-8 text-md-start text-center">
                    <span class="placeholder col-3 rounded-pill mb-3 bg-secondary opacity-50" style="height: 28px;"></span>
                    <span class="placeholder col-8 rounded-pill mb-2 bg-secondary opacity-50 d-block" style="height: 36px;"></span>
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-50" style="height: 16px;"></span>
                </div>
                <div class="col-md-4 text-center text-md-end mt-4 mt-md-0 d-flex justify-content-md-end justify-content-center">
                    <span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 100px; height: 100px;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. QUICK STATS (6 Cards) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 6; $i++)
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-body p-3 text-center">
                    <span class="placeholder rounded-circle mx-auto mb-2 bg-secondary opacity-25 d-block" style="width: 48px; height: 48px;"></span>
                    <span class="placeholder col-6 rounded-pill mx-auto bg-secondary opacity-25 d-block" style="height: 24px;"></span>
                    <span class="placeholder col-8 rounded-pill mx-auto mt-1 bg-secondary opacity-25 d-block" style="height: 12px;"></span>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 3. PEJABAT (3 Cards) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 3; $i++)
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-body d-flex align-items-center">
                    <span class="placeholder rounded-circle me-3 bg-secondary opacity-25" style="width: 48px; height: 48px;"></span>
                    <div class="d-flex flex-column w-100">
                        <span class="placeholder col-6 rounded-pill mb-2 bg-secondary opacity-25" style="height: 14px;"></span>
                        <span class="placeholder col-8 rounded-pill bg-secondary opacity-25" style="height: 10px;"></span>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 4. CHARTS (8:4) --}}
    <div class="row g-4 mb-4">
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
        <div class="col-xl-4">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom pb-3">
                    <span class="placeholder col-7 rounded-pill mb-2 bg-secondary opacity-25" style="height: 22px;"></span>
                </div>
                <div class="card-body pt-4">
                    <span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 350px;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. POLAR CHARTS (6:6) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 2; $i++)
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header text-center pb-0 mt-2">
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center pt-4">
                    <span class="placeholder rounded-circle bg-secondary opacity-25" style="width: 250px; height: 250px;"></span>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 6. TABLES (4:4:4) --}}
    <div class="row g-4">
        @for ($i = 0; $i < 3; $i++)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span>
                    <span class="placeholder col-2 rounded-pill bg-secondary opacity-25" style="height: 24px;"></span>
                </div>
                <div class="card-body p-2 mt-3">
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
