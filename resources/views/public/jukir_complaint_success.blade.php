<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Terkirim</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: linear-gradient(135deg, #e8ecef 0%, #c3cfe2 100%);
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
            max-width: 500px;
            width: 100%;
            text-align: center;
            padding: 3rem 2rem;
        }
        .icon-success {
            font-size: 80px;
            color: #28c76f;
            margin-bottom: 1rem;
        }
        h2 {
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <div class="premium-card">
        <i class="ti ti-circle-check-filled icon-success"></i>
        <h2>Terima Kasih!</h2>
        <p>Laporan pengaduan Anda terhadap juru parkir <strong>{{ $jukir->nama_jukir }}</strong> telah berhasil kami terima dan akan segera ditindaklanjuti.</p>
        
        <div class="mt-4 pt-4 border-top">
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                <img src="{{ asset('assets/images/pekanbaru.png') }}" width="30" alt="">
                <img src="{{ asset('assets/images/dishub.png') }}" width="35" alt="">
            </div>
            <p class="small text-muted mb-0">BLUD UPT Perparkiran<br>Dinas Perhubungan Kota Pekanbaru</p>
        </div>
    </div>

</body>
</html>
