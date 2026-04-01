# Changelog

Semua catatan perubahan (History Log) dari aplikasi **Sistem Parkir Terpadu (SPT) / SiPKS** dicatat di bawah ini.

## [v1.2.9] - 2026-04-01
- Penerapan **Akses View-Only** secara menyeluruh untuk role Pimpinan di semua halaman demi menjaga integritas laporan dan mencegah _human error_.
- Patch mitigasi celah keamanan **IDOR** (_Insecure Direct Object Reference_) pada modul pengajuan dan persetujuan lokasi parkir oleh Koordinator Lapangan.
- Implementasi Rich Text Editor (Quill.js) pada menu Manajemen Versi Aplikasi untuk penulisan log yang lebih ekspresif.
- Berbagai _minor bug squashing_, _code cleaning_, dan pengoptimalan *query* database.

## [v1.2.5] - 2026-03-15
- Optimasi tata letak halaman profil pengaturan (_Settings_) dan informasi UPT Perparkiran.
- Perbaikan _bug_ kritikal pada algoritma rumusan kalkulasi otomatis target setoran bulanan.
- Peningkatan sistem notifikasi _SweetAlert_ agar menjadi lebih responsif dan informatif.

## [v1.2.0] - 2026-03-01
- Fitur pengajuan perpindahan (_Add/Remove_) titik parkir secara paperless oleh Koordinator / Mitra.
- Sistem validasi setoran secara riil tiap bulan atau harian.
- Integrasi Peta Interaktif (Leaflet.js) untuk melacak posisi koordinat aktual titik parkir langsung dari dashboard.

## [v1.1.0] - 2026-02-14
- Penambahan modul _Role-Based Access Control_ (RBAC) & Manajemen Pengguna (Admin, Staff, Koordinator Lapangan, Bendahara, Pimpinan).
- Implementasi alur birokrasi Surat Keputusan (SK) dan Perjanjian Kerjasama (PKS).
- Perbaikan drastis pada antarmuka UI/UX di Dashboard Admin dan Dashboard Petugas Lapangan.

## [v1.0.0] - 2026-01-05
- Inisialisasi awal _codebase_ aplikasi Sistem Parkir Terpadu (SPT).
- Fitur dasar manajemen Master Data (Ruas Jalan, Titik Lokasi, Bank BLUD, dll).
- Setup hierarki keamanan arsitektur MVC pada ekosistem Laravel dan struktur _templating_ premium.
