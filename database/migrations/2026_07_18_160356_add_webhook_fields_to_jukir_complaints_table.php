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
            $table->string('report_code')->nullable()->unique()->after('id');
            $table->json('evidence_urls')->nullable()->after('description');
            $table->string('field_officer_name')->nullable()->after('status');
            $table->text('follow_up_description')->nullable()->after('field_officer_name');
            $table->json('follow_up_evidence_urls')->nullable()->after('follow_up_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jukir_complaints', function (Blueprint $table) {
            $table->dropColumn([
                'report_code',
                'evidence_urls',
                'field_officer_name',
                'follow_up_description',
                'follow_up_evidence_urls'
            ]);
        });
    }
};
