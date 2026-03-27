<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'road_section_id',
        'name',
        'status',
        'daily_deposit',
        'latitude',
        'longitude',
        'image',
        'proposal_document',
        'official_report_document',
    ];

    //Relaso RoadSection
    public function roadSection()
    {
        return $this->belongsTo(RoadSection::class);
    }

    // Relasi ke Agreements melalui tabel pivot agreement_parking_locations
    public function agreements()
    {
        return $this->belongsToMany(Agreement::class, 'agreement_parking_locations')
            ->withPivot('assigned_date', 'removed_date', 'status')
            ->withTimestamps();
    }

    public function histories()
    {
        // Urutkan dari yang terbaru
        return $this->hasMany(ParkingLocationHistory::class)->latest();
    }
}
