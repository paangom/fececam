<?php

namespace App\Models\CC;


use Illuminate\Database\Eloquent\Model;

class CC_MOVIMTO_DIARIO extends Model
{
    protected $table = 'CC.CC_MOVIMTO_DIARIO';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;
    //protected $with = ['cf_catal_transactiones', 'cf_subtip_transac'];
    protected $fillable = [
        'COD_EMPRESA'
        ,'NUM_MOVIMIENTO'
        ,'NUM_CUENTA'
        ,'COD_PRODUCTO'
        ,'TIP_TRANSACCION'
        ,'SUBTIP_TRANSAC'
        ,'COD_SISTEMA'
        ,'FEC_MOVIMIENTO'
        ,'NUM_DOCUMENTO'
        ,'EST_MOVIMIENTO'
        ,'IND_APL_CARGO'
        ,'MON_MOVIMIENTO'
        ,'DES_MOVIMIENTO'
        ,'SISTEMA_FUENTE'
        ,'NUM_MOV_FUENTE'
        ,'COD_AGENCIA'
        ,'COD_USUARIO'
        ,'NUM_TRANSACCION'
        ,'APELLIDO_REPRESENTANTE'
        ,'NOMBRE_REPRESENTANTE'
        ,'COD_TIPO_ID_REPRESENTANTE'
        ,'NUM_ID_REPRESENTANTE'
        ,'FEC_NACIMIENTO_REPRESENTANTE'
        ,'FEC_EXPIRACION_REPRESENTANTE'
        ,'DIRECCION_REPRESENTANTE'
        ,'DESCRIPTION_REPRESENTANTE'
        ,'VER_SALDO'
    ];


}
