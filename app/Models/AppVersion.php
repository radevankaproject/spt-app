<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'release_date',
        'changelog',
    ];

    protected $casts = [
        'release_date' => 'date', // ✅ TAMBAHKAN BARIS INI
    ];
}
