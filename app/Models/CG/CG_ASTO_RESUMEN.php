<?php

namespace App\Models\CG;

use Illuminate\Database\Eloquent\Model;

class CG_ASTO_RESUMEN extends Model
{
    protected $table = 'CG.CG_ASTO_RESUMEN';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'COD_EMPRESA',
        'COD_AGENCIA',
        'NUM_ASIENTO',
        'TIP_TRANSACCION',
        'SUBTIP_TRANSAC',
        'COD_SISTEMA',
        'NUM_SECUENCIA',
        'FEC_MOVIMIENTO',
        'DES_ASIENTO',
        'EST_ASIENTO',
        'FEC_ASIENTO',
        'FEC_REGISTRO',
        'COD_USUARIO',
        'IND_LIQUIDACION',
        'IND_POST_CIERRE',
    ];
}
