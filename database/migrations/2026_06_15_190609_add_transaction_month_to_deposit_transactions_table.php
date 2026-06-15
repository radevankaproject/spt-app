<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->date('transaction_month')->nullable()->after('agreement_id');
        });

        // Backfill data - Only for is_validated = 1
        $agreements = DB::table('agreements')->get();
        foreach($agreements as $agreement) {
            $deposits = DB::table('deposit_transactions')
                        ->where('agreement_id', $agreement->id)
                        ->where('is_validated', 1)
                        ->orderBy('id', 'asc')
                        ->get();
            
            $startDate = Carbon::parse($agreement->start_date)->startOfMonth();
            foreach($deposits as $index => $deposit) {
                $targetMonth = $startDate->copy()->addMonths($index)->format('Y-m-01');
                DB::table('deposit_transactions')->where('id', $deposit->id)->update(['transaction_month' => $targetMonth]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_month');
        });
    }
};
