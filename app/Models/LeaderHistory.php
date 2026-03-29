<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeaderHistory extends Model
{
    protected $fillable = ['leader_id', 'status_jabatan', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
