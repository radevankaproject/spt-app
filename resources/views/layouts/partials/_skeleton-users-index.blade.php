{{-- resources/views/layouts/partials/_skeleton-users-index.blade.php --}}

{{-- 1. Kerangka untuk Judul & Tombol Aksi --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 200px;"></div>
    <div class="skeleton skeleton-text" style="width: 250px;"></div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        {{-- 2. Kerangka untuk Header Card --}}
        <div style="width: 40%;">
            <div class="skeleton skeleton-text" style="width: 250px;"></div>
            <div class="skeleton skeleton-text skeleton-text-sm mt-2" style="width: 150px;"></div>
        </div>
        <div class="d-flex justify-content-md-end align-items-center gap-4" style="width: 55%;">
            <div class="skeleton skeleton-input" style="width: 180px; height: 38px;"></div>
            <div class="skeleton skeleton-button" style="width: 120px; height: 38px;"></div>
            <div class="skeleton skeleton-button" style="width: 140px; height: 38px;"></div>
        </div>
    </div>
    <div class="card-body">
        {{-- 3. Kerangka untuk Tabel --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 35%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 25%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 15%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 15%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 10%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="skeleton skeleton-avatar me-4"></div>
                                    <div>
                                        <div class="skeleton skeleton-text" style="width: 150px;"></div>
                                        <div class="skeleton skeleton-text skeleton-text-sm mt-2" style="width: 80px;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text" style="width: 90%;"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-badge"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-badge"></div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <div class="skeleton skeleton-icon me-2"></div>
                                    <div class="skeleton skeleton-icon"></div>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        {{-- 4. Kerangka untuk Paginasi --}}
        <div class="mt-4 d-flex justify-content-end">
            <div class="skeleton skeleton-text" style="width: 200px;"></div>
        </div>
    </div>
</div>
