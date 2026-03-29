<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'treasurer_id',
        'created_by_user_id',
        'validated_by_user_id', // ✅ TAMBAHKAN INI
        'referral_code',
        'deposit_date',
        'amount',
        'proof_of_transfer',
        'notes',
        'is_validated',
        'validation_date',
    ];

    protected $casts = [
        'deposit_date'    => 'date',
        'amount'          => 'decimal:2',
        'is_validated'    => 'boolean',
        'validation_date' => 'datetime', // <-- UBAH INI (dan pastikan tipe castnya datetime, bukan date)
    ];

    // Relasi ke Agreement
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    // Relasi ke User Creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ✅ TAMBAHKAN RELASI INI DI BAWAH
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by_user_id');
    }

    public function treasurer()
    {
        return $this->belongsTo(Treasurer::class);
    }
}
