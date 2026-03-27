<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyDepositTarget extends Model
{
    protected $fillable = ['yearly_deposit_target_id', 'month', 'target_amount'];

    public function yearlyTarget()
    {
        return $this->belongsTo(YearlyDepositTarget::class, 'yearly_deposit_target_id');
    }

    // ✅ AUTO UPDATE: Setiap kali ada aksi di bulanan, suruh tahunan hitung ulang!
    protected static function booted()
    {
        // Saat data bulanan disimpan (Create/Update)
        static::saved(function ($monthlyTarget) {
            $monthlyTarget->yearlyTarget->recalculateTotal();
        });

        // Saat data bulanan dihapus
        static::deleted(function ($monthlyTarget) {
            $monthlyTarget->yearlyTarget->recalculateTotal();
        });
    }
}
