<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositPendingNotification extends Notification
{
    use Queueable;

    protected $deposit;

    public function __construct($deposit)
    {
        $this->deposit = $deposit;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Setoran Baru (Menunggu Validasi)',
            'desc' => "Setoran Rp " . number_format($this->deposit->amount, 0, ',', '.') . " menunggu validasi.",
            'icon' => 'tabler-cash',
            'color' => 'info',
            'url' => route('masterdata.deposit-transactions.index')
        ];
    }
}
