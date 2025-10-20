{{-- resources/views/layouts/partials/_skeleton-field-coordinator-show.blade.php --}}

<div class="row">
    {{-- ✅ KOLOM KIRI: PROFIL & KTP --}}
    <div class="col-xl-4 col-lg-5 col-md-5">
        {{-- Kartu Profil --}}
        <div class="card mb-6">
            <div class="card-body pt-12">
                <div class="d-flex align-items-center flex-column">
                    <div class="skeleton skeleton-avatar-lg mb-4"></div>
                    <div class="skeleton skeleton-text mb-2" style="width: 60%; height: 1.25rem;"></div>
                    <div class="skeleton skeleton-badge mb-6"></div>
                </div>
                <div class="d-flex justify-content-around flex-wrap my-6">
                    <div class="d-flex align-items-center me-5 gap-4">
                        <div class="skeleton skeleton-icon" style="width: 42px; height: 42px;"></div>
                        <div>
                            <div class="skeleton skeleton-text" style="width: 30px; height: 1.2rem;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm mt-1" style="width: 70px;"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="skeleton skeleton-icon" style="width: 42px; height: 42px;"></div>
                        <div>
                            <div class="skeleton skeleton-text" style="width: 30px; height: 1.2rem;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm mt-1" style="width: 80px;"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton skeleton-text mb-4"
                    style="width: 100px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;"></div>
                <ul class="list-unstyled mb-6">
                    @for ($i = 0; $i < 5; $i++)
                        <li class="mb-3">
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: {{ rand(70, 90) }}%;">
                            </div>
                        </li>
                    @endfor
                </ul>
                <div class="d-flex justify-content-center">
                    <div class="skeleton skeleton-button" style="width: 120px; height: 38px;"></div>
                </div>
            </div>
        </div>
        {{-- Kartu KTP --}}
        <div class="card">
            <div class="card-body">
                <div class="skeleton skeleton-text mb-4"
                    style="width: 100px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;"></div>
                <div class="skeleton rounded-3" style="height: 150px;"></div>
            </div>
        </div>
    </div>

    {{-- ✅ KOLOM KANAN: TABEL LOKASI & SETORAN --}}
    <div class="col-xl-8 col-lg-7 col-md-7">
        {{-- Tabel Lokasi Parkir --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 250px;"></div>
            </div>
            <div class="table-responsive text-nowrap">
                {{-- Skeleton Tabel --}}
                @include('layouts.partials._skeleton-table', ['rows' => 4, 'cols' => 3])
            </div>
        </div>
        {{-- Tabel Riwayat Setoran --}}
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 280px;"></div>
            </div>
            <div class="table-responsive text-nowrap">
                {{-- Skeleton Tabel --}}
                @include('layouts.partials._skeleton-table', ['rows' => 5, 'cols' => 3])
            </div>
        </div>
    </div>
</div>
