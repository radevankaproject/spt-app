# Changelog

Semua catatan perubahan (History Log) dari aplikasi **Sistem Parkir Terpadu (SPT) / SiPKS** dicatat di bawah ini.

## [v1.3.0] - 2026-06-06

- **Major Overhaul Dashboard:** Redesain antarmuka (_Premium Dashboard_) untuk seluruh peran pengguna (Admin, Pimpinan, Bendahara, Staff Keuangan, & Staff PKS) agar 100% selaras dengan tema Vuexy, termasuk integrasi UI-Avatars otomatis dan _skeleton loading_.
- **Sistem Keamanan & Akses:** Implementasi pemulihan kata sandi (_Forgot Password_) yang lebih aman menggunakan _One Time Password_ (OTP) terintegrasi langsung dengan WhatsApp (Fonnte API), ditopang oleh penanganan _ConnectionException_ yang _graceful_ via Laravel Http Facade.
- **Peningkatan Kapabilitas Backup:** Modul _Backup_ kini mendukung eksekusi _Full Application Snapshot_ (Database + Kode Sumber) secara komprehensif, dilengkapi animasi _spinner_ progresif.
- **Penyempurnaan Modul Agreement (PKS):** Penambahan klasifikasi **Jenis Perjanjian** (Sementara/Draft/Rilis) pada _database_ dan UI, penyempurnaan form _Create/Edit_, serta pembaruan format _output_ dokumen PDF.
- **Penyempurnaan Profil Pengguna & Formatting:** Integrasi kolom `phone_number` dan `employee_number` (NIP) untuk pelaporan dengan pencarian nomor terpusat (multi-role). Ditambah dengan implementasi _helper_ standar penulisan NIP Indonesia yang seragam di seluruh halaman _dashboard_ dan tabel.
- **Refactoring & Pembersihan _Bug_ IDE:** Penghapusan fungsi _deprecated_, pengoptimalan _query_, pembersihan sisa file _.zip_, serta pembaruan mendetail *Tech Stack* di dokumen `README.md`.

## [v1.2.9] - 2026-04-01

- **Role-Based Access Fix:** Penerapan **Akses View-Only** secara menyeluruh untuk role Pimpinan di semua halaman demi menjaga integritas laporan dan mencegah _human error_, serta membuka kembali akses edit target setoran untuk Pimpinan saat dibutuhkan.
- **Security Update:** Patch mitigasi celah keamanan **IDOR** (_Insecure Direct Object Reference_) pada modul pengajuan dan persetujuan lokasi parkir oleh Koordinator Lapangan.
- **Fitur Baru (Manajemen Versi):** Implementasi _Rich Text Editor_ (Quill.js) pada menu Manajemen Versi Aplikasi untuk penulisan *log* yang lebih ekspresif dan dinamis (terekam di _database_).
- **Dokumentasi Lanjutan:** Penambahan _End-User License Agreement_ (EULA) untuk perlindungan legalitas kepemilikan aplikasi pada Klien UPT Perparkiran.
- **Optimization:** Berbagai _minor bug squashing_, _code cleaning_, dan pengoptimalan _query_ database.

## [v1.2.0] - 2026-03-30

- **Digitalisasi Workflow (Paperless):** Menghadirkan fitur baru (_Update sPKP_) berupa pengajuan penambahan, perpindahan, atau pencabutan titik parkir secara sistem dari Koordinator/Mitra ke Dinas.
- **Penyempurnaan Modul Master Data:** Optimalisasi *flow* validasi setoran secara riil.
- **Sistem Notifikasi:** Peningkatan *feedback* UI dengan integrasi sistem _SweetAlert_ untuk respon interaksi pengguna yang lebih baik.

## [v1.1.0] - 2025-10-20

- **Performance & UI Upgrade:** _Upgrade skeleton_ secara masif (*upgrade skeleton all page*) pada seluruh halaman untuk mempercepat persepsi waktu *loading* aplikasi bagi *user* ketika berpindah menu (meningkatkan _User Experience_ / UX).

## [v1.0.0] - 2025-08-04

- **Rilis Perdana (Publish v1.0.0):** Inisialisasi stabil _codebase_ aplikasi Sistem Parkir Terpadu (SPT).
- **File Management:** Pengaturan awal _storage link_ untuk manajemen berkas terintegrasi.
- **Manajemen GIS Terpusat:** Penambahan fitur koordinat (Latitude/Longitude) beserta opsi pengunggahan *file* di manajemen Titik Parkir (Parking Location).
- **Manajemen Transaksi:** Implementasi alur input setoran/pembayaran (Report Deposite Menu) beserta fitur _edit_ transaksi yang bisa ditangani oleh role _Staff Keuangan_.
- **Sistem PKS (Staff PKS):** Alokasi modul kerja spesifik untuk role *Staff PKS* guna mendata surat keputusan dan kesepakatan parkir.
- **UI Engine Integration:** Inisialisasi awal adopsi template premium (Vuexy base) yang sudah disesuaikan dan di-fix secara iteratif.

