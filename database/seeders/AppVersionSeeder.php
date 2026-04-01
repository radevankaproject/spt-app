<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppVersion::truncate();

        $versions = [
            [
                'version' => 'v1.0.0',
                'release_date' => Carbon::now()->subMonths(3),
                'changelog' => '<ul><li>Inisialisasi aplikasi Sistem Parkir Terpadu (SPT).</li><li>Fitur manajemen Master Data (Ruas Jalan, Titik Lokasi, Bank BLUD, dll).</li><li>Setup dasar arsitektur MVC dan templating dashboard.</li></ul>'
            ],
            [
                'version' => 'v1.1.0',
                'release_date' => Carbon::now()->subMonths(2),
                'changelog' => '<ul><li>Penambahan modul Role-Based Access Control (RBAC) & Manajemen Pengguna.</li><li>Implementasi alur Surat Keputusan (SK) dan Perjanjian Kerjasama (PKS).</li><li>Perbaikan UI/UX pada Dashboard Admin & Petugas Lapangan.</li></ul>'
            ],
            [
                'version' => 'v1.2.0',
                'release_date' => Carbon::now()->subMonths(1),
                'changelog' => '<ul><li>Fitur pengajuan perpindahan (Add/Remove) titik parkir oleh Koordinator / Mitra.</li><li>Sistem validasi setoran secara riil tiap bulan atau harian.</li><li>Integrasi Peta Interaktif (Leaflet.js) untuk melacak posisi koordinat aktual titik parkir.</li></ul>'
            ],
            [
                'version' => 'v1.2.5',
                'release_date' => Carbon::now()->subWeeks(2),
                'changelog' => '<ul><li>Optimasi halaman profil pengaturan (Settings) dan UPT Perparkiran.</li><li>Perbaikan bug pada rumusan kalkulasi otomatis target setoran.</li><li>Peningkatan sistem notifikasi *SweetAlert* agar lebih responsif.</li></ul>'
            ],
            [
                'version' => 'v1.2.9',
                'release_date' => Carbon::now(),
                'changelog' => '<ul><li>Penerapan <strong>Akses View-Only</strong> secara menyeluruh untuk role Pimpinan di semua halaman demi menjaga integritas data.</li><li>Patch mitigasi celah keamanan IDOR (Insecure Direct Object Reference) pada modul persetujuan lokasi parkir oleh Koordinator Lapangan.</li><li>Implementasi Rich Text Editor (Quill.js) pada menu Manajemen Versi Aplikasi ini sendiri! 🚀</li><li>Berbagai minor bug squashing, code cleaning, dan pengoptimalan *query* database.</li></ul>'
            ],
        ];

        foreach ($versions as $v) {
            AppVersion::create($v);
        }

        // Membersihkan cache agar update terbaca di sistem footer
        Cache::forget('version');
    }
}
