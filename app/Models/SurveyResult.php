<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'surveyor_id',
        'photo',
        'notes',
        'latitude',
        'longitude',
        'synced',
    ];

    protected $casts = [
        'synced'    => 'boolean',
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ── Relations ────────────────────────────────────────────────
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }
}
