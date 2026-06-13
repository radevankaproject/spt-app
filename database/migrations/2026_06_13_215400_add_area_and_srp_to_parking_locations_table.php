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
        Schema::table('parking_locations', function (Blueprint $table) {
            $table->decimal('estimated_area', 10, 2)->nullable()->after('daily_deposit')->comment('Estimasi luas wilayah parkir (m²)');
            $table->unsignedInteger('estimated_srp_r2')->nullable()->after('estimated_area')->comment('Estimasi jumlah SRP Roda 2');
            $table->unsignedInteger('estimated_srp_r4')->nullable()->after('estimated_srp_r2')->comment('Estimasi jumlah SRP Roda 4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_locations', function (Blueprint $table) {
            $table->dropColumn(['estimated_area', 'estimated_srp_r2', 'estimated_srp_r4']);
        });
    }
};
