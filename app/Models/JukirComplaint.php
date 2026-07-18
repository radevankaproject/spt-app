<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JukirComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'jukir_id',
        'reporter_name',
        'reporter_phone',
        'category',
        'description',
        'status',
        'admin_note',
        'evidence',
        'user_id',
        'report_code',
        'sync_officer',
        'evidence_urls',
        'field_officer_name',
        'follow_up_description',
        'follow_up_evidence_urls',
        'is_violation_proven',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'evidence' => 'array',
        'evidence_urls' => 'array',
        'follow_up_evidence_urls' => 'array',
        'is_violation_proven' => 'boolean',
    ];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class, 'jukir_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
