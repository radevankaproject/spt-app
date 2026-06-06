<div id="skeleton-loader" class="container-fluid p-0">
    {{-- 1. WELCOME BANNER SKELETON --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm placeholder-glow rounded-4" style="height: 220px; background: #eef0f8;">
                <div class="card-body p-4 p-md-5 d-flex align-items-center">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-8 text-md-start text-center">
                            <span class="placeholder col-6 rounded-pill mb-3 bg-secondary opacity-50 d-block" style="height: 35px;"></span>
                            <span class="placeholder col-8 rounded bg-secondary opacity-50 d-block mb-4" style="height: 20px;"></span>
                            <div class="d-inline-flex gap-3">
                                <span class="placeholder rounded bg-secondary opacity-50" style="width: 150px; height: 40px;"></span>
                                <span class="placeholder rounded bg-secondary opacity-50" style="width: 180px; height: 40px;"></span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center text-md-end mt-4 mt-md-0 d-flex justify-content-md-end justify-content-center">
                            <span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 140px; height: 140px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. KPI SKELETON --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0 rounded-4 placeholder-glow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="placeholder col-8 rounded-pill bg-secondary opacity-25" style="height: 14px;"></span>
                        <span class="placeholder rounded bg-secondary opacity-25" style="width: 48px; height: 48px;"></span>
                    </div>
                    <span class="placeholder col-6 rounded-pill bg-secondary opacity-25 d-block mt-3 mb-2" style="height: 24px;"></span>
                    <span class="placeholder col-4 rounded-pill bg-secondary opacity-25" style="height: 12px;"></span>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 3. QUICK SEARCH SKELETON --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 placeholder-glow">
                <div class="card-header pt-3 pb-2 border-bottom">
                    <span class="placeholder col-3 rounded-pill bg-secondary opacity-25" style="height: 20px;"></span>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <span class="placeholder col-4 rounded-pill bg-secondary opacity-25 d-block mb-2" style="height: 12px;"></span>
                            <span class="placeholder col-12 rounded bg-secondary opacity-25" style="height: 38px;"></span>
                        </div>
                        <div class="col-md-4">
                            <span class="placeholder col-4 rounded-pill bg-secondary opacity-25 d-block mb-2" style="height: 12px;"></span>
                            <span class="placeholder col-12 rounded bg-secondary opacity-25" style="height: 38px;"></span>
                        </div>
                        <div class="col-md-4">
                            <span class="placeholder col-4 rounded-pill bg-secondary opacity-25 d-block mb-2" style="height: 12px;"></span>
                            <span class="placeholder col-12 rounded bg-secondary opacity-25" style="height: 38px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. CHARTS SKELETON --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 placeholder-glow">
                <div class="card-header pt-4 pb-0 d-flex justify-content-between">
                    <div>
                        <span class="placeholder col-8 rounded-pill bg-secondary opacity-25 d-block mb-1" style="height: 20px;"></span>
                        <span class="placeholder col-4 rounded-pill bg-secondary opacity-25" style="height: 12px;"></span>
                    </div>
                    <span class="placeholder col-2 rounded-pill bg-secondary opacity-25" style="height: 28px;"></span>
                </div>
                <div class="card-body mt-3">
                    <span class="placeholder col-12 rounded bg-secondary opacity-25" style="height: 280px;"></span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 placeholder-glow">
                <div class="card-header pt-4 pb-0">
                    <span class="placeholder col-8 rounded-pill bg-secondary opacity-25 d-block mb-1" style="height: 20px;"></span>
                    <span class="placeholder col-6 rounded-pill bg-secondary opacity-25" style="height: 12px;"></span>
                </div>
                <div class="card-body mt-3">
                    <span class="placeholder col-12 rounded bg-secondary opacity-25" style="height: 280px;"></span>
                </div>
            </div>
        </div>
    </div>
</div>
