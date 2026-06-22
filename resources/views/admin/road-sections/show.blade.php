@extends('layouts.contentNavbarLayout')

@section('title', 'Detail Ruas Jalan - ' . $roadSection->name)

@section('page-style')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin=""/>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .premium-map {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            z-index: 1;
        }
        .premium-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            color: #5d596c !important;
            border-bottom: 2px solid #e1e0e6;
        }
        .premium-table td {
            vertical-align: middle;
        }
        /* Custom Map Popup */
        .premium-leaflet-popup .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .premium-leaflet-popup .leaflet-popup-content {
            margin: 0;
            width: 280px !important;
        }
        .popup-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px 15px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .popup-body {
            padding: 15px;
        }
    </style>
@endsection

@section('content')
    {{-- Header & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <a href="{{ route('masterdata.road-sections.index') }}" class="text-muted fw-light text-decoration-none">Ruas Jalan /</a> 
                {{ $roadSection->name }}
            </h4>
            <p class="text-muted mb-0">Detail informasi dan peta titik parkir pada ruas jalan ini.</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a href="{{ route('masterdata.road-sections.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill">
                <i class="ti tabler-arrow-left me-1"></i> Kembali
            </a>
            @if(Auth::user()->role !== 'leader')
            <button type="button" class="btn btn-primary shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal{{ $roadSection->id }}">
                <i class="ti tabler-pencil me-1"></i> Edit Ruas
            </button>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Card Info Ruas Jalan --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100 glass-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-road icon-24px"></i></span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $roadSection->name }}</h5>
                            <span class="badge bg-label-info rounded-pill mt-1">{{ $roadSection->zone }}</span>
                        </div>
                    </div>
                    
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-map-pin text-primary me-2"></i>
                            <span class="fw-medium text-heading me-2">Koordinat Tengah:</span>
                            <span>{{ $roadSection->latitude && $roadSection->longitude ? $roadSection->latitude . ', ' . $roadSection->longitude : 'Belum Diatur' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-parking text-primary me-2"></i>
                            <span class="fw-medium text-heading me-2">Total Titik:</span>
                            <span class="badge bg-label-primary">{{ $totalLocations }} Lokasi</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti tabler-circle-check text-success me-2"></i>
                            <span class="fw-medium text-heading me-2">Tersedia:</span>
                            <span class="badge bg-label-success">{{ $availableLocations }} Lokasi</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ti tabler-circle-x text-danger me-2"></i>
                            <span class="fw-medium text-heading me-2">Tidak Tersedia:</span>
                            <span class="badge bg-label-danger">{{ $unavailableLocations }} Lokasi</span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        {{-- Daftar Lokasi Parkir --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100 glass-card border-0">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <h5 class="card-title mb-0 fw-bold">Daftar Titik Lokasi Parkir</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
                        <table class="table premium-table table-hover mb-0">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Nama Lokasi & Tarif</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="35%">Info Perjanjian Terikat</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($roadSection->parkingLocations as $index => $location)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $location->name }}</span>
                                                <span class="text-muted small mt-1">
                                                    <i class="ti tabler-cash me-1 text-success"></i> 
                                                    Rp {{ number_format($location->daily_deposit, 0, ',', '.') }} / hari
                                                </span>
                                                @if($location->latitude && $location->longitude)
                                                    <small class="text-info mt-1"><i class="ti tabler-map-pin me-1"></i> Terpetakan</small>
                                                @else
                                                    <small class="text-warning mt-1"><i class="ti tabler-alert-circle me-1"></i> Belum Dipetakan</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = $location->status == 'tersedia' ? 'bg-label-success' : 'bg-label-danger';
                                            @endphp
                                            <span class="badge rounded-pill {{ $statusClass }} fw-bold">
                                                {{ strtoupper(str_replace('_', ' ', $location->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($location->status == 'tidak_tersedia' && $location->agreements->isNotEmpty())
                                                @php
                                                    $activeAgreement = $location->agreements->first();
                                                    $cName = $activeAgreement->fieldCoordinator->user->name ?? 'N/A';
                                                    $cAvatar = ($activeAgreement->fieldCoordinator->user && $activeAgreement->fieldCoordinator->user->img)
                                                        ? asset('storage/'.$activeAgreement->fieldCoordinator->user->img)
                                                        : "https://ui-avatars.com/api/?name=" . urlencode($cName) . "&background=random&color=fff&size=24&rounded=true&bold=true";
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $cAvatar }}" alt="Avatar" class="rounded-circle shadow-sm me-3" width="36" height="36" style="object-fit: cover;">
                                                    <div>
                                                        <a href="{{ route('masterdata.agreements.show', $activeAgreement->id) }}" class="fw-bold text-primary d-block mb-1">
                                                            {{ $activeAgreement->agreement_number }}
                                                        </a>
                                                        <small class="text-muted d-block"><i class="ti tabler-user me-1"></i> {{ $cName }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic"><i class="ti tabler-minus me-1"></i> Belum Terikat</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('masterdata.parking-locations.show', $location->id) }}" class="btn btn-sm btn-icon btn-text-info rounded-pill" data-bs-toggle="tooltip" title="Detail Lokasi">
                                                <i class="ti tabler-eye icon-22px"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png') }}" width="120" class="mb-3 opacity-50" alt="No Data">
                                            <h6 class="fw-bold text-dark mb-1">Belum Ada Titik Lokasi</h6>
                                            <p class="text-muted small">Ruas jalan ini belum memiliki titik lokasi parkir yang terdaftar.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card glass-card border-0">
                <div class="card-header d-flex justify-content-between align-items-center pb-2">
                    <h5 class="card-title mb-0 fw-bold"><i class="ti tabler-map-2 text-primary me-2"></i>Peta Persebaran Lokasi</h5>
                </div>
                <div class="card-body p-3">
                    <div id="roadSectionMap" class="premium-map w-100 min-h-500px" style="height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->role !== 'leader')
    {{-- Modal Edit Ruas Jalan --}}
    <div class="modal fade" id="editModal{{ $roadSection->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-label-primary border-bottom">
                    <h5 class="modal-title fw-bold">Edit Ruas Jalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('masterdata.road-sections.update', $roadSection->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body mt-2">
                        @if ($totalLocations > 0)
                            <div class="alert alert-warning d-flex align-items-center p-2 mb-4" role="alert">
                                <i class="ti tabler-info-circle me-2"></i>
                                <small>Zona tidak dapat diubah karena sudah memiliki titik parkir terdaftar.</small>
                            </div>
                        @endif

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="name" placeholder="Nama Ruas" value="{{ $roadSection->name }}" required />
                                    <label>Nama Ruas Jalan</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block mb-2">Pilih Zona</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input name="zone" class="form-check-input" type="radio" value="Zona 2" id="editZone2{{ $roadSection->id }}" {{ $roadSection->zone == 'Zona 2' ? 'checked' : '' }} {{ $totalLocations > 0 ? 'disabled' : '' }} />
                                        <label class="form-check-label" for="editZone2{{ $roadSection->id }}"> Zona 2 </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="zone" class="form-check-input" type="radio" value="Zona 3" id="editZone3{{ $roadSection->id }}" {{ $roadSection->zone == 'Zona 3' ? 'checked' : '' }} {{ $totalLocations > 0 ? 'disabled' : '' }} />
                                        <label class="form-check-label" for="editZone3{{ $roadSection->id }}"> Zona 3 </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="coordinates" placeholder="Contoh: 0.5333, 101.4500" value="{{ $roadSection->latitude && $roadSection->longitude ? $roadSection->latitude . ', ' . $roadSection->longitude : '' }}" />
                                    <label>Koordinat Titik Tengah (Latitude, Longitude)</label>
                                </div>
                                <small class="text-muted"><i class="ti tabler-info-circle"></i> Opsional. Dapat di-copy langsung dari Google Maps.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('page-script')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Map Initialization
            @php
                $mapDataArray = $roadSection->parkingLocations->map(function($loc) {
                    return [
                        'id' => $loc->id,
                        'name' => $loc->name,
                        'lat' => $loc->latitude,
                        'lng' => $loc->longitude,
                        'status' => $loc->status,
                        'daily_deposit' => $loc->daily_deposit,
                        'has_agreement' => $loc->agreements->isNotEmpty(),
                        'image' => $loc->image ? asset('storage/'.$loc->image) : null,
                        'url' => route('masterdata.parking-locations.show', $loc->id)
                    ];
                })->filter(function($loc) {
                    return !empty($loc['lat']) && !empty($loc['lng']);
                })->values();
            @endphp
            const mapData = @json($mapDataArray);

            // Titik Tengah: 
            // 1. Jika ruas jalan punya koordinat, gunakan itu.
            // 2. Jika tidak, gunakan rata-rata titik parkir.
            // 3. Jika kosong, default ke Pekanbaru.
            
            let centerLat = {{ $roadSection->latitude ?? 'null' }};
            let centerLng = {{ $roadSection->longitude ?? 'null' }};
            let defaultZoom = 15;

            if (centerLat === null || centerLng === null) {
                if (mapData.length > 0) {
                    // Hitung rata-rata jika ruas jalan belum ada koordinat
                    let sumLat = 0, sumLng = 0;
                    mapData.forEach(d => {
                        sumLat += parseFloat(d.lat);
                        sumLng += parseFloat(d.lng);
                    });
                    centerLat = sumLat / mapData.length;
                    centerLng = sumLng / mapData.length;
                } else {
                    // Default Pekanbaru
                    centerLat = 0.5028;
                    centerLng = 101.4474;
                    defaultZoom = 13;
                }
            }

            const map = L.map('roadSectionMap').setView([centerLat, centerLng], defaultZoom);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50,
            });

            // Icon Definitions
            const createIcon = (color) => {
                return L.divIcon({
                    className: 'custom-pin',
                    html: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin-filled" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="${color}" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M18.364 4.636a9 9 0 0 1 .203 12.519l-.203 .21l-4.243 4.242a3 3 0 0 1 -4.097 .135l-.144 -.135l-4.244 -4.243a9 9 0 0 1 12.728 -12.728zm-6.364 3.364a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" stroke-width="0" fill="${color}" />
                           </svg>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -36]
                });
            };

            const iconSuccess = createIcon('#28c76f'); // Hijau (Tersedia)
            const iconDanger = createIcon('#ea5455');  // Merah (Tidak Tersedia)

            const bounds = [];

            mapData.forEach(loc => {
                const isAvailable = loc.status === 'tersedia';
                const statusBadge = isAvailable 
                    ? '<span class="badge bg-success mb-2">TERSEDIA</span>'
                    : '<span class="badge bg-danger mb-2">TIDAK TERSEDIA</span>';
                
                const icon = isAvailable ? iconSuccess : iconDanger;

                // Media: Image or Pin if no image
                const mediaHtml = loc.image 
                    ? `<div style="height: 120px; width: 100%; overflow: hidden; position: relative;">
                           <img src="${loc.image}" style="width: 100%; height: 100%; object-fit: cover;" alt="Lokasi">
                       </div>`
                    : `<div style="height: 120px; width: 100%; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                           <i class="ti tabler-map-pin text-muted" style="font-size: 3rem;"></i>
                       </div>`;

                const agreementInfo = !isAvailable && loc.has_agreement 
                    ? `<small class="text-primary fw-bold d-block mt-2"><i class="ti tabler-file-check me-1"></i> Terikat PKS</small>`
                    : ``;

                const popupContent = `
                    <div class="premium-leaflet-popup">
                        ${mediaHtml}
                        <div class="popup-body">
                            ${statusBadge}
                            <h6 class="fw-bold mb-1 text-dark">${loc.name}</h6>
                            <p class="text-muted small mb-3 mb-0">
                                <i class="ti tabler-cash me-1"></i> Rp ${parseInt(loc.daily_deposit).toLocaleString('id-ID')} / hari
                            </p>
                            ${agreementInfo}
                            <div class="mt-3 text-center">
                                <a href="${loc.url}" class="btn btn-sm btn-primary w-100 rounded-pill">Detail Lokasi</a>
                            </div>
                        </div>
                    </div>
                `;

                const marker = L.marker([parseFloat(loc.lat), parseFloat(loc.lng)], {icon: icon})
                    .bindPopup(popupContent, {
                        className: 'premium-leaflet-popup'
                    });
                
                markers.addLayer(marker);
                bounds.push([parseFloat(loc.lat), parseFloat(loc.lng)]);
            });

            map.addLayer(markers);

            // Sesuaikan zoom agar semua marker terlihat jika ruas jalan tidak punya koordinat
            if ({{ $roadSection->latitude ? 'false' : 'true' }} && bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
            }
        });
    </script>
@endsection
