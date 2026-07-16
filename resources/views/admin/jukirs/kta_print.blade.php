<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTA - {{ $jukir->nama_jukir }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            @if(request('preview'))
                background: transparent;
                margin: 0;
                padding: 0;
                overflow: hidden;
            @else
                background: #222;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 40px 20px;
                gap: 40px;
                min-height: 100vh;
            @endif
        }

        /* ============ CONTROLS ============ */
        .controls {
            @if(request('preview'))
                display: none !important;
            @else
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 999;
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            @endif
        }
        .controls a, .controls button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #fff;
        }
        .btn-nav { background: #444; border: 1px solid #555; }
        .btn-nav:hover { background: #555; }
        .btn-dl-front { background: #2563eb; }
        .btn-dl-front:hover { background: #1d4ed8; }
        .btn-dl-back { background: #059669; }
        .btn-dl-back:hover { background: #047857; }
        .btn-print { background: #db2777; }
        .btn-print:hover { background: #be185d; }

        /* ============ CARD BASE ============ */
        .kta-card {
            width: 638px;
            height: 1013px;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* Variables for colors */
        :root {
            --orange: #f97316;
            --darkblue: #0b1a30;
            --gray-text: #6b7280;
        }

        /* GUILLOCHE PATTERN */
        .wave-pattern {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.05) 2px, transparent 3px);
            background-size: 30px 30px;
            opacity: 0.4;
            z-index: 1;
        }
        .wave-pattern::before {
            content: '';
            position: absolute;
            inset: -50%;
            background: repeating-radial-gradient(
                circle at center,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.03) 11px,
                rgba(255,255,255,0.03) 12px
            );
            z-index: 1;
        }

        /* =================================================================
           FRONT PAGE
           ================================================================= */
        
        .front-layout {
            display: flex;
            height: 100%;
            width: 100%;
        }

        /* --- Left Column (78%) --- */
        .front-left {
            width: 78%;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Top White Area */
        .front-top {
            height: 48%; /* Adjusting height ratio to make it dense */
            background: #ffffff;
            position: relative;
            padding: 15px 15px; /* Reduced padding to fit more */
            z-index: 10;
            display: flex;
            flex-direction: column;
        }
        .separator-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--orange), transparent);
            margin: 0 auto;
            width: 85%;
            position: relative;
            z-index: 5;
        }
        
        /* ONLY on the data jukir (top white area) */
        .bg-silhouette {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('{{ asset("assets/images/image-Background.png") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.25; 
            z-index: 1;
        }

        .header-box {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 5;
            margin-bottom: 15px;
        }
        .header-logos {
            display: flex;
            align-items: center;
        }
        .header-logos img.logo-pemko {
            height: 70px;
            width: 70px;
            object-fit: contain;
            position: relative;
            z-index: 2;
            margin-right: -15px; 
            filter: drop-shadow(2px 0 3px rgba(0,0,0,0.2));
        }
        .header-logos img.logo-dishub {
            height: 70px;
            width: 70px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(2px 0 3px rgba(0,0,0,0.2));
        }
        .header-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 5px;
        }
        .header-text .t1 {
            font-family: 'Oswald', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.05;
            letter-spacing: 0;
        }
        .header-text .t2 {
            font-family: 'Oswald', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--darkblue);
            line-height: 1.05;
            letter-spacing: 0.5px;
        }

        .info-box {
            display: flex;
            gap: 25px;
            position: relative;
            z-index: 5;
            align-items: center;
            margin-top: auto;
            margin-bottom: auto;
            width: 100%;
            padding: 0 5px;
        }
        
        /* Modern Premium Photo Frame (Portrait Ratio) */
        .photo-area {
            width: 170px; 
            height: 215px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--orange), #fcd34d);
            padding: 4px; /* acts as the border */
            box-shadow: 0 15px 35px rgba(0,0,0,0.15), 0 5px 15px rgba(0,0,0,0.1);
            flex-shrink: 0;
            position: relative;
            z-index: 10;
        }
        .photo-inner {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff; /* Internal white padding effect */
        }
        .photo-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 9px;
        }
        .photo-placeholder {
            font-size: 70px;
            font-weight: 900;
            color: #9ca3af;
        }
        
        /* MICROTEXT SECURITY FEATURE */
        .microtext-container {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 20;
            border-radius: 9px;
            overflow: hidden;
        }
        .micro-line {
            position: absolute;
            font-size: 4.5px;
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.4);
            white-space: nowrap;
            letter-spacing: 0.5px;
            text-shadow: 0px 0px 2px rgba(0,0,0,0.8), 1px 1px 1px rgba(0,0,0,0.5);
        }
        .micro-top { 
            top: 2px; 
            left: 2px; 
        }
        .micro-bottom { 
            bottom: 2px; 
            left: 2px; 
        }
        .micro-left {
            top: 100%;
            left: 2px;
            transform-origin: top left;
            transform: rotate(-90deg);
        }
        .micro-right {
            top: 0;
            left: calc(100% - 2px);
            transform-origin: top left;
            transform: rotate(90deg);
        }

        .name-area {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }
        @php
            $words = explode(' ', strtoupper($jukir->nama_jukir));
            $lastName = array_pop($words);
            $firstName = implode(' ', $words);
            if (empty($firstName)) { $firstName = $lastName; $lastName = ''; }
        @endphp
        .name-first {
            font-size: 34px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.05;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }
        .name-last {
            font-size: 34px;
            font-weight: 800;
            color: var(--orange);
            line-height: 1.05;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }
        .divider-micro {
            width: 50px;
            height: 4px;
            background: linear-gradient(90deg, var(--orange), #fcd34d);
            border-radius: 2px;
            margin-bottom: 15px;
        }

        /* Modern Stats Grid */
        .modern-stats {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .stat-row {
            display: flex;
            gap: 20px;
        }
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        .stat-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .stat-val {
            font-size: 19px;
            font-weight: 900;
            color: #1e293b;
        }
        .val-highlight {
            color: var(--orange);
            font-size: 20px;
        }

        .badge-berlaku {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, var(--orange) 0%, #c2410c 100%);
            color: #fff !important;
            padding: 4px 14px;
            border-radius: 8px;
            font-size: 19px;
            font-weight: 900;
            box-shadow: 0 4px 12px rgba(194, 65, 12, 0.3);
            letter-spacing: 0.5px;
            border: 1px solid #fdba74;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            margin-top: 2px;
        }

        /* Bottom Blue Area */
        .front-bottom {
            height: 52%;
            background: var(--darkblue);
            position: relative;
            padding: 20px 20px; /* Tighter padding */
            color: #fff;
            z-index: 10;
            display: flex;
            flex-direction: column;
        }

        .fb-content {
            position: relative;
            z-index: 5;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .loc-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: center;
            width: 100%;
        }
        .loc-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .loc-label {
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }
        .loc-value {
            font-size: 26px; /* Slightly larger since there's no box */
            font-weight: 900;
            color: #fff;
            line-height: 1.2;
            letter-spacing: 0.5px;
        }
        .loc-value-highlight {
            color: var(--orange);
        }
        .bottom-divider {
            width: 100%;
            height: 0;
            border-bottom: 2px dashed rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 5;
            margin: 15px 0 12px 0;
        }

        /* QR and Report Layout */
        .report-wrapper {
            margin-top: auto;
            display: flex;
            width: 100%;
            align-items: flex-end;
            padding-bottom: 5px;
            position: relative;
            z-index: 5;
        }
        .report-left {
            width: 76%;
            display: flex;
            flex-direction: column;
        }
        .report-right {
            width: 24%;
            display: flex;
            flex-direction: column;
            align-items: flex-end; /* Push to right */
            justify-content: flex-end;
        }

        .report-text {
            font-family: 'Oswald', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: #cbd5e1;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .report-title {
            font-family: 'Oswald', sans-serif;
            font-size: 34px;
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            margin-bottom: -4px;
        }
        .report-number {
            font-family: 'Oswald', sans-serif;
            font-size: 48px;
            font-weight: 700;
            color: var(--orange);
            line-height: 1;
            white-space: nowrap;
            letter-spacing: -1px;
            text-shadow: 0 2px 10px rgba(249, 115, 22, 0.3);
        }

        .qr-label {
            font-size: 12px;
            font-weight: 800;
            color: var(--orange);
            text-align: center;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .qr-box-front {
            background: #fff;
            padding: 5px;
            border-radius: 8px;
            width: 90px;
            height: 90px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
        }
        .qr-box-front img {
            width: 100%;
            height: 100%;
        }

        .print-date {
            position: absolute;
            bottom: 6px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.15); /* Lebih samar lagi */
            letter-spacing: 1.5px;
            z-index: 5;
            font-family: 'Inter', sans-serif;
        }

        /* --- Right Column (22%) --- */
        .front-right {
            width: 22%;
            height: 100%;
            background: var(--orange);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .front-right .wave-pattern {
            background-image: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.15) 2px, transparent 3px);
        }
        .front-right .wave-pattern::before {
            background: repeating-radial-gradient(
                circle at center,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.1) 11px,
                rgba(255,255,255,0.1) 12px
            );
        }
        .text-vertical {
            transform: rotate(-90deg);
            white-space: nowrap;
            font-size: 90px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 5px;
            position: relative;
            z-index: 5;
        }


        /* =================================================================
           BACK PAGE
           ================================================================= */
        .kta-back {
            background: var(--orange);
            position: relative;
        }
        .kta-back .wave-pattern {
            background-image: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 2px, transparent 3px);
        }
        .kta-back .wave-pattern::before {
            background: repeating-radial-gradient(
                circle at center,
                transparent,
                transparent 15px,
                rgba(255,255,255,0.08) 16px,
                rgba(255,255,255,0.08) 17px
            );
        }

        .back-content {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            padding: 40px 30px;
        }

        .rules-title {
            font-size: 22px;
            font-weight: 900;
            color: #000;
            margin-bottom: 12px;
        }
        .rules-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .rules-list li {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            display: flex;
            gap: 8px;
        }
        .rules-list .num {
            flex-shrink: 0;
        }
        .rules-list .hl-black {
            color: #000;
            font-weight: 900;
        }

        .sig-card {
            margin-top: 45px;
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            align-self: center;
        }
        .sig-role {
            font-size: 20px;
            font-weight: 900;
            color: #000;
            line-height: 1.3;
        }
        .sig-space {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }
        .sig-space img {
            max-height: 120px;
            max-width: 260px;
        }
        .sig-name {
            font-size: 22px;
            font-weight: 900;
            color: #000;
        }
        .sig-line {
            width: 90%;
            height: 3px;
            border-bottom: 3px dashed #000;
            margin: 5px auto 8px;
        }
        .sig-nip {
            font-size: 20px;
            font-weight: 900;
            color: #000;
        }

        .back-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: var(--darkblue);
            z-index: 10;
            padding: 25px 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        .back-footer .wave-pattern {
            opacity: 0.15;
            background-size: 40px 40px;
        }
        .bf-title {
            font-size: 24px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 5px;
            position: relative;
            z-index: 5;
        }
        .bf-address {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            position: relative;
            z-index: 5;
        }

        @if(request('preview') == 'front')
            #kta-back { display: none !important; }
            body { padding: 0 !important; background: transparent !important; align-items: flex-start !important; }
            .controls { display: none !important; }
            .kta-card { box-shadow: none !important; }
        @elseif(request('preview') == 'back')
            #kta-front { display: none !important; }
            body { padding: 0 !important; background: transparent !important; align-items: flex-start !important; }
            .controls { display: none !important; }
            .kta-card { box-shadow: none !important; }
        @endif

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }
            .controls { display: none !important; }
            .kta-card {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 20px;
                /* Force background graphics to print */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body>

    <div class="controls" id="controls">
        @if(!request()->has('preview'))
            <a href="{{ url()->previous() }}" class="btn-nav">← Kembali</a>
        @endif
        <button class="btn-print" onclick="printCard()">🖨️ Cetak Langsung (Ukuran Asli)</button>
        <button class="btn-dl-front" onclick="downloadCard('kta-front', 'KTA-Front-{{ $jukir->id_jukir }}')">📥 Download Depan</button>
        <button class="btn-dl-back" onclick="downloadCard('kta-back', 'KTA-Back-{{ $jukir->id_jukir }}')">📥 Download Belakang</button>
    </div>

    <!-- =================================================================
         FRONT PAGE
         ================================================================= -->
    <div class="kta-card kta-front" id="kta-front">
        <div class="front-layout">
            
            <!-- LEFT COLUMN -->
            <div class="front-left">
                <!-- Top Half (White) -->
                <div class="front-top">
                    <!-- Silhouette ONLY on data jukir area -->
                    <div class="bg-silhouette"></div>
                    
                    <div class="header-box">
                        <div class="header-logos">
                            <!-- Overlapping logos -->
                            <img src="{{ asset('assets/images/pekanbaru.png') }}" class="logo-pemko" alt="Pemko">
                            <img src="{{ asset('assets/images/dishub.png') }}" class="logo-dishub" alt="Dishub">
                        </div>
                        <div class="header-text">
                            <div class="t1">PEMERINTAH KOTA PEKANBARU</div>
                            <div class="t2">DINAS PERHUBUNGAN</div>
                        </div>
                    </div>

                    <div class="separator-line"></div>

                    <div class="info-box">
                        <!-- Premium Portrait Photo Frame -->
                        <div class="photo-area">
                            <div class="photo-inner">
                                @if($jukir->image)
                                    <img src="{{ asset('storage/' . $jukir->image) }}" alt="Foto">
                                @else
                                    <div class="photo-placeholder">{{ strtoupper(substr($jukir->nama_jukir, 0, 1)) }}</div>
                                @endif
                                
                                <!-- Microtext overlay (4 Sisi) -->
                                @php
                                    $rawMicro = ($jukir->no_ktp ?? '') . $jukir->nama_jukir . $jukir->id_jukir;
                                    $microText = strtoupper(str_replace(' ', '', $rawMicro));
                                    $microTextLong = str_repeat($microText, 10); // Diperbanyak agar cukup mengitari foto
                                @endphp
                                <div class="microtext-container">
                                    <div class="micro-line micro-top">{{ $microTextLong }}</div>
                                    <div class="micro-line micro-right">{{ $microTextLong }}</div>
                                    <div class="micro-line micro-bottom">{{ $microTextLong }}</div>
                                    <div class="micro-line micro-left">{{ $microTextLong }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="name-area">
                            <div class="name-first">{{ $firstName }}</div>
                            <div class="name-last">{{ $lastName }}</div>
                            
                            <div class="divider-micro"></div>
                            
                            <div class="modern-stats">
                                <div class="stat-row">
                                    <div class="stat-item" style="flex: 1;">
                                        <span class="stat-label">NIK</span>
                                        <span class="stat-val">{{ $jukir->no_ktp ?? '-' }}</span>
                                    </div>
                                    <div class="stat-item" style="flex: 1;">
                                        <span class="stat-label">ID REG</span>
                                        <span class="stat-val val-highlight">{{ $jukir->id_jukir }}</span>
                                    </div>
                                </div>
                                <div class="stat-item" style="margin-top: 2px;">
                                    <span class="stat-label">Masa Berlaku</span>
                                    <div>
                                        <span class="badge-berlaku">
                                            @if($jukir->kta_end_date)
                                                {{ strtoupper(\Carbon\Carbon::parse($jukir->kta_end_date)->translatedFormat('F Y')) }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Half (Blue) -->
                <div class="front-bottom">
                    <!-- Old wave pattern restored -->
                    <div class="wave-pattern"></div>

                    <div class="fb-content">
                        <div class="loc-grid">
                            <div class="loc-item">
                                <span class="loc-label">Lokasi Parkir</span>
                                <span class="loc-value">{{ strtoupper($jukir->parkingLocation->name ?? 'BELUM DITENTUKAN') }}</span>
                            </div>
                            <div class="loc-item">
                                <span class="loc-label">Alamat / Ruas Jalan</span>
                                <span class="loc-value">{{ strtoupper($jukir->parkingLocation->roadSection->name ?? 'BELUM DITENTUKAN') }}</span>
                            </div>
                            <div class="loc-item">
                                <span class="loc-label">Pengelola Wilayah</span>
                                @php
                                    $pengelolaName = '-';
                                    if($jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty()) {
                                        $activeAgr = $jukir->parkingLocation->agreements->first();
                                        $pengelolaName = $activeAgr->fieldCoordinator->user->name ?? '-';
                                    }
                                @endphp
                                <span class="loc-value loc-value-highlight">{{ strtoupper($pengelolaName) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bottom-divider"></div>
                    
                    <!-- Report and QR Section -->
                    <div class="report-wrapper">
                        <!-- Left 80% -->
                        <div class="report-left">
                            <div class="report-text">LAPORKAN JIKA TERDAPAT PELANGGARAN ATURAN ATAU TARIF PARKIR</div>
                            <div class="report-title">LAYANAN PENGADUAN</div>
                            <div class="report-number">0812-6639-7770</div>
                        </div>
                        <!-- Right 20% (QR) -->
                        <div class="report-right">
                            <div class="qr-label">Atau Scan<br>Disini</div>
                            <div class="qr-box-front">
                                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR">
                            </div>
                        </div>
                    </div>

                    <!-- Subtle Print Date -->
                    <div class="print-date">DICETAK: {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('d F Y H:i')) }}</div>

                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="front-right">
                <!-- Old wave pattern restored -->
                <div class="wave-pattern"></div>
                
                <div class="text-vertical">JURU PARKIR</div>
            </div>
            
        </div>
    </div>


    <!-- =================================================================
         BACK PAGE
         ================================================================= -->
    <div class="kta-card kta-back" id="kta-back">
        <!-- Old wave pattern restored -->
        <div class="wave-pattern"></div>

        <div class="back-content">
            <div class="rules-title">PERATURAN :</div>
            <ul class="rules-list">
                <li><span class="num">1.</span> <span>Memungut tarif jasa layanan parkir sesuai dengan ketentuan <span class="hl-black">Peraturan Walikota Pekanbaru No. 02 Th 2025.</span></span></li>
                <li><span class="num">2.</span> <span>Menyerahkan karcis kepada pengguna jasa layanan parkir.</span></li>
                <li><span class="num">3.</span> <span>Menggunakan atribut juru parkir dan tanda pengenal resmi (KTA).</span></li>
                <li><span class="num">4.</span> <span>Menjaga Keamanan Ketertiban dan Kebersihan di sekitar lokasi kerja.</span></li>
                <li><span class="num">5.</span> <span>Melakukan pelayanan sesuai dengan standar pelayanan minimal yang telah ditetapkan.</span></li>
            </ul>

            <div class="sig-card">
                <div class="sig-role">
                    @if($activeLeader && strtoupper($activeLeader->status_jabatan ?? '') === 'PLT')
                        PLT KEPALA UPT SELAKU PIMPINAN<br>BLUD UPT PERPARKIRAN
                    @else
                        KEPALA UPT SELAKU PIMPINAN<br>BLUD UPT PERPARKIRAN
                    @endif
                </div>

                <div class="sig-space">
                    @if($activeLeader && isset($activeLeader->signature) && $activeLeader->signature)
                        <img src="{{ asset('storage/' . $activeLeader->signature) }}" alt="Tanda Tangan">
                    @endif
                </div>

                <div class="sig-name">
                    @if($activeLeader)
                        {{ strtoupper($activeLeader->user->name ?? '-') }}
                    @else
                        RAFIT DWI FEBRI, S.STP
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-nip">
                    NIP : {{ formatNip($activeLeader->employee_number ?? '199602242018081002') }}
                </div>
            </div>
        </div>

        <div class="back-footer">
            <div class="wave-pattern"></div>
            <div class="bf-title">BLUD UPT PERPARKIRAN</div>
            <div class="bf-address">
                JL. ABDUL RAHMAN HAMID, KOMPLEKS PERKANTORAN TENAYAN<br>
                RAYA - GEDUNG B.9 LT.1 DAN 2, KEC. TENAYAN RAYA,<br>
                PEKANBARU, RIAU, 28285
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadCard(elementId, filename) {
            const element = document.getElementById(elementId);
            const controls = document.getElementById('controls');
            controls.style.display = 'none';

            // Set scale to 1 so the pixel dimensions match the exact CSS dimensions (638x1013)
            html2canvas(element, {
                scale: 1, 
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                width: 638,
                height: 1013,
                logging: false,
            }).then(canvas => {
                controls.style.display = 'flex';
                const link = document.createElement('a');
                link.download = filename + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(err => {
                controls.style.display = 'flex';
                console.error('Error:', err);
                alert('Gagal generate gambar. Silakan coba lagi.');
            });
        }

        function printCard() {
            const printWindow = window.open('', '_blank');
            const frontHtml = document.getElementById('kta-front').outerHTML;
            const backHtml = document.getElementById('kta-back').outerHTML;
            
            // Collect all internal and external styles
            let styles = '';
            for (let i = 0; i < document.styleSheets.length; i++) {
                try {
                    const sheet = document.styleSheets[i];
                    if (sheet.href) {
                        styles += `<link rel="stylesheet" href="${sheet.href}">`;
                    } else {
                        let rules = sheet.cssRules || sheet.rules;
                        let cssText = '';
                        for (let j = 0; j < rules.length; j++) {
                            cssText += rules[j].cssText;
                        }
                        styles += `<style>${cssText}</style>`;
                    }
                } catch(e) {}
            }

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print KTA - {{ $jukir->nama_jukir }}</title>
                    ${styles}
                    <style>
                        @media print {
                            @page {
                                size: A4;
                                margin: 10mm;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                                background: white !important;
                                display: block !important;
                                -webkit-print-color-adjust: exact !important;
                                print-color-adjust: exact !important;
                            }
                            .print-container {
                                display: flex;
                                flex-direction: row;
                                gap: 10mm;
                            }
                            .kta-wrapper {
                                width: 5.398cm;
                                height: 8.56cm;
                                position: relative;
                                overflow: hidden;
                                border: 1px dashed #ccc; /* Panduan potong ringan */
                            }
                            .kta-card {
                                transform-origin: top left !important;
                                /* 5.398cm = 204.019px -> 204.019 / 638px = 0.31977 */
                                transform: scale(0.31977) !important;
                                margin: 0 !important;
                                box-shadow: none !important;
                            }
                        }
                        
                        /* Tampilan preview di tab baru sebelum dialog print muncul */
                        body { background: #f0f0f0; display: flex; justify-content: center; padding: 40px; }
                        .print-container { display: flex; gap: 20px; }
                        .kta-wrapper { width: 5.398cm; height: 8.56cm; position: relative; overflow: hidden; border: 1px dashed #999; background: white; }
                        .kta-card { transform-origin: top left; transform: scale(0.31977); margin: 0; box-shadow: none !important; }
                        .controls { display: none !important; }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="kta-wrapper">${frontHtml}</div>
                        <div class="kta-wrapper">${backHtml}</div>
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(() => {
                                window.print();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</body>
</html>
