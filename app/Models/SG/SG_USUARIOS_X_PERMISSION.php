<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_USUARIOS_X_PERMISSION extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_USUARIOS_X_PERMISSION';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_USUARIO',
        'COD_PERM',
    ];

}
