<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jukir extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jukir',
        'parking_location_id',
        'no_ktp',
        'phone_number',
        'image',
        'is_active',
    ];

    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }
}
