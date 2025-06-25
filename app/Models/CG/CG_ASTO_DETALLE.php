<?php

namespace App\Models\CG;

use Illuminate\Database\Eloquent\Model;

class CG_ASTO_DETALLE extends Model
{
    protected $table = 'CG.CG_ASTO_DETALLE';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'COD_EMPRESA',
        'COD_AGENCIA',
        'NUM_ASIENTO',
        'NUM_LINEA',
        'CUENTA_CONTABLE',
        'FEC_MOVIMIENTO',
        'DEBITO',
        'CREDITO',
        'DEBITO_CTA',
        'CREDITO_CTA',
        'DETALLE',
        'TIP_CAM_BASE',
        'TIP_CAM_CTA',
        'REFERENCIA',
        'NUM_TRANSACCION',
    ];
}
