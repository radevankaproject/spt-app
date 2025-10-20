{{-- resources/views/layouts/partials/_skeleton-deposit-transactions-index.blade.php --}}

{{-- Skeleton Judul Halaman --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 250px; height: 1.75rem;"></div>
    <div class="skeleton skeleton-text" style="width: 200px; height: 1rem;"></div>
</div>

{{-- Skeleton Kartu Filter --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="skeleton skeleton-text" style="width: 180px; height: 1.5rem;"></div>
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
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="skeleton skeleton-input"></div>
            </div>
        </div>
        <div class="pt-4 d-flex justify-content-end gap-2">
            <div class="skeleton skeleton-button" style="width: 100px;"></div>
            <div class="skeleton skeleton-button" style="width: 150px;"></div>
        </div>
    </div>
</div>

{{-- Skeleton Tabel Utama --}}
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        <div class="card-title mb-0">
            <div class="skeleton skeleton-text mb-2" style="width: 300px; height: 1.5rem;"></div>
            <div class="skeleton skeleton-text" style="width: 200px;"></div>
        </div>
        <div class="skeleton skeleton-button" style="width: 160px;"></div>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 120px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 150px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 100px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 100px;"></div>
                        </th>
                        <th>
                            <div class="skeleton skeleton-text" style="width: 80px;"></div>
                        </th>
                        <th class="text-center">
                            <div class="skeleton skeleton-text mx-auto" style="width: 50px;"></div>
                        </th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
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
                            <td>
                                <div class="skeleton skeleton-text"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-badge"></div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="skeleton skeleton-avatar rounded-3" style="width: 32px; height: 32px;">
                                    </div>
                                    <div class="skeleton skeleton-avatar rounded-3 ms-2"
                                        style="width: 32px; height: 32px;"></div>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-end">
            <div class="skeleton skeleton-text" style="width: 250px;"></div>
        </div>
    </div>
</div>
