{{-- _skeleton-leader-dashboard.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">
    {{-- 1. HERO --}}
    <div class="card mb-4 border-0 shadow-sm placeholder-glow" style="height: 200px; background: #eef0f8;">
        <div class="card-body p-4 d-flex align-items-center">
            <div class="row w-100 align-items-center">
                <div class="col-md-8"><span class="placeholder col-8 rounded-pill mb-2 bg-secondary opacity-50 d-block" style="height: 36px;"></span><span class="placeholder col-5 rounded-pill bg-secondary opacity-50" style="height: 16px;"></span></div>
                <div class="col-md-4 text-center text-md-end"><span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 120px; height: 120px;"></span></div>
            </div>
        </div>
    </div>
    {{-- 2. QUICK STATS (6) --}}
    <div class="row g-4 mb-4">
        @for ($i = 0; $i < 6; $i++)
        <div class="col-xl-2 col-md-4 col-6"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body p-3 text-center"><span class="placeholder rounded-circle mx-auto mb-2 bg-secondary opacity-25 d-block" style="width: 48px; height: 48px;"></span><span class="placeholder col-6 rounded-pill mx-auto bg-secondary opacity-25 d-block" style="height: 24px;"></span></div></div></div>
        @endfor
    </div>
    {{-- 3. SEARCH --}}
    <div class="card mb-4 shadow-sm border-0 placeholder-glow"><div class="card-body"><div class="row g-4">@for ($i = 0; $i < 3; $i++)<div class="col-md-4"><span class="placeholder col-12 rounded-pill bg-secondary opacity-25" style="height: 38px;"></span></div>@endfor</div></div></div>
    {{-- 4. MAP + TABLE --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body"><span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 400px;"></span></div></div></div>
        <div class="col-xl-4"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body">@for ($i = 0; $i < 6; $i++)<div class="d-flex mb-3"><span class="placeholder col-10 rounded-pill bg-secondary opacity-25" style="height: 14px;"></span></div>@endfor</div></div></div>
    </div>
    {{-- 5. CHARTS --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body pt-4"><span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 320px;"></span></div></div></div>
        <div class="col-xl-4"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-body pt-4"><span class="placeholder col-12 rounded-3 bg-secondary opacity-25" style="height: 320px;"></span></div></div></div>
    </div>
    {{-- 6. 2 TABLES --}}
    <div class="row g-4">
        @for ($i = 0; $i < 2; $i++)
        <div class="col-lg-6"><div class="card h-100 shadow-sm border-0 placeholder-glow"><div class="card-header border-bottom pb-3"><span class="placeholder col-5 rounded-pill bg-secondary opacity-25" style="height: 18px;"></span></div><div class="card-body">@for ($j = 0; $j < 4; $j++)<div class="d-flex mb-3"><span class="placeholder col-10 rounded-pill bg-secondary opacity-25" style="height: 14px;"></span></div>@endfor</div></div></div>
        @endfor
    </div>
</div>
