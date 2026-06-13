{{-- Skeleton: Edit Profil --}}

{{-- Breadcrumb skeleton --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <div class="skeleton skeleton-text" style="width: 180px; height: 1.5rem;"></div>
    </div>
    <div class="mt-2 mt-md-0">
        <div class="skeleton skeleton-button rounded-pill" style="width: 180px;"></div>
    </div>
</div>

{{-- Hero Card Skeleton --}}
<div class="skeleton mb-4" style="height: 140px; border-radius: 1rem;"></div>

<div class="row g-4">
    {{-- Left: Form Profil --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="skeleton skeleton-text" style="width: 160px; height: 1.2rem;"></div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    @for($i = 0; $i < 4; $i++)
                    <div class="col-md-6">
                        <div class="skeleton skeleton-text-sm mb-2" style="width: 100px;"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    @endfor
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <div class="skeleton skeleton-button rounded-pill" style="width: 150px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Role Info + Password --}}
    <div class="col-xl-4 col-lg-5">
        {{-- Role Info --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-info">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="skeleton skeleton-text" style="width: 160px; height: 1.2rem;"></div>
            </div>
            <div class="card-body pt-4">
                @for($i = 0; $i < 3; $i++)
                <div class="mb-3">
                    <div class="skeleton skeleton-text-sm mb-2" style="width: 120px;"></div>
                    <div class="skeleton skeleton-input" style="height: 38px;"></div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Password --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="skeleton skeleton-text" style="width: 140px; height: 1.2rem;"></div>
            </div>
            <div class="card-body pt-4">
                @for($i = 0; $i < 3; $i++)
                <div class="mb-3">
                    <div class="skeleton skeleton-text-sm mb-2" style="width: 140px;"></div>
                    <div class="skeleton skeleton-input" style="height: 38px;"></div>
                </div>
                @endfor
                <div class="skeleton skeleton-button w-100 rounded-pill mt-2" style="height: 38px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
