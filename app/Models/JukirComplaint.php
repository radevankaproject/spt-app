<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JukirComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'jukir_id',
        'reporter_name',
        'reporter_phone',
        'description',
        'category',
        'status',
    ];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }
}
