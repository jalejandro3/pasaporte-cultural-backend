<?php

namespace App\Infrastructure\Activity;

use Illuminate\Database\Eloquent\Model;

class EloquentActivity extends Model
{
    protected $table = 'activities';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'title', 'description', 'country', 'city', 'address', 'total_hours', 'verification_code'];
    public $timestamps = true;
}
