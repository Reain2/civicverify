<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function surveyResults()
    {
        return $this->hasMany(SurveyResult::class, 'surveyor_id');
    }

    public function assignedReports()
    {
        return $this->hasMany(Report::class, 'assigned_surveyor_id');
    }

    // ── Role helpers ────────────────────────────────────────────
    public function isMasyarakat(): bool
    {
        return $this->role === 'masyarakat';
    }

    public function isSurveyor(): bool
    {
        return $this->role === 'surveyor';
    }

    public function isKonsultan(): bool
    {
        return $this->role === 'konsultan';
    }

    public function isKementerian(): bool
    {
        return $this->role === 'kementerian';
    }
}
