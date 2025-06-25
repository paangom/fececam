<?php

namespace App\Repositories;

use App\Models\CC\CC_AUTORI_X_CUENTA;
use App\Models\CC\CC_CARA_X_PRODUCTO;
use App\Models\CC\CC_CUENTA_EFECTIVO;
use App\Models\CC\CC_MOVIMTO_DIARIO;
use App\Models\CF\CF_CALENDARIOS;
use App\Models\CF\CF_PARAMETROS_X_AGENCIA;
use App\Models\CF\CF_SERIES_X_EMPRESA;
use App\Models\CG\CG_ASTO_DETALLE;
use App\Models\CG\CG_ASTO_RESUMEN;
use App\Models\CJ\CJ_CONTABILIDAD_OPERACION;
use App\Models\CJ\CJ_MAX_X_CAJERO;
use App\Models\CJ\CJ_SALDOS_DIARIOS;
use App\Models\CJ\CJ_TRAN_DIARIO_DETA;
use App\Models\CJ\CJ_TRAN_DIARIO_ENCA;
use App\Models\SG\SG_USUARIOS_X_TRANSACCION;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

/**
 * Class TodoRepository
 * @package App\Repositories
 * @version Juin 04, 2025, 11:36 am UTC
*/

class TodoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        //return AnnonceCategorie::class;
    }

    public function SG_USUARIOS_X_TRANSACCION($COD_EMPRESA, $COD_AGENCIA, $COD_USUARIO, $COD_SISTEMA, $TIP_TRANSACCION)
    {
        try{
            $SG_USUARIOS_X_TRANSACCION = SG_USUARIOS_X_TRANSACCION::where('COD_EMPRESA', $COD_EMPRESA)
                ->where('COD_AGENCIA', $COD_AGENCIA)
                ->where('COD_USUARIO', $COD_USUARIO)
                ->where('COD_SISTEMA', $COD_SISTEMA)
                ->where('TIP_TRANSACCION', $TIP_TRANSACCION)->count();
            if($SG_USUARIOS_X_TRANSACCION > 0){
                return ['code' => 200, 'data' => $SG_USUARIOS_X_TRANSACCION, 'message' => ""];
            }
            else{
                return ['code' => 403, 'data' => "", 'message' => ""];
            }

        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function chekOuvertureCaisse($COD_EMPRESA, $COD_AGENCIA)
    {
        try{
            $CF_PARAMETROS_X_AGENCIA = CF_PARAMETROS_X_AGENCIA::where('COD_AGENCIA', $COD_AGENCIA)
                ->where('COD_EMPRESA', $COD_EMPRESA)
                ->where('COD_PARAMETRO', 'CIERRE_GENERAL')->first();
            if($CF_PARAMETROS_X_AGENCIA){
                if($CF_PARAMETROS_X_AGENCIA->VAL_PARAMETRO == 'N'){
                    return ['code' => 200, 'data' => $CF_PARAMETROS_X_AGENCIA, 'message' => ""];
                }
                else{
                    return ['code' => 405, 'data' => "", 'message' => "Caissa agence non overte!"];
                }
            }
            else{
                return ['code' => 404, 'data' => "", 'message' => "Caissa agence non paramètré!"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function MAX_X_CAJERO($COD_EMPRESA, $COD_SISTEMA, $COD_AGENCIA, $COD_USUARIO, $TIP_TRANSACCION){
        try{
            $CJ_MAX_X_CAJERO = CJ_MAX_X_CAJERO::where('COD_AGENCIA', $COD_AGENCIA)
                ->where('COD_EMPRESA', $COD_EMPRESA)
                ->where('COD_SISTEMA', $COD_SISTEMA)
                ->where('COD_CAJERO', $COD_USUARIO)
                ->where('TIP_TRANSACCION', $TIP_TRANSACCION)->firstOrFail();
            return ['code' => 200, 'data' => $CJ_MAX_X_CAJERO, 'message' => ""];
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function getDateSYSTEME($COD_EMPRESA, $COD_AGENCIA, $COD_SISTEMA){
        try{
            $CF_CALENDARIOS = CF_CALENDARIOS::where('COD_EMPRESA', $COD_EMPRESA)
                ->where('COD_AGENCIA', $COD_AGENCIA)
                ->where('COD_SISTEMA', $COD_SISTEMA)->first();
            if($CF_CALENDARIOS){
                return ['code' => 200, 'data' => $CF_CALENDARIOS->FEC_HOY, 'message' => ""];
            }
            else{
                return ['code' => 404, 'data' => "", 'message' => "Date système de l'agence introuvable!"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function IncValSiguienteEmpresa(){
        try{
            $CF_SERIES_X_EMPRESA_QUERY = CF_SERIES_X_EMPRESA::whereIn('COD_SERIE', ['CC_MOV_DIA', 'CONS_ASTO']);
            $CF_SERIES_X_EMPRESA = $CF_SERIES_X_EMPRESA_QUERY->pluck('VAL_SIGUIENTE', 'COD_SERIE')->toArray();
            if(count($CF_SERIES_X_EMPRESA) == 2){
                $CF_SERIES_X_EMPRESA_UPDATE = $CF_SERIES_X_EMPRESA_QUERY->get();
                foreach ($CF_SERIES_X_EMPRESA_UPDATE as $one){
                    $one->VAL_SIGUIENTE = $one->VAL_SIGUIENTE + 1;
                    $one->update();
                }
                return ['code' => 200, 'data' =>  (object)$CF_SERIES_X_EMPRESA, 'message' => ""];
            }
            else{
                return ['code' => 404, 'data' => "", 'message' => "Echec génération du numéro de série de l'entité"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function MAX_REFERENCIA(){
        try{
            $CG_ASTO_DETALLE = CG_ASTO_DETALLE::where('DETALLE', 'EFECTIVO DE CAJAS')->orderByDesc('REFERENCIA')->first();
            if($CG_ASTO_DETALLE){
                $REFERENCIA = $CG_ASTO_DETALLE->REFERENCIA+1;
                return ['code' => 200, 'data' => $REFERENCIA, 'message' => ""];
            }
            else{
                return ['code' => 404, 'data' => "", 'message' => "Echec génération numéro de référence de la caisse"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function MOVIMTO_DIARIO($NUM_MOVIMIENTO, $NUM_CUENTA, $COD_PRODUCTO, $MON_MOVIMIENTO, $FEC_MOVIMIENTO, $COD_EMPRESA, $TIP_TRANSACCION, $SUBTIP_TRANSAC, $COD_SISTEMA, $NUM_DOCUMENTO, $EST_MOVIMIENTO, $IND_APL_CARGO, $DES_MOVIMIENTO, $SISTEMA_FUENTE, $NUM_MOV_FUENTE, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA){
        try{
            $CC_MOVIMTO_DIARIO = CC_MOVIMTO_DIARIO::create([
                'COD_EMPRESA' => $COD_EMPRESA,
                'NUM_MOVIMIENTO' => $NUM_MOVIMIENTO,
                'NUM_CUENTA' => $NUM_CUENTA,
                'COD_PRODUCTO' => $COD_PRODUCTO,
                'TIP_TRANSACCION' => $TIP_TRANSACCION,
                'SUBTIP_TRANSAC' => $SUBTIP_TRANSAC,
                'COD_SISTEMA' => $COD_SISTEMA,
                'FEC_MOVIMIENTO' => $FEC_MOVIMIENTO,
                'NUM_DOCUMENTO' => $NUM_DOCUMENTO,
                'EST_MOVIMIENTO' => $EST_MOVIMIENTO,
                'IND_APL_CARGO' => $IND_APL_CARGO,
                'MON_MOVIMIENTO' => $MON_MOVIMIENTO,
                'DES_MOVIMIENTO' => $DES_MOVIMIENTO,
                'SISTEMA_FUENTE' => $SISTEMA_FUENTE,
                'NUM_MOV_FUENTE' => $NUM_MOV_FUENTE,
                'COD_AGENCIA' => $COD_AGENCIA,
                'COD_USUARIO' => $COD_USUARIO,
                'DES_REFERENCIA' => $DES_REFERENCIA
            ]);
            return ['code' => 200, 'data' => $CC_MOVIMTO_DIARIO, 'message' => ""];
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function TRAN_DIARIO_DETA($COD_EMPRESA, $COD_AGENCIA, $MTO_DOCUMENTO, $NUM_SECUENCIA_DOC, $NUM_SECUENCIA_DET, $COD_MONEDA, $COD_FORMA_PAGO, $TIP_DOCUMENTO)
    {
        try{
            $CJ_TRAN_DIARIO_DETA = CJ_TRAN_DIARIO_DETA::create([
                'COD_EMPRESA' => $COD_EMPRESA,
                'COD_AGENCIA' => $COD_AGENCIA,
                'NUM_SECUENCIA_DOC' => $NUM_SECUENCIA_DOC,
                'NUM_SECUENCIA_DET' => $NUM_SECUENCIA_DET,
                'COD_MONEDA' => $COD_MONEDA,
                'COD_FORMA_PAGO' => $COD_FORMA_PAGO,
                'TIP_DOCUMENTO' => $TIP_DOCUMENTO,
                'MTO_DOCUMENTO' => $MTO_DOCUMENTO
            ]);
            return ['code' => 200, 'data' => $CJ_TRAN_DIARIO_DETA, 'message' => ""];
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function ASTO_RESUMEN($NUM_ASIENTO, $COD_EMPRESA, $COD_AGENCIA, $TIP_TRANSACCION, $SUBTIP_TRANSAC, $COD_SISTEMA, $FEC_MOVIMIENTO, $DES_ASIENTO, $EST_ASIENTO, $COD_USUARIO, $IND_LIQUIDACION, $IND_POST_CIERRE)
    {
        try{
            $CG_ASTO_RESUMEN = CG_ASTO_RESUMEN::create([
                "COD_EMPRESA" => $COD_EMPRESA,
                "COD_AGENCIA" => $COD_AGENCIA,
                "NUM_ASIENTO" => $NUM_ASIENTO,
                "TIP_TRANSACCION" => $TIP_TRANSACCION,
                "SUBTIP_TRANSAC" => $SUBTIP_TRANSAC,
                "COD_SISTEMA" => $COD_SISTEMA,
                "FEC_MOVIMIENTO" => $FEC_MOVIMIENTO,
                "DES_ASIENTO" => $DES_ASIENTO,
                "EST_ASIENTO" => $EST_ASIENTO,
                "FEC_ASIENTO" => $FEC_MOVIMIENTO,
                "FEC_REGISTRO" => $FEC_MOVIMIENTO,
                "COD_USUARIO" => $COD_USUARIO,
                "IND_LIQUIDACION" => $IND_LIQUIDACION,
                "IND_POST_CIERRE" => $IND_POST_CIERRE,
            ]);
            return ['code' => 200, 'data' => $CG_ASTO_RESUMEN, 'message' => ""];
        }
        catch (\Exception $e){
            return ['code' => 500, 'data' => "", 'message' => $e->getMessage()];
        }
    }

    public function GetCuentaContableOperation($COD_CAJERO, $COD_CONCEPTO='EFECTIVO')
    {
        try{
            $CJ_CONTABILIDAD_OPERACION = CJ_CONTABILIDAD_OPERACION::where('COD_CONCEPTO', $COD_CONCEPTO)->where('COD_CAJERO', $COD_CAJERO)->firstOrFail();
            return ['code' => 200, 'data' => $CJ_CONTABILIDAD_OPERACION->CUENTA_CONTABLE, 'message' => ""];
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function SOLDE_AGENT($COD_CAJERO){
        try{
            $CJ_SALDOS_DIARIOS = CJ_SALDOS_DIARIOS::where('COD_CAJERO',$COD_CAJERO)->where('IND_ESTADO', 'A')->first();
            if($CJ_SALDOS_DIARIOS){
                return ['code' => 200, 'solde' => $CJ_SALDOS_DIARIOS->SAL_ACT_EFECTIVO];
            }
            else{
                return ['code' => 404, 'message' => "Solde agence indisponible!"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * @authorize
     */
    public function UPDATE_SOLDE_AGENT($COD_CAJERO, $MONTANT, $OPERATION){
        try{
            $IND_ESTADO = 'A';
            $CJ_SALDOS_DIARIOS = CJ_SALDOS_DIARIOS::where('COD_CAJERO', $COD_CAJERO)->where('IND_ESTADO', $IND_ESTADO)->first();
            if($CJ_SALDOS_DIARIOS){
                $ACUAL_SALDO = $CJ_SALDOS_DIARIOS->SAL_ACT_EFECTIVO;
                if($OPERATION == 'C'){
                    $CJ_SALDOS_DIARIOS->SAL_ACT_EFECTIVO = $ACUAL_SALDO + $MONTANT;
                    $CJ_SALDOS_DIARIOS->update();
                    return $this->SOLDE_AGENT($COD_CAJERO);
                }
                else if($OPERATION == 'D'){
                    $CJ_SALDOS_DIARIOS->SAL_ACT_EFECTIVO = $ACUAL_SALDO - $MONTANT;
                    $CJ_SALDOS_DIARIOS->update();
                    return $this->SOLDE_AGENT($COD_CAJERO);
                }
                else{
                    return ['code' => 402, 'message' => "Opération non reconnu"];
                }
            }
            else{
                return ['code' => 401, 'message' => "Solde caisse indisponible!"];
            }
        }
        catch (\Exception $e){
            //dd($e->getMessage());
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function ASTO_DETALLE($NUM_ASIENTO, $NUM_LINEA, $CUENTA_CONTABLE, $DEBITO, $CREDITO, $REFERENCIA, $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO, $TIP_CAM_BASE, $TIP_CAM_CTA)
    {
        try{
            $res = CG_ASTO_DETALLE::create([
                "COD_EMPRESA" => $COD_EMPRESA,
                "COD_AGENCIA" => $COD_AGENCIA,
                "NUM_ASIENTO" => $NUM_ASIENTO,
                "NUM_LINEA" => $NUM_LINEA,
                "CUENTA_CONTABLE" => $CUENTA_CONTABLE,
                "FEC_MOVIMIENTO" => $FEC_MOVIMIENTO,
                "DEBITO" => $DEBITO,
                "CREDITO" => $CREDITO,
                "DEBITO_CTA" => $DEBITO,
                "CREDITO_CTA" => $CREDITO,
                "DETALLE" => $DETAILE_ASTO,
                "TIP_CAM_BASE" => $TIP_CAM_BASE,
                "TIP_CAM_CTA" => $TIP_CAM_CTA,
                "REFERENCIA" => $REFERENCIA
            ]);
            return ['code' => 200, 'data' => $res];
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function SOLDE_CLIENT($NUM_CUENTA, $DECOUVERT=false){
        try{
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::where('NUM_CUENTA', $NUM_CUENTA)->first();
            if($CC_CUENTA_EFECTIVO){
                if($CC_CUENTA_EFECTIVO->IND_ESTADO == 'A'){
                    $SAL_DISPONIBLE = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE;
                    $SAL_RESERVA = 0;
                    //$SAL_RESERVA = $CC_CUENTA_EFECTIVO->SAL_RESERVA;
                    $SAL_DECOUVERT = 0;
                    if ($DECOUVERT && $CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S') {
                        $SAL_DECOUVERT = $CC_CUENTA_EFECTIVO->MON_SOB_NO_AUT;
                    }
                    $SALDO =  (($SAL_DISPONIBLE+$SAL_RESERVA)-$SAL_DECOUVERT);
                    return ['code' => 200, 'solde' => $SALDO];
                }
                else{
                    return ['code' => 405, 'message' => "Compte non actif!"];
                }
            }
            else{
                return ['code' => 404, 'message' => "Compte non trouvé!"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function UPDATE_SOLDE_CLIENTE($NUM_CUENTA, $MONTANT, $OPERATION, $FEC_ULT_MOVIMIENTO){
        try{
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::where('NUM_CUENTA', $NUM_CUENTA)->firstOrFail();

            if($OPERATION == 'C'){
                $SAL_DISPONIBLE = 0;
                $MON_SOB_NO_AUT = 0;
                $MON_COMISION = 0;
                $MON_UTILIZADO = 0;
                $auth = true;
                $solde_dispo = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE;
                $solde_decouvert_retirer = $CC_CUENTA_EFECTIVO->MON_SOB_NO_AUT;
                if($solde_dispo > 0){
                    $SAL_DISPONIBLE = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE + $MONTANT;
                }
                else if($solde_dispo == 0 && $solde_decouvert_retirer == 0){
                    $SAL_DISPONIBLE = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE + $MONTANT;
                }
                else if($solde_dispo == 0 && $solde_decouvert_retirer > 0){
                    $CC_AUTORI_X_CUENTA = CC_AUTORI_X_CUENTA::where('NUM_CUENTA', $NUM_CUENTA)->where('IND_ESTADO', 'A')->firstOrFail();
                    $MON_COMISION = $CC_AUTORI_X_CUENTA->MON_COMISION;

                    $MON_PAGADO = $CC_AUTORI_X_CUENTA->MON_PAGADO +($MONTANT- $MON_COMISION);
                    if($solde_decouvert_retirer >= $MONTANT){
                        $MON_UTILIZADO = ($CC_AUTORI_X_CUENTA->MON_UTILIZADO - $MONTANT) + $MON_COMISION;
                        $MON_SOB_NO_AUT = ($solde_decouvert_retirer - $MONTANT) + $MON_COMISION;
                    }
                    else{
                        $SAL_DISPONIBLE = $MONTANT - ($solde_decouvert_retirer + $MON_COMISION);
                        $MON_SOB_NO_AUT = $CC_AUTORI_X_CUENTA->MON_SOB_NO_AUT;
                        $MON_UTILIZADO =$CC_AUTORI_X_CUENTA->MON_UTILIZADO0;
                        $MON_COMISION = $CC_AUTORI_X_CUENTA->MON_COMISION;
                    }
                    if($MONTANT >= $MON_COMISION)
                        $MON_COMISION =  $CC_AUTORI_X_CUENTA->MON_COMISION;
                    else
                        $MON_COMISION = $MON_COMISION - $MONTANT;
                }
                else{
                    $auth = false;
                }
                $error = false;

                if($auth){
                    if($solde_decouvert_retirer > 0){
                        $champs = ["SAL_DISPONIBLE" => $SAL_DISPONIBLE, "MON_SOB_NO_AUT" => $MON_SOB_NO_AUT, "FEC_ULT_MOVIMIENTO" => $FEC_ULT_MOVIMIENTO, ];
                    }
                    else{
                        $champs = ["SAL_DISPONIBLE" => $SAL_DISPONIBLE, "FEC_ULT_MOVIMIENTO" => $FEC_ULT_MOVIMIENTO];
                    }
                    $CC_CUENTA_EFECTIVO->update($champs);
                    if($solde_decouvert_retirer > 0){
                        $champs = ["MON_PAGADO" => $MON_PAGADO, "MON_UTILIZADO" => $MON_UTILIZADO, "MON_COMISION" => $MON_COMISION];
                        $CC_AUTORI_X_CUENTA->update($champs);
                    }
                    return $this->SOLDE_CLIENT($NUM_CUENTA);
                }
                else{
                    return ['code' => 406, 'message' => "Une incohérence est identifiér sur le traitement du découvert."];
                }
            }
            else if($OPERATION == 'D'){
                if($CC_CUENTA_EFECTIVO->IND_SOBGRO === 'S'){
                    if($CC_CUENTA_EFECTIVO->SAL_DISPONIBLE >=  $MONTANT){
                        $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE - $MONTANT;
                        $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_ULT_MOVIMIENTO;
                        $CC_CUENTA_EFECTIVO->update();
                        return $this->SOLDE_CLIENT($NUM_CUENTA);
                    }
                    else{
                        $MONTANT_RESTANT = $MONTANT - $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE;
                        $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE = 0;
                        $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_ULT_MOVIMIENTO;
                        $CC_CUENTA_EFECTIVO->MON_SOB_NO_AUT = $CC_CUENTA_EFECTIVO->MON_SOB_NO_AUT + $MONTANT_RESTANT;
                        $CC_CUENTA_EFECTIVO->update();
                        $CC_AUTORI_X_CUENTA = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta->where('IND_ESTADO', 'A');
                        if($CC_AUTORI_X_CUENTA->count() > 0) {
                            foreach ($CC_AUTORI_X_CUENTA as $X_CUENTA) {
                                $RESTANT_DECOUVERT = $X_CUENTA->MON_AUTORIZADO - $X_CUENTA->MON_UTILIZADO;
                                if ($RESTANT_DECOUVERT >= $MONTANT_RESTANT) {
                                    $MON_COMISSION = ceil(($X_CUENTA->POR_COMISION * $MONTANT_RESTANT)/100);
                                    $X_CUENTA->MON_UTILIZADO = $X_CUENTA->MON_UTILIZADO + $MONTANT_RESTANT;
                                    $X_CUENTA->MON_COMISION = $X_CUENTA->MON_COMISION + $MON_COMISSION;
                                    $X_CUENTA->update();
                                    break;
                                } else {
                                    $MON_DEBIT = $X_CUENTA->MON_AUTORIZADO - $X_CUENTA->MON_UTILIZADO;
                                    $MON_COMISSION = ceil(($X_CUENTA->POR_COMISION * $MON_DEBIT)/100);
                                    $X_CUENTA->MON_UTILIZADO = $X_CUENTA->MON_AUTORIZADO;
                                    $X_CUENTA->MON_COMISION = $X_CUENTA->MON_COMISION+$MON_COMISSION;
                                    $X_CUENTA->update();
                                    $MONTANT_RESTANT = $MONTANT_RESTANT - $MON_DEBIT;
                                }
                            }
                        }
                        return $this->SOLDE_CLIENT($NUM_CUENTA);
                    }
                }
                else{
                    if($CC_CUENTA_EFECTIVO->SAL_DISPONIBLE >= $MONTANT){
                        $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE = $CC_CUENTA_EFECTIVO->SAL_DISPONIBLE - $MONTANT;
                        $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_ULT_MOVIMIENTO;
                        $CC_CUENTA_EFECTIVO->update();
                        return $this->SOLDE_CLIENT($NUM_CUENTA);
                    }
                    else return ['code' => 400, 'message' => "Solde client insuffisant"];
                }
            }
            else{
                return ['code' => 407, 'message' => "Type d'opération non reconnu!"];
            }
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function TRAN_DIARIO_ENCA($COD_EMPRESA, $COD_AGENCIA, $COD_CAJERO, $COD_CLIENTE, $MTO_MOVIMIENTO, $MTO_EFECTIVO, $NUM_MOV_ENTE, $MON_SALDO_ANTERIOR, $MON_SALDO_DISPONIBLE, $NUM_SECUENCIA_DOC, $ASIENTO_CONTABLE, $COD_ENTE, $COD_MONEDA_ORIGEN, $COD_SISTEMA, $TIP_TRANSACCION, $SUB_TIP_TRANSAC, $FEC_TRANSACCION, $IND_ESTADO, $MTO_VUELTO, $IND_DESGLOSE, $OBSERVACIONES, $MTO_COMISION, $NUM_SEC_DEP_CC, $TIP_ENTE=NULL, $MTO_DOCUMENTO=NULL, $NUM_SEC_COMPROBANTE=NULL)
    {
        try{
            $CJ_TRAN_DIARIO_ENCA =  CJ_TRAN_DIARIO_ENCA::create([
                'COD_EMPRESA' => $COD_EMPRESA,
                'COD_AGENCIA' => $COD_AGENCIA,
                'NUM_SECUENCIA_DOC' => $NUM_SECUENCIA_DOC,
                'COD_CAJERO' => $COD_CAJERO,
                'COD_MONEDA_ORIGEN' => $COD_MONEDA_ORIGEN,
                'COD_CLIENTE' => $COD_CLIENTE,
                'COD_SISTEMA' => $COD_SISTEMA,
                'TIP_TRANSACCION' => $TIP_TRANSACCION,
                'SUB_TIP_TRANSAC' => $SUB_TIP_TRANSAC,
                'FEC_TRANSACCION' => $FEC_TRANSACCION,
                'IND_ESTADO' => $IND_ESTADO,
                'MTO_MOVIMIENTO' => $MTO_MOVIMIENTO,
                'MTO_EFECTIVO' => $MTO_EFECTIVO,
                'MTO_VUELTO' => $MTO_VUELTO,
                'ASIENTO_CONTABLE' => $ASIENTO_CONTABLE,
                'COD_ENTE' => $COD_ENTE,
                'NUM_MOV_ENTE' => $NUM_MOV_ENTE,
                'IND_DESGLOSE' => $IND_DESGLOSE,
                'OBSERVACIONES' => $OBSERVACIONES,
                'NUM_SEC_DEP_CC' => $NUM_SEC_DEP_CC,
                'MON_SALDO_ANTERIOR' => $MON_SALDO_ANTERIOR,
                'MON_SALDO_DISPONIBLE' => $MON_SALDO_DISPONIBLE,
                'MTO_COMISION' => $MTO_COMISION,
                'TIP_ENTE' => $TIP_ENTE,
                'MTO_DOCUMENTO' => $MTO_DOCUMENTO,
                'NUM_SEC_COMPROBANTE' => $NUM_SEC_COMPROBANTE,
            ]);
            return ['code' => 200, 'message' => '', 'data' => $CJ_TRAN_DIARIO_ENCA];
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage(), 'data' => ''];
        }
    }

    public function GetCuentaContableProducto($COD_PRODUCTO)
    {
        try{
            $CC_CARA_X_PRODUCTO = CC_CARA_X_PRODUCTO::where('COD_PRODUCTO', $COD_PRODUCTO)->firstOrFail();
            return ['code' => 200, 'message' => '', 'data' => $CC_CARA_X_PRODUCTO->CUENTA_CONTABLE];
        }
        catch (\Exception $e){
            return ['code' => 500, 'message' => $e->getMessage(), 'data' => ''];
        }
    }

}
