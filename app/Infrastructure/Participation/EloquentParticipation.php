<?php

namespace App\Infrastructure\Participation;

use Illuminate\Database\Eloquent\Model;

class EloquentParticipation extends Model
{
    protected $table = 'participations';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'activity_id', 'assistant_id', 'required_hours', 'status', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    public $timestamps = true;
}
