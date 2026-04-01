<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Sesuaikan isi ENUM dengan role yang antum punya sebelumnya, tambahkan 'bendahara'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'leader', 'staff_pks', 'staff_keu', 'field_coordinator', 'treasurer') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_role', function (Blueprint $table) {
            //
        });
    }
};
