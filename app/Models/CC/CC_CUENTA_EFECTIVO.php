<?php

namespace App\Models\CC;


use App\Models\CF\CF_PRODUCTOS;
use App\Models\CL\CL_CLIENTES;
use Illuminate\Database\Eloquent\Model;

class CC_CUENTA_EFECTIVO extends Model
{
    protected $table = 'CC.CC_CUENTA_EFECTIVO';
    protected $primaryKey = ['COD_EMPRESA', 'NUM_CUENTA'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'COD_EMPRESA',
        'NUM_CUENTA',
        "COD_AGENCIA",
        "COD_CATEGORIA",
        "COD_SISTEMA",
        "COD_PRODUCTO",
        "COD_CLIENTE",
        "COD_DIRRECCION",
        "IND_ESTADO",
        "FEC_ESTADO",
        "FEC_APERTURA",
        "FEC_INI_SOBGRO",
        "FEC_ULT_ACT_INT",
        "FEC_ULT_CAP_INT",
        "NOM_CHEQUERA",
        "IND_TIP_CARGOS",
        "IND_CTA_ALTERNA",
        "IND_PAG_INTERES",

        "SAL_DISPONIBLE",
        "SAL_RESERVA",
        "SAL_TRANSITO",
        "SAL_CONSULTADO",
        "SAL_CONGELADO",
        "SAL_PROMEDIO",
        "SAL_ULT_CORTE",
        "MON_RESERVA_UTL",
        "MON_SOBGRO_AUT",
        "MON_SOB_NO_AUT",
        "MON_SOBGRO_DISP",
        "MON_TOTAL_CARGO",
        "INT_CAP_CONGELA",
        "INT_CAP_RESERVA",
        "INT_POR_PAGAR",
        "INT_SOBGRO_AUT",
        "INT_RESERVA_UTL",
        "IND_SOBGRO",
        "NUM_CTA_RELACIONADA",
        "INT_MES_ACTUAL",
        "IND_CORRESPONDENCIA",
        "FEC_ULT_MOVIMIENTO",
        "COD_MONEDA",
        "OBS_ESTADO_CUENTA",
        "MON_MAX_SOBGRO_TEMP",
        "COD_USUARIO"
    ];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('NUM_CUENTA', $this->getAttribute('NUM_CUENTA'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }

    public function cf_producto(){
        return $this->belongsTo(CF_PRODUCTOS::class, 'COD_PRODUCTO', 'COD_PRODUCTO');
    }

    public function cl_cliente(){
        return $this->belongsTo(CL_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }

    public function cc_autori_x_cuenta() {
        return $this->hasMany(CC_AUTORI_X_CUENTA::class, 'NUM_CUENTA', 'NUM_CUENTA');
    }

}
