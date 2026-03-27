<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agreement;
use App\Models\AgreementHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredAgreements extends Command
{
    /**
     * Nama perintah
     */
    protected $signature = 'agreements:check-status';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Mengecek dan mengupdate otomatis status PKS (H-7 menjadi Pending Renewal, lewat batas menjadi Expired) TANPA melepaskan lokasi parkir.';

    /**
     * Eksekusi logic
     */
    public function handle()
    {
        $this->info('Mulai mengecek status PKS (Pending Renewal & Expired)...');
        $now = now();

        DB::beginTransaction();
        try {
            $expiredCount = 0;
            $pendingCount = 0;

            // ==========================================
            // 1. LOGIKA KEDALUWARSA (EXPIRED)
            // ==========================================
            // Ambil PKS (Active / Pending Renewal) yang end_date-nya SUDAH LEWAT dari hari ini
            $expiredAgreements = Agreement::whereIn('status', ['active', 'pending_renewal'])
                ->whereDate('end_date', '<', $now->toDateString())
                ->get();

            foreach ($expiredAgreements as $agreement) {
                // Ubah status PKS menjadi expired
                $agreement->update(['status' => 'expired']);

                // Catat di History PKS
                AgreementHistory::create([
                    'agreement_id'       => $agreement->id,
                    'changed_by_user_id' => null, // Sistem
                    'event_type'         => 'status_changed',
                    'notes'              => 'Status diubah otomatis menjadi "Expired" oleh sistem karena masa berlaku telah habis. (Lokasi parkir tetap ditahan).',
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                // ✅ FIX: Logika pelepasan lokasi parkir dihapus di sini.
                // Lokasi akan tetap terikat agar Zona tidak hilang saat akan di-Edit/Perpanjang.

                $expiredCount++;
            }

            // ==========================================
            // 2. LOGIKA MASA TENGGANG (PENDING RENEWAL)
            // ==========================================
            // Ambil PKS Active yang end_date-nya sisa <= 7 hari (tapi belum kedaluwarsa)
            $pendingAgreements = Agreement::where('status', 'active')
                ->whereDate('end_date', '>=', $now->toDateString())
                ->whereDate('end_date', '<=', $now->copy()->addDays(7)->toDateString())
                ->get();

            foreach ($pendingAgreements as $agreement) {
                // Ubah status PKS menjadi pending_renewal
                $agreement->update(['status' => 'pending_renewal']);

                // Catat di History PKS
                AgreementHistory::create([
                    'agreement_id'       => $agreement->id,
                    'changed_by_user_id' => null, // Sistem
                    'event_type'         => 'status_changed',
                    'notes'              => 'Status diubah otomatis menjadi "Pending Renewal" karena sisa masa berlaku 7 hari atau kurang.',
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                $pendingCount++;
            }

            DB::commit();

            $this->info("Selesai! {$expiredCount} PKS Expired, {$pendingCount} PKS Pending Renewal.");
            Log::info("CronJob Status PKS: {$expiredCount} Expired, {$pendingCount} Pending Renewal.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            Log::error('CheckExpiredAgreements Command Error: ' . $e->getMessage());
        }
    }
}
