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
        Schema::table('jukirs', function (Blueprint $table) {
            $table->string('id_jukir', 10)->nullable()->after('id');
            $table->string('image_ktp')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jukirs', function (Blueprint $table) {
            $table->dropColumn(['id_jukir', 'image_ktp']);
        });
    }
};
