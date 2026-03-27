<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_location_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_location_id')->constrained()->cascadeOnDelete();
            // Menyimpan ID user (admin/staff) yang melakukan aksi
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action'); // Contoh: 'created', 'updated', 'deleted', 'owner_changed', 'status_changed'
            $table->text('description'); // Deskripsi yang ramah dibaca manusia

            // Kolom JSON untuk menyimpan detail data sebelum dan sesudah (opsional tapi sangat berguna)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_location_histories');
    }
};
