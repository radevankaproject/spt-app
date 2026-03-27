{{-- resources/views/layouts/partials/_skeleton-deposit-targets.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-4">
    {{-- Header Skeleton --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="w-50">
            <div class="placeholder-glow"><span class="placeholder col-6 rounded-pill" style="height: 28px;"></span></div>
            <div class="placeholder-glow mt-2"><span class="placeholder col-4 rounded-pill" style="height: 15px;"></span></div>
        </div>
        <div class="w-25 text-end">
            <div class="placeholder-glow"><span class="placeholder col-8 rounded-pill" style="height: 15px;"></span></div>
        </div>
    </div>

    {{-- Chart Skeleton --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="placeholder-glow"><span class="placeholder col-3 rounded-pill" style="height: 20px;"></span></div>
        </div>
        <div class="card-body pt-4">
            <div class="placeholder-glow"><span class="placeholder col-12 rounded-3" style="height: 320px;"></span></div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Form Skeleton (Kiri) --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="placeholder-glow"><span class="placeholder col-6 rounded-pill" style="height: 20px;"></span></div>
                </div>
                <div class="card-body mt-4">
                    <div class="placeholder-glow mb-4"><span class="placeholder col-12 rounded-pill"></span><span class="placeholder col-10 rounded-pill mt-1"></span></div>
                    <div class="placeholder-glow mb-3"><span class="placeholder col-12 rounded-3" style="height: 50px;"></span></div>
                    <div class="placeholder-glow mb-3"><span class="placeholder col-12 rounded-3" style="height: 50px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-12 rounded-3" style="height: 50px;"></span></div>
                </div>
                <div class="card-footer border-top text-end pt-3 pb-3">
                    <div class="placeholder-glow">
                        <span class="placeholder col-3 rounded-3 me-2" style="height: 38px;"></span>
                        <span class="placeholder col-4 rounded-3" style="height: 38px;"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accordion Skeleton (Kanan) --}}
        <div class="col-lg-8 col-md-7">
            @for ($i = 0; $i < 3; $i++)
            <div class="card mb-3 border-0 shadow-sm rounded-3">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3 w-50">
                        <div class="placeholder-glow"><span class="placeholder rounded-circle" style="width: 40px; height: 40px;"></span></div>
                        <div class="w-100">
                            <div class="placeholder-glow"><span class="placeholder col-4 rounded-pill" style="height: 12px;"></span></div>
                            <div class="placeholder-glow mt-1"><span class="placeholder col-6 rounded-pill" style="height: 18px;"></span></div>
                        </div>
                    </div>
                    <div class="w-25 text-end">
                        <div class="placeholder-glow"><span class="placeholder col-8 rounded-pill" style="height: 12px;"></span></div>
                        <div class="placeholder-glow mt-1"><span class="placeholder col-10 rounded-pill" style="height: 18px;"></span></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
