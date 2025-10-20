<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::with('creator')->latest()->get();
        return view('admin.backup.index', compact('backups'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);

            // ✅ LOGIKA BARU YANG LEBIH KUAT
            // Cari backup terbaru secara manual di storage
            $disk       = config('backup.backup.destination.disks')[0];
            $backupPath = config('backup.backup.name');

            $allBackups = Storage::disk($disk)->files($backupPath);

            if (empty($allBackups)) {
                throw new \Exception('Backup command ran, but no backup file was found.');
            }

            // Urutkan file berdasarkan waktu modifikasi terakhir untuk menemukan yang terbaru
            usort($allBackups, function ($a, $b) use ($disk) {
                return Storage::disk($disk)->lastModified($b) <=> Storage::disk($disk)->lastModified($a);
            });

            $latestBackupPath = $allBackups[0];

            Backup::create([
                'file_name'          => basename($latestBackupPath),
                'file_path'          => $latestBackupPath,
                'file_size'          => Storage::disk($disk)->size($latestBackupPath),
                'created_by_user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('admin.backup.index')->with('success', 'Backup database berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Spatie backup failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        $disk = 'local'; // Sesuai dengan konfigurasi di config/backup.php

        // ✅ Gunakan Storage::exists() untuk mengecek file di disk yang benar
        if (Storage::disk($disk)->exists($backup->file_path)) {
            // ✅ Gunakan Storage::download() untuk mengunduh file
            return Storage::disk($disk)->download($backup->file_path, $backup->file_name);
        }

        return redirect()->back()->with('error', 'File backup tidak ditemukan di disk.');
    }

    public function destroy(Backup $backup)
    {
        $disk = 'local'; // Sesuai dengan konfigurasi di config/backup.php

        DB::beginTransaction();
        try {
            // ✅ Gunakan Storage::delete() untuk menghapus file
            if (Storage::disk($disk)->exists($backup->file_path)) {
                Storage::disk($disk)->delete($backup->file_path);
            }

            $backup->delete();
            DB::commit();

            return redirect()->route('admin.backup.index')->with('success', 'Backup berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus backup: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus backup.');
        }
    }
}
