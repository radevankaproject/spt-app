{{-- _skeleton-treasurer-dashboard.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">
    {{-- 1. HERO (8:4) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm placeholder-glow" style="height: 200px; background: #eef0f8;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-8"><span class="placeholder col-8 rounded-pill mb-2 bg-secondary opacity-50 d-block" style="height: 36px;"></span><span class="placeholder col-5 rounded-pill bg-secondary opacity-50" style="height: 16px;"></span></div>
                        <div class="col-md-4 text-center text-md-end"><span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 100px; height: 100px;"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card h-100 shadow-sm border-0 placeholder-glow">
                <div class="card-header border-bottom pb-0 text-center"><span class="placeholder col-6 rounded-pill mb-2 bg-secondary opacity-25 d-block mx-auto" style="height: 18px;"></span></div>
                <div class="card-body d-flex justify-content-center align-items-center pt-4">
                    <span class="placeholder rounded-circle bg-secondary opacity-25" style="width: 180px; height: 180px;"></span>
                </div>
            </div>
        </div>
    </div>
    {{-- 2. QUICK STATS (4) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="col-md-3 col-6"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body p-3 text-center"><span class="placeholder col-6 rounded-pill mx-auto bg-secondary opacity-25 d-block mb-3" style="height: 24px;"></span><span class="placeholder col-8 rounded-pill mx-auto bg-secondary opacity-25 d-block" style="height: 14px;"></span></div></div></div>
        @endfor
    </div>
    {{-- 3. 2 TABLES --}}
    <div class="row g-4">
        <div class="col-xl-7 col-lg-7"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-header border-bottom pb-3"><span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span></div><div class="card-body">@for ($j = 0; $j < 5; $j++)<div class="d-flex mb-3 align-items-center"><span class="placeholder rounded-circle bg-secondary opacity-25 me-3" style="width: 40px; height: 40px;"></span><span class="placeholder col-8 rounded-pill bg-secondary opacity-25" style="height: 14px;"></span></div>@endfor</div></div></div>
        <div class="col-xl-5 col-lg-5"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-header border-bottom pb-3"><span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span></div><div class="card-body">@for ($j = 0; $j < 5; $j++)<div class="d-flex mb-3 align-items-center"><span class="placeholder rounded-circle bg-secondary opacity-25 me-3" style="width: 40px; height: 40px;"></span><span class="placeholder col-8 rounded-pill bg-secondary opacity-25" style="height: 14px;"></span></div>@endfor</div></div></div>
    </div>
</div>
