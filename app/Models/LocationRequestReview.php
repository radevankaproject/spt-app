<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequestReview extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Cast tanggal agar otomatis jadi objek Carbon
    protected $casts = [
        'survey_date' => 'date',
    ];

    public function locationRequest()
    {
        return $this->belongsTo(LocationRequest::class);
    }

    // Siapa staff yang survey
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
