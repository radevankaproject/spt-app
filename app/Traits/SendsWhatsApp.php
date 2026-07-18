<?php

namespace App\Traits;

use App\Models\UptProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsWhatsApp
{
    /**
     * Mengirim Pesan WhatsApp via Fonnte
     * 
     * @param string $phoneNumber Nomor telepon tujuan
     * @param string $message Isi pesan
     * @return void
     */
    protected function sendWhatsAppNotification($phoneNumber, $message)
    {
        try {
            $uptProfile = UptProfile::first();

            if (! $uptProfile || empty($uptProfile->api_token_fonnte) || empty($phoneNumber)) {
                return;
            }

            // Bersihkan format nomor telepon (hanya angka)
            $phoneFormatted = preg_replace('/[^0-9]/', '', $phoneNumber);

            Http::withHeaders([
                'Authorization' => $uptProfile->api_token_fonnte,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phoneFormatted,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('Fonnte WA Error: '.$e->getMessage());
        }
    }
}
