<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;


    protected $table = 'MAWAI_USER_MASTER';
    protected $primaryKey = 'ENG_CD';

    public $incrementing = false;

    protected $fillable = [
        'USER_NAME', 'EMAIL_ID', 'USER_PASSWORD',
    ];

    protected $hidden = [
        'USER_PASSWORD', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->USER_PASSWORD;
    }

    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_ID;
    }
}
