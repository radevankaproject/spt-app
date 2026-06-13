<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldCoordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'address',
        'id_card_number',
        'id_card_img',
        'phone_number',
        'last_updated_by',
    ];

    //relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }
}
