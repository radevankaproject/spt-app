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
        Schema::create('survey_parking_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_location_id')->constrained('parking_locations')->onDelete('cascade');
            $table->text('survey_tajuk')->nullable();
            $table->foreignId('jukir_id')->nullable()->constrained('jukirs')->onDelete('set null');
            $table->text('survey_tanam')->nullable();
            $table->string('surveyor')->nullable();
            $table->date('survey_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_parking_locations');
    }
};
