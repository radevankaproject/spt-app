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
        Schema::table('upt_profiles', function (Blueprint $table) {
            // Menambahkan field baru
            // 'after' opsional, hanya agar urutan kolom di database lebih rapi
            $table->string('login_greetings')->nullable()->after('app_name');
            $table->string('api_token_fonnte')->nullable()->after('login_greetings');
            $table->text('about_us')->nullable()->after('website');
            $table->text('privacy_policy')->nullable()->after('about_us');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upt_profiles', function (Blueprint $table) {
            // Menghapus field jika migrasi di-rollback
            $table->dropColumn([
                'login_greetings',
                'api_token_fonnte',
                'about_us',
                'privacy_policy'
            ]);
        });
    }
};
