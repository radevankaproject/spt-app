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
        Schema::table('jukir_complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('jukir_complaints', 'is_violation_proven')) {
                $table->boolean('is_violation_proven')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jukir_complaints', function (Blueprint $table) {
            if (Schema::hasColumn('jukir_complaints', 'is_violation_proven')) {
                $table->dropColumn('is_violation_proven');
            }
        });
    }
};
