<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Tambahkan 'pending' ke dalam jajaran ENUM, dan jadikan default
        DB::statement("ALTER TABLE agreements MODIFY COLUMN status ENUM('active', 'expired', 'terminated', 'pending_renewal', 'pending') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke asal jika di-rollback (Pastikan tidak ada data yang masih 'pending' saat rollback)
        DB::statement("ALTER TABLE agreements MODIFY COLUMN status ENUM('active', 'expired', 'terminated', 'pending_renewal') NOT NULL DEFAULT 'active'");
    }
};
