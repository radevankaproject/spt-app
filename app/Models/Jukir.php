<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jukir extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jukir',
        'nama_jukir',
        'tanggal_lahir',
        'alamat',
        'parking_location_id',
        'no_ktp',
        'phone_number',
        'image',
        'image_ktp',
        'is_active',
        'is_blacklisted',
        'kta_type',
        'kta_start_date',
        'kta_end_date'
    ];

    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }

    public function histories()
    {
        return $this->hasMany(JukirHistory::class)->latest();
    }

    public function violations()
    {
        return $this->hasMany(JukirViolation::class)->latest();
    }
}
