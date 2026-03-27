<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Parent (Target Tahunan)
        Schema::create('yearly_deposit_targets', function (Blueprint $table) {
            $table->id();
            $table->year('year')->unique(); // 1 Tahun hanya ada 1 baris
            $table->decimal('total_target', 15, 2)->default(0); // Total otomatis
            $table->timestamps();
        });

        // Tabel Child (Target Bulanan)
        Schema::create('monthly_deposit_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yearly_deposit_target_id')->constrained('yearly_deposit_targets')->cascadeOnDelete();
            $table->integer('month'); // Bulan 1 - 12
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->timestamps();

            // Mencegah duplikasi bulan di tahun yang sama
            $table->unique(['yearly_deposit_target_id', 'month'], 'unique_monthly_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_deposit_targets');
        Schema::dropIfExists('yearly_deposit_targets');
    }
};
