<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo',
        'description',
        'latitude',
        'longitude',
        'province',
        'city',
        'district',
        'status',
        'rejection_reason',
        'assigned_surveyor_id',
    ];

    // ── Relations ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surveyResult()
    {
        return $this->hasOne(SurveyResult::class);
    }

    public function assignedSurveyor()
    {
        return $this->belongsTo(User::class, 'assigned_surveyor_id');
    }

    // ── Scopes ──────────────────────────────────────────────────
    public function scopeVerified($query)
    {
        return $query->where('status', 'terverifikasi');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Helpers ─────────────────────────────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'terverifikasi' => 'bg-green-100 text-green-800',
            'ditolak'       => 'bg-red-100 text-red-800',
            default         => 'bg-yellow-100 text-yellow-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'terverifikasi' => 'Terverifikasi',
            'ditolak'       => 'Ditolak',
            default         => 'Menunggu Verifikasi',
        };
    }
}
