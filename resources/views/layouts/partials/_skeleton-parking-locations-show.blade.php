{{-- resources/views/layouts/partials/_skeleton-parking-locations-show.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">

    {{-- ✅ HEADER SKELETON --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 placeholder-glow">
                <div class="d-flex align-items-center">
                    <span class="placeholder rounded-3 me-3 bg-secondary opacity-25" style="width: 54px; height: 54px;"></span>
                    <div>
                        <span class="placeholder rounded-pill mb-1 bg-secondary opacity-25" style="width: 250px; height: 24px;"></span><br>
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 180px; height: 14px;"></span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="placeholder rounded-2 bg-secondary opacity-25" style="width: 100px; height: 38px;"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ✅ KOLOM KIRI (User Sidebar) - 4 Kolom --}}
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <div class="card mb-4 border-0 shadow-sm placeholder-glow">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-column">
                        <span class="placeholder bg-secondary opacity-25 mt-3 mb-3" style="width: 140px; height: 140px; border-radius: 0.5rem;"></span>
                        <div class="text-center w-100">
                            <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25 d-block mx-auto" style="width: 150px; height: 20px;"></span>
                            <span class="placeholder rounded-pill bg-secondary opacity-25 mx-auto" style="width: 110px; height: 32px;"></span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center flex-wrap mt-4 pt-3 pb-4 border-bottom">
                        <div class="d-flex align-items-start mt-3 gap-3 w-100 justify-content-center">
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 48px; height: 48px;"></span>
                            <div>
                                <span class="placeholder rounded-pill mb-1 bg-secondary opacity-25 d-block" style="width: 120px; height: 20px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 80px; height: 12px;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pb-3 border-bottom mt-4 mb-3">
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 16px;"></span>
                    </div>
                    <div class="info-container">
                        <ul class="list-unstyled mb-4">
                            @for ($i = 0; $i < 4; $i++)
                            <li class="mb-3 d-flex align-items-center">
                                <span class="placeholder rounded-circle bg-secondary opacity-25 me-2" style="width: 20px; height: 20px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25 me-2" style="width: 80px; height: 16px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 16px;"></span>
                            </li>
                            @endfor
                        </ul>
                        
                        <div class="d-flex justify-content-center pt-3">
                            <span class="placeholder w-100 rounded-2 bg-secondary opacity-25" style="height: 38px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ KOLOM KANAN (User Content) - 8 Kolom --}}
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <div class="nav-align-top mb-4 placeholder-glow">
                {{-- Tabs Skeleton --}}
                <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2">
                    @for ($i = 0; $i < 4; $i++)
                    <li class="nav-item">
                        <span class="placeholder rounded-2 bg-secondary opacity-25" style="width: 150px; height: 40px; display: inline-block;"></span>
                    </li>
                    @endfor
                </ul>

                <div class="tab-content bg-transparent p-0 shadow-none">
                    {{-- Tab 1 Skeleton (PKS) --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 200px; height: 20px;"></span>
                        </div>
                        <div class="card-body pt-4">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border opacity-50">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 48px; height: 48px;"></span>
                                    <div>
                                        <span class="placeholder rounded-pill mb-2 bg-secondary opacity-50 d-block" style="width: 140px; height: 20px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-50" style="width: 100px; height: 16px;"></span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="placeholder rounded-pill mb-2 bg-secondary opacity-50 d-block" style="width: 80px; height: 20px;"></span>
                                    <span class="placeholder rounded-pill bg-secondary opacity-50" style="width: 120px; height: 12px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
