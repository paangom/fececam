<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_USUARIOS extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_USUARIOS';
    protected $primaryKey = 'COD_USUARIO';
    public $incrementing = false;
    protected $keyType = 'string';
    //protected $with = ['sg_puesto', 'cf_empresa', 'cf_agencia', 'sg_roles'];
    protected $fillable = [
        'IND_ACTIVO',
        'COD_AGENCIA',
        'NOM_USUARIO',
        'COD_PUESTO',
        'COD_AGENCIA',
        'PALABRA_PASO',
        'COD_USUARIO_BD',
        'PALABRA_PASO_BD',
        'TIP_USUARIO',
        'TIPO_ACCESO',
        'TIP_FUNCION',
        'NIVEL_FUNCION',
        'FEC_VENC_USUARIO',
        'FEC_VENC_PALABRA_PASO',
        'COD_IDIOMA',
        'COD_USUARIO',
        'IND_PRINCIPIANTE',
        'COD_EMPRESA',
        'FEC_INGRESO'
    ];

}
