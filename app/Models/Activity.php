<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'country',
        'city',
        'address',
        'duration',
        'started_at',
        'finished_at',
    ];

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    public function activeQrCode(): HasOne
    {
        return $this->hasOne(QrCode::class)->where('active', true);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_activity')
            ->withPivot('status', 'started_at', 'finished_at')
            ->withTimestamps();
    }
}
