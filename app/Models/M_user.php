<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class M_user extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'M_USER';
    protected $primaryKey = 'ID_USER';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'email',
        'full_name',
        'ID_DIVISI',
        'ID_SUBDIVISI',
        'ID_ROLE'
    ];

    protected $hidden = [
        'password',
    ];
}
