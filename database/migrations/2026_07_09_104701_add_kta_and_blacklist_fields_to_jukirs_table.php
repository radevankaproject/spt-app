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
            $table->boolean('is_blacklisted')->default(false)->after('is_active');
            $table->string('kta_type')->nullable()->after('is_blacklisted'); // 'baru' or 'perpanjangan'
            $table->date('kta_start_date')->nullable()->after('kta_type');
            $table->date('kta_end_date')->nullable()->after('kta_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jukirs', function (Blueprint $table) {
            $table->dropColumn(['is_blacklisted', 'kta_type', 'kta_start_date', 'kta_end_date']);
        });
    }
};
