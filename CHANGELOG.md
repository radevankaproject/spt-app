# Changelog

Semua catatan perubahan (History Log) dari aplikasi **Sistem Parkir Terpadu (SPT) / SiPKS** dicatat di bawah ini. Dokumen ini merangkum seluruh perjalanan evolusi aplikasi dari inisialisasi awal hingga versi mutakhir.

## [v1.4.0] - 2026-06-12
**_"The Map Dashboard & Document Upload Update"_**

Pembaruan signifikan yang berfokus pada penyempurnaan UI/UX Dashboard Pimpinan, penyempurnaan modul PDF, serta penambahan kapabilitas pengarsipan dokumen PKS fisik (bertanda tangan) secara digital.

- **Dashboard Pimpinan Interaktif:** 
  - Mengintegrasikan Peta Persebaran Lokasi Parkir (Leaflet.js) langsung ke dalam _dashboard_ Pimpinan dengan tampilan visual yang komprehensif, _auto fit-bounds_ ([60, 60] padding, zoom 13), serta optimasi _rendering_ anti-macet via *delay*.
  - Merombak tabel Kontrak PKS Terbaru dengan hanya menampilkan PKS Aktif dan menambahkan metrik analitik: jumlah titik parkir aktif, setoran harian, dan target bulanan.
- **Pembaruan Fitur Titik Parkir:** 
  - Lokasi parkir dengan status "Tidak Tersedia" kini difilter agar fitur *edit*-nya hanya mengizinkan pengubahan Nama Lokasi, Koordinat, Foto, dan dokumen PDF (pengajuan/berita acara), sementara isian lain terkunci otomatis.
  - Peta pada halaman *edit* titik parkir dioptimalkan agar selalu termuat penuh (anti kotak abu-abu/terpotong) dan letak *marker* selalu presisi.
- **Fitur Baru - Perpanjangan PKS (Renewal):** 
  - Menghadirkan kapabilitas untuk memperpanjang PKS (*Renewal*). Saat diperpanjang, seluruh titik parkir aktif akan otomatis dimigrasi dari PKS lama ke PKS baru. 
  - PKS lama secara cerdas akan diubah statusnya menjadi *expired* dan tanggal berahirnya otomatis disesuaikan (*end date* dipotong ke hari sebelum *start date* PKS baru), sehingga tidak ada celah kekosongan waktu maupun tumpang tindih masa aktif.
- **Manajemen Arsip PKS & Expired:** 
  - PKS berstatus *expired* otomatis menyembunyikan tombol Edit dan Print di halaman indeks maupun detail.
  - Saat membuka detail PKS berstatus *expired*, antarmuka secara intuitif akan langsung membuka tab "Arsip PKS".
  - Menambahkan perlindungan saat mengunggah PDF *scan* (arsip fisik). PKS *expired* yang sudah memiliki *file scan* tidak bisa diunggah ulang. Pada PKS aktif, pengguna akan diberi peringatan *SweetAlert* jika ingin menimpa *file* lama.
- **Sistem Penyimpanan Hibrida (Ghostscript):** Melakukan perbaikan krusial pada alur unggah dan kompresi PDF (Ghostscript) agar menggunakan _temporary directory_ sistem (`sys_get_temp_dir()`) dan dipindahkan via `Storage::disk('public')`. Ini memecahkan masalah isu izin akses (Permission Denied) antara *web-server* (`www-data`) dan CLI (`php artisan serve`), memastikan unggahan sukses di semua environment.


## [v1.3.1] - 2026-06-07
**_"Agreement Enhancements & Workflow Refinements"_**

Pembaruan yang menitikberatkan pada penyempurnaan alur kerja Perjanjian Kerjasama (PKS), optimasi dokumen PDF, sinkronisasi data antar tabel, serta perbaikan minor pada UI/UX dan sistem *routing*.

- **Penyempurnaan Dokumen PKS (PDF):** Menghapus implementasi *page-break* paksa pada templat PDF (`agreement.blade.php`) untuk mencegah munculnya halaman kosong yang tidak diinginkan ketika teks alamat terlalu panjang. 
- **Manajemen Status Jabatan (Pimpinan):** Menambahkan field `status_jabatan` pada form modal Pimpinan (`LeaderController`). Sistem kini mendeteksi status seperti "Plt." atau "Plh." dan menampilkannya secara otomatis dengan format huruf kapital di awal (Title Case) pada hasil cetak dokumen PKS (contoh: "Plt. Kepala UPT Perparkiran").
- **Manajemen Arsip Digital PKS:** Menambahkan fitur unggah dokumen hasil _scan_ PKS fisik (bertanda tangan) dalam format PDF (maks. 1MB). Terintegrasi dengan **Ghostscript** untuk mengompresi ukuran file PDF secara cerdas sebelum disimpan ke server. Antarmuka unggah dilengkapi dengan _Progress Bar_ premium dan validasi SweetAlert. Arsip kini dapat diunduh langsung oleh Admin, Pimpinan, Staff PKS, maupun Koordinator Lapangan.
- **Fitur Baru - Peta Wilayah Parkir:** Menghadirkan modul interaktif "Peta Wilayah Parkir" yang memvisualisasikan seluruh titik sebaran parkir dalam antarmuka peta digital terintegrasi (berbasis Mapbox). Dilengkapi dengan efek _Skeleton Loading_ untuk transisi halaman yang elegan.
- **Pembaruan Status & Shortcut Navigasi:** 
  - Menambahkan *global shortcut* `Ctrl + /` atau `Ctrl + K` untuk secara instan mengaktifkan (fokus) kotak pencarian global pada *navbar*, mempercepat efisiensi navigasi pengguna.
  - Menambahkan status baru "Menunggu Perpanjangan" (`pending_renewal`) pada opsi *dropdown* form *Edit Agreement*.
- **Optimalisasi Sinkronisasi Data Pengguna:** Memperbaiki celah logika sinkronisasi antara tabel peran spesifik (`leaders`, `field_coordinators`, `treasurers`) dengan tabel induk (`users`). Kini setiap kali terdapat pembaruan Nomor Handphone, sistem secara serentak akan memperbaruinya pada tabel induk. Turut dieksekusi skrip retroaktif (`fix_phone.php`) untuk menambal data lama yang kosong tanpa perlu *refresh database*.
- **Penyempurnaan Modal UI (UX):** 
  - Menghapus form halaman terpisah (_Create/Edit_) untuk Pimpinan agar lebih seragam, mencegah kerancuan dan sepenuhnya beralih ke desain Modal satu pintu.
  - Menambahkan tombol _Clear_ (ikon silang merah) secara *inline* menggunakan struktur _input-group_ pada input "Tanggal Akhir Menjabat" di modal Pimpinan dan Bendahara, memungkinkan pengguna mengosongkan nilai dengan satu kali klik.
- **Perbaikan Rute & Sidebar (Role Authorization):** 
  - Memperbaiki _bug_ di mana menu _sidebar_ "Titik Parkir" ikut menyala (*active*) ketika halaman "Peta Wilayah Parkir" dibuka. 
  - Melakukan restrukturisasi hak akses secara ketat untuk rute `parking-locations.map`. Peta Sebaran Parkir kini secara eksklusif hanya dapat diakses oleh Admin, Pimpinan, Bendahara, dan Staff PKS, sekaligus memblokir akses yang tidak relevan dari peran Staff Keuangan.


## [v1.3.0] - 2026-06-06
**_"The Enterprise Dashboard & Security Update"_**

Pembaruan masif yang difokuskan pada perombakan _user interface_ secara ekstensif, peningkatan standar keamanan otentikasi tingkat tinggi, serta fitur-fitur administratif esensial.

- **Major Overhaul Dashboard (UI/UX):** Redesain antarmuka (_Premium Dashboard_) untuk seluruh peran pengguna (Admin, Pimpinan, Bendahara, Staff Keuangan, & Staff PKS). Tampilan kini 100% selaras dengan tema Vuexy Premium, diintegrasikan dengan modul profil dinamis (UI-Avatars API) berdasarkan nama pengguna, serta implementasi efek _Skeleton Loading_ untuk transisi halaman yang lebih mulus.
- **Sistem Keamanan & Akses (WhatsApp OTP):** Implementasi metode pemulihan kata sandi (_Forgot Password_) yang jauh lebih modern dan aman. Sistem kini mengirimkan *One Time Password* (OTP) 6 digit yang *expired* dalam 5 menit, terintegrasi langsung dengan WhatsApp melalui gateway Fonnte API. Dilengkapi juga dengan mekanisme *Rate Limiting* (maksimal 5 kali percobaan) untuk mencegah serangan _Brute Force_.
- **Network Resilience:** Penanganan _ConnectionException_ secara _graceful_ via Laravel Http Facade untuk modul pengiriman pesan OTP. Jika API Fonnte mengalami *downtime*, sistem tidak akan _crash_ (Internal Server Error 500) melainkan memberikan notifikasi ramah kepada pengguna dan mencatatnya dalam *log*.
- **Peningkatan Kapabilitas Backup:** Modul _Backup_ kini mendukung eksekusi *Full Application Snapshot* (pengunduhan *database* SQL sekaligus kompresi _source code_ secara utuh). Dilengkapi animasi _spinner_ progresif pada tombol unduh.
- **Penyempurnaan Modul Agreement (PKS):** Penambahan klasifikasi **Jenis Perjanjian** (Sementara/Draft/Rilis) pada _database_ dan antarmuka. Menyempurnakan form *Create/Edit* untuk Staff PKS serta melakukan *rendering* ulang pada _output_ cetak dokumen PDF agar lebih informatif.
- **Penyempurnaan Profil Pengguna & Formatting (Multi-Role):** Integrasi ekstensif kolom `phone_number` dan `employee_number` (NIP) untuk pelaporan pada tingkatan Users, Leaders, dan Treasurers. Ditambah dengan sistem pencarian _multi-role_ otomatis saat *user* meminta OTP. Format penulisan NIP juga telah distandardisasi menggunakan _helper_ NipIndoFormat.
- **Refactoring & Pembersihan:** Penghapusan fungsi _deprecated_ (`imagedestroy`), pengoptimalan *query*, perbaikan kompatibilitas metode _download_ bawaan Laravel, pembersihan sisa *file build/zip* lama, dan perombakan deskripsi *Tech Stack* di dokumen `README.md`.

## [v1.2.9] - 2026-04-01
**_"Security Patch & Application Compliance"_**

Pembaruan krusial yang difokuskan pada penambalan celah keamanan sistem, penerapan hierarki akses birokrasi, dan kepatuhan perizinan (*compliance*).

- **Role-Based Access Fix (View-Only Mode):** Penerapan aturan pembatasan akses (*Akses View-Only*) secara menyeluruh untuk *role* Pimpinan (Leader) di semua halaman aplikasi demi menjaga integritas pelaporan, melindungi sistem dari _human error_, serta secara dinamis membuka kembali hak untuk mengedit *Deposit Target* (Target Setoran) ketika memang dibutuhkan secara struktural.
- **Security Update (Anti-IDOR):** Penambalan celah keamanan tingkat tinggi **IDOR** (_Insecure Direct Object Reference_) pada modul pengajuan dan persetujuan (Approval) Lokasi Parkir oleh Koordinator Lapangan. Mencegah manipulasi parameter ID pada URL yang berpotensi diretas oleh _user_ tidak sah.
- **Manajemen Versi Terintegrasi:** Implementasi modul _Rich Text Editor_ (Quill.js) untuk manajemen *changelog* bawaan aplikasi. Admin kini dapat menulis catatan perubahan versi (*log*) secara ekspresif, rapi, dan dinamis langsung di dalam sistem tanpa menyentuh *database* secara manual.
- **Kepatuhan Legal (EULA):** Penambahan dokumen _End-User License Agreement_ (EULA) yang komprehensif pada aplikasi untuk menegaskan perlindungan hak cipta dan legalitas kepemilikan piranti lunak bagi klien UPT Perparkiran.
- **Optimization:** Berbagai perbaikan _minor bug_ (_squashing_), pembersihan kode (_code cleaning_), dan pengoptimalan algoritma *query* Eloquent ORM di *database*.

## [v1.2.0] - 2026-03-30
**_"Digital Workflow & Master Data Optimization"_**

Transformasi pengelolaan operasional dari berbasis kertas (konvensional) menjadi sepenuhnya *paperless*.

- **Digitalisasi Workflow (Update sPKP):** Menghadirkan fitur revolusioner berupa pengajuan titik lokasi baru, perpindahan lahan, atau pencabutan titik parkir langsung dari panel sistem Koordinator Lapangan/Mitra untuk dievaluasi oleh Dinas.
- **Penyempurnaan Modul Master Data:** Optimalisasi proses *flow* validasi setoran harian/bulanan agar laporan keuangan yang ditinjau Bendahara tersinkronisasi lebih cepat dan presisi secara *real-time*.
- **Sistem Notifikasi Berbasis UX:** Integrasi masif *SweetAlert2* pada berbagai *action* CRUD (Create, Read, Update, Delete) guna memberikan *feedback* dialog antarmuka yang modern, dinamis, dan menghindarkan *user* dari kebingungan eksekusi sistem.

## [v1.1.0] - 2025-10-20
**_"Performance Leap & UX Enhancements"_**

- **Massive Performance Upgrade:** Penerapan sistem *Upgrade Skeleton All Page* yang secara dramatis mengubah teknik transisi pemuatan (_loading_) aplikasi. Menggunakan konsep visual *skeleton placeholders* untuk meminimalkan waktu tunggu layar kosong (*blank screen*) saat menavigasi menu yang berisi tabel berat. Meningkatkan retensi dan psikologis _User Experience_ (UX) secara drastis.

## [v1.0.0] - 2025-08-04
**_"The Genesis - Rilis Perdana SiPKS"_**

Versi *Milestone* pertama. Aplikasi dirilis menjadi stabil *(Production Ready)* setelah siklus pengerjaan *core logic* dari bulan Juli 2025.

- **Rilis Perdana & File Management:** Penetapan fondasi arsitektur kode utama (*Codebase Init*) dan eksekusi konfigurasi _storage:link_ untuk menangani direktori penyimpan ribuan _file_ unggahan secara aman (*secure file storage*).
- **Manajemen GIS Terpusat (Sistem Informasi Geografis):** Peluncuran modul peta interaktif dengan *Leaflet.js*. Admin dan pengawas dapat mendaftarkan titik koordinat spesifik (*Latitude/Longitude*) untuk lahan parkir, yang divisualisasikan dalam bentuk pemetaan digital.
- **Manajemen Transaksi BLUD:** Implementasi modul *Report Deposite* (Laporan Setoran Keuangan). Mendukung pencatatan riwayat pembayaran retribusi parkir hingga proses moderasi pengeditan data oleh *role Staff Keuangan*.
- **Manajemen PKS & Surat Keputusan:** Alokasi panel khusus untuk *Staff PKS* guna mendata riwayat kontrak perjanjian (*Agreements*) dan legalitas Surat Keputusan bagi penunjukan mitra pengelola lahan parkir.
- **UI Engine Integration:** Hasil asimilasi iteratif dari _template_ dasar *Vuexy Premium* ke dalam kerangka aplikasi *Blade Laravel*. Disusun dengan gaya _Enterprise_ yang proporsional dan responsif secara lintas perangkat.

