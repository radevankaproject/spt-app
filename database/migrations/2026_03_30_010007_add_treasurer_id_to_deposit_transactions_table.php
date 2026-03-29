<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            // Ditambahkan relasi ke tabel treasurers (Boleh null kalau misal bendaharanya dihapus permanen suatu saat)
            $table->foreignId('treasurer_id')->nullable()->after('agreement_id')->constrained('treasurers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->dropForeign(['treasurer_id']);
            $table->dropColumn('treasurer_id');
        });
    }
};
