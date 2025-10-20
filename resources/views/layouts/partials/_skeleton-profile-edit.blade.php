{{-- resources/views/layouts/partials/_skeleton-profile-edit.blade.php --}}

<div class="row">
    {{-- Navigasi Tabs Skeleton --}}
    <div class="col-md-12">
        <div class="card mb-6">
            <div class="card-body">
                <div class="d-flex justify-content-start gap-4 flex-wrap">
                    <div class="skeleton skeleton-text" style="width: 120px; height: 1.5rem;"></div>
                    <div class="skeleton skeleton-text" style="width: 150px; height: 1.5rem;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 1.5rem;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Konten Utama Form Skeleton --}}
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <div class="skeleton skeleton-text" style="width: 200px; height: 1.5rem;"></div>
            </div>
            <div class="card-body py-6">
                <div class="d-flex align-items-start align-items-sm-center gap-6">
                    {{-- Avatar & Upload/Reset Skeleton --}}
                    <div class="skeleton skeleton-avatar rounded-circle" style="width: 100px; height: 100px;"></div>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="skeleton skeleton-button" style="width: 120px; height: 38px;"></div>
                        <div class="skeleton skeleton-button" style="width: 100px; height: 38px;"></div>
                    </div>
                </div>

                <hr class="my-6" />

                {{-- Form Fields Skeleton --}}
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="skeleton skeleton-text mb-2" style="width: 80px;"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="skeleton skeleton-text mb-2" style="width: 80px;"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="skeleton skeleton-text mb-2" style="width: 80px;"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="skeleton skeleton-text mb-2" style="width: 80px;"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                </div>
                <div class="mt-6 d-flex justify-content-end">
                    <div class="skeleton skeleton-button" style="width: 150px; height: 38px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
