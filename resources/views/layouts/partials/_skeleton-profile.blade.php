{{-- Skeleton: Profil Saya --}}

{{-- Breadcrumb skeleton --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <div class="skeleton skeleton-text" style="width: 200px; height: 1.5rem;"></div>
    </div>
    <div class="mt-2 mt-md-0">
        <div class="skeleton skeleton-button rounded-pill" style="width: 180px;"></div>
    </div>
</div>

{{-- Hero Card Skeleton --}}
<div class="skeleton mb-4" style="height: 130px; border-radius: 1rem;"></div>

<div class="row g-4">
    {{-- Left Column: Info --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="skeleton skeleton-text" style="width: 160px; height: 1.2rem;"></div>
            </div>
            <div class="card-body py-2">
                @for($i = 0; $i < 6; $i++)
                <div style="padding: 0.75rem 0; border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <div class="skeleton skeleton-text-sm mb-2" style="width: 80px;"></div>
                    <div class="skeleton skeleton-text" style="width: {{ rand(50, 90) }}%;"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Right Column: Stats + Table --}}
    <div class="col-xl-8 col-lg-7">
        {{-- Stat Cards Skeleton --}}
        <div class="row g-4 mb-4">
            @for($i = 0; $i < 3; $i++)
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-75">
                                <div class="skeleton skeleton-text-sm mb-2" style="width: 70%;"></div>
                                <div class="skeleton skeleton-text" style="width: 40%; height: 1.5rem;"></div>
                            </div>
                            <div class="skeleton" style="width: 40px; height: 40px; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Activity Table Skeleton --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="skeleton skeleton-text" style="width: 180px; height: 1.2rem;"></div>
                <div class="skeleton rounded-pill" style="width: 80px; height: 24px;"></div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4"><div class="skeleton skeleton-text-sm" style="width: 100px;"></div></th>
                                <th class="py-3"><div class="skeleton skeleton-text-sm" style="width: 80px;"></div></th>
                                <th class="py-3 pe-4 text-end"><div class="skeleton skeleton-text-sm" style="width: 60px;"></div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 4; $i++)
                            <tr>
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton me-3" style="width: 32px; height: 32px; border-radius: 6px;"></div>
                                        <div>
                                            <div class="skeleton skeleton-text mb-1" style="width: 120px;"></div>
                                            <div class="skeleton skeleton-text-sm" style="width: 80px;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3"><div class="skeleton rounded-pill" style="width: 80px; height: 22px;"></div></td>
                                <td class="py-3 pe-4 text-end"><div class="skeleton skeleton-text-sm" style="width: 70px;"></div></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
