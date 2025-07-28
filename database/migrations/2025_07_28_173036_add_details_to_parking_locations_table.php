<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_locations', function (Blueprint $table) {
            // Menambahkan kolom setelah 'name'
            $table->decimal('daily_deposit', 15, 2)->default(0)->after('name');
            $table->string('latitude')->nullable()->after('daily_deposit');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('image')->nullable()->after('longitude');
            $table->string('proposal_document')->nullable()->after('image');
            $table->string('official_report_document')->nullable()->after('proposal_document');
        });
    }

    public function down(): void
    {
        Schema::table('parking_locations', function (Blueprint $table) {
            $table->dropColumn([
                'daily_deposit',
                'latitude',
                'longitude',
                'image',
                'proposal_document',
                'official_report_document'
            ]);
        });
    }
};
