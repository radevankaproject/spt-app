<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke PKS
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    // Relasi ke Titik Parkir (Hanya ada isinya kalau jenisnya 'remove')
    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }

    // Relasi ke Hasil Survey (Review)
    public function review()
    {
        return $this->hasOne(LocationRequestReview::class);
    }
}
