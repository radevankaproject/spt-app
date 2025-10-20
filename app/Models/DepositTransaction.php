<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_code', // <-- TAMBAHKAN INI
        'agreement_id',
        'deposit_date',
        'amount',
        'is_validated',
        'validation_date', // <-- UBAH INI
        'notes',
        'created_by_user_id',
        'proof_of_transfer',
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
}
