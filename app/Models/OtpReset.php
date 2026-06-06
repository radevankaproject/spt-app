<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpReset extends Model
{
    use HasFactory;

    protected $fillable = ['phone_number', 'otp_code', 'attempts', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
