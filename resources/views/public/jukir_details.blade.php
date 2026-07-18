<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Juru Parkir - {{ $jukir->nama_jukir }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            color: #333;
        }
        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #0b78a9 0%, #1a3a7a 100%);
            z-index: 0;
        }
        .profile-header {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2.5rem 2rem 1.5rem;
            color: #fff;
        }
        .profile-img-container {
            width: 130px;
            height: 130px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            padding: 5px;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            position: relative;
        }
        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e9ecef;
        }
        .status-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #28a745;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .status-badge.expired {
            background: #dc3545;
        }
        .status-badge i {
            font-size: 16px;
            color: #fff;
        }
        .profile-name {
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.2rem;
            color: #1a3a7a;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
            margin-top: 1rem;
        }
        .profile-id {
            font-weight: 600;
            font-size: 0.95rem;
            color: #495057;
            letter-spacing: 1px;
            background: rgba(26, 58, 122, 0.08);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }
        
        .info-section {
            padding: 0 2rem 1.5rem;
            position: relative;
            z-index: 1;
        }
        .info-group {
            background: #fff;
            border-radius: 1.2rem;
            padding: 1.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: transform 0.2s ease;
        }
        .info-group:hover {
            transform: translateY(-2px);
        }
        .info-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(11, 120, 169, 0.1) 0%, rgba(26, 58, 122, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #0b78a9;
        }
        .info-icon i {
            font-size: 24px;
        }
        .info-content {
            flex-grow: 1;
        }
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-weight: 700;
            color: #343a40;
            font-size: 1.05rem;
            line-height: 1.3;
        }
        .info-value.highlight {
            color: #0b78a9;
        }
        
        .action-section {
            padding: 2rem;
            background: rgba(248, 249, 250, 0.9);
            border-top: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        .btn-report {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            width: 100%;
            box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-report:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(231, 76, 60, 0.4);
            color: #fff;
        }
        
        .footer-note {
            font-size: 0.85rem;
            color: #868e96;
            margin-top: 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="premium-card">
        <div class="profile-header">
            <div class="profile-img-container">
                @php
                    $isExpired = \Carbon\Carbon::parse($jukir->kta_end_date)->isPast();
                @endphp
                <img src="{{ $jukir->image ? asset('storage/' . $jukir->image) : asset('assets/img/avatars/1.png') }}" alt="{{ $jukir->nama_jukir }}" class="profile-img">
                <div class="status-badge {{ $isExpired ? 'expired' : '' }}" title="Status KTA: {{ $isExpired ? 'Kedaluwarsa' : 'Aktif' }}">
                    <i class="ti {{ $isExpired ? 'tabler-x' : 'tabler-check' }}"></i>
                </div>
            </div>
            <h1 class="profile-name">{{ $jukir->nama_jukir }}</h1>
            <div class="profile-id"><i class="ti tabler-id-badge-2 me-1"></i> ID: {{ $jukir->id_jukir }}</div>
        </div>

        <div class="info-section">
            
            <div class="info-group">
                <div class="info-icon">
                    <i class="ti tabler-shield-check"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Status KTA</div>
                    <div class="info-value">
                        @if($isExpired)
                            <span class="badge bg-danger rounded-pill px-3 shadow-sm py-2"><i class="ti tabler-x me-1"></i> Kedaluwarsa</span>
                        @else
                            <span class="badge bg-success rounded-pill px-3 shadow-sm py-2"><i class="ti tabler-check me-1"></i> Aktif & Resmi</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-icon">
                    <i class="ti tabler-map-pin-filled"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Titik Parkir</div>
                    <div class="info-value highlight">{{ $jukir->parkingLocation->name ?? 'Belum Ditentukan' }}</div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-icon">
                    <i class="ti tabler-road"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Ruas Jalan</div>
                    <div class="info-value">{{ $jukir->parkingLocation->roadSection->name ?? '-' }}</div>
                </div>
            </div>
            
            @if($jukir->jenis_kelamin)
            <div class="info-group">
                <div class="info-icon">
                    <i class="ti {{ $jukir->jenis_kelamin == 'L' ? 'tabler-gender-male' : 'tabler-gender-female' }}"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-value">{{ $jukir->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
            </div>
            @endif

        </div>

        <div class="action-section">
            <h6 class="text-muted mb-3 fw-bold" style="font-size: 0.95rem;">Ada masalah dengan Juru Parkir ini?</h6>
            <a href="{{ route('public.jukir.complaint.create', $jukir->id_jukir) }}" class="btn-report">
                <i class="ti tabler-alert-triangle-filled"></i> Buat Pengaduan
            </a>
            
            <div class="footer-note">
                <i class="ti tabler-shield-lock me-1"></i> Dikelola oleh Dinas Perhubungan
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
