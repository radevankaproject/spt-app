# Changelog

Semua catatan perubahan (History Log) dari aplikasi **Sistem Parkir Terpadu (SPT) / SiPKS** dicatat di bawah ini. Dokumen ini merangkum seluruh perjalanan evolusi aplikasi dari inisialisasi awal hingga versi mutakhir.

## [v2.0.0] - 2026-06-18

**_"The Vuexy 10.11.1 Evolution & Ultimate M3 Preloader"_**

Pembaruan masif dan lompatan arsitektur antarmuka terbesar dalam sejarah aplikasi. Peningkatan dari versi dasar ke versi teranyar Vuexy dengan perombakan total pada sistem pemuatan (*loading*).

- **Full Vuexy v10.11.1 Migration:** 
    - Melakukan sinkronisasi dan migrasi menyeluruh dari seluruh kerangka kerja (*layouting*), aset, hingga komponen dasar ke **Vuexy versi 10.11.1 (Full Version Bootstrap 5)**.
    - Halaman otentikasi (Login, Register, Forgot Password, Reset Password, OTP) sepenuhnya diganti menggunakan struktur modern `auth-login-basic` dari Vuexy 10.11.1 dengan visual yang jauh lebih *clean* dan elegan.
- **Pemusnahan Sistem Skeleton Klasik:**
    - Menghapus 49+ file dan *script* `_skeleton-*.blade.php` lama secara permanen. Menghilangkan beban *render* HTML berganda pada setiap halaman (*page load*), sehingga ukuran DOM jauh lebih ringan dan cepat.
- **Inovasi Material 3 (M3) Squiggly Preloader:**
    - Memperkenalkan *Global Premium Preloader* berteknologi tinggi di `commonMaster.blade.php`. 
    - Menggunakan *requestAnimationFrame* dan perhitungan fungsi *Sine/Cosine* presisi untuk merender *SVG Squiggly Line* ala **Material 3 (Android 13+/Flutter M3)** yang memutar secara meliuk-liuk (bergerigi lembut) mengelilingi logo SiPKS.
    - Disempurnakan dengan animasi *stroke-dasharray* murni via CSS yang akan berhenti secara presisi (*indeterminate spinner*) tepat di *milisecond* ketika `window.onload` menyatakan seluruh gambar dan halaman siap.
- **Pembersihan Modul (*Housekeeping*):**
    - Menghapus sisa-sisa tata letak bawaan Laravel Breeze (`app.blade.php`, `guest.blade.php`) dan file-file `*copy.php` cadangan yang sudah menjadi *clutter* di ruang *server*.

## [v1.5.0] - 2026-06-13

**_"Premium Profile & Master Data Auditing"_**

Pembaruan yang berfokus pada perombakan total antarmuka Profil Pengguna menjadi lebih premium, dinamis, serta penguatan _audit trail_ (rekam jejak sejarah) pada Master Data.

- **Perombakan Total Dashboard & UI Roles (Premium Dashboard):**
    - Mendesain ulang seluruh halaman *dashboard* dari setiap *role* (Admin, Pimpinan, Bendahara, Staff Keuangan, Staff PKS) dengan tata letak visual standar *premium government*.
    - Menghadirkan komponen *Quick Stats* (6 kartu statistik), *Hero Search Card* gradasi, serta tabel informatif dengan desain minimalis tanpa menghilangkan fungsionalitas.
    - Menyesuaikan *Skeleton Loading* agar setiap transisi sinkron dengan tata letak *dashboard* terbaru.
- **Fitur Baru - Adendum/Diskon Khusus (Keringanan Tagihan):**
    - Mengakomodasi kebutuhan lapangan dengan penambahan form "Potongan/Keringanan" pada transaksi setoran bulanan. Nominal tagihan akan otomatis dikurangi berdasarkan besaran potongan.
    - Diperkuat dengan **Sistem Audit Diskon**, di mana sistem otomatis mendeteksi dan mencatat profil *user* yang menyetujui pemotongan tagihan tersebut. Rincian nama pemberi diskon dan alasannya kini dipampang transparan pada halaman *Detail Setoran* serta cetakan Struk PDF.
- **Perombakan Premium UI Halaman Setoran & Alur Pembayaran Sekuensial:**
    - Mendesain ulang halaman Detail Transaksi Setoran (*show*) dan Formulir Setoran (*create/edit*) dengan tampilan ultra-premium ala sistem instansi resmi (*Glassmorphism*, transisi halus, penjajaran kolom yang presisi, dan *Skeleton Loading* khusus).
    - Melengkapi fitur interaktif berupa Modal/Popup elegan untuk memperbesar *thumbnail* lampiran Bukti Transfer langsung tanpa membuka *tab* baru.
    - **Pembaruan Sistematis:** Menerapkan alur pembayaran **Sekuensial (Wajib Berurutan)**. Pengguna dipaksa melunasi tunggakan bulan terlama terlebih dahulu sebelum dapat membayar tagihan bulan terbaru. Sistem secara otomatis memberikan peringatan elegan jika mencoba melewati urutan bayar.
    - **Penguncian Akses Pintar (Validation Lock):** Mencegah *double-input* atau transaksi ganda. Jika seorang Koordinator Lapangan masih memiliki riwayat transaksi setoran yang berstatus *Pending / Menunggu Validasi*, maka form setoran baru akan **terkunci rapat** hingga Bendahara menyelesaikan validasi tersebut.
- **Inovasi Navigasi - Tab Jatuh Tempo:**
    - Menambahkan **Tab Jatuh Tempo** di posisi terdepan pada Halaman Indeks Setoran. Fitur ini menyeleksi dan mendeteksi seluruh PKS yang menunggak secara *real-time*.
    - Menambahkan tombol "Bayar Sekarang" yang langsung menghubungkan pengguna ke form input setoran dengan sistem pengisian kolom otomatis *(Auto-Trigger Target Agreement)*.
    - Kolom pencarian disederhanakan dan dioptimasi di tingkat *Controller* agar merender hasil dengan lebih gegas (*Super Fast Live Search*).
- **Pembaruan Modul Cetak PDF & Sidebar:**
    - Optimalisasi *layouting* cetak Struk Setoran (PDF) agar selalu presisi termuat pada 1 halaman utuh (dikunci dengan *page-break* CSS modern).
    - Mengganti nomenklatur menu di panel Sidebar dari *"Validasi Setoran"* menjadi *"Input Setoran"* untuk memperjelas fungsionalitas bagi multi-peran admin/keuangan.
- **Perombakan Total Profil Pengguna (Premium Profile):**
    - Redesain antarmuka Profil menjadi jauh lebih modern, premium, dan dinamis dengan efek *Skeleton Loading* untuk transisi.
    - Mengintegrasikan UI-Avatars API sebagai *fallback* elegan jika *user* belum mengunggah foto profil.
    - Fitur *Live Search* langsung terintegrasi di dalam *card* Aktivitas/Informasi Detail Pengguna.
    - Menampilkan informasi historis spesifik berdasarkan peran pengguna (Admin, Staff PKS, Bendahara, Staff Keu, Pimpinan), memastikan data yang relevan tampil di satu layar.
- **Penyempurnaan Manajemen Koordinator (SPA Modals):**
    - Akses Edit Data Lengkap dan Edit Data Login (Username/Password/Email) Koordinator Lapangan kini terintegrasi langsung di dalam halaman Indeks menggunakan teknologi *Single Page Application* (SPA) / Modals. Menghapus navigasi paksa ke halaman *show*.
    - Penerapan limitasi akses yang ketat; hanya *role* Admin yang diizinkan untuk memodifikasi Data Login Koordinator.
- **Penguatan Audit Trail (Rekam Jejak & Sejarah):**
    - **Titik Parkir:** Sistem kini melacak sejarah setiap perubahan informasi titik parkir menggunakan tabel `parking_location_histori`.
    - **Arsip PKS:** Modifikasi atau unggah ulang dokumen *scan* PKS (PDF) kini direkam secara permanen ke dalam tabel `agreement_pdf_histories`.
    - **Koordinator & Ruas Jalan:** Menambahkan field `last_updated_by` untuk melacak secara persis identitas *user* (Admin/Staff) terakhir yang membuat atau mengubah data tersebut.
- **Pembaruan Fitur Titik Parkir (Estimasi Luas & SRP):**
    - Menambahkan metrik baru pada form pendaftaran dan *edit* Titik Parkir berupa: Estimasi Luas Wilayah (m²), Estimasi Satuan Ruang Parkir (SRP) Roda 2, dan SRP Roda 4. Informasi ini turut diintegrasikan ke halaman Detail Lokasi dengan peringatan bahwa setoran tidak bergantung pada parameter tersebut.
- **Penanganan Error Premium & Perbaikan Bug 419:**
    - Mendesain ulang seluruh halaman *error* sistem (404, 403, 500, 419, 401, 503) menjadi sangat premium menggunakan tipografi dan ilustrasi *Vuexy Style*.
    - Memperbaiki _bug_ *Page Expired* (419) yang sering muncul di halaman *login* dengan menerapkan *smart redirect*; pengguna yang sesinya masih aktif akan otomatis diarahkan ke *dashboard* masing-masing *role* tanpa harus melihat *form login* lagi.
    - Menyempurnakan pelaporan *error* ke dalam `laravel.log` dengan pesan *error 500* yang formal dan elegan khusus untuk *environment production*.
- **Penyempurnaan Form Manajemen Versi (Changelog):**
    - Mengganti editor Quill.js yang rentan *error* validasi (*hidden input bug*) dengan *Textarea* standar yang tangguh.
    - Menambahkan dukungan penuh *Markdown* bawaan Laravel (`Str::markdown()`) sehingga pengguna kini bisa langsung *copy-paste* catatan rilis berformat raw `.md` secara murni tanpa merusak format aslinya.
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
