<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YearlyDepositTarget extends Model
{
    protected $fillable = ['year', 'total_target'];

    // Relasi ke Bulanan
    public function monthlyTargets()
    {
        return $this->hasMany(MonthlyDepositTarget::class);
    }

    // Fungsi untuk menghitung ulang total secara otomatis
    public function recalculateTotal()
    {
        $this->update([
            'total_target' => $this->monthlyTargets()->sum('target_amount')
        ]);
    }
}
