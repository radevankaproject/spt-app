@extends('layouts.contentNavbarLayout')

@section('title', 'Peta Wilayah Parkir')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin="" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
    .parking-map-container {
        height: 75vh;
        width: 100%;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        z-index: 1;
    }
    
    /* Paksa Font Template ke Leaflet */
    .leaflet-container, 
    .leaflet-popup-content,
    .custom-popup {
        font-family: 'Outfit', sans-serif !important;
    }

    /* Premium Custom Popup */
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        border: none;
    }
    .custom-popup .leaflet-popup-tip {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .custom-popup .leaflet-popup-content {
        margin: 0;
        width: 320px !important;
    }
    .custom-popup .popup-header {
        background: linear-gradient(135deg, #666cff 0%, #9499ff 100%);
        color: white;
        padding: 1.25rem;
        position: relative;
    }
    .custom-popup .popup-header img {
        border: 2px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-popup .popup-header h6 {
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .custom-popup .popup-header .badge-status {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-weight: 500;
        backdrop-filter: blur(4px);
    }
    .custom-popup .popup-body {
        padding: 1.25rem;
        background-color: #fff;
    }
    .custom-popup .info-item {
        margin-bottom: 0.85rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .custom-popup .info-item i {
        margin-top: 0.1rem;
        font-size: 1.25rem;
        color: #666cff;
        background: rgba(102, 108, 255, 0.1);
        padding: 0.4rem;
        border-radius: 0.5rem;
    }
    .custom-popup .info-content p {
        margin-bottom: 0;
        font-size: 0.8rem;
        color: #a1acb8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .custom-popup .info-content span {
        font-weight: 600;
        color: #566a7f;
        font-size: 0.95rem;
        display: block;
    }
    
    .filter-card {
        transition: all 0.3s ease;
    }
    .filter-card:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(165, 163, 174, 0.2) !important;
    }
    
    /* Red Marker Cluster */
    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background-color: rgba(255, 76, 81, 0.6) !important;
    }
    .marker-cluster-small div,
    .marker-cluster-medium div,
    .marker-cluster-large div {
        background-color: rgba(255, 76, 81, 0.9) !important;
        color: white !important;
    }
</style>
@endsection



@section('content')

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card bg-primary text-white border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-white fw-bold">{{ number_format($totalParkingLocations, 0, ',', '.') }}</h4>
                    <span class="small text-white-50">Total Seluruh Lokasi Parkir</span>
                </div>
                <div class="avatar avatar-md bg-white text-primary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri icon-base ti tabler-map-pin fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card bg-success text-white border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-white fw-bold">{{ number_format($totalMappedLocations, 0, ',', '.') }}</h4>
                    <span class="small text-white-50">Lokasi Dengan Koordinat</span>
                </div>
                <div class="avatar avatar-md bg-white text-success rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri icon-base ti tabler-live-view fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-white fw-bold">{{ number_format($totalParkingLocations - $totalMappedLocations, 0, ',', '.') }}</h4>
                    <span class="small text-white-50">Lokasi Belum Terpetakan</span>
                </div>
                <div class="avatar avatar-md bg-white text-info rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri icon-base ti tabler-map-pin-minus fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm filter-card">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="ri icon-base ti tabler-search me-1"></i> Cari Nama Lokasi</label>
                        <input type="text" id="filterName" class="form-control" placeholder="Ketik nama lokasi parkir...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="ri icon-base ti tabler-map-pin-2 me-1"></i> Filter Zona</label>
                        <select id="filterZone" class="form-select select2">
                            <option value="">-- Semua Zona --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="ri icon-base ti tabler-route me-1"></i> Filter Ruas Jalan</label>
                        <select id="filterRoad" class="form-select select2">
                            <option value="">-- Semua Ruas Jalan --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="ri icon-base ti tabler-user-star me-1"></i> Filter Koordinator</label>
                        <select id="filterCoordinator" class="form-select select2">
                            <option value="">-- Semua Koordinator --</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <div>
                    <h5 class="card-title mb-1"><i class="ri icon-base ti tabler-map-pin text-primary me-2"></i>Peta Wilayah Parkir Kota Pekanbaru</h5>
                    <p class="text-muted mb-0 small">Menampilkan seluruh titik parkir yang terdata beserta status kerjasamanya.</p>
                </div>
                <div>
                    <span class="badge bg-danger rounded-pill px-3 py-2 text-white shadow-sm" id="resultCount"><i class="ri icon-base ti tabler-map-pin-filled me-1"></i> Memuat Data...</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="parkingMap" class="parking-map-container"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin="" defer></script>
@vite(["resources/assets/vendor/libs/select2/select2.js"])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        let map;
        let markersCluster;
        let allParkingData = @json($parkingLocations);

        // Populate Filter Options
        function populateFilters() {
            let zones = new Set();
            let coordinators = new Set();

            allParkingData.forEach(loc => {
                if (loc.road_section && loc.road_section.zone) {
                    zones.add(loc.road_section.zone);
                }
                
                if (loc.agreements && loc.agreements.length > 0 && loc.agreements[0].field_coordinator && loc.agreements[0].field_coordinator.user) {
                    coordinators.add(loc.agreements[0].field_coordinator.user.name);
                }
            });

            const zoneSelect = $('#filterZone');
            const coordSelect = $('#filterCoordinator');

            Array.from(zones).sort().forEach(z => zoneSelect.append(new Option(z, z)));
            Array.from(coordinators).sort().forEach(c => coordSelect.append(new Option(c, c)));

            // Init Select2
            $('.select2').select2({
                placeholder: 'Pilih salah satu',
                allowClear: true
            });
            
            populateRoads();
        }

        function populateRoads() {
            const roadSelect = $('#filterRoad');
            const selectedZone = $('#filterZone').val();
            
            roadSelect.empty();
            roadSelect.append(new Option('-- Semua Ruas Jalan --', ''));
            
            let roads = new Set();
            allParkingData.forEach(loc => {
                if (loc.road_section && loc.road_section.name) {
                    if (!selectedZone || loc.road_section.zone === selectedZone) {
                        roads.add(loc.road_section.name);
                    }
                }
            });
            
            Array.from(roads).sort().forEach(r => roadSelect.append(new Option(r, r)));
        }

        // Initialize Map
        function initMap() {
            const pekanbaruLat = 0.5333;
            const pekanbaruLng = 101.4500;
            
            const bounds = [
                [0.3500, 101.3000],
                [0.6800, 101.6000] 
            ];
            
            map = L.map('parkingMap', {
                center: [pekanbaruLat, pekanbaruLng],
                zoom: 13,
                maxBounds: bounds,
                maxBoundsViscosity: 1.0,
                minZoom: 11
            });
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            markersCluster = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50,
            });

            map.addLayer(markersCluster);
            renderMarkers(allParkingData);
        }

        // Render Markers based on data
        function renderMarkers(data) {
            if (!markersCluster) return;
            markersCluster.clearLayers();

            const activeIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            const inactiveIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            let count = 0;

            data.forEach(function(location) {
                if (location.latitude && location.longitude) {
                    count++;
                    
                    let hasActiveAgreement = location.agreements && location.agreements.length > 0;
                    let icon = hasActiveAgreement ? activeIcon : inactiveIcon;
                    let statusBadge = hasActiveAgreement 
                        ? `<span class="badge badge-status rounded-pill"><i class="ri icon-base ti tabler-check me-1"></i>PKS Aktif</span>` 
                        : `<span class="badge badge-status rounded-pill" style="background-color: rgba(0,0,0,0.3); border-color: transparent;"><i class="ri icon-base ti tabler-x me-1"></i>Belum PKS</span>`;
                    
                    let coordinatorName = hasActiveAgreement && location.agreements[0].field_coordinator && location.agreements[0].field_coordinator.user
                        ? location.agreements[0].field_coordinator.user.name 
                        : 'Belum Ada Mitra';

                    let roadName = location.road_section ? location.road_section.name : '-';
                    let zoneName = location.road_section && location.road_section.zone ? location.road_section.zone : '-';

                    let imageUrl = location.image 
                        ? `{{ asset('storage') }}/${location.image}` 
                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(location.name)}&background=random&color=fff&size=100`;

                    let popupContent = `
                        <div class="custom-popup">
                            <div class="popup-header d-flex align-items-center gap-3">
                                <img src="${imageUrl}" alt="Image" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1 text-truncate" style="max-width: 200px;" title="${location.name}">${location.name}</h6>
                                    ${statusBadge}
                                </div>
                            </div>
                            <div class="popup-body">
                                <div class="info-item">
                                    <i class="ri icon-base ti tabler-map-pin-range"></i>
                                    <div class="info-content">
                                        <p>Zona & Ruas Jalan</p>
                                        <span class="text-truncate" style="max-width: 200px;" title="${zoneName} - ${roadName}">${zoneName} &bull; ${roadName}</span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <i class="ri icon-base ti tabler-user-star"></i>
                                    <div class="info-content">
                                        <p>Koordinator Lapangan</p>
                                        <span class="text-truncate" style="max-width: 200px;" title="${coordinatorName}">${coordinatorName}</span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <i class="ri icon-base ti tabler-currency-dollar"></i>
                                    <div class="info-content">
                                        <p>Potensi Setoran / Hari</p>
                                        <span class="text-success">Rp ${new Intl.NumberFormat('id-ID').format(location.daily_target_revenue || 0)}</span>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <a href="/masterdata/parking-locations/${location.id}" class="btn btn-primary rounded-pill shadow-sm">
                                        <i class="ri icon-base ti tabler-eye me-1"></i> Detail Lokasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;

                    let marker = L.marker([location.latitude, location.longitude], { icon: icon })
                        .bindPopup(popupContent, { className: 'custom-popup' });
                    
                    markersCluster.addLayer(marker);
                }
            });

            document.getElementById('resultCount').innerHTML = `<i class="ri icon-base ti tabler-map-pin-filled me-1"></i> ${count} Titik Ditemukan`;
        }

        // Apply Filters
        function applyFilters() {
            const fName = document.getElementById('filterName').value.toLowerCase();
            const fZone = $('#filterZone').val();
            const fRoad = $('#filterRoad').val();
            const fCoord = $('#filterCoordinator').val();

            const filtered = allParkingData.filter(loc => {
                let matchName = loc.name.toLowerCase().includes(fName);
                
                let matchZone = true;
                if (fZone) {
                    matchZone = loc.road_section && loc.road_section.zone === fZone;
                }

                let matchRoad = true;
                if (fRoad) {
                    matchRoad = loc.road_section && loc.road_section.name === fRoad;
                }

                let matchCoord = true;
                if (fCoord) {
                    let cName = (loc.agreements && loc.agreements.length > 0 && loc.agreements[0].field_coordinator && loc.agreements[0].field_coordinator.user) 
                        ? loc.agreements[0].field_coordinator.user.name : '';
                    matchCoord = cName === fCoord;
                }

                return matchName && matchZone && matchRoad && matchCoord;
            });

            renderMarkers(filtered);
        }

        // Event Listeners for Filters
        document.getElementById('filterName').addEventListener('input', applyFilters);
        $('#filterCoordinator').on('change', applyFilters);

        $('#filterZone').on('change', function() {
            populateRoads();
            applyFilters();
        });

        $('#filterRoad').on('change', function() {
            applyFilters();
            
            const selectedRoadName = $(this).val();
            if (selectedRoadName) {
                // Prioritaskan latitude/longitude utama dari Master Ruas Jalan
                const roadData = allParkingData.find(loc => loc.road_section && loc.road_section.name === selectedRoadName && loc.road_section.latitude && loc.road_section.longitude);
                
                if (roadData) {
                    map.flyTo([roadData.road_section.latitude, roadData.road_section.longitude], 16, {
                        animate: true,
                        duration: 1.5
                    });
                } else {
                    // Fallback: Jika ruas jalan belum punya lat/long tengah, zoom fit ke semua titik parkirnya
                    const filteredLocations = allParkingData.filter(loc => loc.road_section && loc.road_section.name === selectedRoadName && loc.latitude && loc.longitude);
                    if (filteredLocations.length > 0) {
                        const latLngs = filteredLocations.map(loc => [loc.latitude, loc.longitude]);
                        map.flyToBounds(latLngs, { animate: true, duration: 1.5, maxZoom: 16, padding: [50, 50] });
                    }
                }
            } else {
                // Reset map kembali ke Pekanbaru
                map.flyTo([0.5333, 101.4500], 13, { animate: true, duration: 1.5 });
            }
        });

        // Run Initializers
        populateFilters();
        initMap();

        window.addEventListener('load', function() {
            setTimeout(function() {
                if (map) {
                    map.invalidateSize();
                }
            }, 400); 
        });
    });
</script>
@endsection
