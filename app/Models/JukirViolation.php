<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JukirViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'jukir_id',
        'user_id',
        'description',
        'violation_date',
    ];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
