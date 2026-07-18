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
            $table->string('complaint_website_link')->nullable()->after('api_token_fonnte');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upt_profiles', function (Blueprint $table) {
            $table->dropColumn('complaint_website_link');
        });
    }
};
