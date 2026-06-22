<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositValidatedNotification extends Notification
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
            'title' => 'Setoran Divalidasi',
            'desc' => "Setoran Rp " . number_format($this->deposit->amount, 0, ',', '.') . " telah divalidasi.",
            'icon' => 'tabler-check',
            'color' => 'success',
            'url' => route('masterdata.deposit-transactions.index')
        ];
    }
}
