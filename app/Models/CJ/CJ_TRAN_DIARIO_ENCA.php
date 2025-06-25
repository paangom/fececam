<?php

namespace App\Models\CJ;


use Illuminate\Database\Eloquent\Model;

class CJ_TRAN_DIARIO_ENCA extends Model
{
    protected $table = 'CJ.CJ_TRAN_DIARIO_ENCA';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'COD_EMPRESA',
        'COD_AGENCIA',
        'COD_CAJERO',
        'COD_MONEDA_ORIGEN',
        'NUM_SECUENCIA_DOC',
        'COD_CLIENTE',
        'COD_SISTEMA',
        'TIP_TRANSACCION',
        'SUB_TIP_TRANSAC',
        'FEC_TRANSACCION',
        'IND_ESTADO',
        'MTO_MOVIMIENTO',
        'MTO_EFECTIVO',
        'ASIENTO_CONTABLE',
        'NUM_SEC_DEP_CC',
        'COD_ENTE',
        'NUM_MOV_ENTE',
        'OBSERVACIONES',
        'MON_SALDO_ANTERIOR',
        'MON_SALDO_DISPONIBLE',
        'NUM_TRANSACCION',
        'NUM_SEC_COMPROBANTE',
    ];

}
