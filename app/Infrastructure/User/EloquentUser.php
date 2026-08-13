<?php

namespace App\Infrastructure\User;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['id', 'first_name', 'last_name', 'id_document', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class EloquentUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
