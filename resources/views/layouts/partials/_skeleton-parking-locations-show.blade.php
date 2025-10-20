{{-- resources/views/layouts/partials/_skeleton-parking-locations-show.blade.php --}}

{{-- 1. Kerangka untuk Judul & Tombol Kembali --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="skeleton skeleton-text" style="width: 250px; height: 28px;"></div>
    <div class="skeleton skeleton-button" style="width: 150px; height: 38px;"></div>
</div>

<div class="row g-6">
    {{-- ✅ KOLOM KIRI: INFORMASI LOKASI & PKS --}}
    <div class="col-lg-5">
        {{-- Kartu Informasi Lokasi --}}
        <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="skeleton skeleton-text" style="width: 150px;"></div>
                <div class="skeleton skeleton-button" style="width: 60px; height: 32px;"></div>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @for ($i = 0; $i < 5; $i++)
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 30%;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 50%;"></div>
                        </li>
                    @endfor
                </ul>
            </div>
        </div>

        {{-- Kartu Informasi PKS --}}
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 250px;"></div>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @for ($i = 0; $i < 4; $i++)
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 35%;"></div>
                            <div class="skeleton skeleton-text skeleton-text-sm" style="width: 45%;"></div>
                        </li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>

    {{-- ✅ KOLOM KANAN: PETA LOKASI --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 120px;"></div>
            </div>
            <div class="card-body">
                <div class="skeleton rounded-3" style="height: 450px;"></div>
                <div class="d-flex justify-content-between mt-3">
                    <div class="skeleton skeleton-text skeleton-text-sm" style="width: 150px;"></div>
                    <div class="skeleton skeleton-text skeleton-text-sm" style="width: 150px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ BARIS BAWAH: DOKUMEN PENDUKUNG --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="skeleton skeleton-text" style="width: 200px;"></div>
            </div>
            <div class="card-body">
                <div class="row g-6">
                    <div class="col-md-6">
                        <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 100px;"></div>
                        <div class="skeleton rounded-3" style="height: 600px;"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton skeleton-text skeleton-text-sm mb-2" style="width: 120px;"></div>
                        <div class="skeleton rounded-3" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
