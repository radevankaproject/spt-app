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
            background: #222;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            gap: 40px;
            min-height: 100vh;
        }

        /* ============ CONTROLS ============ */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
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

        /* Header */
        .header-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            position: relative;
            z-index: 5;
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
            background: #fff;
            border-radius: 50%;
        }
        .header-logos img.logo-dishub {
            height: 70px;
            width: 70px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            background: #fff;
            border-radius: 50%;
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

        /* Photo & Name Info */
        .info-box {
            display: flex;
            gap: 18px;
            position: relative;
            z-index: 5;
            align-items: center;
            margin-top: 15px; /* Push it down a bit to center vertically in the white area */
        }
        /* Circular Photo with Premium Orange Border */
        .photo-area {
            width: 190px; /* Slight reduction for better balance */
            height: 190px;
            border-radius: 50%;
            background: #ccc;
            border: 5px solid var(--orange);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4), inset 0 2px 10px rgba(0,0,0,0.2);
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .photo-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            font-size: 70px;
            font-weight: 900;
            color: #fff;
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
            font-size: 38px;
            font-weight: 900;
            color: #111;
            line-height: 1;
            margin-bottom: 2px;
        }
        .name-last {
            font-size: 38px;
            font-weight: 900;
            color: var(--orange);
            line-height: 1;
            margin-bottom: 12px;
        }
        .info-nik {
            font-size: 17px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .info-reg {
            font-size: 22px;
            font-weight: 900;
            color: var(--orange);
            margin-bottom: 8px;
        }
        .info-valid-label {
            font-size: 14px;
            font-style: italic;
            font-weight: 600;
            color: #374151;
            margin-bottom: -2px;
        }
        .info-valid-date {
            font-size: 22px;
            font-weight: 900;
            color: var(--orange);
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
            display: flex;
            flex-direction: column;
            gap: 12px; /* Tighter gaps */
            flex-grow: 1;
        }
        .data-group {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
        .data-label {
            font-size: 26px; /* Bigger label */
            font-weight: 800;
            color: var(--orange);
            margin-bottom: 2px;
        }
        .data-value {
            font-size: 28px; /* Bigger value */
            font-weight: 900;
            color: #fff;
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
            color: #fff;
            letter-spacing: 0;
            margin-bottom: -2px;
        }
        .report-title {
            font-family: 'Oswald', sans-serif;
            font-size: 38px;
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            margin-bottom: -5px;
        }
        .report-number {
            font-family: 'Oswald', sans-serif;
            font-size: 52px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            white-space: nowrap;
            letter-spacing: -1.5px;
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

    </style>
</head>
<body>

    <div class="controls" id="controls">
        @if(!request()->has('preview'))
            <a href="{{ url()->previous() }}" class="btn-nav">← Kembali</a>
        @endif
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

                    <div class="info-box">
                        <!-- Circular photo frame -->
                        <div class="photo-area">
                            @if($jukir->image)
                                <img src="{{ asset('storage/' . $jukir->image) }}" alt="Foto">
                            @else
                                <div class="photo-placeholder">{{ strtoupper(substr($jukir->nama_jukir, 0, 1)) }}</div>
                            @endif
                        </div>
                        <div class="name-area">
                            <div class="name-first">{{ $firstName }}</div>
                            <div class="name-last">{{ $lastName }}</div>
                            <div class="info-nik">{{ $jukir->no_ktp ?? '1401060501840004' }}</div>
                            <div class="info-reg">ID REG : {{ $jukir->id_jukir }}</div>
                            <div class="info-valid-label">Berlaku Sampai dengan:</div>
                            <div class="info-valid-date">
                                @if($jukir->kta_end_date)
                                    {{ strtoupper(\Carbon\Carbon::parse($jukir->kta_end_date)->translatedFormat('F Y')) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Half (Blue) -->
                <div class="front-bottom">
                    <!-- Old wave pattern restored -->
                    <div class="wave-pattern"></div>

                    <div class="fb-content">
                        <div class="data-group">
                            <div class="data-label">LOKASI :</div>
                            <div class="data-value">{{ strtoupper($jukir->parkingLocation->name ?? 'BELUM DITENTUKAN') }}</div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">ALAMAT:</div>
                            <div class="data-value">{{ strtoupper($jukir->parkingLocation->roadSection->name ?? 'BELUM DITENTUKAN') }}</div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">PENGELOLA:</div>
                            @php
                                $pengelolaName = '-';
                                if($jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty()) {
                                    $pengelolaName = $jukir->parkingLocation->agreements->first()->leader->user->name ?? '-';
                                }
                            @endphp
                            <div class="data-value">{{ strtoupper($pengelolaName) }}</div>
                        </div>
                    </div>
                    
                    <!-- Report and QR Section -->
                    <div class="report-wrapper">
                        <!-- Left 80% -->
                        <div class="report-left">
                            <div class="report-text">LAPORKAN KAMI JIKA MEMINTA LEBIH DARI PERATURAN</div>
                            <div class="report-title">NO PENGADUAN</div>
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
                    @php
                        $hasRealLeader = $jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty();
                    @endphp
                    @if($hasRealLeader)
                        PENGELOLA / KORLAP<br>LOKASI PARKIR
                    @else
                        PLT KEPALA UPT SELAKU PIMPINAN<br>BLUD UPT PERPARKIRAN
                    @endif
                </div>

                <div class="sig-space">
                    @if($hasRealLeader && isset($activeLeader->user->img) && $activeLeader->user->img)
                        <img src="{{ asset('storage/' . $activeLeader->user->img) }}" alt="Tanda Tangan">
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
                    @if($hasRealLeader)
                        ID : {{ $activeLeader->employee_number ?? '-' }}
                    @else
                        NIP : {{ $activeLeader->employee_number ?? '19960224 201808 1 002' }}
                    @endif
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
    </script>
</body>
</html>
