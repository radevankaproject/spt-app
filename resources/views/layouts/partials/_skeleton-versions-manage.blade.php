{{-- resources/views/layouts/partials/_skeleton-versions-manage.blade.php --}}

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 28px;"></div>
    <div class="skeleton skeleton-text" style="width: 180px;"></div>
</div>

<div class="row g-6">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 200px;"></div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-input"></div>
                    </div>
                    <div class="col-12">
                        <div class="skeleton skeleton-input" style="height: 200px;"></div>
                    </div>
                </div>
                <div class="pt-6 text-end">
                    <div class="skeleton skeleton-button" style="width: 120px; height: 38px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 150px;"></div>
            </div>
            <div class="card-body">
                @for ($i = 0; $i < 3; $i++)
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="skeleton skeleton-text" style="width: 120px; height: 1.1rem;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 100px;"></div>
                        </div>
                        <div class="mt-3">
                            <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 90%;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 80%;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 85%;"></div>
                        </div>
                    </div>
                @endfor
                <div class="mt-4 d-flex justify-content-end">
                    <div class="skeleton skeleton-text" style="width: 200px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
