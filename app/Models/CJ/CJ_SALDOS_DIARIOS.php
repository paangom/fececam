<?php

namespace App\Models\CJ;


use Illuminate\Database\Eloquent\Model;

class CJ_SALDOS_DIARIOS extends Model
{
    protected $table = 'CJ.CJ_SALDOS_DIARIOS';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        "COD_EMPRESA",
        "COD_AGENCIA",
        "COD_CAJERO",
        "COD_MONEDA",
        "FEC_CIERRE",
        "SAL_INI_EFECTIVO",
        "SAL_INI_DOCUMENTO",
        "SAL_ACT_EFECTIVO",
        "SAL_ACT_DOCUMENTO",
        "IND_ESTADO"
    ];

}
