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

    protected $with = ['role', 'divisi', 'subdivisi'];

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(M_role::class, 'ID_ROLE', 'ID_ROLE');
    }

    // Relasi ke Divisi
    public function divisi()
    {
        return $this->belongsTo(M_divisi::class, 'ID_DIVISI', 'ID_DIVISI');
    }

    // Relasi ke Subdivisi
    public function subdivisi()
    {
        return $this->belongsTo(M_subdivisi::class, 'ID_SUBDIVISI', 'ID_SUBDIVISI');
    }
}
