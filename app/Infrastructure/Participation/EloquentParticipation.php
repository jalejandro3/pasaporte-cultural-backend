<?php

namespace App\Infrastructure\Participation;

use Illuminate\Database\Eloquent\Model;

class EloquentParticipation extends Model
{
    protected $table = 'participations';
    protected $fillable = ['activity_id', 'assistant_id', 'status', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    public $timestamps = true;
}
