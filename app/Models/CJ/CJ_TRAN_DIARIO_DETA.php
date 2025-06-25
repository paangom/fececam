<?php

namespace App\Models\CJ;


use Illuminate\Database\Eloquent\Model;

class CJ_TRAN_DIARIO_DETA extends Model
{
    protected $table = 'CJ.CJ_TRAN_DIARIO_DETA';
    protected $primaryKey = 'NUM_SECUENCIA_DET';
    public $incrementing = false;
    protected $fillable = [
        "COD_EMPRESA",
        "COD_AGENCIA",
        "NUM_SECUENCIA_DOC",
        "NUM_SECUENCIA_DET",
        "COD_MONEDA",
        "COD_FORMA_PAGO",
        "TIP_DOCUMENTO",
        "ID_DOCUMENTO",
        "ID_ADICIONAL",
        "MTO_DOCUMENTO",
        "TIP_EMISOR",
        "COD_EMISOR"
    ];

}
