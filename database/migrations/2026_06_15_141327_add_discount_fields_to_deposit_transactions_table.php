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
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->decimal('discount_amount', 15, 2)->default(0)->after('amount');
            $table->text('discount_notes')->nullable()->after('discount_amount');
            $table->unsignedBigInteger('discount_approved_by_user_id')->nullable()->after('discount_notes');
            
            $table->foreign('discount_approved_by_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->dropForeign(['discount_approved_by_user_id']);
            $table->dropColumn(['discount_amount', 'discount_notes', 'discount_approved_by_user_id']);
        });
    }
};
