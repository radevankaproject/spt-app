<div class="row mb-4 placeholder-glow">
    <div class="col-12">
        <div class="card border-0 shadow-sm placeholder" style="height: 110px; border-radius: 0.5rem; width: 100%;"></div>
    </div>
</div>

<h6 class="text-muted fw-bold text-uppercase mb-3 placeholder-glow"><span class="placeholder col-3"></span></h6>
<div class="row g-4 mb-4 placeholder-glow">
    @for ($i = 0; $i < 2; $i++)
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center py-3">
                    <div class="placeholder rounded-circle me-3" style="width: 48px; height: 48px;"></div>
                    <div class="w-100">
                        <div class="placeholder col-6 mb-2 rounded"></div>
                        <div class="placeholder col-4 mb-1 rounded d-block"></div>
                        <div class="placeholder col-5 rounded d-block"></div>
                    </div>
                </div>
            </div>
        </div>
    @endfor
</div>

<h6 class="text-muted fw-bold text-uppercase mb-3 placeholder-glow"><span class="placeholder col-2"></span></h6>
<div class="row g-4 mb-4 placeholder-glow">
    @for ($i = 0; $i < 3; $i++)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="placeholder rounded-circle me-2" style="width: 32px; height: 32px;"></div>
                        <div class="placeholder col-6 rounded"></div>
                    </div>
                    <div class="placeholder col-8 rounded" style="height: 24px;"></div>
                    <div class="placeholder col-5 mt-2 rounded d-block"></div>
                </div>
            </div>
        </div>
    @endfor
</div>

<div class="row g-4 placeholder-glow">
    @for ($i = 0; $i < 2; $i++)
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom pb-3">
                    <div class="placeholder col-4 rounded" style="height: 20px;"></div>
                </div>
                <div class="card-body pt-3">
                    @for ($j = 0; $j < 3; $j++)
                        <div class="d-flex justify-content-between mb-3">
                            <div class="placeholder col-5 rounded"></div>
                            <div class="placeholder col-3 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endfor
</div>
