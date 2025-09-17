<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class M_subdivisi extends Model
{
   use HasFactory;
   
   protected $table = "M_SUBDIVISI";
   protected $primaryKey = 'ID_SUBDIVISI';

   public $timestamps = false;

   protected $fillable = [
   'ID_DIVISI',
    'NAMA_SUBDIVISI',
    'CREATE_BY'
   ];

   public function divisi()
    {
        return $this->belongsTo(M_divisi::class, 'ID_DIVISI', 'ID_DIVISI');
    }
}
