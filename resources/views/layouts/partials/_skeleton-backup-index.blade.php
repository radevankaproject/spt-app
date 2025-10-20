{{-- resources/views/layouts/partials/_skeleton-backup-index.blade.php --}}

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 200px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 150px;"></div>
</div>

<div class="card mb-6">
    <div class="card-body text-center">
        <div class="skeleton rounded-3 mx-auto" style="font-size: 80px; width: 80px; height: 80px;"></div>
        <div class="skeleton skeleton-text mt-4 mx-auto" style="width: 250px; height: 1.2rem;"></div>
        <div class="skeleton skeleton-text skeleton-text-sm mt-3 mx-auto" style="width: 80%;"></div>
        <div class="skeleton skeleton-text skeleton-text-sm mt-2 mx-auto" style="width: 60%;"></div>
        <div class="skeleton skeleton-button mt-4 mx-auto" style="width: 200px; height: 48px;"></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="skeleton skeleton-text" style="width: 150px;"></div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 35%;">
                        <div class="skeleton skeleton-text skeleton-text-sm"></div>
                    </th>
                    <th style="width: 15%;">
                        <div class="skeleton skeleton-text skeleton-text-sm"></div>
                    </th>
                    <th style="width: 20%;">
                        <div class="skeleton skeleton-text skeleton-text-sm"></div>
                    </th>
                    <th style="width: 15%;">
                        <div class="skeleton skeleton-text skeleton-text-sm"></div>
                    </th>
                    <th style="width: 15%;">
                        <div class="skeleton skeleton-text skeleton-text-sm"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 3; $i++)
                    <tr>
                        <td>
                            <div class="skeleton skeleton-text" style="width: 90%"></div>
                        </td>
                        <td>
                            <div class="skeleton skeleton-text" style="width: 60%"></div>
                        </td>
                        <td>
                            <div class="skeleton skeleton-text" style="width: 80%"></div>
                        </td>
                        <td>
                            <div class="skeleton skeleton-badge"></div>
                        </td>
                        <td>
                            <div class="d-flex">
                                <div class="skeleton skeleton-icon me-2"></div>
                                <div class="skeleton skeleton-icon"></div>
                            </div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
