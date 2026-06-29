# Changelog

Semua catatan perubahan (History Log) dari aplikasi **Sistem Perjanjian Kerja Sama Perparkiran (SPKP)** dicatat di bawah ini. Dokumen ini merangkum seluruh perjalanan evolusi aplikasi dari inisialisasi awal hingga versi mutakhir.

## [v2.3.0] - 2026-06-29

**_"Survey Data Integration & Export Grouping Mastery"_**

Pembaruan signifikan yang berfokus pada digitalisasi survei potensi lahan parkir, strukturisasi laporan dinamis, dan penyempurnaan estetika ekspor dokumen ke kelas premium.

- **Modul Survey Potensi Lokasi (Baru):**
    - Menambahkan fitur pendataan terintegrasi untuk mencatat hasil survei potensi harian (Survey Tajuk dan Survey Tanam) pada setiap titik parkir.
    - Form _Create_ kini dirancang cerdas dengan logika _Update or Create_. Jika ruas jalan sudah memiliki data survei, form otomatis menarik data lama (berfungsi ganda sebagai _Bulk Edit_), sehingga meminimalkan redundansi input. Input angka 0 (nol) maupun penambahan keterangan (_notes_) dikelola secara presisi dan persisten ke dalam _database_.
    - Indeks daftar survei dilengkapi _filter_ pencarian berlapis berdasarkan Zona (dengan UI _Premium Dot Radio_) dan Ruas Jalan (menggunakan _Select2_ yang adaptif terhadap zona terpilih).
- **Restrukturisasi Profil Titik Parkir:**
    - Mengrombak halaman Detail Titik Parkir (`show`) menggunakan struktur navigasi _Tab Pills_ vertikal yang elegan.
    - Informasi kini dikategorikan rapi ke dalam tab "Data Perjanjian & Setoran", "Data Survey Potensi", dan "Data Estimasi SRP & Luas Wilayah".
- **Laporan PDF & Excel Berbasis Grouping (Super Premium):**
    - Mesin pelaporan kini mengelompokkan (_grouping_) titik parkir secara otomatis berdasarkan Ruas Jalan.
    - Menambahkan kalkulasi akurat berupa baris **Subtotal** di akhir setiap ruas jalan dan **Total Keseluruhan** di akhir laporan untuk metrik Setoran, Survey Tajuk, dan Survey Tanam.
    - **Premium Typography & Layout:** Cetakan PDF dirombak total menggunakan tipografi kustom tingkat lanjut (**Work Sans**) dengan layout header yang menyajikan _summary box_ informatif (Total Titik Lokasi & Waktu Cetak), serta pemilihan palet warna laporan yang elegan.
    - Sistem _export_ Excel sepenuhnya disinkronkan dengan desain PDF menggunakan metode migrasi dari `FromCollection` ke `FromView`, memastikan kolom dan rekapitulasi ruas jalan tercetak secara 1:1 antara PDF dan Excel.
- **Manajemen Juru Parkir (SPA & Image Processing):**
    - Mengubah modul pengelolaan data Jukir menjadi *Single Page Application* (SPA) menggunakan arsitektur Modal terintegrasi untuk proses Tambah dan Edit, menghilangkan navigasi halaman ganda.
    - Mengimplementasikan *Select2* adaptif pada penentuan Titik Parkir dengan fitur *auto-fill* (menampilkan Nama Korlap, Zona, dan Ruas Jalan secara dinamis) persis di bawah kolom pilihan.
    - **Smart Image Processor:** Foto profil Jukir kini diproses secara asinkron (langsung di browser via JavaScript/Canvas). Foto akan terpotong bulat otomatis (1:1 *circle crop*) dan ukuran *file*-nya dikompresi berulang secara progresif (maksimal < 50KB) sebelum dikirim ke server. Dilengkapi *progress bar* visual premium.

## [v2.2.0] - 2026-06-25

Pembaruan besar-besaran untuk menyelaraskan seluruh tampilan halaman daftar (index) ke dalam desain kelas Premium, memberikan estetika modern bergaya *glassmorphism* dan *mesh gradients*.

- **Premium Glass Card & Hero Header:**
    - Menyuntikkan `page-hero` beraksen *mesh-primary* yang mewah dan dilengkapi dengan watermark icon besar pada seluruh halaman `index.blade.php`.
    - Mengganti *card* bawaan dengan desain tembus pandang (`glass-card` dengan efek `anim-2`) untuk memberikan ilusi kedalaman.
    - Menyesuaikan *padding* seragam (`p-4`) ke seluruh `card-header` dan `card-body` agar komposisi jarak lebih rapi dan simetris.
- **Konsistensi Visual & Interaksi Tombol:**
    - Mengubah wujud seluruh tombol aksi utama menjadi oval presisi (`rounded-pill btn-action`) agar memiliki lengkungan simetris dan efek melayang.
    - Memperbaiki kompatibilitas *Tabler Icon* agar tidak gagal muat dengan mengganti ke ikon standar yang didukung.
- **Penyempurnaan Modul Tabel:**
    - Judul kolom pada tabel dipertegas dengan gaya kapital, tebal, dan warna utama aplikasi (`text-uppercase fw-bold text-primary`).
    - Modul `admin/location_requests` dan `field_coordinator/location_requests` kini menggunakan fitur kalender **Flatpickr** untuk filter tanggal.
    - Tombol aksi pada daftar permintaan lokasi kini otomatis berubah menjadi teks "Lihat Details" jika pengajuan sudah disetujui atau ditolak.
## [v2.1.0] - 2026-06-23

**_"Premium Experience, Comprehensive Profiling & Interactivity Update"_**

Pembaruan masif yang berfokus pada detail pengalaman pengguna kelas atas, perombakan tampilan profil _user_, perbaikan tata letak modal dan interaktivitas secara menyeluruh.

- **Perombakan Halaman Profil Kelas Premium:**
    - Mendesain ulang tampilan detail pengguna (`show.blade.php`) di seluruh level otoritas (Admin, Pimpinan, Bendahara, Koordinator Lapangan, dan Pengguna Umum).
    - Menerapkan _2-column card layout_ berstandar tinggi yang menampilkan foto profil (Avatar) dinamis, tautan sosial, serta atribut profil yang detail, terstruktur, dan sangat elegan.
    - Mengintegrasikan pembaruan _UI Profile_ ke berbagai _controller_ secara konsisten.
- **Interaktivitas Dashboard & Pemetaan Data:**
    - ApexCharts (Top 10 Pendapatan Ruas Jalan) pada Dashboard Admin, Pimpinan, dan Staff PKS kini sepenuhnya interaktif. Pengguna yang mengklik salah satu bar chart akan diarahkan langsung ke halaman detail Ruas Jalan terkait (`road-sections.show`).
    - Menambahkan rute dan antarmuka `show` khusus untuk Ruas Jalan sebagai pusat informasi rinci.
    - Memperbaiki masalah `UrlGenerationException` saat memuat _route_ dinamis melalui interaksi grafik.
- **Standarisasi Format Data & Komponen:**
    - Mengimplementasikan *helper* `NipIndoFormat.php` khusus untuk memformat tampilan NIP Aparatur Sipil Negara secara otomatis dengan standar BKN (Contoh: `19900101 201001 1 001`).
    - Standardisasi integrasi **Flatpickr** (Pemilih Tanggal & Waktu) di seluruh Form Modal. Memperbaiki *bug z-index* di mana kalender tersembunyi/tertutup di belakang elemen Bootstrap Modal.
- **Penyempurnaan Sistem OTP & Otentikasi (Lupa Password):**
    - Alur pengiriman OTP WhatsApp disempurnakan. Menambahkan _route_ `GET` `forgot-password/otp` sehingga mencegah terjadinya _error Method Not Allowed_ (Error 405) saat memuat ulang halaman/gagal memvalidasi _form_.
    - _Timer_ hitung mundur "Kirim Ulang" kini bersifat persisten berkat integrasi antara `localStorage` dan peladen (`Session`), sehingga tidak di-_reset_ ke 60 detik secara sepihak jika pengguna melakukan _refresh_.
    - Formulir OTP dilengkapi fitur _Auto Submit_ yang langsung tereksekusi segera setelah angka ke-6 diketik/di-paste.
    - Memberikan umpan balik visual premium berupa _loading spinner_ dan perubahan status "Memuat...", "Mengirim...", dan "Menyimpan..." pada seluruh tombol _submit_ form otentikasi.
- **Desain Halaman Error _Glassmorphism_:**
    - Mengganti total rancangan `layout.blade.php` untuk merespons halaman _error_ (404, 500, dll) menjadi desain _Glassmorphism_ (Kaca Buram) mutakhir.
    - Latar belakang mengusung efek _gradient mesh_ beranimasi dengan komponen geometri melayang, serta logo institusi (UPT SiPKS) yang dipigura dengan pendaran cahaya elegan (_glow pulse_).
    - Memperkenalkan halaman khusus `429.blade.php` merespons status _Too Many Requests_.
- **Pembaruan Fitur QR Code & Keamanan PKS:**
    - Algoritma verifikasi dokumen Perjanjian Kerjasama (PKS) ditingkatkan menggunakan _Generator Alfanumerik 10 karakter_ (`rand(10)`) yang memperkuat keabsahan identitas berkas.
    - QR Code kini dilengkapi cap logo institusi SiPKS (Tengah) untuk menegaskan keaslian otentik.
- **Pembersihan & Perbaikan Visual Ekstra:**
    - Eksekusi stabilisasi visualisasi profil, penanganan tipografi, standardisasi _icon_, hingga penyeragaman tata letak tabel-tabel data master.

## [v2.0.0] - 2026-06-18

**_"The Vuexy 10.11.1 Evolution & Ultimate M3 Preloader"_**

Pembaruan masif dan lompatan arsitektur antarmuka terbesar dalam sejarah aplikasi. Peningkatan dari versi dasar ke versi teranyar Vuexy dengan perombakan total pada sistem pemuatan (_loading_).

- **Full Vuexy v10.11.1 Migration:**
    - Melakukan sinkronisasi dan migrasi menyeluruh dari seluruh kerangka kerja (_layouting_), aset, hingga komponen dasar ke **Vuexy versi 10.11.1 (Full Version Bootstrap 5)**.
    - Halaman otentikasi (Login, Register, Forgot Password, Reset Password, OTP) sepenuhnya diganti menggunakan struktur modern `auth-login-basic` dari Vuexy 10.11.1 dengan visual yang jauh lebih _clean_ dan elegan.
- **Pemusnahan Sistem Skeleton Klasik:**
    - Menghapus 49+ file dan _script_ `_skeleton-*.blade.php` lama secara permanen. Menghilangkan beban _render_ HTML berganda pada setiap halaman (_page load_), sehingga ukuran DOM jauh lebih ringan dan cepat.
- **Inovasi Material 3 (M3) Squiggly Preloader:**
    - Memperkenalkan _Global Premium Preloader_ berteknologi tinggi di `commonMaster.blade.php`.
    - Menggunakan _requestAnimationFrame_ dan perhitungan fungsi _Sine/Cosine_ presisi untuk merender _SVG Squiggly Line_ ala **Material 3 (Android 13+/Flutter M3)** yang memutar secara meliuk-liuk (bergerigi lembut) mengelilingi logo SiPKS.
    - Disempurnakan dengan animasi _stroke-dasharray_ murni via CSS yang akan berhenti secara presisi (_indeterminate spinner_) tepat di _milisecond_ ketika `window.onload` menyatakan seluruh gambar dan halaman siap.
- **Pembersihan Modul (_Housekeeping_):**
    - Menghapus sisa-sisa tata letak bawaan Laravel Breeze (`app.blade.php`, `guest.blade.php`) dan file-file `*copy.php` cadangan yang sudah menjadi _clutter_ di ruang _server_.

## [v1.5.0] - 2026-06-13

**_"Premium Profile & Master Data Auditing"_**

Pembaruan yang berfokus pada perombakan total antarmuka Profil Pengguna menjadi lebih premium, dinamis, serta penguatan _audit trail_ (rekam jejak sejarah) pada Master Data.

- **Perombakan Total Dashboard & UI Roles (Premium Dashboard):**
    - Mendesain ulang seluruh halaman _dashboard_ dari setiap _role_ (Admin, Pimpinan, Bendahara, Staff Keuangan, Staff PKS) dengan tata letak visual standar _premium government_.
    - Menghadirkan komponen _Quick Stats_ (6 kartu statistik), _Hero Search Card_ gradasi, serta tabel informatif dengan desain minimalis tanpa menghilangkan fungsionalitas.
    - Menyesuaikan _Skeleton Loading_ agar setiap transisi sinkron dengan tata letak _dashboard_ terbaru.
- **Fitur Baru - Adendum/Diskon Khusus (Keringanan Tagihan):**
    - Mengakomodasi kebutuhan lapangan dengan penambahan form "Potongan/Keringanan" pada transaksi setoran bulanan. Nominal tagihan akan otomatis dikurangi berdasarkan besaran potongan.
    - Diperkuat dengan **Sistem Audit Diskon**, di mana sistem otomatis mendeteksi dan mencatat profil _user_ yang menyetujui pemotongan tagihan tersebut. Rincian nama pemberi diskon dan alasannya kini dipampang transparan pada halaman _Detail Setoran_ serta cetakan Struk PDF.
- **Perombakan Premium UI Halaman Setoran & Alur Pembayaran Sekuensial:**
    - Mendesain ulang halaman Detail Transaksi Setoran (_show_) dan Formulir Setoran (_create/edit_) dengan tampilan ultra-premium ala sistem instansi resmi (_Glassmorphism_, transisi halus, penjajaran kolom yang presisi, dan _Skeleton Loading_ khusus).
    - Melengkapi fitur interaktif berupa Modal/Popup elegan untuk memperbesar _thumbnail_ lampiran Bukti Transfer langsung tanpa membuka _tab_ baru.
    - **Pembaruan Sistematis:** Menerapkan alur pembayaran **Sekuensial (Wajib Berurutan)**. Pengguna dipaksa melunasi tunggakan bulan terlama terlebih dahulu sebelum dapat membayar tagihan bulan terbaru. Sistem secara otomatis memberikan peringatan elegan jika mencoba melewati urutan bayar.
    - **Penguncian Akses Pintar (Validation Lock):** Mencegah _double-input_ atau transaksi ganda. Jika seorang Koordinator Lapangan masih memiliki riwayat transaksi setoran yang berstatus _Pending / Menunggu Validasi_, maka form setoran baru akan **terkunci rapat** hingga Bendahara menyelesaikan validasi tersebut.
- **Inovasi Navigasi - Tab Jatuh Tempo:**
    - Menambahkan **Tab Jatuh Tempo** di posisi terdepan pada Halaman Indeks Setoran. Fitur ini menyeleksi dan mendeteksi seluruh PKS yang menunggak secara _real-time_.
    - Menambahkan tombol "Bayar Sekarang" yang langsung menghubungkan pengguna ke form input setoran dengan sistem pengisian kolom otomatis _(Auto-Trigger Target Agreement)_.
    - Kolom pencarian disederhanakan dan dioptimasi di tingkat _Controller_ agar merender hasil dengan lebih gegas (_Super Fast Live Search_).
- **Pembaruan Modul Cetak PDF & Sidebar:**
    - Optimalisasi _layouting_ cetak Struk Setoran (PDF) agar selalu presisi termuat pada 1 halaman utuh (dikunci dengan _page-break_ CSS modern).
    - Mengganti nomenklatur menu di panel Sidebar dari _"Validasi Setoran"_ menjadi _"Input Setoran"_ untuk memperjelas fungsionalitas bagi multi-peran admin/keuangan.
- **Perombakan Total Profil Pengguna (Premium Profile):**
    - Redesain antarmuka Profil menjadi jauh lebih modern, premium, dan dinamis dengan efek _Skeleton Loading_ untuk transisi.
    - Mengintegrasikan UI-Avatars API sebagai _fallback_ elegan jika _user_ belum mengunggah foto profil.
    - Fitur _Live Search_ langsung terintegrasi di dalam _card_ Aktivitas/Informasi Detail Pengguna.
    - Menampilkan informasi historis spesifik berdasarkan peran pengguna (Admin, Staff PKS, Bendahara, Staff Keu, Pimpinan), memastikan data yang relevan tampil di satu layar.
- **Penyempurnaan Manajemen Koordinator (SPA Modals):**
    - Akses Edit Data Lengkap dan Edit Data Login (Username/Password/Email) Koordinator Lapangan kini terintegrasi langsung di dalam halaman Indeks menggunakan teknologi _Single Page Application_ (SPA) / Modals. Menghapus navigasi paksa ke halaman _show_.
    - Penerapan limitasi akses yang ketat; hanya _role_ Admin yang diizinkan untuk memodifikasi Data Login Koordinator.
- **Penguatan Audit Trail (Rekam Jejak & Sejarah):**
    - **Titik Parkir:** Sistem kini melacak sejarah setiap perubahan informasi titik parkir menggunakan tabel `parking_location_histori`.
    - **Arsip PKS:** Modifikasi atau unggah ulang dokumen _scan_ PKS (PDF) kini direkam secara permanen ke dalam tabel `agreement_pdf_histories`.
    - **Koordinator & Ruas Jalan:** Menambahkan field `last_updated_by` untuk melacak secara persis identitas _user_ (Admin/Staff) terakhir yang membuat atau mengubah data tersebut.
- **Pembaruan Fitur Titik Parkir (Estimasi Luas & SRP):**
    - Menambahkan metrik baru pada form pendaftaran dan _edit_ Titik Parkir berupa: Estimasi Luas Wilayah (m²), Estimasi Satuan Ruang Parkir (SRP) Roda 2, dan SRP Roda 4. Informasi ini turut diintegrasikan ke halaman Detail Lokasi dengan peringatan bahwa setoran tidak bergantung pada parameter tersebut.
- **Penanganan Error Premium & Perbaikan Bug 419:**
    - Mendesain ulang seluruh halaman _error_ sistem (404, 403, 500, 419, 401, 503) menjadi sangat premium menggunakan tipografi dan ilustrasi _Vuexy Style_.
    - Memperbaiki _bug_ _Page Expired_ (419) yang sering muncul di halaman _login_ dengan menerapkan _smart redirect_; pengguna yang sesinya masih aktif akan otomatis diarahkan ke _dashboard_ masing-masing _role_ tanpa harus melihat _form login_ lagi.
    - Menyempurnakan pelaporan _error_ ke dalam `laravel.log` dengan pesan _error 500_ yang formal dan elegan khusus untuk _environment production_.
- **Penyempurnaan Form Manajemen Versi (Changelog):**
    - Mengganti editor Quill.js yang rentan _error_ validasi (_hidden input bug_) dengan _Textarea_ standar yang tangguh.
    - Menambahkan dukungan penuh _Markdown_ bawaan Laravel (`Str::markdown()`) sehingga pengguna kini bisa langsung _copy-paste_ catatan rilis berformat raw `.md` secara murni tanpa merusak format aslinya.
- **Optimasi Performa Dashboard:** Memperbaiki isu performa kueri N+1 pada Dashboard Leader dan Staff Keuangan terkait pengambilan data transaksi setoran bulanan PKS.

## [v1.4.0] - 2026-06-12

**_"The Map Dashboard & Document Upload Update"_**

Pembaruan signifikan yang berfokus pada penyempurnaan UI/UX Dashboard Pimpinan, penyempurnaan modul PDF, serta penambahan kapabilitas pengarsipan dokumen PKS fisik (bertanda tangan) secara digital.

- **Dashboard Pimpinan Interaktif:**
    - Mengintegrasikan Peta Persebaran Lokasi Parkir (Leaflet.js) langsung ke dalam _dashboard_ Pimpinan dengan tampilan visual yang komprehensif, _auto fit-bounds_ ([60, 60] padding, zoom 13), serta optimasi _rendering_ anti-macet via _delay_.
    - Merombak tabel Kontrak PKS Terbaru dengan hanya menampilkan PKS Aktif dan menambahkan metrik analitik: jumlah titik parkir aktif, setoran harian, dan target bulanan.
- **Pembaruan Fitur Titik Parkir:**
    - Lokasi parkir dengan status "Tidak Tersedia" kini difilter agar fitur _edit_-nya hanya mengizinkan pengubahan Nama Lokasi, Koordinat, Foto, dan dokumen PDF (pengajuan/berita acara), sementara isian lain terkunci otomatis.
    - Peta pada halaman _edit_ titik parkir dioptimalkan agar selalu termuat penuh (anti kotak abu-abu/terpotong) dan letak _marker_ selalu presisi.
- **Fitur Baru - Perpanjangan PKS (Renewal):**
    - Menghadirkan kapabilitas untuk memperpanjang PKS (_Renewal_). Saat diperpanjang, seluruh titik parkir aktif akan otomatis dimigrasi dari PKS lama ke PKS baru.
    - PKS lama secara cerdas akan diubah statusnya menjadi _expired_ dan tanggal berahirnya otomatis disesuaikan (_end date_ dipotong ke hari sebelum _start date_ PKS baru), sehingga tidak ada celah kekosongan waktu maupun tumpang tindih masa aktif.
- **Manajemen Arsip PKS & Expired:**
    - PKS berstatus _expired_ otomatis menyembunyikan tombol Edit dan Print di halaman indeks maupun detail.
    - Saat membuka detail PKS berstatus _expired_, antarmuka secara intuitif akan langsung membuka tab "Arsip PKS".
    - Menambahkan perlindungan saat mengunggah PDF _scan_ (arsip fisik). PKS _expired_ yang sudah memiliki _file scan_ tidak bisa diunggah ulang. Pada PKS aktif, pengguna akan diberi peringatan _SweetAlert_ jika ingin menimpa _file_ lama.
- **Sistem Penyimpanan Hibrida (Ghostscript):** Melakukan perbaikan krusial pada alur unggah dan kompresi PDF (Ghostscript) agar menggunakan _temporary directory_ sistem (`sys_get_temp_dir()`) dan dipindahkan via `Storage::disk('public')`. Ini memecahkan masalah isu izin akses (Permission Denied) antara _web-server_ (`www-data`) dan CLI (`php artisan serve`), memastikan unggahan sukses di semua environment.
- **Sistem Navigasi Cerdas (Smart Search):** Mengintegrasikan fitur pencarian global pada _navbar_ yang berfungsi pada semua _role_. Sistem ini mampu mencari data secara instan dari seluruh sistem: PKS, Titik Parkir, dan User Jukir, dengan _lazy loading_ agar tidak membebani server.
- **Sistem Notifikasi Real-time:** Menerapkan sistem notifikasi _real-time_ berbasis WebSocket (`Laravel WebSockets`) untuk pembaruan saldo digital _Deposit_ dan status transaksi. Pembaruan saldo kini tampil instan tanpa perlu _refresh_ halaman (melalui _Vuexy-style_ `Snackbar`).
- **Penyempurnaan Database Deposit:**
    - Menghapus _Unique Constraint_ pada tabel `deposit_transactions` agar sistem dapat mengakomodasi lebih dari 5 (lima) setoran dengan nominal Rp 0,- dalam satu periode waktu yang sama (misal: 03-06-2026).
    - Memisahkan pencatatan data dari _Unique Constraint_ ke _Business Logic_. Sistem kini wajib memiliki data minimal 1 (satu) setoran minimal Rp 50.000,- (Lima Puluh Ribu Rupiah) untuk menghasilkan nomor PPNP/BPKP.

## [v1.3.1] - 2026-06-07

**_"Agreement Enhancements & Workflow Refinements"_**

Pembaruan yang menitikberatkan pada penyempurnaan alur kerja Perjanjian Kerjasama (PKS), optimasi dokumen PDF, sinkronisasi data antar tabel, serta perbaikan minor pada UI/UX dan sistem _routing_.

- **Penyempurnaan Dokumen PKS (PDF):** Menghapus implementasi _page-break_ paksa pada templat PDF (`agreement.blade.php`) untuk mencegah munculnya halaman kosong yang tidak diinginkan ketika teks alamat terlalu panjang.
- **Manajemen Status Jabatan (Pimpinan):** Menambahkan field `status_jabatan` pada form modal Pimpinan (`LeaderController`). Sistem kini mendeteksi status seperti "Plt." atau "Plh." dan menampilkannya secara otomatis dengan format huruf kapital di awal (Title Case) pada hasil cetak dokumen PKS (contoh: "Plt. Kepala UPT Perparkiran").
- **Manajemen Arsip Digital PKS:** Menambahkan fitur unggah dokumen hasil _scan_ PKS fisik (bertanda tangan) dalam format PDF (maks. 1MB). Terintegrasi dengan **Ghostscript** untuk mengompresi ukuran file PDF secara cerdas sebelum disimpan ke server. Antarmuka unggah dilengkapi dengan _Progress Bar_ premium dan validasi SweetAlert. Arsip kini dapat diunduh langsung oleh Admin, Pimpinan, Staff PKS, maupun Koordinator Lapangan.
- **Fitur Baru - Peta Wilayah Parkir:** Menghadirkan modul interaktif "Peta Wilayah Parkir" yang memvisualisasikan seluruh titik sebaran parkir dalam antarmuka peta digital terintegrasi (berbasis Mapbox). Dilengkapi dengan efek _Skeleton Loading_ untuk transisi halaman yang elegan.
- **Pembaruan Status & Shortcut Navigasi:**
    - Menambahkan _global shortcut_ `Ctrl + /` atau `Ctrl + K` untuk secara instan mengaktifkan (fokus) kotak pencarian global pada _navbar_, mempercepat efisiensi navigasi pengguna.
    - Menambahkan status baru "Menunggu Perpanjangan" (`pending_renewal`) pada opsi _dropdown_ form _Edit Agreement_.
- **Optimalisasi Sinkronisasi Data Pengguna:** Memperbaiki celah logika sinkronisasi antara tabel peran spesifik (`leaders`, `field_coordinators`, `treasurers`) dengan tabel induk (`users`). Kini setiap kali terdapat pembaruan Nomor Handphone, sistem secara serentak akan memperbaruinya pada tabel induk. Turut dieksekusi skrip retroaktif (`fix_phone.php`) untuk menambal data lama yang kosong tanpa perlu _refresh database_.
- **Penyempurnaan Modal UI (UX):**
    - Menghapus form halaman terpisah (_Create/Edit_) untuk Pimpinan agar lebih seragam, mencegah kerancuan dan sepenuhnya beralih ke desain Modal satu pintu.
    - Menambahkan tombol _Clear_ (ikon silang merah) secara _inline_ menggunakan struktur _input-group_ pada input "Tanggal Akhir Menjabat" di modal Pimpinan dan Bendahara, memungkinkan pengguna mengosongkan nilai dengan satu kali klik.
- **Perbaikan Rute & Sidebar (Role Authorization):**
    - Memperbaiki _bug_ di mana menu _sidebar_ "Titik Parkir" ikut menyala (_active_) ketika halaman "Peta Wilayah Parkir" dibuka.
    - Melakukan restrukturisasi hak akses secara ketat untuk rute `parking-locations.map`. Peta Sebaran Parkir kini secara eksklusif hanya dapat diakses oleh Admin, Pimpinan, Bendahara, dan Staff PKS, sekaligus memblokir akses yang tidak relevan dari peran Staff Keuangan.

## [v1.3.0] - 2026-06-06

**_"The Enterprise Dashboard & Security Update"_**

Pembaruan masif yang difokuskan pada perombakan _user interface_ secara ekstensif, peningkatan standar keamanan otentikasi tingkat tinggi, serta fitur-fitur administratif esensial.

- **Major Overhaul Dashboard (UI/UX):** Redesain antarmuka (_Premium Dashboard_) untuk seluruh peran pengguna (Admin, Pimpinan, Bendahara, Staff Keuangan, & Staff PKS). Tampilan kini 100% selaras dengan tema Vuexy Premium, diintegrasikan dengan modul profil dinamis (UI-Avatars API) berdasarkan nama pengguna, serta implementasi efek _Skeleton Loading_ untuk transisi halaman yang lebih mulus.
- **Sistem Keamanan & Akses (WhatsApp OTP):** Implementasi metode pemulihan kata sandi (_Forgot Password_) yang jauh lebih modern dan aman. Sistem kini mengirimkan _One Time Password_ (OTP) 6 digit yang _expired_ dalam 5 menit, terintegrasi langsung dengan WhatsApp melalui gateway Fonnte API. Dilengkapi juga dengan mekanisme _Rate Limiting_ (maksimal 5 kali percobaan) untuk mencegah serangan _Brute Force_.
- **Network Resilience:** Penanganan _ConnectionException_ secara _graceful_ via Laravel Http Facade untuk modul pengiriman pesan OTP. Jika API Fonnte mengalami _downtime_, sistem tidak akan _crash_ (Internal Server Error 500) melainkan memberikan notifikasi ramah kepada pengguna dan mencatatnya dalam _log_.
- **Peningkatan Kapabilitas Backup:** Modul _Backup_ kini mendukung eksekusi _Full Application Snapshot_ (pengunduhan _database_ SQL sekaligus kompresi _source code_ secara utuh). Dilengkapi animasi _spinner_ progresif pada tombol unduh.
- **Penyempurnaan Modul Agreement (PKS):** Penambahan klasifikasi **Jenis Perjanjian** (Sementara/Draft/Rilis) pada _database_ dan antarmuka. Menyempurnakan form _Create/Edit_ untuk Staff PKS serta melakukan _rendering_ ulang pada _output_ cetak dokumen PDF agar lebih informatif.
- **Penyempurnaan Profil Pengguna & Formatting (Multi-Role):** Integrasi ekstensif kolom `phone_number` dan `employee_number` (NIP) untuk pelaporan pada tingkatan Users, Leaders, dan Treasurers. Ditambah dengan sistem pencarian _multi-role_ otomatis saat _user_ meminta OTP. Format penulisan NIP juga telah distandardisasi menggunakan _helper_ NipIndoFormat.
- **Refactoring & Pembersihan:** Penghapusan fungsi _deprecated_ (`imagedestroy`), pengoptimalan _query_, perbaikan kompatibilitas metode _download_ bawaan Laravel, pembersihan sisa _file build/zip_ lama, dan perombakan deskripsi _Tech Stack_ di dokumen `README.md`.

## [v1.2.9] - 2026-04-01

**_"Security Patch & Application Compliance"_**

Pembaruan krusial yang difokuskan pada penambalan celah keamanan sistem, penerapan hierarki akses birokrasi, dan kepatuhan perizinan (_compliance_).

- **Role-Based Access Fix (View-Only Mode):** Penerapan aturan pembatasan akses (_Akses View-Only_) secara menyeluruh untuk _role_ Pimpinan (Leader) di semua halaman aplikasi demi menjaga integritas pelaporan, melindungi sistem dari _human error_, serta secara dinamis membuka kembali hak untuk mengedit _Deposit Target_ (Target Setoran) ketika memang dibutuhkan secara struktural.
- **Security Update (Anti-IDOR):** Penambalan celah keamanan tingkat tinggi **IDOR** (_Insecure Direct Object Reference_) pada modul pengajuan dan persetujuan (Approval) Lokasi Parkir oleh Koordinator Lapangan. Mencegah manipulasi parameter ID pada URL yang berpotensi diretas oleh _user_ tidak sah.
- **Manajemen Versi Terintegrasi:** Implementasi modul _Rich Text Editor_ (Quill.js) untuk manajemen _changelog_ bawaan aplikasi. Admin kini dapat menulis catatan perubahan versi (_log_) secara ekspresif, rapi, dan dinamis langsung di dalam sistem tanpa menyentuh _database_ secara manual.
- **Kepatuhan Legal (EULA):** Penambahan dokumen _End-User License Agreement_ (EULA) yang komprehensif pada aplikasi untuk menegaskan perlindungan hak cipta dan legalitas kepemilikan piranti lunak bagi klien UPT Perparkiran.
- **Optimization:** Berbagai perbaikan _minor bug_ (_squashing_), pembersihan kode (_code cleaning_), dan pengoptimalan algoritma _query_ Eloquent ORM di _database_.

## [v1.2.0] - 2026-03-30

**_"Digital Workflow & Master Data Optimization"_**

Transformasi pengelolaan operasional dari berbasis kertas (konvensional) menjadi sepenuhnya _paperless_.

- **Digitalisasi Workflow (Update sPKP):** Menghadirkan fitur revolusioner berupa pengajuan titik lokasi baru, perpindahan lahan, atau pencabutan titik parkir langsung dari panel sistem Koordinator Lapangan/Mitra untuk dievaluasi oleh Dinas.
- **Penyempurnaan Modul Master Data:** Optimalisasi proses _flow_ validasi setoran harian/bulanan agar laporan keuangan yang ditinjau Bendahara tersinkronisasi lebih cepat dan presisi secara _real-time_.
- **Sistem Notifikasi Berbasis UX:** Integrasi masif _SweetAlert2_ pada berbagai _action_ CRUD (Create, Read, Update, Delete) guna memberikan _feedback_ dialog antarmuka yang modern, dinamis, dan menghindarkan _user_ dari kebingungan eksekusi sistem.

## [v1.1.0] - 2025-10-20

**_"Performance Leap & UX Enhancements"_**

- **Massive Performance Upgrade:** Penerapan sistem _Upgrade Skeleton All Page_ yang secara dramatis mengubah teknik transisi pemuatan (_loading_) aplikasi. Menggunakan konsep visual _skeleton placeholders_ untuk meminimalkan waktu tunggu layar kosong (_blank screen_) saat menavigasi menu yang berisi tabel berat. Meningkatkan retensi dan psikologis _User Experience_ (UX) secara drastis.

## [v1.0.0] - 2025-08-04

**_"The Genesis - Rilis Perdana SiPKS"_**

Versi _Milestone_ pertama. Aplikasi dirilis menjadi stabil _(Production Ready)_ setelah siklus pengerjaan _core logic_ dari bulan Juli 2025.

- **Rilis Perdana & File Management:** Penetapan fondasi arsitektur kode utama (_Codebase Init_) dan eksekusi konfigurasi _storage:link_ untuk menangani direktori penyimpan ribuan _file_ unggahan secara aman (_secure file storage_).
- **Manajemen GIS Terpusat (Sistem Informasi Geografis):** Peluncuran modul peta interaktif dengan _Leaflet.js_. Admin dan pengawas dapat mendaftarkan titik koordinat spesifik (_Latitude/Longitude_) untuk lahan parkir, yang divisualisasikan dalam bentuk pemetaan digital.
- **Manajemen Transaksi BLUD:** Implementasi modul _Report Deposite_ (Laporan Setoran Keuangan). Mendukung pencatatan riwayat pembayaran retribusi parkir hingga proses moderasi pengeditan data oleh _role Staff Keuangan_.
- **Manajemen PKS & Surat Keputusan:** Alokasi panel khusus untuk _Staff PKS_ guna mendata riwayat kontrak perjanjian (_Agreements_) dan legalitas Surat Keputusan bagi penunjukan mitra pengelola lahan parkir.
- **UI Engine Integration:** Hasil asimilasi iteratif dari _template_ dasar _Vuexy Premium_ ke dalam kerangka aplikasi _Blade Laravel_. Disusun dengan gaya _Enterprise_ yang proporsional dan responsif secara lintas perangkat.
