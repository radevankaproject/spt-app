<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen PKS</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .card-header-premium {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        /* Checkmark Animation */
        .checkmark__circle, .cross__circle {
            stroke-dasharray: 166; stroke-dashoffset: 166;
            stroke-width: 3; stroke-miterlimit: 10;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark__circle { stroke: #28c76f; }
        .cross__circle { stroke: #ea5455; }
        
        .checkmark, .cross {
            width: 80px; height: 80px;
            border-radius: 50%; display: block;
            stroke-width: 3; stroke: #fff; stroke-miterlimit: 10;
            margin: 0 auto 1.5rem auto;
        }
        .checkmark {
            box-shadow: inset 0px 0px 0px #28c76f;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .cross {
            box-shadow: inset 0px 0px 0px #ea5455;
            animation: fill-red .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark__check, .cross__line {
            transform-origin: 50% 50%;
            stroke-dasharray: 48; stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 40px #28c76f; } }
        @keyframes fill-red { 100% { box-shadow: inset 0px 0px 0px 40px #ea5455; } }
        
        /* Detail info */
        .info-list {
            list-style: none; padding: 0; margin: 0;
        }
        .info-list li {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-label { color: #6e6b7b; font-size: 0.9rem; display: flex; align-items: center; }
        .info-value { font-weight: 600; color: #5e5873; text-align: right; }
        
        /* Accordion Custom */
        .accordion-premium {
            padding: 1.5rem;
            background: #f8f9fa;
        }
        .accordion-item {
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 12px !important;
            margin-bottom: 0.5rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .accordion-button {
            font-weight: 600;
            color: #5e5873;
            padding: 1rem 1.25rem;
            box-shadow: none !important;
            background: transparent !important;
        }
        .accordion-button:not(.collapsed) {
            color: #696cff;
            background: rgba(105, 108, 255, 0.05) !important;
        }
        .accordion-button::after {
            background-size: 1rem;
        }
        .location-item {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex; align-items: center;
            transition: all 0.2s ease;
        }
        .location-item:last-child { border-bottom: none; }
        .location-item.has-link:hover {
            background-color: rgba(105, 108, 255, 0.04);
        }
        .location-item.has-link:hover .location-icon {
            transform: scale(1.05);
        }
        .location-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
            display: flex; align-items: center; justify-content: center;
            margin-right: 1rem;
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body>
    <div class="premium-card">
        @if ($agreement)
            <div class="card-header-premium">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
                <h3 class="fw-bold mb-1" style="color: #28c76f;">Dokumen Terverifikasi</h3>
                <p class="text-muted mb-0">PKS ini terverifikasi sebagai dokumen asli yang sah.</p>
            </div>
            
            <ul class="info-list">
                <li>
                    <span class="info-label"><i class="ti ti-file-certificate me-2 text-primary fs-5"></i> Nomor PKS</span>
                    <span class="info-value text-primary">{{ $agreement->agreement_number }}</span>
                </li>
                <li>
                    <span class="info-label"><i class="ti ti-user me-2 text-info fs-5"></i> Koordinator</span>
                    <span class="info-value">{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}</span>
                </li>
                <li>
                    <span class="info-label"><i class="ti ti-activity me-2 text-warning fs-5"></i> Status</span>
                    <span class="info-value">
                        @if ($agreement->status == 'active')
                            <span class="badge bg-success rounded-pill px-3 py-2">Aktif s.d {{ $agreement->end_date->translatedFormat('d M Y') }}</span>
                        @elseif ($agreement->status == 'expired')
                            <span class="badge bg-danger rounded-pill px-3 py-2">Expired pada {{ $agreement->end_date->translatedFormat('d M Y') }}</span>
                        @elseif ($agreement->status == 'pending_renewal')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending (Berakhir {{ $agreement->end_date->translatedFormat('d M Y') }})</span>
                        @elseif ($agreement->status == 'terminated')
                            <span class="badge bg-dark rounded-pill px-3 py-2">Diputus pada {{ $agreement->updated_at->translatedFormat('d M Y') }}</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($agreement->status) }}</span>
                        @endif
                    </span>
                </li>
            </ul>

            @if ($agreement->status !== 'expired')
                <div class="accordion-premium">
                    <h6 class="fw-bold text-muted mb-3"><i class="ti ti-map-pins me-1"></i> Daftar Lokasi Parkir ({{ $agreement->activeParkingLocations->count() }})</h6>
                    
                    @php
                        // Grouping parking locations by road section name
                        $groupedLocations = $agreement->activeParkingLocations->groupBy(function($loc) {
                            return $loc->roadSection->name ?? 'Tanpa Ruas Jalan';
                        });
                    @endphp

                    <div class="accordion" id="locationsAccordion">
                        @forelse ($groupedLocations as $roadName => $locations)
                            @php
                                $collapseId = 'collapse' . Str::slug($roadName);
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                        <i class="ti ti-road me-2 text-primary"></i> 
                                        {{ $roadName }}
                                        <span class="badge bg-primary rounded-pill ms-auto">{{ $locations->count() }}</span>
                                    </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse" data-bs-parent="#locationsAccordion">
                                    <div class="accordion-body p-0">
                                        @foreach ($locations as $location)
                                            @php
                                                $hasCoords = $location->latitude && $location->longitude;
                                                $gmapsUrl = $hasCoords ? "https://maps.google.com/?q={$location->latitude},{$location->longitude}" : "#";
                                            @endphp
                                            <a href="{{ $gmapsUrl }}" 
                                               {{ $hasCoords ? 'target="_blank"' : '' }} 
                                               class="location-item text-decoration-none d-flex align-items-center {{ $hasCoords ? 'has-link' : '' }}" 
                                               style="{{ $hasCoords ? 'cursor: pointer;' : 'cursor: default;' }}">
                                               
                                                <div class="location-icon" style="overflow: hidden; flex-shrink: 0; {{ $location->image ? 'background: transparent; padding: 0;' : '' }}">
                                                    @if ($location->image)
                                                        <img src="{{ Storage::url($location->image) }}" alt="Img" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                                    @else
                                                        <i class="ti ti-map-pin"></i>
                                                    @endif
                                                </div>
                                                <div class="w-100 text-start">
                                                    <div class="fw-semibold text-dark d-flex align-items-center">
                                                        {{ $location->name }}
                                                        @if ($hasCoords)
                                                            <i class="ti ti-external-link ms-2 text-primary" style="font-size: 0.9rem;" title="Buka di Google Maps"></i>
                                                        @endif
                                                    </div>
                                                    <div class="small text-muted d-flex justify-content-between mt-1">
                                                        <span><i class="ti ti-cash me-1"></i> Rp {{ number_format($location->daily_deposit, 0, ',', '.') }}/hari</span>
                                                        <span class="badge bg-light text-dark border">{{ $location->roadSection->zone ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="ti ti-map-off fs-1 mb-2"></i>
                                <p class="mb-0">Tidak ada lokasi parkir aktif yang terhubung.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                @php
                    $renewalAgreement = \App\Models\Agreement::where('field_coordinator_id', $agreement->field_coordinator_id)
                        ->where('id', '>', $agreement->id)
                        ->whereIn('status', ['active', 'pending_renewal', 'pending'])
                        ->latest()
                        ->first();
                @endphp
                <div class="p-4 bg-white" style="border-radius: 0 0 1.5rem 1.5rem;">
                    <div class="alert alert-warning border-warning" role="alert" style="background-color: rgba(255, 159, 67, 0.1);">
                        <div class="text-center">
                            <i class="ti ti-alert-triangle fs-1 text-warning mb-2 d-block"></i>
                            <h5 class="fw-bold text-warning mb-1">PKS Telah Berakhir</h5>
                            <p class="mb-0 small text-muted">Daftar lokasi parkir tidak lagi ditampilkan karena masa berlaku dokumen ini telah habis.</p>
                        </div>
                        
                        @if($renewalAgreement)
                            <hr class="border-warning my-3 opacity-25">
                            <div class="text-center">
                                <p class="mb-2 text-dark fw-medium small">PKS ini telah diperpanjang ke dokumen baru:</p>
                                <a href="{{ route('public.agreement.verify', $renewalAgreement->verification_code) }}" class="btn btn-warning w-100 fw-bold shadow-sm" style="border-radius: 8px; background: #ff9f43; color: #fff; border: none;">
                                    <i class="ti ti-external-link me-1"></i> Lihat PKS Lanjutan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="text-center py-3 bg-light border-top" style="font-size: 0.8rem; color: #a1acb8;">
                Sistem Perparkiran Kota Pekanbaru &copy; {{ date('Y') }}
            </div>

        @else
            <div class="card-header-premium">
                <svg class="cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="cross__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="cross__line" fill="none" d="M16 16 36 36 M36 16 16 36" />
                </svg>
                <h3 class="fw-bold mb-1" style="color: #ea5455;">Verifikasi Gagal</h3>
                <p class="text-muted mb-0">Dokumen PKS tidak ditemukan atau tidak sah.</p>
            </div>
            <div class="p-4 text-center">
                <p class="mb-0 text-muted">Pastikan Anda memindai QR Code dari dokumen yang resmi diterbitkan oleh UPT Perparkiran Kota Pekanbaru.</p>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
