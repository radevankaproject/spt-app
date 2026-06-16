<div class="row">
    <!-- Filter Skeleton -->
    <div class="col-12 mb-4">
        <div class="card skeleton-card border-0" style="border-radius: 0.75rem; box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);">
            <div class="card-header border-bottom bg-transparent pb-3 pt-4">
                <div class="skeleton-box" style="height: 24px; width: 200px; border-radius: 4px;"></div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <div class="col-md-4 col-lg-3">
                        <div class="skeleton-box mb-2" style="height: 16px; width: 60px; border-radius: 4px;"></div>
                        <div class="skeleton-box" style="height: 38px; width: 100%; border-radius: 6px;"></div>
                    </div>
                    <div class="col-md-8 col-lg-5">
                        <div class="skeleton-box mb-2" style="height: 16px; width: 80px; border-radius: 4px;"></div>
                        <div class="skeleton-box" style="height: 38px; width: 100%; border-radius: 6px;"></div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="skeleton-box mb-2" style="height: 16px; width: 120px; border-radius: 4px;"></div>
                        <div class="skeleton-box" style="height: 38px; width: 100%; border-radius: 6px;"></div>
                    </div>
                    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4 pt-2 border-top">
                        <div class="skeleton-box mb-3 mb-md-0" style="height: 24px; width: 300px; border-radius: 4px;"></div>
                        <div class="d-flex gap-2">
                            <div class="skeleton-box" style="height: 38px; width: 100px; border-radius: 6px;"></div>
                            <div class="skeleton-box" style="height: 38px; width: 140px; border-radius: 6px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Skeleton -->
    <div class="col-12 mb-4">
        <div class="card skeleton-card border-0" style="border-radius: 0.75rem; box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-bottom bg-transparent pb-3 pt-4">
                <div class="skeleton-box" style="height: 24px; width: 150px; border-radius: 4px;"></div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <div class="skeleton-box" style="height: 38px; width: 100px; border-radius: 6px;"></div>
                    <div class="skeleton-box" style="height: 38px; width: 100px; border-radius: 6px;"></div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center"><div class="skeleton-box mx-auto" style="height: 16px; width: 20px; border-radius: 4px;"></div></th>
                                <th width="20%"><div class="skeleton-box" style="height: 16px; width: 100px; border-radius: 4px;"></div></th>
                                <th width="20%"><div class="skeleton-box" style="height: 16px; width: 100px; border-radius: 4px;"></div></th>
                                <th width="10%" class="text-center"><div class="skeleton-box mx-auto" style="height: 16px; width: 60px; border-radius: 4px;"></div></th>
                                <th width="20%"><div class="skeleton-box" style="height: 16px; width: 120px; border-radius: 4px;"></div></th>
                                <th width="10%" class="text-center"><div class="skeleton-box mx-auto" style="height: 16px; width: 80px; border-radius: 4px;"></div></th>
                                <th width="15%" class="text-end"><div class="skeleton-box ms-auto" style="height: 16px; width: 100px; border-radius: 4px;"></div></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td class="text-center"><div class="skeleton-box mx-auto" style="height: 16px; width: 20px; border-radius: 4px;"></div></td>
                                <td>
                                    <div class="skeleton-box mb-1" style="height: 18px; width: 80%; border-radius: 4px;"></div>
                                </td>
                                <td><div class="skeleton-box" style="height: 16px; width: 70%; border-radius: 4px;"></div></td>
                                <td class="text-center"><div class="skeleton-box mx-auto" style="height: 24px; width: 60px; border-radius: 12px;"></div></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-box rounded-circle me-2" style="height: 24px; width: 24px;"></div>
                                        <div class="skeleton-box" style="height: 16px; width: 60%; border-radius: 4px;"></div>
                                    </div>
                                </td>
                                <td class="text-center"><div class="skeleton-box mx-auto" style="height: 24px; width: 80px; border-radius: 12px;"></div></td>
                                <td class="text-end"><div class="skeleton-box ms-auto" style="height: 16px; width: 60px; border-radius: 4px;"></div></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center p-4 border-top">
                    <div class="skeleton-box" style="height: 16px; width: 200px; border-radius: 4px;"></div>
                    <div class="skeleton-box" style="height: 38px; width: 300px; border-radius: 6px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
