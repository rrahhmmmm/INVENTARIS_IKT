<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class M_user extends Model
{
    use HasFactory;

    protected $table = "M_USER";
    protected $primaryKey = "ID_USER";
    
    public $timestamps = false;

    protected $fillable =[
        'username',
        'password',
        'email',
        'full_name',
        'ID_DIVISIS',
        'ID_SUBDIVISI',
        'ID_ROLE',
        'create_by'
    ];
}
