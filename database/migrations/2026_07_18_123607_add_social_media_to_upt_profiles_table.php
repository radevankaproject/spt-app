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
            $table->string('social_fb')->nullable();
            $table->string('social_ig')->nullable();
            $table->string('social_tiktok')->nullable();
            $table->string('social_x')->nullable();
            $table->string('social_youtube')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upt_profiles', function (Blueprint $table) {
            $table->dropColumn(['social_fb', 'social_ig', 'social_tiktok', 'social_x', 'social_youtube']);
        });
    }
};
