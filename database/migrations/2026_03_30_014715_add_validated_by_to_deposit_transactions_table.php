<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            // Mencatat ID User yang melakukan validasi
            $table->foreignId('validated_by_user_id')->nullable()->after('validation_date')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->dropForeign(['validated_by_user_id']);
            $table->dropColumn('validated_by_user_id');
        });
    }
};
