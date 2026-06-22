<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepositTransaction;
use App\Models\User;
use App\Notifications\DepositPendingNotification;
use App\Notifications\DepositValidatedNotification;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $staffs = User::whereIn('role', ['admin', 'staff_keu'])->get();
        $pendingDeposits = DepositTransaction::where('is_validated', false)->get();

        foreach ($pendingDeposits as $deposit) {
            Notification::send($staffs, new DepositPendingNotification($deposit));
        }

        $validatedDeposits = DepositTransaction::where('is_validated', true)->orderBy('validation_date', 'desc')->take(20)->get();
        foreach ($validatedDeposits as $deposit) {
            if ($deposit->treasurer && $deposit->treasurer->user) {
                $deposit->treasurer->user->notify(new DepositValidatedNotification($deposit));
            }
        }
    }
}
