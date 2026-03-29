{{-- resources/views/layouts/partials/_skeleton-field-coordinator-show.blade.php --}}
<div id="skeleton-loader" class="container-fluid p-0">

    {{-- ✅ HEADER & FILTER TAHUN SKELETON --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3 placeholder-glow">
        <div class="w-100" style="max-width: 300px;">
            <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25" style="width: 250px; height: 28px; display: block;"></span>
            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 200px; height: 14px; display: block;"></span>
        </div>
        <div class="d-flex gap-2 w-100 justify-content-md-end overflow-hidden">
            <span class="placeholder rounded-pill bg-primary opacity-50" style="width: 100px; height: 38px;"></span>
            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 38px;"></span>
            <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 38px;"></span>
        </div>
    </div>

    <div class="row g-4">
        {{-- ✅ KOLOM KIRI (Profil & Statistik) - 4 Kolom --}}
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4 border-0 shadow-sm placeholder-glow">
                <div class="card-body pt-5 text-center">
                    {{-- Avatar Bulat --}}
                    <span class="placeholder rounded-circle bg-secondary opacity-25 mb-4 d-inline-block shadow-sm" style="width: 120px; height: 120px;"></span>
                    <span class="placeholder rounded-pill mb-2 bg-secondary opacity-25 d-block mx-auto" style="width: 150px; height: 22px;"></span>
                    <span class="placeholder rounded-pill bg-warning opacity-50 d-block mx-auto mb-4" style="width: 130px; height: 24px;"></span>

                    {{-- Kotak Statistik --}}
                    <div class="bg-lighter rounded-3 p-3 mb-4 text-start">
                        <span class="placeholder rounded-pill bg-primary opacity-25 mb-3" style="width: 140px; height: 16px; display: block;"></span>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 120px; height: 14px;"></span>
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 30px; height: 14px;"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 130px; height: 14px;"></span>
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 30px; height: 14px;"></span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span class="placeholder rounded bg-secondary opacity-25" style="width: 110px; height: 14px;"></span>
                            <span class="placeholder rounded bg-success opacity-25" style="width: 90px; height: 14px;"></span>
                        </div>
                    </div>

                    {{-- Info Personal --}}
                    <div class="pb-2 border-bottom text-start mb-3">
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 120px; height: 16px;"></span>
                    </div>
                    <ul class="list-unstyled mb-4 text-start">
                        @for ($i = 0; $i < 4; $i++)
                        <li class="d-flex align-items-center mb-3">
                            <span class="placeholder rounded-circle bg-secondary opacity-25 me-2" style="width: 16px; height: 16px;"></span>
                            <span class="placeholder rounded-pill bg-secondary opacity-25 w-75" style="height: 14px;"></span>
                        </li>
                        @endfor
                    </ul>
                    <div class="d-grid gap-2">
                        <span class="placeholder rounded-2 bg-primary opacity-25 w-100" style="height: 38px;"></span>
                    </div>
                </div>
            </div>

            {{-- Kartu KTP Skeleton --}}
            <div class="card border-0 shadow-sm placeholder-glow">
                <div class="card-body">
                    <div class="pb-2 border-bottom mb-3">
                        <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px; height: 16px;"></span>
                    </div>
                    <span class="placeholder w-100 rounded-3 bg-secondary opacity-25" style="height: 150px; display: block;"></span>
                </div>
            </div>
        </div>

        {{-- ✅ KOLOM KANAN (Portofolio Kontrak) - 8 Kolom --}}
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="placeholder-glow">

                {{-- Judul PKS Aktif --}}
                <div class="d-flex align-items-center mb-3">
                    <span class="placeholder rounded-circle bg-primary opacity-25 me-2" style="width: 24px; height: 24px;"></span>
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 200px; height: 20px;"></span>
                </div>

                {{-- Skeleton PKS Aktif (1 Card) --}}
                <div class="card mb-5 border-0 shadow-sm">
                    <div class="card-header bg-secondary bg-opacity-10 d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <span class="placeholder rounded-pill bg-primary opacity-50 mb-1 d-block" style="width: 150px; height: 20px;"></span>
                            <span class="placeholder rounded-pill bg-secondary opacity-25 d-block" style="width: 180px; height: 14px;"></span>
                        </div>
                        <span class="placeholder rounded-pill bg-success opacity-25" style="width: 80px; height: 24px;"></span>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <span class="placeholder rounded-circle bg-secondary opacity-25 me-3" style="width: 32px; height: 32px;"></span>
                                    <div>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25 mb-1 d-block" style="width: 60px; height: 16px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25 d-block" style="width: 100px; height: 12px;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <span class="placeholder rounded-circle bg-success opacity-25 me-3" style="width: 32px; height: 32px;"></span>
                                    <div>
                                        <span class="placeholder rounded-pill bg-success opacity-25 mb-1 d-block" style="width: 90px; height: 16px;"></span>
                                        <span class="placeholder rounded-pill bg-secondary opacity-25 d-block" style="width: 100px; height: 12px;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top text-end">
                            <span class="placeholder rounded-2 bg-primary opacity-25" style="width: 130px; height: 32px;"></span>
                        </div>
                    </div>
                </div>

                {{-- Judul Riwayat --}}
                <div class="d-flex align-items-center mb-3 mt-5">
                    <span class="placeholder rounded-circle bg-secondary opacity-25 me-2" style="width: 24px; height: 24px;"></span>
                    <span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 220px; height: 20px;"></span>
                </div>

                {{-- Skeleton Tabel Riwayat --}}
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-75"></span></th>
                                    <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-50"></span></th>
                                    <th><span class="placeholder rounded-pill bg-secondary opacity-25 w-75"></span></th>
                                    <th class="text-center"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 60px;"></span></th>
                                    <th class="text-center"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 40px;"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 3; $i++)
                                <tr>
                                    <td class="py-3"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 100px;"></span></td>
                                    <td class="py-3">
                                        <span class="placeholder rounded-pill bg-secondary opacity-25 mb-1 d-block" style="width: 80px;"></span>
                                        <span class="placeholder rounded-pill bg-danger opacity-25 d-block" style="width: 80px;"></span>
                                    </td>
                                    <td class="py-3"><span class="placeholder rounded-pill bg-success opacity-25" style="width: 90px;"></span></td>
                                    <td class="py-3 text-center"><span class="placeholder rounded-pill bg-secondary opacity-25" style="width: 70px; height: 20px;"></span></td>
                                    <td class="py-3 text-center"><span class="placeholder rounded-circle bg-secondary opacity-25" style="width: 28px; height: 28px;"></span></td>
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
