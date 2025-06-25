<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_USUARIOS_X_TRANSACCION extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_USUARIOS_X_TRANSACCION';


    protected $fillable = [
        'COD_EMPRESA',
        'COD_SISTEMA',
        'COD_USUARIO',
        'COD_AGENCIA',
        'TIP_TRANSACCION'
    ];
}
