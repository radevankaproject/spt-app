{{-- resources/views/layouts/partials/_skeleton-agreement-history-index.blade.php --}}

{{-- Skeleton untuk Judul Halaman & Breadcrumb --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 300px; height: 1.75rem;"></div>
    <div class="skeleton skeleton-text" style="width: 220px; height: 1rem;"></div>
</div>

{{-- Skeleton untuk Card Filter --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="skeleton skeleton-text" style="width: 150px; height: 1.5rem;"></div>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-10">
                <div class="skeleton skeleton-text mb-2" style="width: 200px;"></div>
                <div class="skeleton skeleton-input"></div>
            </div>
            <div class="col-md-2">
                {{-- Samakan tinggi dengan skeleton-input --}}
                <div class="skeleton skeleton-button" style="height: 54px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Skeleton untuk Hasil Timeline --}}
<div class="row overflow-hidden">
    <div class="col-12">
        <div class="skeleton skeleton-text mx-auto mb-6" style="width: 40%; height: 1.25rem;"></div>
        <ul class="timeline timeline-center">
            {{-- Skeleton Timeline Item 1 (Kiri) --}}
            <li class="timeline-item timeline-item-left">
                <span class="timeline-indicator skeleton"></span>
                <div class="timeline-event card p-0 skeleton">
                    <div class="card-header">
                        <div class="skeleton skeleton-text w-75"></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="skeleton skeleton-avatar avatar-xs me-2"></div>
                            <div class="skeleton skeleton-text" style="width: 120px;"></div>
                        </div>
                    </div>
                </div>
            </li>
            {{-- Skeleton Timeline Item 2 (Kanan) --}}
            <li class="timeline-item timeline-item-right">
                <span class="timeline-indicator skeleton"></span>
                <div class="timeline-event card p-0 skeleton">
                    <div class="card-header">
                        <div class="skeleton skeleton-text w-75"></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="skeleton skeleton-avatar avatar-xs me-2"></div>
                            <div class="skeleton skeleton-text" style="width: 120px;"></div>
                        </div>
                    </div>
                </div>
            </li>
            {{-- Skeleton Timeline Item 3 (Kiri) --}}
            <li class="timeline-item timeline-item-left">
                <span class="timeline-indicator skeleton"></span>
                <div class="timeline-event card p-0 skeleton">
                    <div class="card-header">
                        <div class="skeleton skeleton-text w-75"></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="skeleton skeleton-avatar avatar-xs me-2"></div>
                            <div class="skeleton skeleton-text" style="width: 120px;"></div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
