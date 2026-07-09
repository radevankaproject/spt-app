<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jukir_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jukir_id')->constrained('jukirs')->cascadeOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_phone')->nullable();
            $table->text('description');
            $table->string('category'); // tarif, pelayanan, keamanan, kebersihan, lainnya
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jukir_complaints');
    }
};
