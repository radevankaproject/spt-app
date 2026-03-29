<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TreasurerHistory extends Model
{
    protected $fillable = ['treasurer_id', 'status_jabatan', 'start_date', 'end_date'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];
}
