<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementPdfHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'file_path',
        'notes',
        'generated_by_user_id',
    ];

    /**
     * Get the agreement that this PDF history belongs to.
     */
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    /**
     * Get the user who generated this PDF.
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }
}
