{{-- resources/views/layouts/partials/_skeleton-agreement-show.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">

    {{-- ✅ HEADER SKELETON --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 placeholder-glow">
        <div>
            <span class="placeholder rounded-pill mb-1 bg-secondary opacity-25" style="width: 250px; height: 24px; display: block;"></span>
            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 180px; height: 14px; display: block;"></span>
        </div>
        <div class="d-flex gap-2">
            <span class="placeholder rounded-2 bg-secondary opacity-25" style="width: 100px; height: 38px;"></span>
            <span class="placeholder rounded-2 bg-primary opacity-50" style="width: 120px; height: 38px;"></span>
        </div>
    </div>

    <div class="row g-4">
        {{-- ✅ KOLOM KIRI (Profil Korlap & Info PKS) - 4 Kolom --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm placeholder-glow">
                <div class="card-body pt-5 text-center">
                    {{-- Avatar Bulat --}}
                    <span class="placeholder rounded-circle bg-secondary opacity-25 mb-3 d-inline-block" style="width: 120px; height: 120px; border: 4px solid #fff; box-shadow: 0 .125rem .25rem rgba(161,172,184,.15);"></span>
                    <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25 d-block mx-auto" style="width: 160px; height: 20px;"></span>
                    <span class="placeholder rounded-pill bg-primary opacity-25 d-block mx-auto" style="width: 130px; height: 24px;"></span>

                    {{-- Kotak Setoran & Lokasi --}}
                    <div class="row text-center mb-4 mt-4 g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column align-items-center">
                                <span class="placeholder rounded bg-secondary opacity-25 mb-2" style="width: 32px; height: 32px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25 mb-1" style="width: 40px; height: 20px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 70px; height: 12px;"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column align-items-center">
                                <span class="placeholder rounded bg-info opacity-25 mb-2" style="width: 32px; height: 32px;"></span>
                                <span class="placeholder rounded-pill bg-info opacity-25 mb-1" style="width: 70px; height: 20px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 80px; height: 12px;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 110px; height: 14px;"></span>
                    </div>

                    {{-- Detail Kontrak List --}}
                    <ul class="list-unstyled mb-4 text-start">
                        @for ($i = 0; $i < 4; $i++)
                        <li class="d-flex align-items-center mb-3">
                            <span class="placeholder rounded-circle bg-secondary opacity-25 me-2" style="width: 20px; height: 20px;"></span>
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 80px; height: 14px;"></span>
                                <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 16px;"></span>
                            </div>
                        </li>
                        @endfor
                    </ul>

                    {{-- Tombol PDF --}}
                    <div class="d-grid gap-2">
                        <span class="placeholder rounded-2 bg-danger opacity-25 w-100" style="height: 38px;"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ KOLOM KANAN (Tabs & Konten) - 8 Kolom --}}
        <div class="col-xl-8 col-lg-7">
            <div class="nav-align-top mb-4 placeholder-glow">
                {{-- Skeleton Tabs (Nav Pills) --}}
                <ul class="nav nav-pills mb-3 gap-2">
                    <li class="nav-item"><span class="placeholder rounded-pill bg-primary opacity-50" style="width: 150px; height: 38px;"></span></li>
                    <li class="nav-item"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 38px;"></span></li>
                    <li class="nav-item"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 140px; height: 38px;"></span></li>
                    <li class="nav-item"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 110px; height: 38px;"></span></li>
                </ul>

                {{-- Skeleton Content (Default ke Overview Grafik) --}}
                <div class="tab-content bg-transparent p-0 shadow-none border-0">

                    {{-- Skeleton Grafik Box --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 200px; height: 18px;"></span>
                        </div>
                        <div class="card-body pt-4">
                            <span class="placeholder w-100 rounded-3 bg-secondary opacity-25" style="height: 300px; display: block;"></span>
                        </div>
                    </div>

                    {{-- Skeleton Tabel Setoran Terbaru --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 180px; height: 18px;"></span>
                            <span class="placeholder rounded-pill bg-primary opacity-25" style="width: 100px; height: 24px;"></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-75"></span></th>
                                            <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-50"></span></th>
                                            <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-50"></span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 3; $i++)
                                        <tr>
                                            <td class="px-4 py-3"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px;"></span></td>
                                            <td class="py-3"><span class="placeholder rounded-pill bg-success opacity-25" style="width: 80px;"></span></td>
                                            <td class="py-3"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 60px;"></span></td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
