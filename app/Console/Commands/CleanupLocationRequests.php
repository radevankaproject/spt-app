<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LocationRequest;
use Illuminate\Support\Facades\Storage;

class CleanupLocationRequests extends Command
{
    /**
     * Nama command yang akan dipanggil di terminal
     */
    protected $signature = 'cleanup:location-requests';

    /**
     * Deskripsi command
     */
    protected $description = 'Menghapus file fisik (gambar & proposal) dari pengajuan yang sudah disetujui/ditolak lebih dari 60 hari untuk menghemat storage server.';

    /**
     * Eksekusi Command
     */
    public function handle()
    {
        $this->info("Memulai proses pembersihan file lampiran usang...");

        // Cari data yang Approved/Rejected, lebih tua dari 60 hari, dan MASIH PUNYA file
        $expiredRequests = LocationRequest::whereIn('status', ['approved', 'rejected'])
            ->where('updated_at', '<', now()->subDays(60))
            ->where(function ($query) {
                $query->whereNotNull('image')->orWhereNotNull('proposal_document');
            })
            ->get();

        if ($expiredRequests->isEmpty()) {
            $this->info("Tidak ada file usang yang perlu dibersihkan hari ini.");
            return;
        }

        $count = 0;
        $sizeSaved = 0;

        foreach ($expiredRequests as $req) {
            // Hapus Image jika ada
            if ($req->image && Storage::disk('public')->exists($req->image)) {
                $sizeSaved += Storage::disk('public')->size($req->image);
                Storage::disk('public')->delete($req->image);
            }
            
            // Hapus Proposal jika ada
            if ($req->proposal_document && Storage::disk('public')->exists($req->proposal_document)) {
                $sizeSaved += Storage::disk('public')->size($req->proposal_document);
                Storage::disk('public')->delete($req->proposal_document);
            }

            // Putuskan link file dari database (kosongkan fieldnya)
            $req->update([
                'image' => null,
                'proposal_document' => null,
            ]);

            $count++;
        }

        $sizeSavedMB = round($sizeSaved / 1048576, 2);
        $this->info("Pembersihan Selesai! Berhasil membersihkan {$count} pengajuan dan menghemat storage sebesar {$sizeSavedMB} MB.");
    }
}