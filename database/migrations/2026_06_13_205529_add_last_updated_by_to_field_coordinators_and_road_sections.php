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
        Schema::table('field_coordinators', function (Blueprint $table) {
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null')->after('is_active');
        });

        Schema::table('road_sections', function (Blueprint $table) {
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_coordinators', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn('last_updated_by');
        });

        Schema::table('road_sections', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn('last_updated_by');
        });
    }
};
