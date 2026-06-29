<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyParkingLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_location_id',
        'survey_tajuk',
        'jukir_id',
        'survey_tanam',
        'surveyor',
        'notes',
        'survey_date',
    ];

    protected $casts = [
        'survey_date' => 'date',
    ];

    public function parkingLocation()
    {
        return $this->belongsTo(ParkingLocation::class);
    }

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }
}
