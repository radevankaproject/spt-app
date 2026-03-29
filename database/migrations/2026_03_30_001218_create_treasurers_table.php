<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ SAPU JAGAT: Hapus tabel hantu sisa error sebelumnya (jika ada)
        Schema::dropIfExists('treasurer_histories');
        Schema::dropIfExists('treasurers');

        // 1. Bikin tabel Bapaknya dulu (Treasurers)
        Schema::create('treasurers', function (Blueprint $table) {
            $table->id();
            // ... (kode antum selanjutnya tetap sama ke bawah) ...
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_number', 18)->unique();
            $table->enum('status_jabatan', ['tetap', 'plt', 'plh'])->default('tetap');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // 2. Baru bikin tabel Anaknya (Treasurer Histories)
        Schema::create('treasurer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treasurer_id')->constrained()->cascadeOnDelete();
            $table->enum('status_jabatan', ['tetap', 'plt', 'plh']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Waktu di-rollback, pastikan drop anaknya dulu, baru bapaknya
        Schema::dropIfExists('treasurer_histories');
        Schema::dropIfExists('treasurers');
    }
};
