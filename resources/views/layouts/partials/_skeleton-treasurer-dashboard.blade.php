<div id="skeleton-loader" class="container-fluid p-0">
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm placeholder-glow" style="height: 220px; background: #eef0f8;">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <span class="placeholder col-3 rounded-pill mb-3 bg-secondary opacity-50" style="height: 30px;"></span>
                    <span class="placeholder col-6 rounded-pill mb-2 bg-secondary opacity-50" style="height: 40px;"></span>
                    <span class="placeholder col-4 rounded bg-secondary opacity-50 mb-3" style="height: 25px;"></span>
                    <span class="placeholder col-8 rounded-pill bg-secondary opacity-50" style="height: 18px;"></span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm placeholder-glow h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <span class="placeholder col-6 rounded-pill bg-secondary opacity-25 mb-3" style="height: 20px;"></span>
                    <span class="placeholder rounded-circle bg-secondary opacity-25" style="width: 150px; height: 150px;"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 placeholder-glow">
                <div class="card-body d-flex align-items-center">
                    <span class="placeholder rounded me-3 bg-secondary opacity-25" style="width: 54px; height: 54px;"></span>
                    <div class="w-100">
                        <span class="placeholder col-6 rounded-pill mb-2 bg-secondary opacity-25 d-block" style="height: 14px;"></span>
                        <span class="placeholder col-8 rounded-pill bg-secondary opacity-25 d-block" style="height: 22px;"></span>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
    <div class="card shadow-sm border-0 placeholder-glow">
        <div class="card-header pt-4 pb-0"><span class="placeholder col-3 rounded-pill bg-secondary opacity-25" style="height: 24px;"></span></div>
        <div class="card-body"><span class="placeholder col-12 rounded bg-secondary opacity-25 mt-3" style="height: 400px;"></span></div>
    </div>
</div>
