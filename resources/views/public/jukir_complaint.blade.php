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
        /* Custom Progress Bar */
        .progress-premium {
            height: 12px;
            border-radius: 20px;
            background: #e9ecef;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        .progress-premium .progress-bar {
            background: linear-gradient(135deg, #0b78a9, #0d9fd4);
            border-radius: 20px;
            transition: width 0.4s ease;
        }
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .preview-grid img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
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
                <img src="{{ $jukir->image_url }}" class="jukir-avatar" alt="Foto">
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

                <form id="complaintForm" action="{{ route('public.jukir.complaint.store', $jukir->id_jukir) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" class="form-control" placeholder="Masukkan nama Anda" value="{{ old('reporter_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_phone" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('reporter_phone') }}" required>
                        <div class="form-text">Wajib diisi agar kami bisa mengirim notifikasi status pengaduan.</div>
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

                    <!-- Upload Bukti (Baru) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Bukti Pelanggaran (Foto) <span class="text-danger">*</span></label>
                        <input type="file" id="evidence_upload" name="evidence[]" class="form-control" accept="image/png, image/jpeg" multiple required>
                        <div class="form-text">Bisa pilih maksimal 5 foto (Otomatis dikompres &lt; 50Kb).</div>
                        
                        <!-- Preview Images -->
                        <div id="image_preview_container" class="preview-grid d-none"></div>

                        <!-- Progress Bar Premium -->
                        <div id="compressionWrapper" class="mt-3 d-none">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-primary" id="compressionText">Memproses Gambar...</span>
                                <span class="small fw-bold text-muted" id="compressionPercent">0%</span>
                            </div>
                            <div class="progress-premium">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="compressionProgress" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-premium w-100 fs-5 d-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-send"></i> Kirim Pengaduan
                    </button>
                </form>
            </div>
            
        </div>
        <div class="text-center mt-4 text-muted small fw-medium">
            &copy; {{ date('Y') }} BLUD UPT Perparkiran<br>Dinas Perhubungan Kota Pekanbaru
            <div class="mt-2 d-flex justify-content-center gap-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#aboutModal" class="text-decoration-none text-secondary">Tentang Kami</a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-decoration-none text-secondary">Kebijakan Privasi</a>
            </div>
        </div>
    </div>

    <!-- Modals (Dinamis dari UptProfile jika tersedia, kalau tidak hardcode) -->
    @php
        $uptProfile = \App\Models\UptProfile::find(1);
    @endphp

    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 1.2rem; border: none;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">Tentang Kami</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! $uptProfile->about_us ?? '<p>Informasi Tentang Kami belum tersedia.</p>' !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 1.2rem; border: none;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">Kebijakan Privasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! $uptProfile->privacy_policy ?? '<p>Informasi Kebijakan Privasi belum tersedia.</p>' !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('evidence_upload');
            const previewContainer = document.getElementById('image_preview_container');
            const compressionWrapper = document.getElementById('compressionWrapper');
            const progressBar = document.getElementById('compressionProgress');
            const progressPercent = document.getElementById('compressionPercent');
            const progressText = document.getElementById('compressionText');
            const btnSubmit = document.getElementById('btnSubmit');

            fileInput.addEventListener('change', async function(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;

                if (files.length > 5) {
                    alert('Maksimal 5 foto bukti yang dapat diunggah.');
                    fileInput.value = '';
                    return;
                }

                // Reset state
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="ti ti-loader"></i> Memproses...';
                previewContainer.innerHTML = '';
                previewContainer.classList.remove('d-none');
                compressionWrapper.classList.remove('d-none');
                progressBar.style.width = '0%';
                progressPercent.innerText = '0%';

                const dataTransfer = new DataTransfer();
                let completedCount = 0;
                const totalFiles = files.length;

                for (let i = 0; i < totalFiles; i++) {
                    const file = files[i];
                    progressText.innerText = `Mengkompres Gambar ${i + 1} dari ${totalFiles}...`;

                    const options = {
                        maxSizeMB: 0.048, // ~49Kb
                        maxWidthOrHeight: 800,
                        useWebWorker: true,
                        onProgress: (progress) => {
                            // Hitung progress keseluruhan
                            const overallProgress = Math.round(((completedCount * 100) + progress) / totalFiles);
                            progressBar.style.width = `${overallProgress}%`;
                            progressPercent.innerText = `${overallProgress}%`;
                        }
                    };

                    try {
                        const compressedFile = await imageCompression(file, options);
                        dataTransfer.items.add(new File([compressedFile], file.name, { type: compressedFile.type }));
                        
                        // Add to preview
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(compressedFile);
                        previewContainer.appendChild(img);
                        
                    } catch (error) {
                        console.error('Error compress:', error);
                    }
                    completedCount++;
                }

                // Replace input files with compressed files
                fileInput.files = dataTransfer.files;

                // Done
                progressText.innerText = "Kompresi Selesai! Siap Dikirim.";
                progressText.className = "small fw-bold text-success";
                progressBar.className = "progress-bar progress-bar-striped bg-success";
                progressBar.style.width = '100%';
                progressPercent.innerText = '100%';
                
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="ti ti-send"></i> Kirim Pengaduan';
            });
        });
    </script>
</body>
</html>
