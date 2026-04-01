<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 placeholder-glow">
    <div class="placeholder col-3 rounded" style="height: 28px;"></div>
    <div class="placeholder col-1 rounded" style="height: 38px;"></div>
</div>

<div class="card border-0 shadow-sm placeholder-glow">
    <div class="card-header border-bottom pb-3">
        <div class="placeholder col-3 mb-2 rounded d-block" style="height: 20px;"></div>
        <div class="placeholder col-2 rounded d-block"></div>
    </div>
    <div class="card-body pt-4">
        <div class="row g-4">
            {{-- Jenis Pengajuan (Radio) --}}
            <div class="col-md-12">
                <div class="placeholder col-2 mb-3 rounded" style="height: 18px;"></div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="placeholder w-100 rounded" style="height: 85px;"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="placeholder w-100 rounded" style="height: 85px;"></div>
                    </div>
                </div>
            </div>

            {{-- Form Inputs --}}
            <div class="col-md-12">
                <div class="row g-4 p-3 bg-lighter rounded">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="col-md-6">
                            <div class="placeholder col-4 mb-2 rounded" style="height: 16px;"></div>
                            <div class="placeholder w-100 rounded" style="height: 38px;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Textarea Alasan --}}
            <div class="col-md-12">
                <div class="placeholder col-2 mb-2 rounded" style="height: 16px;"></div>
                <div class="placeholder w-100 rounded" style="height: 100px;"></div>
            </div>
        </div>

        <div class="pt-4 text-end">
            <div class="placeholder col-2 rounded" style="height: 40px;"></div>
        </div>
    </div>
</div>
