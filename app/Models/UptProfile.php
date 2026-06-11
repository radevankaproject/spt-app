<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UptProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'app_name',
        'address',
        'logo',
        'phone',
        'phone_number_admin',
        'email',
        'website',
        'login_greetings',
        'api_token_fonnte',
        'about_us',
        'privacy_policy',
    ];
}
