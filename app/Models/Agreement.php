<?php

namespace App\Models;

use App\Http\Controllers\Admin\UptProfileController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_code',
        'agreement_number',
        'leader_id',
        'field_coordinator_id',
        'start_date',
        'end_date',
        'daily_deposit_amount',
        'monthly_deposit_target', // ✅ TAMBAHKAN INI
        'total_deposit_target',   // ✅ TAMBAHKAN INI
        'changed_by_user_id',
        'status',
        'signed_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_deposit_amount' => 'decimal:2',
        'signed_date' => 'date',
    ];

    /**
     * Get the leader associated with the agreement.
     */
    public function leader()
    {
        return $this->belongsTo(Leader::class);
    }

    /**
     * Get the field coordinator associated with the agreement.
     */
    public function fieldCoordinator()
    {
        return $this->belongsTo(FieldCoordinator::class);
    }

    /**
     * The parking locations that belong to the agreement.
     */
    public function parkingLocations()
    {
        // Perbaikan di sini: Menggunakan model pivot kustom
        return $this->belongsToMany(ParkingLocation::class, 'agreement_parking_locations')
            ->using(AgreementParkingLocation::class)
            // ✅ PASTIKAN NAMA 'removed_date' TERTULIS DENGAN BENAR DI SINI
            ->withPivot('assigned_date', 'removed_date', 'status')
            ->withTimestamps();
    }

    /**
     * Relasi BARU yang HANYA mengambil lokasi parkir dengan status 'active' di tabel pivot.
     */
    public function activeParkingLocations()
    {
        return $this->belongsToMany(ParkingLocation::class, 'agreement_parking_locations')
            ->using(AgreementParkingLocation::class) // <-- Pastikan baris ini ada
            ->wherePivot('status', 'active')         // <-- Filter utamanya
            ->withPivot('assigned_date', 'removed_date', 'status')
            ->withTimestamps();
    }


    /**
     * Get the deposit transactions for the agreement.
     */
    public function depositTransactions()
    {
        return $this->hasMany(DepositTransaction::class);
    }

    /**
     * Get the histories for the agreement.
     */
    public function histories()
    {
        return $this->hasMany(AgreementHistory::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    public function pdfHistories()
    {
        return $this->hasMany(AgreementPdfHistory::class)->latest();
    }
}
