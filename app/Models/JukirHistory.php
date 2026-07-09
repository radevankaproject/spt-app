<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JukirHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'jukir_id',
        'user_id',
        'parking_location_id',
        'action',
        'description',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }
}
