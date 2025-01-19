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
        'total_hours',
    ];

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    public function activeQrCode(): HasOne
    {
        return $this->hasOne(QrCode::class)->where('active', true);
    }
}
