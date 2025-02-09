<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'path',
        'active',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function getUrlAttribute(): string
    {
        return url($this->path);
    }
}
