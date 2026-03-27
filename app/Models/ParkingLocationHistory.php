<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLocationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_location_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
    ];

    // Beritahu Laravel agar kolom JSON otomatis diubah menjadi Array PHP
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
