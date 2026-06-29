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
        Schema::create('jukirs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jukir');
            $table->foreignId('parking_location_id')->constrained('parking_locations')->onDelete('cascade');
            $table->string('no_ktp')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jukirs');
    }
};
