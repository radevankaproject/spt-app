<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Juru Parkir</title>
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
            margin: 0 auto;
            overflow: hidden;
        }
        .card-header-premium {
            background: linear-gradient(135deg, #0b78a9 0%, #1a3a7a 100%);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .logo-wrap img {
            width: 50px;
            margin: 0 5px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        .jukir-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .jukir-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0b78a9;
        }
        .jukir-info h5 {
            margin: 0;
            font-weight: 700;
            color: #2c3e50;
        }
        .jukir-info p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .btn-premium {
            background: linear-gradient(135deg, #0b78a9, #0d9fd4);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 120, 169, 0.4);
            color: white;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(11, 120, 169, 0.25);
            border-color: #0b78a9;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="premium-card">
            
            <div class="card-header-premium">
                <div class="logo-wrap mb-3">
                    <img src="{{ asset('assets/images/pekanbaru.png') }}" alt="Pemko">
                    <img src="{{ asset('assets/images/dishub.png') }}" alt="Dishub">
                </div>
                <h4 class="mb-1 fw-bold">Layanan Pengaduan</h4>
                <p class="mb-0 text-white-50">Dinas Perhubungan Kota Pekanbaru</p>
            </div>

            <div class="jukir-profile">
                @if($jukir->image)
                    <img src="{{ asset('storage/' . $jukir->image) }}" class="jukir-avatar" alt="Foto">
                @else
                    <div class="jukir-avatar d-flex align-items-center justify-content-center bg-secondary text-white fs-2 fw-bold">
                        {{ strtoupper(substr($jukir->nama_jukir, 0, 1)) }}
                    </div>
                @endif
                <div class="jukir-info">
                    <p class="text-primary fw-bold mb-0">Terlapor (Juru Parkir):</p>
                    <h5>{{ $jukir->nama_jukir }}</h5>
                    <p>ID Reg: {{ $jukir->id_jukir }}</p>
                    <p><i class="ti ti-map-pin"></i> {{ $jukir->parkingLocation->name ?? '-' }} ({{ $jukir->parkingLocation->roadSection->name ?? '-' }})</p>
                </div>
            </div>

            <div class="p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.jukir.complaint.store', $jukir->id_jukir) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" class="form-control" placeholder="Masukkan nama Anda" value="{{ old('reporter_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp <span class="text-muted">(Opsional)</span></label>
                        <input type="text" name="reporter_phone" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('reporter_phone') }}">
                        <div class="form-text">Untuk keperluan konfirmasi/tindak lanjut jika diperlukan.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Pengaduan <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="tarif" {{ old('category') == 'tarif' ? 'selected' : '' }}>Pungutan Tarif Tidak Sesuai Ketentuan</option>
                            <option value="pelayanan" {{ old('category') == 'pelayanan' ? 'selected' : '' }}>Pelayanan Buruk / Kasar</option>
                            <option value="keamanan" {{ old('category') == 'keamanan' ? 'selected' : '' }}>Kendaraan Rusak / Hilang (Keamanan)</option>
                            <option value="kebersihan" {{ old('category') == 'kebersihan' ? 'selected' : '' }}>Lokasi Parkir Kotor (Kebersihan)</option>
                            <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Deskripsi Kejadian <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Ceritakan detail kejadian (waktu, kronologi, dll)..." required>{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 fs-5 d-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-send"></i> Kirim Pengaduan
                    </button>
                </form>
            </div>
            
        </div>
        <div class="text-center mt-4 text-muted small fw-medium">
            &copy; {{ date('Y') }} BLUD UPT Perparkiran<br>Dinas Perhubungan Kota Pekanbaru
        </div>
    </div>

</body>
</html>
