<?php

namespace App\Models\CC;


use Illuminate\Database\Eloquent\Model;

class CC_AUTORI_X_CUENTA extends Model
{
    protected $table = 'CC.CC_AUTORI_X_CUENTA';
    public $timestamps = false;
    public $incrementing = false;
    public $primaryKey = "NUM_AUTORIZACION";

    protected $fillable = [
        'COD_EMPRESA',
        'NUM_CUENTA',
        "NUM_AUTORIZACION",
        "FEC_ASIGNACION",
        "FEC_VENCIMIENTO",
        "MON_AUTORIZADO",
        "MON_UTILIZADO",
        "MON_PAGADO",
        "TAS_INT_CAPITAL",
        "TAS_INT_MORA",
        "DIAS_ANIO",
        "IND_GARANTIA",
        "IND_COBRO",
        "POR_COMISION",
        "MON_COMISION",
        "DES_AUTORIZACION",
        "INT_POR_COBRAR",
        "COD_USUARIO_APRUEBA",
        "COD_USUARIO",
        "IND_APL_VENCIMIENTO",
        "TIP_AUTORIZACION",
        "INT_POR_COBRAR_TEMP",
        "IND_ESTADO",
        "MON_INT_MORATORIO",
        "IND_COBRO_AUT",
        "INTERES_ANIO"
    ];

}
