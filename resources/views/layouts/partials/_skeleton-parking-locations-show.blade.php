{{-- resources/views/layouts/partials/_skeleton-parking-locations-show.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">

    {{-- ✅ HEADER SKELETON --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 placeholder-glow">
        <div class="d-flex align-items-center">
            <span class="placeholder rounded-3 me-3 bg-secondary opacity-25" style="width: 54px; height: 54px;"></span>
            <div>
                <span class="placeholder rounded-pill mb-1 bg-secondary opacity-25" style="width: 250px; height: 24px;"></span><br>
                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 180px; height: 14px;"></span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="placeholder rounded-2 bg-secondary opacity-25" style="width: 100px; height: 38px;"></span>
            <span class="placeholder rounded-2 bg-primary opacity-50" style="width: 130px; height: 38px;"></span>
        </div>
    </div>

    <div class="row g-4">
        {{-- ✅ KOLOM KIRI (8 Kolom) --}}
        <div class="col-xl-8 col-lg-7">

            {{-- 1. Kartu Info Lokasi --}}
            <div class="card mb-4 border-0 shadow-sm placeholder-glow">
                <div class="card-body d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                    {{-- Hero Image --}}
                    <span class="placeholder bg-secondary opacity-25" style="width: 120px; height: 120px; border-radius: 1rem;"></span>

                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25 d-block" style="width: 150px; height: 20px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 12px;"></span>
                            </div>
                            {{-- Badge Status --}}
                            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 110px; height: 32px;"></span>
                        </div>

                        <div class="row g-3 mt-2">
                            @for ($i = 0; $i < 3; $i++)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <span class="placeholder rounded bg-secondary opacity-25 me-2" style="width: 32px; height: 32px;"></span>
                                    <div>
                                        <span class="placeholder rounded-pill mb-1 bg-secondary opacity-25 d-block" style="width: 80px; height: 10px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 130px; height: 14px;"></span>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Kartu PKS --}}
            <div class="card mb-4 border-0 shadow-sm placeholder-glow">
                <div class="card-header bg-transparent border-bottom">
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 220px; height: 18px;"></span>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border opacity-50">
                        <div class="d-flex align-items-center gap-3">
                            <span class="placeholder rounded-circle bg-secondary opacity-50" style="width: 48px; height: 48px;"></span>
                            <div>
                                <span class="placeholder rounded-pill mb-2 bg-secondary opacity-50 d-block" style="width: 140px; height: 16px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-50" style="width: 100px; height: 12px;"></span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="placeholder rounded-pill mb-2 bg-secondary opacity-50 d-block" style="width: 80px; height: 20px;"></span>
                            <span class="placeholder rounded-pill bg-secondary opacity-50" style="width: 120px; height: 12px;"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Kartu Peta --}}
            <div class="card mb-4 border-0 shadow-sm placeholder-glow">
                <div class="card-header bg-transparent border-bottom">
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 180px; height: 18px;"></span>
                </div>
                <div class="card-body pt-4">
                    {{-- Map Box --}}
                    <span class="placeholder w-100 bg-secondary opacity-25" style="height: 350px; border-radius: 0.75rem;"></span>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 32px;"></span>
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 32px;"></span>
                    </div>
                </div>
            </div>

            {{-- 4. Kartu Dokumen PDF --}}
            <div class="card border-0 shadow-sm placeholder-glow">
                <div class="card-header bg-transparent border-bottom">
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 200px; height: 18px;"></span>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25" style="width: 140px; height: 16px;"></span>
                            <span class="placeholder w-100 bg-secondary opacity-25" style="padding-bottom: 56.25%; border-radius: 0.75rem; display: block;"></span>
                        </div>
                        <div class="col-md-6">
                            <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25" style="width: 140px; height: 16px;"></span>
                            <span class="placeholder w-100 bg-secondary opacity-25" style="padding-bottom: 56.25%; border-radius: 0.75rem; display: block;"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ✅ KOLOM KANAN (Timeline History) - 4 Kolom --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm placeholder-glow">
                <div class="card-header bg-transparent border-bottom">
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 160px; height: 18px;"></span>
                </div>
                <div class="card-body pt-4">
                    <ul class="timeline timeline-dashed mt-3 pb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <li class="timeline-item mb-4">
                                <span class="timeline-indicator bg-secondary opacity-25"><i class="ri-checkbox-blank-circle-line"></i></span>
                                <div class="timeline-event">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 14px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 60px; height: 10px;"></span>
                                    </div>
                                    <span class="placeholder rounded-pill mb-2 d-block bg-secondary opacity-25" style="width: 100%; height: 12px;"></span>
                                    <span class="placeholder rounded-pill mb-3 d-block bg-secondary opacity-25" style="width: 80%; height: 12px;"></span>

                                    {{-- User Info Placeholder --}}
                                    <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 d-inline-flex">
                                        <span class="placeholder rounded-circle bg-secondary opacity-25 me-2" style="width: 20px; height: 20px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 90px; height: 12px;"></span>
                                    </div>
                                </div>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
