{{-- resources/views/layouts/partials/_skeleton-deposit-transaction-show.blade.php --}}

<div class="row invoice-preview">
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
        <div class="card invoice-preview-card p-sm-12 p-6">
            {{-- Header Skeleton --}}
            <div class="card-body invoice-preview-header rounded-4 p-6" style="background-color: rgba(38, 43, 64, .03);">
                <div
                    class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column text-heading align-items-xl-center align-items-md-start align-items-sm-center flex-wrap gap-6">
                    <div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="skeleton skeleton-avatar me-3"
                                style="width: 40px; height: 40px; border-radius: .375rem;"></div>
                            <div>
                                <div class="skeleton skeleton-text mb-2" style="width: 200px; height: 1.2rem;"></div>
                                <div class="skeleton skeleton-text" style="width: 250px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-xl">
                        <div class="skeleton skeleton-text mb-2" style="width: 150px; height: 1.2rem;"></div>
                        <div class="skeleton skeleton-text mb-2" style="width: 200px;"></div>
                        <div class="skeleton skeleton-text mb-1" style="width: 220px;"></div>
                        <div class="skeleton skeleton-badge" style="width: 100px;"></div>
                    </div>
                </div>
            </div>

            {{-- Body Skeleton --}}
            <div class="card-body py-6 px-0">
                <div class="d-flex justify-content-between flex-wrap gap-6">
                    <div>
                        <div class="skeleton skeleton-text mb-3" style="width: 150px; height: 1.1rem;"></div>
                        <div class="skeleton skeleton-text mb-2" style="width: 180px;"></div>
                        <div class="skeleton skeleton-text mb-2" style="width: 160px;"></div>
                        <div class="skeleton skeleton-text" style="width: 170px;"></div>
                    </div>
                    <div class="text-end">
                        <div class="skeleton skeleton-text mb-3" style="width: 100px; height: 1.1rem;"></div>
                        <div class="skeleton skeleton-text mb-2" style="width: 130px;"></div>
                        <div class="skeleton skeleton-text" style="width: 150px;"></div>
                    </div>
                </div>
            </div>

            {{-- Table Skeleton --}}
            <div class="table-responsive border rounded-4">
                <table class="table m-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="skeleton skeleton-text" style="width: 80px;"></div>
                            </th>
                            <th class="text-end">
                                <div class="skeleton skeleton-text ms-auto" style="width: 100px;"></div>
                            </th>
                            <th class="text-center">
                                <div class="skeleton skeleton-text mx-auto" style="width: 90px;"></div>
                            </th>
                            <th class="text-end">
                                <div class="skeleton skeleton-text ms-auto" style="width: 120px;"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="skeleton skeleton-text w-100"></div>
                            </td>
                            <td class="text-end">
                                <div class="skeleton skeleton-text ms-auto" style="width: 80%;"></div>
                            </td>
                            <td class="text-center">
                                <div class="skeleton skeleton-text mx-auto" style="width: 70%;"></div>
                            </td>
                            <td class="text-end">
                                <div class="skeleton skeleton-text ms-auto" style="width: 90%;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Notes & Total Skeleton --}}
            <div class="table-responsive">
                <table class="table m-0 table-borderless">
                    <tbody>
                        <tr>
                            <td class="align-top px-0 py-6">
                                <div class="skeleton skeleton-text mb-2" style="width: 80px;"></div>
                                <div class="skeleton skeleton-text" style="width: 250px;"></div>
                            </td>
                            <td class="text-end pe-0 py-6 w-px-100">
                                <div class="skeleton skeleton-text ms-auto" style="width: 50px; height: 1.2rem;"></div>
                            </td>
                            <td class="text-end px-0 py-6 w-px-100">
                                <div class="skeleton skeleton-text ms-auto" style="width: 100px; height: 1.2rem;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Proof Skeleton --}}
            <hr class="mt-6 mb-6" />
            <div class="card-body p-0">
                <div class="skeleton skeleton-text mb-4" style="width: 120px; height: 1.1rem;"></div>
                <div class="skeleton" style="width: 400px; height: 250px; border-radius: .375rem;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-4 col-12 invoice-actions">
        <div class="card">
            <div class="card-body">
                <div class="skeleton skeleton-button mb-4" style="height: 38px;"></div>
                <div class="skeleton skeleton-button mb-4" style="height: 38px;"></div>
                <div class="skeleton skeleton-button" style="height: 38px;"></div>
            </div>
        </div>
    </div>
</div>
