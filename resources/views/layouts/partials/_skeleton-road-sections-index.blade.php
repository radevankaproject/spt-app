{{-- resources/views/layouts/partials/_skeleton-road-sections-index.blade.php --}}

{{-- 1. Kerangka untuk Judul & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 250px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 180px;"></div>
</div>

<div class="card">
    {{-- 2. Kerangka untuk Header Card --}}
    <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        <div style="width: 40%;">
            <div class="skeleton skeleton-text" style="width: 250px;"></div>
            <div class="skeleton skeleton-text skeleton-text-sm mt-2" style="width: 150px;"></div>
        </div>
        <div class="d-flex justify-content-md-end align-items-center gap-4" style="width: 55%;">
            <div class="d-flex">
                <div class="skeleton skeleton-input" style="width: 180px; height: 38px; border-radius: 6px 0 0 6px;">
                </div>
                <div class="skeleton skeleton-button" style="width: 40px; height: 38px; border-radius: 0 6px 6px 0;">
                </div>
            </div>
            <div class="skeleton skeleton-button" style="width: 180px; height: 38px;"></div>
        </div>
    </div>
    <div class="card-body">
        {{-- 3. Kerangka untuk Tabel --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 20%;">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                        <th style="width: 20%;" class="text-center">
                            <div class="skeleton skeleton-text skeleton-text-sm"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>
                                <div class="skeleton skeleton-text" style="width: {{ rand(50, 80) }}%"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-badge"></div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
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
