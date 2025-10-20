{{-- resources/views/layouts/partials/_skeleton-deposit-report-index.blade.php --}}

{{-- Skeleton Judul Halaman & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 1.75rem;"></div>
    <div class="skeleton skeleton-text" style="width: 200px; height: 1rem;"></div>
</div>

{{-- Skeleton Kartu Filter --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="skeleton skeleton-text" style="width: 150px; height: 1.5rem;"></div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-3">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-2">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-4">
                <div class="skeleton skeleton-input"></div>
            </div>
        </div>
        <div class="row g-4 mt-1">
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-6">
                <div class="skeleton skeleton-input"></div>
            </div>
        </div>
        <div class="pt-4 d-flex justify-content-end gap-2">
            <div class="skeleton skeleton-button" style="width: 120px;"></div>
            <div class="skeleton skeleton-button" style="width: 180px;"></div>
            <div class="skeleton skeleton-button" style="width: 150px;"></div>
        </div>
    </div>
</div>

{{-- Skeleton Tabel Hasil Laporan --}}
<div class="card mt-6">
    <div class="card-header">
        <div class="skeleton skeleton-text" style="width: 40%; height: 1.5rem;"></div>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 150px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 180px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 100px;"></div>
                        </th>
                        <th class="text-end">
                            <div class="skeleton skeleton-text ms-auto" style="width: 120px;"></div>
                        </th>
                        <th class="text-center">
                            <div class="skeleton skeleton-text mx-auto" style="width: 80px;"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop untuk membuat beberapa baris skeleton --}}
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>
                                <div class="skeleton skeleton-text"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text"></div>
                            </td>
                            <td class="text-end">
                                <div class="skeleton skeleton-text ms-auto" style="width: 80%;"></div>
                            </td>
                            <td class="text-center">
                                <div class="skeleton skeleton-badge mx-auto"></div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-end">
                            <div class="skeleton skeleton-text ms-auto" style="width: 70%;"></div>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
