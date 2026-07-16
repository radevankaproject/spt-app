<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KTA Juru Parkir</title>
    <style>
        @page {
            margin: 0px;
            size: 638px 1013px;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000;
        }
        .page {
            width: 638px;
            height: 1013px;
            position: relative;
            overflow: hidden;
            background-color: #d8f1fc; /* Light blue base */
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        
        /* Watermark / Background elements for Front Page */
        .bg-wave-1 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 450px;
            background: #fff;
            clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%); /* Approximate shape */
            /* In dompdf, clip-path is not supported. Use border hacks or absolute images instead */
        }
        
        /* DOMPDF doesn't support complex CSS like clip-path. We'll use simple rectangles and circles to emulate */
        .bg-white-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 380px;
            background-color: #ffffff;
        }
        .bg-triangle-left {
            position: absolute;
            top: 380px;
            left: 0;
            width: 0;
            height: 0;
            border-top: 150px solid #ffffff;
            border-right: 319px solid transparent;
        }
        .bg-triangle-right {
            position: absolute;
            top: 380px;
            right: 0;
            width: 0;
            height: 0;
            border-top: 150px solid #ffffff;
            border-left: 319px solid transparent;
        }
        
        /* Header Logos */
        .header-logos {
            position: absolute;
            top: 25px;
            left: 30px;
            width: 578px;
            height: 100px;
        }
        .logo-pemko {
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
        }
        .logo-dishub {
            position: absolute;
            left: 85px;
            top: 5px;
            width: 80px;
        }
        .header-text {
            position: absolute;
            left: 175px;
            top: 15px;
            width: 400px;
            font-size: 26px;
            font-weight: bold;
            line-height: 1.2;
        }
        .text-dinas {
            color: #d12027; /* Red */
        }
        .text-kota {
            color: #27317e; /* Dark Blue */
        }
        
        /* Photo Container */
        .photo-container {
            position: absolute;
            top: 190px;
            left: 179px; /* (638 - 280) / 2 */
            width: 280px;
            height: 280px;
            background-color: #58cbf6;
            border-radius: 20px;
            transform: rotate(45deg); /* DOMPDF might struggle with rotate, let's see. If not, use standard box */
        }
        /* Fallback if rotate fails in dompdf, but dompdf 2.0+ supports basic transform */
        
        .photo-inner {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 250px;
            height: 250px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            transform: rotate(-45deg); /* Counter rotate the inner image */
        }
        .photo-img {
            width: 350px;
            height: 350px;
            margin-top: -50px;
            margin-left: -50px;
            object-fit: cover;
        }
        
        /* For dompdf compatibility, rotate might be buggy. Let's do a simple frame instead if it breaks, but we will try. */
        
        .photo-simple-container {
            position: absolute;
            top: 160px;
            left: 179px;
            width: 280px;
            height: 280px;
            background-color: #58cbf6;
            border-radius: 20px;
            padding: 10px;
            box-sizing: border-box;
        }
        
        .photo-simple-inner {
            width: 260px;
            height: 260px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
        }
        
        .photo-simple-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nik-text {
            position: absolute;
            top: 440px;
            width: 100%;
            text-align: center;
            font-size: 16px;
            color: #0b78a9;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        /* Name and Title */
        .name-container {
            position: absolute;
            top: 460px;
            width: 100%;
            text-align: center;
        }
        .val-date {
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            background: #ea580c; /* Orange premium */
            display: inline-block;
            padding: 6px 16px;
            border-radius: 8px;
            margin-top: 5px;
            border: 2px solid #fdba74;
        }
        
        .name-text {
            font-size: 38px;
            font-weight: 900;
            color: #212529;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .name-last {
            color: #0b78a9;
        }
        
        .id-reg-box {
            margin-top: 15px;
            display: inline-block;
            background: #0b78a9;
            color: #fff;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .separator-line {
            position: absolute;
            top: 140px;
            left: 10%;
            width: 80%;
            border-bottom: 2px solid #58cbf6;
        }

        /* Location Details */
        .location-container {
            position: absolute;
            top: 610px;
            width: 100%;
            text-align: center;
        }
        .loc-jalan {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .loc-detail {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .loc-detail span {
            color: #0b78a9;
            font-weight: bold;
            font-style: italic;
        }
        .loc-pengelola {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .loc-pengelola span {
            color: #0b78a9;
            font-weight: bold;
            font-style: italic;
        }
        
        /* Validity Date */
        .validity-container {
            position: absolute;
            bottom: 130px;
            width: 100%;
            text-align: center;
        }
        .validity-title {
            font-size: 16px;
            font-weight: bold;
            font-style: italic;
            color: #555;
            margin-bottom: 5px;
        }
        .validity-box {
            display: inline-block;
            background: #ffffff;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 22px;
            font-weight: 900;
            color: #333;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Bottom Elements */
        .qr-code-box {
            position: absolute;
            bottom: 110px;
            left: 50px;
            width: 100px;
            text-align: center;
        }
        .qr-img {
            width: 100px;
            height: 100px;
            background: #fff;
            padding: 5px;
            border-radius: 5px;
        }
        .scan-text {
            background: #000;
            color: #fff;
            font-size: 12px;
            padding: 2px 0;
            border-radius: 3px;
            margin-top: 5px;
        }
        
        .footer-line {
            position: absolute;
            bottom: 85px;
            left: 0;
            width: 100%;
            border-bottom: 3px dashed #000;
        }
        
        .footer-text {
            position: absolute;
            bottom: 25px;
            width: 100%;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.4;
            letter-spacing: 2px;
        }
        .footer-date {
            font-size: 14px;
            color: #6ebcd9;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        
        /* ---------- BACK PAGE ---------- */
        .back-page {
            background-color: #d8f1fc;
        }
        .peraturan-container {
            position: absolute;
            top: 70px;
            left: 50px;
            width: 538px;
            font-size: 20px;
            line-height: 1.4;
        }
        .peraturan-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .peraturan-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .peraturan-list li {
            margin-bottom: 8px;
            padding-left: 30px;
            text-indent: -30px;
        }
        .text-red {
            color: #d12027;
        }
        
        .signature-box {
            position: absolute;
            top: 450px;
            left: 119px; /* (638 - 400) / 2 */
            width: 400px;
            height: 250px;
            background: #fff;
            border-radius: 20px;
            border: 3px solid #8ccde8;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .sig-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sig-image-container {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-image {
            max-height: 100px;
            max-width: 200px;
        }
        .sig-name {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }
        .sig-nip {
            font-size: 16px;
            font-weight: bold;
        }
        
        .back-footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            width: 558px;
        }
        
        .back-logo-pemko {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
        }
        .back-logo-dishub {
            position: absolute;
            left: 70px;
            top: 5px;
            width: 65px;
        }
        .back-header-text {
            position: absolute;
            left: 145px;
            top: 10px;
            font-size: 22px;
            font-weight: bold;
            line-height: 1.1;
        }
        
        .back-address {
            position: absolute;
            top: 80px;
            width: 100%;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.3;
        }
        
        /* Bubbles */
        .bubble {
            position: absolute;
            border-radius: 50px 50px 0 50px;
            background: #0b78a9;
        }
        .b1 { bottom: -20px; right: 20px; width: 80px; height: 80px; }
        .b2 { bottom: 30px; right: 60px; width: 50px; height: 50px; background: #58cbf6; }
        .b3 { bottom: 50px; right: -10px; width: 60px; height: 60px; background: #ffffff; }
        .b4 { bottom: -10px; right: 90px; width: 40px; height: 40px; background: #ffffff; }
    </style>
</head>
<body>

    <!-- FRONT PAGE -->
    <div class="page">
        <!-- Background elements (Emulating the white top part with angle) -->
        <div class="bg-white-top"></div>
        <div class="bg-triangle-left"></div>
        <div class="bg-triangle-right"></div>
        
        <!-- Header -->
        <div class="header-logos">
            <img src="{{ public_path('assets/images/pekanbaru.png') }}" class="logo-pemko" alt="Pemko">
            <img src="{{ public_path('assets/images/dishub.png') }}" class="logo-dishub" alt="Dishub">
            <div class="header-text">
                <div class="text-dinas">DINAS PERHUBUNGAN</div>
                <div class="text-kota">KOTA PEKANBARU</div>
            </div>
        </div>

        <div class="separator-line"></div>

        <!-- Photo -->
        <!-- Dompdf has bugs with rotate, let's use the simple container layout which looks decent -->
        <div class="photo-simple-container">
            <div class="photo-simple-inner">
                @if($jukir->image)
                    <img src="{{ public_path('storage/' . $jukir->image) }}" alt="Foto Jukir">
                @else
                    <div style="width: 100%; height: 100%; background: #ccc; text-align: center; line-height: 260px; font-weight: bold; color: #fff;">FOTO</div>
                @endif
            </div>
        </div>
        
        <div class="nik-text">NIK: {{ $jukir->no_ktp ?? '-' }}</div>
        
        <!-- Name & ID -->
        <div class="name-container">
            @php
                $nameParts = explode(' ', strtoupper($jukir->nama_jukir));
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);
                if (empty($firstName)) {
                    $firstName = $lastName;
                    $lastName = '';
                }
            @endphp
            <div class="name-text">
                {{ $firstName }} <br><span class="name-last">{{ $lastName }}</span>
            </div>
            <div class="id-reg-box">ID REG : {{ $jukir->id_jukir }}</div>
        </div>

        <!-- Location -->
        <div class="location-container">
            <div class="loc-jalan">{{ $jukir->parkingLocation->roadSection->name ?? 'N/A' }}</div>
            <div class="loc-detail"><span>Lokasi :</span> {{ $jukir->parkingLocation->name ?? 'N/A' }}</div>
            @php
                $pengelolaName = 'N/A';
                if($jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty()) {
                    $activeAgr = $jukir->parkingLocation->agreements->first();
                    $pengelolaName = $activeAgr->fieldCoordinator->user->name ?? 'N/A';
                }
            @endphp
            <div class="loc-pengelola"><span>Pengelola :</span> {{ $pengelolaName }}</div>
        </div>

        <!-- Validity -->
        <div class="validity-container">
            <div class="validity-title">Berlaku Sampai dengan:</div>
            <div class="validity-box">
                @if($jukir->kta_end_date)
                    {{ \Carbon\Carbon::parse($jukir->kta_end_date)->translatedFormat('F Y') }}
                @else
                    -
                @endif
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-code-box">
            <div class="qr-img">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="width: 100%; height: 100%;">
            </div>
            <div class="scan-text">📱 Scan Me</div>
        </div>

        <div class="footer-line"></div>
        
        <div class="footer-text">
            BLUD UPT PERPARKIRAN<br>
            DINAS PERHUBUNGAN KOTA PEKANBARU
            <div class="footer-date">Date : {{ $jukir->kta_start_date ? \Carbon\Carbon::parse($jukir->kta_start_date)->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        </div>
        
        <!-- Bubbles Decoration -->
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
    </div>

    <!-- BACK PAGE -->
    <div class="page back-page">
        <div class="peraturan-container">
            <div class="peraturan-title">PERATURAN :</div>
            <ul class="peraturan-list">
                <li>1. Memungut tarif jasa layanan parkir sesuai dengan ketentuan <span class="text-red">Peraturan Walikota Pekanbaru No. 41 Th 2022</span>.</li>
                <li>2. Menyerahkan karcis kepada pengguna jasa layanan parkir.</li>
                <li>3. Menggunakan atribut juru parkir dan tanda pengenal resmi (KTA).</li>
                <li>4. Menjaga Keamanan Ketertiban dan Kebersihan di sekitar lokasi kerja.</li>
                <li>5. Melakukan pelayanan sesuai dengan standar pelayanan minimal yang telah ditetapkan.</li>
            </ul>
        </div>
        
        <div class="signature-box">
            <div class="sig-title">
                @if($activeLeader && strtoupper($activeLeader->status_jabatan ?? '') === 'PLT')
                    PLT KEPALA UPT SELAKU PIMPINAN<br>BLUD UPT PERPARKIRAN
                @else
                    KEPALA UPT SELAKU PIMPINAN<br>BLUD UPT PERPARKIRAN
                @endif
            </div>
            <div class="sig-image-container">
                @if($activeLeader && isset($activeLeader->signature) && $activeLeader->signature)
                    <img src="{{ public_path('storage/' . $activeLeader->signature) }}" class="sig-image">
                @else
                    <!-- Placeholder or Default signature -->
                    <div style="height: 100px; width: 200px;"></div>
                @endif
            </div>
            <div class="sig-name">
                @if($activeLeader)
                    {{ strtoupper($activeLeader->user->name ?? '-') }}
                @else
                    RADINAL MUNANDAR, S.STP
                @endif
            </div>
            <div class="sig-nip">
                @if($activeLeader)
                    ID: {{ $activeLeader->employee_number ?? '-' }}
                @else
                    NIP : {{ formatNip($activeLeader->employee_number ?? '198908232014061001') }}
                @endif
            </div>
        </div>

        <div class="back-footer">
            <div class="header-logos" style="top: 0; left: 0;">
                <img src="{{ public_path('assets/images/pekanbaru.png') }}" class="back-logo-pemko">
                <img src="{{ public_path('assets/images/dishub.png') }}" class="back-logo-dishub">
                <div class="back-header-text">
                    <div class="text-dinas">DINAS PERHUBUNGAN</div>
                    <div class="text-kota">KOTA PEKANBARU</div>
                </div>
            </div>
            
            <div class="back-address">
                BLUD UPT PERPARKIRAN<br>
                JL. ABDUL RAHMAN HAMID, KOMPLEKS PERKANTORAN TENAYAN<br>
                RAYA - GEDUNG B.9 LT.1 DAN 2, KEC. TENAYAN RAYA,<br>
                PEKANBARU, RIAU, 28285
            </div>
        </div>
        
        <!-- Bubbles Decoration -->
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
    </div>

</body>
</html>
