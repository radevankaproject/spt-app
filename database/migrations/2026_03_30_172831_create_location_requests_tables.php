<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('location_request_reviews');
        Schema::dropIfExists('location_requests');

        // 1. BuAT TABEL INDUK TERLEBIH DAHULU
        Schema::create('location_requests', function (Blueprint $table) {
            $table->id();
            // Relasi ke PKS yang sedang berjalan
            $table->foreignId('agreement_id')->constrained('agreements')->cascadeOnDelete();

            // Jenis Pengajuan
            $table->enum('request_type', ['add', 'remove']);

            // JIKA REMOVE: Korlap milih titik mana yang mau dicabut
            $table->foreignId('parking_location_id')->nullable()->constrained('parking_locations')->nullOnDelete();

            // JIKA ADD: Korlap input data titik baru secara manual
            $table->string('road_section_name')->nullable(); // Nama jalan yang diketik Korlap
            $table->string('name')->nullable(); // Nama titik parkir
            $table->decimal('offered_daily_deposit', 15, 2)->nullable(); // Penawaran setoran awal

            // Lampiran untuk ADD
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('image')->nullable();
            $table->string('proposal_document')->nullable();

            // Alasan (Bisa untuk ADD maupun REMOVE)
            $table->text('reason')->nullable();

            // Status Tracking
            $table->enum('status', ['pending', 'surveyed', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // Pesan dari dinas

            $table->timestamps();
        });

        // 2. SETELAH INDUK ADA, BARU BUAT TABEL CHILD (REVIEWS)
        Schema::create('location_request_reviews', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel location_requests yang baru saja dibuat di atas
            $table->foreignId('location_request_id')->constrained('location_requests')->cascadeOnDelete();
            $table->string('report_document')->nullable();

            // Siapa staff PKS yang nge-review/survey lapangan
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // Hasil Survey Lapangan
            $table->date('survey_date');
            $table->text('survey_notes')->nullable();

            // Setoran yang diputuskan oleh Dinas
            $table->decimal('recommended_deposit', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // 3. SAAT ROLLBACK, HAPUS CHILD DULUAN, BARU INDUKNYA
        Schema::dropIfExists('location_request_reviews');
        Schema::dropIfExists('location_requests');
    }
};
