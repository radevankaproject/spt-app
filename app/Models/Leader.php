<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    //relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relasi ke aggrement (sebagai penandatangan)
    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }
}
