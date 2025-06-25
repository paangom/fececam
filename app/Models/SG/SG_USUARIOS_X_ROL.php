<?php

namespace App\Models\SG;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SG_USUARIOS_X_ROL extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_USUARIOS_X_ROL';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_ROL',
        'COD_USUARIO',
        'COD_AGENCIA',
        'FEC_INGRESO'
    ];
}
