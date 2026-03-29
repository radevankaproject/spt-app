<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. Tambah kolom status_jabatan di tabel leaders
    Schema::table('leaders', function (Blueprint $table) {
        $table->enum('status_jabatan', ['tetap', 'plt', 'plh'])->default('tetap')->after('employee_number');
    });

    // 2. Buat tabel Arsip Sejarah Jabatan
    Schema::create('leader_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('leader_id')->constrained()->cascadeOnDelete();
        $table->enum('status_jabatan', ['tetap', 'plt', 'plh']);
        $table->date('start_date');
        $table->date('end_date')->nullable();
        $table->timestamps();
    });
}
};
