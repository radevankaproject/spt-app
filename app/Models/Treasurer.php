<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Treasurer extends Model
{
    protected $fillable = ['user_id', 'employee_number', 'phone_number', 'status_jabatan', 'start_date', 'end_date'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function histories() { return $this->hasMany(TreasurerHistory::class)->orderBy('start_date', 'desc'); }
}
