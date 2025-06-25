<?php

namespace App\Http\Controllers;

use App\Models\CC\CC_CUENTA_EFECTIVO;
use App\Models\CL\CL_CLIENTES;
use App\Models\CL\CL_PERSONAS_FISICAS;
use App\Models\CL\CL_PERSONAS_JURIDICAS;
use App\Repositories\TodoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class TodoController extends Controller
{
    protected $todoRepository;
    protected $COD_EMPRESA;
    public function __construct(TodoRepository $todoRepository)
    {
        //$this->middleware('auth:api', ['except' => ['customerCountService']]);
        $this->todoRepository = $todoRepository;
        $this->COD_EMPRESA = env('COD_EMPRESA');
        $this->COD_MONEDA = env('COD_MONEDA');
    }

    public function customerCount()
    {
        $total = CL_CLIENTES::join('CL.CL_DATOS_ASOCIADO', 'CL.CL_CLIENTES.COD_CLIENTE', '=', 'CL.CL_DATOS_ASOCIADO.COD_CLIENTE')
            ->select('CL.CL_CLIENTES.*')
            ->where('CL.CL_DATOS_ASOCIADO.IND_ESTADO', 'A')
            ->count();
        return response()->json([
            'status' => 'success',
            'nombreDeClients' => $total,
        ]);
    }

    public function customerInfos($codeCliente)
    {
        $cl_cliente = CL_CLIENTES::with(['clDirClientes', 'clDirClientes.PA_PAIS', 'clIDsCliente','clIDsCliente.cl_tipos_id', 'ccCuentasEfectivos', 'ccCuentasEfectivos.cf_producto'])->where('COD_CLIENTE', $codeCliente)->first();
        if(!$cl_cliente){
            return response()->json([
                'status' => 'failed',
                'message' => "Le code Client '$codeCliente' n'existe pas",
            ], 401);
        }
        $comptes = [];
        foreach ($cl_cliente->ccCuentasEfectivos as $ccCuentaEfectivo) {
            $comptes[] = ['numero' => $ccCuentaEfectivo->NUM_CUENTA, 'produit' => $ccCuentaEfectivo->cf_producto?->NOM_PRODUCTO];
        }
        if($cl_cliente->IND_PERSONA == 'F'){
            $cl_persona_fisica = CL_PERSONAS_FISICAS::with(['cl_sector_economico'])->where('COD_CLIENTE', $codeCliente)->first();
            $data = [
                'nomClient' => $cl_cliente->NOM_CLIENTE,
                'telephoneClient' => $cl_cliente->TEL_PRINCIPAL != "" ? $cl_cliente->TEL_PRINCIPAL : $cl_cliente->TEL_SECUNDARIO,
                'dateCreation' => Carbon::parse($cl_cliente->FEC_INGRESO)->format('Y-m-d H:i:s'),
                'typeClient' => 'Personne physique',
                'genreClient' => $cl_persona_fisica->IND_SEXO == 'M' ? 'Masculin' : 'Féminin',
                'nationaliteClient' => $cl_persona_fisica->NACIONALIDAD,
                'paysResidenceClient' => $cl_cliente->clDirClientes?->PA_PAIS?->NOM_PAIS,
                'villeResidence' => $cl_cliente->clDirClientes?->PA_PROVINCIA?->DES_PROVINCIA,
                'paysNaissanceClient' => $cl_persona_fisica->NACIONALIDAD,
                'villeNaissance' => $cl_persona_fisica->LUGAR_NACIMIENTO,
                'codePostalClient' => $cl_cliente->clDirClientes?->COD_POSTAL ?? '',
                'adresseClient' => $cl_cliente->clDirClientes?->DET_DIRECCION,
                'typePiece' => $cl_cliente->clIDsCliente[0]?->cl_tipos_id?->DES_TIPO_ID,
                'numeroIdentification' => $cl_cliente->clIDsCliente[0]?->NUM_ID,
                'dateNaissanceClient' => Carbon::parse($cl_cliente->clDatosAssociado->FECH_NACIMIENTO ?? Carbon::now())->format('Y-m-d'),
                'codeActivite' => $cl_persona_fisica->cl_sector_economico?->DES_SECTOR,
                'nomMere' => 'AR',
                'email' => 'AR',
                'typeComptes' => $comptes
            ];
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        }
        elseif($cl_cliente->IND_PERSONA == 'J'){
            $cl_persona_juridica = CL_PERSONAS_JURIDICAS::with(['cl_clase_sociedad', 'cl_sector_economico'])->where('COD_CLIENTE', $codeCliente)->first();
            $data = [
                'nomClient' => $cl_cliente->NOM_CLIENTE,
                'telephoneClient' => $cl_cliente->TEL_PRINCIPAL != "" ? $cl_cliente->TEL_PRINCIPAL : $cl_cliente->TEL_SECUNDARIO,
                'dateCreation' => Carbon::parse($cl_cliente->FEC_INGRESO)->format('Y-m-d H:i:s'),
                'typeClient' => $cl_persona_juridica->cl_clase_sociedad?->DES_SOCIEDAD,
                'paysResidenceClient' => $cl_cliente->clDirClientes?->PA_PAIS?->NOM_PAIS,
                'villeResidence' => $cl_cliente->clDirClientes?->PA_PROVINCIA?->DES_PROVINCIA,
                'codePostalClient' => $cl_cliente->clDirClientes?->COD_POSTAL ?? '',
                'adresseClient' => $cl_cliente->clDirClientes?->DET_DIRECCION,
                'identificationFiscale' => $cl_cliente->clIDsCliente->where('COD_TIPO_ID', 110)->first()?->NUM_ID ?? '',
                'identificationRccm' => $cl_cliente->clIDsCliente->where('COD_TIPO_ID', 111)->first()?->NUM_ID ?? '',
                'codeActivite' => $cl_persona_juridica->cl_sector_economico?->DES_SECTOR,
                'categorieEntreprise' => $cl_persona_juridica->CLASE_SOCIEDAD,
                'typeComptes' => $comptes
            ];
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => "Type de client non reconnu!",
            ], 404);
        }

    }

    public function cuentaInfos($codAgencia, $numeroCompte)
    {
        $cc_cuenta = CC_CUENTA_EFECTIVO::where('COD_AGENCIA', $codAgencia)->where('NUM_CUENTA', $numeroCompte)->first();
        if(!$cc_cuenta){
            return response()->json([
                'status' => 'failed',
                'message' => "Le code agence et le numéro de compte ne correspondent pas.",
            ], 401);
        }
        $data = [
            'codeAgence' => $cc_cuenta->COD_AGENCIA,
            'numeroCompte' => $cc_cuenta->NUM_CUENTA,
            'dateOuvertureCompte' => Carbon::parse($cc_cuenta->FEC_APERTURA)->format('Y-m-d H:i:s'),
            'iban' => 'AR',
            'solde' => $cc_cuenta-> SAL_DISPONIBLE
        ];
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function customerCreate(Request $request)
    {

        return response()->json([
            'status' => 'success',
            'todo' => null,
        ]);
    }

    public function creditCompte(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numeroCompte' => 'required|string|max:15',
            'codeAgenceCompte' => 'required|string|max:10',
            'montantTransaction' => 'required|numeric|min:0',
            'commissionBanque' => 'required|numeric|min:0',
            'commissionPartenaire' => 'required|numeric|min:0',
            'motifTransaction' => 'required|string|max:255',
            'transactionDate' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 422);
        }

        try{
            $COD_AGENCIA = $request->codeAgenceCompte;
            $NUM_CUENTA = $request->numeroCompte;
            //$MONTANT = $request->montantTransaction + $request->commissionBanque + $request->commissionPartenaire;
            $MONTANT = $request->montantTransaction;
            $COD_EMPRESA = $this->COD_EMPRESA;
            $DES_REFERENCIA = $request->motifTransaction;
            $COD_SISTEMA = 'CC';
            $TIP_TRANSACCION_CREDIT = env('TIP_TRANSACCION_CREDIT');
            $SUBTIP_TRANSAC= env('SUBTIP_TRANSAC');
            $COD_USUARIO = env('COD_USUARIO');
            $NUM_DOCUMENTO_MOVIMTO_DIARIO = '1';
            $EST_MOVIMIENTO_MOVIMTO_DIARIO = 'A';
            $IND_APL_CARGO_MOVIMTO_DIARIO = 'N';
            $SISTEMA_FUENTE_MOVIMTO_DIARIO = 'CC';
            $NUM_MOV_FUENTE_MOVIMTO_DIARIO = '0';
            $DES_MOVIMIENTO_MOVIMTO_DIARIO_DEPOT="NOTE DE CRÉDIT";


            DB::beginTransaction();
            $date = $this->todoRepository->getDateSYSTEME($COD_EMPRESA, $COD_AGENCIA, $COD_SISTEMA);
             if($date['code'] != 200){
                 return response()->json([
                    'status' => 'failed',
                     'message' => $date['message'],
                 ], $date['code']);
             }
            $tab = explode(' ', $date['data']);
            $FEC_MOVIMIENTO = $tab[0].' '.date('H:i:s');
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::with(['cf_producto'])->where('NUM_CUENTA', $NUM_CUENTA)->where('COD_AGENCIA', $COD_AGENCIA)->first();
            if(!$CC_CUENTA_EFECTIVO){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le numéro du compte et le code de l'agence ne correspondent pas.",
                ], 407);
            }

            /*  DEBUT TRAITEMENT DECOUVERT */
            if($CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S' && $CC_CUENTA_EFECTIVO?->cc_autori_x_cuenta->IND_ESTADO == 'A'){

                $DECOUVERTS = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta;
                if($DECOUVERTS->count() > 0){
                    $DECOUVERT = $DECOUVERTS[0];
                    if($DECOUVERT->MON_COMISION >= 0 || $DECOUVERT->MON_UTILIZADO > 0){
                        return response()->json([
                            'status' => 'failed',
                            'message' => "L'état du compte ne permet pas d'effectuer cette opération",
                        ], 522);
                    }
                }
            }
            /*  FIN TRAITEMENT DECOUVERT */
            $NUM_MOVIMI = $this->todoRepository->IncValSiguienteEmpresa();
            if($NUM_MOVIMI['code'] != 200) {
                return response()->json([
                   'status' => 'failed',
                    'message' => $NUM_MOVIMI['message'],
                ], $NUM_MOVIMI['code']);
            }
            $NUM_MOVIMIENTO = $NUM_MOVIMI['data']->CC_MOV_DIA;
            $SOLDE_CLIENT_AVANT = $this->todoRepository->SOLDE_CLIENT($NUM_CUENTA);
            if($SOLDE_CLIENT_AVANT['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $SOLDE_CLIENT_AVANT['message'],
                ], $SOLDE_CLIENT_AVANT['code']);
            }

            $CREDITER_SOLDE_CLIENTE = $this->todoRepository->UPDATE_SOLDE_CLIENTE($NUM_CUENTA, $MONTANT, $DEPOT='C', $FEC_MOVIMIENTO);
            if($CREDITER_SOLDE_CLIENTE['code'] != 200){
                return response()->json([
                   'status' => 'failed',
                    'message' => $CREDITER_SOLDE_CLIENTE['message'],
                ], $CREDITER_SOLDE_CLIENTE['code']);
            }
            $SOLDE_CLIENT_APRES = $CREDITER_SOLDE_CLIENTE['solde'];

            $MOVIMTO_DIARIO = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_CREDIT, $SUBTIP_TRANSAC, $COD_SISTEMA, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DEPOT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
            if($MOVIMTO_DIARIO['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $MOVIMTO_DIARIO['message'],
                ], $MOVIMTO_DIARIO['code']);
            }

            $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_MOVIMIENTO;
            $CC_CUENTA_EFECTIVO->update();
            DB::commit();
            if (env('APP_ENV') == 'local') {
                $tel = env('TEL_ADMIN');
            } else {
                $tel = "";
                $CL_CLIENTE = CL_CLIENTES::where('COD_CLIENTE', $CC_CUENTA_EFECTIVO->COD_CLIENTE)
                    ->where('COD_EMPRESA', $CC_CUENTA_EFECTIVO->COD_EMPRESA)->first();
                if($CL_CLIENTE){
                    $tel = $CL_CLIENTE->TEL_PRINCIPAL;
                }

            }
            if($tel != ""){
                $message = "Dépôt espèces, le " . date('Y-m-d H:i:s') . " compte " .  $CC_CUENTA_EFECTIVO->COD_CLIENTE . " du montant de " . Utils::getFormatMoney($MONTANT) . ". Nouveau solde: " . number_format($SOLDE_CLIENT_APRES, 0, ".", " ");
                //$this->todoRepository->sendSMS($tel, $message);
            }
            return response()->json([
                'status' => 'success',
                'message' => "Opération de dépot effectée avec succès!",
                'referenceTransaction' => $NUM_MOVIMIENTO,
            ]);
        }
        catch (\Exception $e){
            return [
                'code' => 500,
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function creditCompteOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numeroCompte' => 'required|string|max:15',
            'codeAgenceCompte' => 'required|string|max:10',
            'montantTransaction' => 'required|numeric|min:0',
            'commissionBanque' => 'required|numeric|min:0',
            'commissionPartenaire' => 'required|numeric|min:0',
            'motifTransaction' => 'required|string|max:255',
            'transactionDate' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 422);
        }

       /* $request->validate(,
            [
                'numeroCompte.required' => 'Le numéro de compte est obligatoire.',
                'numeroCompte.max' => 'Le numéro de compte ne doit pas dépasser 15 caractères.',

                'codeAgenceCompte.required' => 'Le code agence est obligatoire.',
                'codeAgenceCompte.max' => 'Le code agence ne doit pas dépasser 10 caractères.',

                'montantTransaction.required' => 'Le montant de la transaction est requis.',
                'montantTransaction.integer' => 'Le montant de la transaction doit être un entier.',
                'montantTransaction.min' => 'Le montant doit être supérieur ou égal à 0.',

                'commissionBanque.required' => 'La commission banque est obligatoire.',
                'commissionBanque.integer' => 'La commission banque doit être un entier.',
                'commissionBanque.min' => 'La commission banque doit être ≥ 0.',

                'commissionPartenaire.required' => 'La commission partenaire est obligatoire.',
                'commissionPartenaire.integer' => 'La commission partenaire doit être un entier.',
                'commissionPartenaire.min' => 'La commission partenaire doit être ≥ 0.',

                'motifTransaction.required' => 'Le motif de la transaction est requis.',
                'motifTransaction.max' => 'Le motif ne doit pas dépasser 255 caractères.',

                'transactionDate.required' => 'La date de la transaction est requise.',
                'transactionDate.date_format' => 'La date doit être au format YYYY-MM-DD HH:MM:SS.',
            ]);*/
        try{
            $COD_AGENCIA = $request->codeAgenceCompte;
            $NUM_CUENTA = $request->numeroCompte;
            //$MONTANT = $request->montantTransaction + $request->commissionBanque + $request->commissionPartenaire;
            $MONTANT = $request->montantTransaction;
            $COD_EMPRESA = $this->COD_EMPRESA;
            $DES_REFERENCIA = $request->motifTransaction;
            $COD_SISTEMA = 'CJ';
            $COD_SISTEMA_MOVIMTO_DIARIO = 'CC';
            $TIP_TRANSACCION_CREDIT = env('TIP_TRANSACCION_CREDIT');
            $SUBTIP_TRANSAC= env('SUBTIP_TRANSAC');
            $COD_USUARIO = 'SAF2000';
            $NUM_DOCUMENTO_MOVIMTO_DIARIO = '1';
            $EST_MOVIMIENTO_MOVIMTO_DIARIO = 'A';
            $IND_APL_CARGO_MOVIMTO_DIARIO = 'N';
            $SISTEMA_FUENTE_MOVIMTO_DIARIO = 'CC';
            $NUM_MOV_FUENTE_MOVIMTO_DIARIO = '0';
            $DES_MOVIMIENTO_MOVIMTO_DIARIO_DEPOT="NOTE DE CRÉDIT";


            $TIP_TRANSACCION_DECOUVERT = env('TIP_TRANSACCION_DECOUVERT');
            $SUBTIP_TRANSAC_DECOUVERT = env('SUBTIP_TRANSAC_DECOUVERT');
            $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT = env('DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT');

            $DES_ASIENTO_DECOUVERT = "PAIEMENT DE COMMISSION SUR DECOUVERT";
            $EST_ASIENTO_ASTO_DECOUVERT ="P";
            $IND_LIQUIDACION_ASTO_DECOUVERT = "N";
            $IND_POST_CIERRE_ASTO_DECOUVERT = "N";

            $DETAILE_ASTO_ASTO = "DEBIT POUR PAIEMENT DE COMMISSION SUR DE";
            $TIP_CAM_BASE_DEPOT = 1.00;
            $TIP_CAM_CTA_DEPOT = 1.00;

            $TIP_TRANSACCION_MOVIMTO_DIARIO_DEPOT=16;
            $SUBTIP_TRANSAC_MOVIMTO_DIARIO_DEPOT=1;

            $SUB_TIP_TRANSAC = '';
            $COD_MONEDA = $this->COD_MONEDA;
            $MTO_COMISSION = 0;

            DB::beginTransaction();
            //$checkOuvertureCaisse = $this->todoRepository->chekOuvertureCaisse($COD_EMPRESA, $COD_AGENCIA);
            $checkOuvertureCaisse['code']=200;
            if($checkOuvertureCaisse['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $checkOuvertureCaisse['message'],
                ], $checkOuvertureCaisse['code']);

            }

            $date = $this->todoRepository->getDateSYSTEME($COD_EMPRESA, $COD_AGENCIA, $COD_SISTEMA);
             if($date['code'] != 200){
                 return response()->json([
                    'status' => 'failed',
                     'message' => $date['message'],
                 ], $date['code']);
             }
            $tab = explode(' ', $date['data']);
            $FEC_MOVIMIENTO = $tab[0].' '.date('H:i:s');
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::with(['cf_producto'])->where('NUM_CUENTA', $NUM_CUENTA)->where('COD_AGENCIA', $COD_AGENCIA)->first();
            if(!$CC_CUENTA_EFECTIVO){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le numéro du compte et le code de l'agence ne correspondent pas.",
                ], 407);
            }

            /*  DEBUT TRAITEMENT DECOUVERT */
            if($CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S' && $CC_CUENTA_EFECTIVO?->cc_autori_x_cuenta->IND_ESTADO == 'A'){

                $DECOUVERTS = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta;
                if($DECOUVERTS->count() > 0){
                    $DECOUVERT = $DECOUVERTS[0];
                    if($DECOUVERT->MON_COMISION >= 0 || $DECOUVERT->MON_UTILIZADO > 0){
                        return response()->json([
                            'status' => 'failed',
                            'message' => "L'état du compte ne permet pas d'effectuer cette opération",
                        ], 522);
                        /*$NUM_MOVIMI_DECOUVERT = $this->todoRepository->IncValSiguienteEmpresa();
                        if($NUM_MOVIMI_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $NUM_MOVIMI_DECOUVERT['message'],
                            ], $NUM_MOVIMI_DECOUVERT['code']);
                        }
                        $NUM_MOVIMIENTO_DECOUVERT = $NUM_MOVIMI_DECOUVERT['data']->CC_MOV_DIA;
                        $CONS_ASTO_DECOUVERT = $NUM_MOVIMI_DECOUVERT['data']->CONS_ASTO;

                        $MAX_REFERENCIA_DECOUVERT = $this->todoRepository->MAX_REFERENCIA();
                        if($MAX_REFERENCIA_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $MAX_REFERENCIA_DECOUVERT['message'],
                            ], $MAX_REFERENCIA_DECOUVERT['code']);
                        }

                        $MOVIMTO_DIARIO_DECOUVERT = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO_DECOUVERT, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_DECOUVERT, $SUBTIP_TRANSAC_DECOUVERT, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
                        if($MOVIMTO_DIARIO_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $MOVIMTO_DIARIO_DECOUVERT['message'],
                            ], $MOVIMTO_DIARIO_DECOUVERT['code']);
                        }
                       $ASTO_RESUMEN_DECOUVERT = $this->todoRepository->ASTO_RESUMEN($CONS_ASTO_DECOUVERT, $COD_EMPRESA, $COD_AGENCIA, $TIP_TRANSACCION_DECOUVERT, $SUBTIP_TRANSAC_DECOUVERT, $COD_SISTEMA, $FEC_MOVIMIENTO, $DES_ASIENTO_DECOUVERT, $EST_ASIENTO_ASTO_DECOUVERT, $COD_USUARIO, $IND_LIQUIDACION_ASTO_DECOUVERT, $IND_POST_CIERRE_ASTO_DECOUVERT);
                        if($ASTO_RESUMEN_DECOUVERT['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_RESUMEN_DECOUVERT['message'],
                            ], $ASTO_RESUMEN_DECOUVERT['code']);
                        }

                        $CUENTA_CONTABLE_CAJERO = $this->todoRepository->GetCuentaContableOperation($COD_USUARIO);
                        if($CUENTA_CONTABLE_CAJERO['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $CUENTA_CONTABLE_CAJERO['message'],
                            ], $CUENTA_CONTABLE_CAJERO['code']);
                        }

                        $ASTO_DETALLE_CAJERO = $this->todoRepository->ASTO_DETALLE($CONS_ASTO_DECOUVERT, '1', $CUENTA_CONTABLE_CAJERO['data'], $MONTANT, '0.00', $MAX_REFERENCIA_DECOUVERT['data'], $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO_ASTO, $TIP_CAM_BASE_DEPOT, $TIP_CAM_CTA_DEPOT);
                        if($ASTO_DETALLE_CAJERO['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_DETALLE_CAJERO['message'],
                            ], $ASTO_DETALLE_CAJERO['code']);
                        }
                        $COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT = env('COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT');
                        $ASTO_DETALLE_BOVEDA = $this->todoRepository->ASTO_DETALLE($CONS_ASTO_DECOUVERT, '2', $COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT, '0.00', $MONTANT, $MAX_REFERENCIA_DECOUVERT['data'], $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO_ASTO, $TIP_CAM_BASE_DEPOT, $TIP_CAM_CTA_DEPOT);
                        if($ASTO_DETALLE_BOVEDA['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_DETALLE_BOVEDA['message'],
                            ], $ASTO_DETALLE_BOVEDA['code']);
                        }*/
                    }
                }
            }
            /*  FIN TRAITEMENT DECOUVERT */
            $NUM_MOVIMI = $this->todoRepository->IncValSiguienteEmpresa();
            if($NUM_MOVIMI['code'] != 200) {
                return response()->json([
                   'status' => 'failed',
                    'message' => $NUM_MOVIMI['message'],
                ], $NUM_MOVIMI['code']);
            }
            $NUM_MOVIMIENTO = $NUM_MOVIMI['data']->CC_MOV_DIA;
            $SOLDE_CLIENT_AVANT = $this->todoRepository->SOLDE_CLIENT($NUM_CUENTA);
            if($SOLDE_CLIENT_AVANT['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $SOLDE_CLIENT_AVANT['message'],
                ], $SOLDE_CLIENT_AVANT['code']);
            }

            $CREDITER_SOLDE_CLIENTE = $this->todoRepository->UPDATE_SOLDE_CLIENTE($NUM_CUENTA, $MONTANT, $DEPOT='C', $FEC_MOVIMIENTO);
            if($CREDITER_SOLDE_CLIENTE['code'] != 200){
                return response()->json([
                   'status' => 'failed',
                    'message' => $CREDITER_SOLDE_CLIENTE['message'],
                ], $CREDITER_SOLDE_CLIENTE['code']);
            }
            //$SOLDE_CLIENT_AVANT = $SOLDE_CLIENT_AVANT['solde'];
            $SOLDE_CLIENT_APRES = $CREDITER_SOLDE_CLIENTE['solde'];

            $MOVIMTO_DIARIO = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_CREDIT, $SUBTIP_TRANSAC, $COD_SISTEMA_MOVIMTO_DIARIO, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DEPOT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
            if($MOVIMTO_DIARIO['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $MOVIMTO_DIARIO['message'],
                ], $MOVIMTO_DIARIO['code']);
            }

            $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_MOVIMIENTO;
            $CC_CUENTA_EFECTIVO->update();
            DB::commit();
            if (env('APP_ENV') == 'local') {
                $tel = env('TEL_ADMIN');
            } else {
                $tel = "";
                $CL_CLIENTE = CL_CLIENTES::where('COD_CLIENTE', $CC_CUENTA_EFECTIVO->COD_CLIENTE)
                    ->where('COD_EMPRESA', $CC_CUENTA_EFECTIVO->COD_EMPRESA)->first();
                if($CL_CLIENTE){
                    $tel = $CL_CLIENTE->TEL_PRINCIPAL;
                }

            }
            if($tel != ""){
                $message = "Dépôt espèces, le " . date('Y-m-d H:i:s') . " compte " .  $CC_CUENTA_EFECTIVO->COD_CLIENTE . " du montant de " . Utils::getFormatMoney($MONTANT) . ". Nouveau solde: " . number_format($SOLDE_CLIENT_APRES, 0, ".", " ");
                //$this->todoRepository->sendSMS($tel, $message);
            }
            return response()->json([
                'status' => 'success',
                'message' => "Opération de dépot effectée avec succès!",
                'referenceTransaction' => $NUM_MOVIMIENTO,
            ]);
        }
        catch (\Exception $e){
            return [
                'code' => 500,
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
    public function debitCompte(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numeroCompte' => 'required|string|max:15',
            'codeAgenceCompte' => 'required|string|max:10',
            'montantTransaction' => 'required|numeric|min:0',
            'commissionBanque' => 'required|numeric|min:0',
            'commissionPartenaire' => 'required|numeric|min:0',
            'motifTransaction' => 'required|string|max:255',
            'transactionDate' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 422);
        }
        try{
            $COD_AGENCIA = $request->codeAgenceCompte;
            $NUM_CUENTA = $request->numeroCompte;
            $MONTANT = $request->montantTransaction + $request->commissionBanque + $request->commissionPartenaire;
            $COD_EMPRESA = $this->COD_EMPRESA;
            $DES_REFERENCIA = $request->motifTransaction;
            $COD_SISTEMA = 'CC';
            $TIP_TRANSACCION_DEBIT = env('TIP_TRANSACCION_DEBIT');
            $SUBTIP_TRANSAC = env('SUBTIP_TRANSAC');
            $COD_USUARIO = env('COD_USUARIO');
            $NUM_DOCUMENTO_MOVIMTO_DIARIO = '1';
            $EST_MOVIMIENTO_MOVIMTO_DIARIO = 'A';
            $IND_APL_CARGO_MOVIMTO_DIARIO = 'N';
            $SISTEMA_FUENTE_MOVIMTO_DIARIO = 'CC';
            $NUM_MOV_FUENTE_MOVIMTO_DIARIO = '0';
            $DES_MOVIMIENTO_MOVIMTO_DIARIO="NOTE DE DÉBIT";

            DB::beginTransaction();

            $date = $this->todoRepository->getDateSYSTEME($COD_EMPRESA, $COD_AGENCIA, $COD_SISTEMA);
             if($date['code'] != 200){
                 return response()->json([
                    'status' => 'failed',
                     'message' => $date['message'],
                 ], $date['code']);
             }
            $tab = explode(' ', $date['data']);
            $FEC_MOVIMIENTO = $tab[0].' '.date('H:i:s');
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::with(['cf_producto'])->where('NUM_CUENTA', $NUM_CUENTA)->where('COD_AGENCIA', $COD_AGENCIA)->first();
            if(!$CC_CUENTA_EFECTIVO){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le numéro du compte et le code de l'agence ne correspondent pas.",
                ], 407);
            }

            $SOLDE_CLIENT = $this->todoRepository->SOLDE_CLIENT($NUM_CUENTA);

            if($SOLDE_CLIENT['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $SOLDE_CLIENT['message'],
                ], $SOLDE_CLIENT['code']);
            }
            $SOLDE_CLIENT_AVANT = $SOLDE_CLIENT['solde'];
            /*if($CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S' && $CC_CUENTA_EFECTIVO?->cc_autori_x_cuenta->IND_ESTADO == 'A') {
                $CC_AUTORI_X_CUENTA = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta;
                if ($CC_AUTORI_X_CUENTA->count() > 0) {
                    foreach ($CC_AUTORI_X_CUENTA as $DECOUVERT){
                        $MONTANT_DECOUVERT += ($DECOUVERT->MON_AUTORIZADO - $DECOUVERT->MON_UTILIZADO);
                    }
                }
            }*/
            //$SOLDE_GLOBAL_CLIENT = $SOLDE_CLIENT_AVANT + $MONTANT_DECOUVERT;
            $SOLDE_GLOBAL_CLIENT = $SOLDE_CLIENT_AVANT;
            if($SOLDE_GLOBAL_CLIENT < $MONTANT){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le solde du client est insuffisant.",
                ], 402);
            }

            $NUM_MOVIMI = $this->todoRepository->IncValSiguienteEmpresa();
            if($NUM_MOVIMI['code'] != 200) {
                return response()->json([
                   'status' => 'failed',
                    'message' => $NUM_MOVIMI['message'],
                ], $NUM_MOVIMI['code']);
            }
            $NUM_MOVIMIENTO = $NUM_MOVIMI['data']->CC_MOV_DIA;
            $CREDITER_SOLDE_CLIENTE = $this->todoRepository->UPDATE_SOLDE_CLIENTE($NUM_CUENTA, $MONTANT, $DEPOT='D', $FEC_MOVIMIENTO);
            if($CREDITER_SOLDE_CLIENTE['code'] != 200){
                return response()->json([
                   'status' => 'failed',
                    'message' => $CREDITER_SOLDE_CLIENTE['message'],
                ], $CREDITER_SOLDE_CLIENTE['code']);
            }
            $SOLDE_CLIENT_APRES = $CREDITER_SOLDE_CLIENTE['solde'];

            $MOVIMTO_DIARIO = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_DEBIT, $SUBTIP_TRANSAC, $COD_SISTEMA, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
            if($MOVIMTO_DIARIO['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $MOVIMTO_DIARIO['message'],
                ], $MOVIMTO_DIARIO['code']);
            }

            $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_MOVIMIENTO;
            $CC_CUENTA_EFECTIVO->update();
            DB::commit();
            if (env('APP_ENV') == 'local') {
                $tel = env('TEL_ADMIN');
            } else {
                $tel = "";
                $CL_CLIENTE = CL_CLIENTES::where('COD_CLIENTE', $CC_CUENTA_EFECTIVO->COD_CLIENTE)
                    ->where('COD_EMPRESA', $CC_CUENTA_EFECTIVO->COD_EMPRESA)->first();
                if($CL_CLIENTE){
                    $tel = $CL_CLIENTE->TEL_PRINCIPAL;
                }

            }
            if($tel != ""){
                $message = "Dépôt espèces, le " . date('Y-m-d H:i:s') . " compte " .  $CC_CUENTA_EFECTIVO->COD_CLIENTE . " du montant de " . Utils::getFormatMoney($MONTANT) . ". Nouveau solde: " . number_format($SOLDE_CLIENT_APRES, 0, ".", " ");
                //$this->todoRepository->sendSMS($tel, $message);
            }
            return response()->json([
                'status' => 'success',
                'message' => "Opération de dépot effectée avec succès!",
                'referenceTransaction' => $NUM_MOVIMIENTO,
            ]);
        }
        catch (\Exception $e){
            return [
                'code' => 500,
                'status' => 'failed',
                'message' => $e,
            ];
        }
    }

    public function debitCompteOld(Request $request)
    {
        $request->validate([
            'numeroCompte' => 'required|string|max:15',
            'codeAgenceCompte' => 'required|string|max:10',
            'montantTransaction' => 'required|numeric|min:0',
            'commissionBanque' => 'required|numeric|min:0',
            'commissionPartenaire' => 'required|numeric|min:0',
            'motifTransaction' => 'required|string|max:255',
            'transactionDate' => 'required|date_format:Y-m-d H:i:s',
        ],
            [
                'numeroCompte.required' => 'Le numéro de compte est obligatoire.',
                'numeroCompte.max' => 'Le numéro de compte ne doit pas dépasser 15 caractères.',

                'codeAgenceCompte.required' => 'Le code agence est obligatoire.',
                'codeAgenceCompte.max' => 'Le code agence ne doit pas dépasser 10 caractères.',

                'montantTransaction.required' => 'Le montant de la transaction est requis.',
                'montantTransaction.integer' => 'Le montant de la transaction doit être un entier.',
                'montantTransaction.min' => 'Le montant doit être supérieur ou égal à 0.',

                'commissionBanque.required' => 'La commission banque est obligatoire.',
                'commissionBanque.integer' => 'La commission banque doit être un entier.',
                'commissionBanque.min' => 'La commission banque doit être ≥ 0.',

                'commissionPartenaire.required' => 'La commission partenaire est obligatoire.',
                'commissionPartenaire.integer' => 'La commission partenaire doit être un entier.',
                'commissionPartenaire.min' => 'La commission partenaire doit être ≥ 0.',

                'motifTransaction.required' => 'Le motif de la transaction est requis.',
                'motifTransaction.max' => 'Le motif ne doit pas dépasser 255 caractères.',

                'transactionDate.required' => 'La date de la transaction est requise.',
                'transactionDate.date_format' => 'La date doit être au format YYYY-MM-DD HH:MM:SS.',
            ]);
        try{
            $COD_AGENCIA = $request->codeAgenceCompte;
            $NUM_CUENTA = $request->numeroCompte;
            $MONTANT = $request->montantTransaction + $request->commissionBanque + $request->commissionPartenaire;
            $COD_EMPRESA = $this->COD_EMPRESA;
            $DES_REFERENCIA = $request->motifTransaction;
            $COD_SISTEMA = 'CJ';
            $COD_SISTEMA_MOVIMTO_DIARIO = 'CC';
            $TIP_TRANSACCION_DEPOT = env('TIP_TRANSACCION_TRANSFERT');
            $COD_USUARIO = '';
            $NUM_DOCUMENTO_MOVIMTO_DIARIO = '';
            $EST_MOVIMIENTO_MOVIMTO_DIARIO = '';
            $IND_APL_CARGO_MOVIMTO_DIARIO = '';
            $SISTEMA_FUENTE_MOVIMTO_DIARIO = '';
            $NUM_MOV_FUENTE_MOVIMTO_DIARIO = '';

            $TIP_TRANSACCION_DECOUVERT = env('TIP_TRANSACCION_DECOUVERT');
            $SUBTIP_TRANSAC_DECOUVERT = env('SUBTIP_TRANSAC_DECOUVERT');
            $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT = env('DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT');

            $DES_ASIENTO_DECOUVERT = "PAIEMENT DE COMMISSION SUR DECOUVERT";
            $EST_ASIENTO_ASTO_DECOUVERT ="P";
            $IND_LIQUIDACION_ASTO_DECOUVERT = "N";
            $IND_POST_CIERRE_ASTO_DECOUVERT = "N";

            $DETAILE_ASTO_ASTO = "DEBIT POUR PAIEMENT DE COMMISSION SUR DE";
            $TIP_CAM_BASE_DEPOT = 1.00;
            $TIP_CAM_CTA_DEPOT = 1.00;

            $TIP_TRANSACCION_MOVIMTO_DIARIO_DEPOT=16;
            $SUBTIP_TRANSAC_MOVIMTO_DIARIO_DEPOT=1;
            $DES_MOVIMIENTO_MOVIMTO_DIARIO_DEPOT="DÉPÔT EN COMPTES";

            $SUB_TIP_TRANSAC = '';
            $COD_MONEDA = $this->COD_MONEDA;
            $MTO_COMISSION = 0;
            $MONTANT_DECOUVERT=0;

            DB::beginTransaction();
            $checkOuvertureCaisse = $this->todoRepository->chekOuvertureCaisse($COD_EMPRESA, $COD_AGENCIA);
            if($checkOuvertureCaisse['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $checkOuvertureCaisse['message'],
                ], $checkOuvertureCaisse['code']);

            }

            $SG_USUARIOS_X_TRANSACCION = $this->todoRepository->SG_USUARIOS_X_TRANSACCION($COD_EMPRESA, $COD_AGENCIA,$COD_USUARIO, $COD_SISTEMA, $TIP_TRANSACCION_DEPOT);
            if($SG_USUARIOS_X_TRANSACCION['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Vous n'êtes pas autorisé à effectuer cette opération.",
                ], 405);
            }

            $MAX_X_CAJERO = $this->todoRepository->MAX_X_CAJERO($COD_EMPRESA, $COD_SISTEMA, $COD_AGENCIA, $COD_USUARIO, $TIP_TRANSACCION_DEPOT);
            if($MAX_X_CAJERO['code'] != 200){
                return response()->json([
                   'status' => 'failed',
                    'message' => $MAX_X_CAJERO['message'],
                ], $MAX_X_CAJERO['code']);
            }
            if($MAX_X_CAJERO['data']->MTO_MAXIMO < $MONTANT){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Vous n'êtes pas autorisé à effectuer une opération de ce montant(montant maximum= ".$MAX_X_CAJERO['data']->MTO_MAXIMO.").",
                ],406);
            }

            $date = $this->todoRepository->getDateSYSTEME($COD_EMPRESA, $COD_AGENCIA, $COD_SISTEMA);
             if($date['code'] != 200){
                 return response()->json([
                    'status' => 'failed',
                     'message' => $MAX_X_CAJERO['message'],
                 ], $MAX_X_CAJERO['code']);
             }
            $tab = explode(' ', $date['data']);
            $FEC_MOVIMIENTO = $tab[0].' '.date('H:i:s');
            $CC_CUENTA_EFECTIVO = CC_CUENTA_EFECTIVO::with(['cf_producto'])->where('NUM_CUENTA', $NUM_CUENTA)->where('COD_AGENCIA', $COD_AGENCIA)->first();
            if(!$CC_CUENTA_EFECTIVO){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le numéro du compte et le code de l'agence ne correspondent pas.",
                ], 407);
            }
            /*  DEBUT TRAITEMENT DECOUVERT */
            /*if($CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S' && $CC_CUENTA_EFECTIVO?->cc_autori_x_cuenta->IND_ESTADO == 'A'){
                $DECOUVERT = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta;
                if($DECOUVERT){
                    if($DECOUVERT->MON_COMISION >= 0){
                        $NUM_MOVIMI_DECOUVERT = $this->todoRepository->IncValSiguienteEmpresa();
                        if($NUM_MOVIMI_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $NUM_MOVIMI_DECOUVERT['message'],
                            ], $NUM_MOVIMI_DECOUVERT['code']);
                        }
                        $NUM_MOVIMIENTO_DECOUVERT = $NUM_MOVIMI_DECOUVERT['data']->CC_MOV_DIA;
                        $CONS_ASTO_DECOUVERT = $NUM_MOVIMI_DECOUVERT['data']->CONS_ASTO;

                        $MAX_REFERENCIA_DECOUVERT = $this->todoRepository->MAX_REFERENCIA();
                        if($MAX_REFERENCIA_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $MAX_REFERENCIA_DECOUVERT['message'],
                            ], $MAX_REFERENCIA_DECOUVERT['code']);
                        }

                        $MOVIMTO_DIARIO_DECOUVERT = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO_DECOUVERT, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_DECOUVERT, $SUBTIP_TRANSAC_DECOUVERT, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_DECOUVERT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
                        if($MOVIMTO_DIARIO_DECOUVERT['code'] != 200){
                            return response()->json([
                                'status' => 'failed',
                                'message' => $MOVIMTO_DIARIO_DECOUVERT['message'],
                            ], $MOVIMTO_DIARIO_DECOUVERT['code']);
                        }
                       $ASTO_RESUMEN_DECOUVERT = $this->todoRepository->ASTO_RESUMEN($CONS_ASTO_DECOUVERT, $COD_EMPRESA, $COD_AGENCIA, $TIP_TRANSACCION_DECOUVERT, $SUBTIP_TRANSAC_DECOUVERT, $COD_SISTEMA, $FEC_MOVIMIENTO, $DES_ASIENTO_DECOUVERT, $EST_ASIENTO_ASTO_DECOUVERT, $COD_USUARIO, $IND_LIQUIDACION_ASTO_DECOUVERT, $IND_POST_CIERRE_ASTO_DECOUVERT);
                        if($ASTO_RESUMEN_DECOUVERT['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_RESUMEN_DECOUVERT['message'],
                            ], $ASTO_RESUMEN_DECOUVERT['code']);
                        }

                        $CUENTA_CONTABLE_CAJERO = $this->todoRepository->GetCuentaContableOperation($COD_USUARIO);
                        if($CUENTA_CONTABLE_CAJERO['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $CUENTA_CONTABLE_CAJERO['message'],
                            ], $CUENTA_CONTABLE_CAJERO['code']);
                        }

                        $ASTO_DETALLE_CAJERO = $this->todoRepository->ASTO_DETALLE($CONS_ASTO_DECOUVERT, '1', $CUENTA_CONTABLE_CAJERO['data'], $MONTANT, '0.00', $MAX_REFERENCIA_DECOUVERT['data'], $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO_ASTO, $TIP_CAM_BASE_DEPOT, $TIP_CAM_CTA_DEPOT);
                        if($ASTO_DETALLE_CAJERO['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_DETALLE_CAJERO['message'],
                            ], $ASTO_DETALLE_CAJERO['code']);
                        }
                        $COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT = env('COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT');
                        $ASTO_DETALLE_BOVEDA = $this->todoRepository->ASTO_DETALLE($CONS_ASTO_DECOUVERT, '2', $COMPTE_COMPTABLE_ASTO_DETALLE_DECOUVERT, '0.00', $MONTANT, $MAX_REFERENCIA_DECOUVERT['data'], $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO_ASTO, $TIP_CAM_BASE_DEPOT, $TIP_CAM_CTA_DEPOT);
                        if($ASTO_DETALLE_BOVEDA['code'] != 200) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => $ASTO_DETALLE_BOVEDA['message'],
                            ], $ASTO_DETALLE_BOVEDA['code']);
                        }
                    }
                }
            }*/
            /*  FIN TRAITEMENT DECOUVERT */

            $SOLDE_AGENT = $this->todoRepository->SOLDE_AGENT($COD_USUARIO);
            if($SOLDE_AGENT['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $SOLDE_AGENT['message'],
                ], $SOLDE_AGENT['code']);
            }
            $SOLDE_AGENT_AVANT = $SOLDE_AGENT['solde'];
            if($SOLDE_AGENT_AVANT < $MONTANT){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le solde du caissier est insuffisant.",
                ], 403);
            }
            $SOLDE_CLIENT = $this->todoRepository->SOLDE_CLIENT($NUM_CUENTA);

            if($SOLDE_CLIENT['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $SOLDE_CLIENT['message'],
                ], $SOLDE_CLIENT['code']);
            }
            $SOLDE_CLIENT_AVANT = $SOLDE_CLIENT['solde'];
            if($CC_CUENTA_EFECTIVO->IND_SOBGRO == 'S' && $CC_CUENTA_EFECTIVO?->cc_autori_x_cuenta->IND_ESTADO == 'A') {
                $CC_AUTORI_X_CUENTA = $CC_CUENTA_EFECTIVO->cc_autori_x_cuenta;
                if ($CC_AUTORI_X_CUENTA->count() > 0) {
                    foreach ($CC_AUTORI_X_CUENTA as $DECOUVERT){
                        $MONTANT_DECOUVERT += ($DECOUVERT->MON_AUTORIZADO - $DECOUVERT->MON_UTILIZADO);
                    }
                }
            }
            $SOLDE_GLOBAL_CLIENT = $SOLDE_CLIENT_AVANT + $MONTANT_DECOUVERT;
            if($SOLDE_GLOBAL_CLIENT < $MONTANT){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Le solde du client est insuffisant.",
                ], 402);
            }


            $NUM_MOVIMI = $this->todoRepository->IncValSiguienteEmpresa();
            if($NUM_MOVIMI['code'] != 200) {
                return response()->json([
                   'status' => 'failed',
                    'message' => $NUM_MOVIMI['message'],
                ], $NUM_MOVIMI['code']);
            }
            $NUM_MOVIMIENTO = $NUM_MOVIMI['data']->CC_MOV_DIA;
            $CONS_ASTO = $NUM_MOVIMI['data']->CONS_ASTO;

            $NUM_MOVIMI_AGENCIA = $this->todoRepository->IncValSiguienteAgencia($this->COD_AGENCIA);
            if($NUM_MOVIMI_AGENCIA['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $NUM_MOVIMI_AGENCIA['message'],
                ], $NUM_MOVIMI_AGENCIA['code']);
            }
            $ENCABEZADO = $NUM_MOVIMI_AGENCIA['data']->ENCABEZADO;
            $DETALLE = $NUM_MOVIMI_AGENCIA['data']->DETALLE;
            $BOLETA = $NUM_MOVIMI_AGENCIA['data']->BOLETA;
            $NUM_SEC_DEP_CC = $NUM_MOVIMIENTO;

            $MAX_REFERENCIA = $this->todoRepository->MAX_REFERENCIA();
            if($MAX_REFERENCIA['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $MAX_REFERENCIA['message'],
                ], $MAX_REFERENCIA['code']);
            }
            $MAX_REFERENCIA = $MAX_REFERENCIA['data'];

            $UPDATE_SOLDE_AGENT = $this->todoRepository->UPDATE_SOLDE_AGENT($COD_USUARIO, $MONTANT, $DEPOT='D');

            if($UPDATE_SOLDE_AGENT['code'] != 0){
                return response()->json([
                    'status' => 'failed',
                    'message' => $UPDATE_SOLDE_AGENT['message'],
                ], $UPDATE_SOLDE_AGENT['code']);
            }

            //$SOLDE_AGENT_APRES = $UPDATE_SOLDE_AGENT['solde'];

            $CREDITER_SOLDE_CLIENTE = $this->todoRepository->UPDATE_SOLDE_CLIENTE($NUM_CUENTA, $MONTANT, $DEPOT='D', $FEC_MOVIMIENTO);
            if($CREDITER_SOLDE_CLIENTE['code'] != 200){
                return response()->json([
                   'status' => 'failed',
                    'message' => $CREDITER_SOLDE_CLIENTE['message'],
                ], $CREDITER_SOLDE_CLIENTE['code']);
            }
            $SOLDE_CLIENT_AVANT = $SOLDE_CLIENT_AVANT['solde'];
            $SOLDE_CLIENT_APRES = $CREDITER_SOLDE_CLIENTE['solde'];

            $TIP_TRANSACCION_MOVIMTO_DIARIO=44;
            $SUBTIP_TRANSAC_MOVIMTO_DIARIO=1;
            $DES_MOVIMIENTO_MOVIMTO_DIARIO="RETRAIT AU COMPTE";

            $MOVIMTO_DIARIO = $this->todoRepository->MOVIMTO_DIARIO($NUM_MOVIMIENTO, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $CC_CUENTA_EFECTIVO->COD_PRODUCTO, $MONTANT, $FEC_MOVIMIENTO, $COD_EMPRESA,  $TIP_TRANSACCION_MOVIMTO_DIARIO_RETRAIT, $SUBTIP_TRANSAC_MOVIMTO_DIARIO_RETRAIT, $COD_SISTEMA_MOVIMTO_DIARIO, $NUM_DOCUMENTO_MOVIMTO_DIARIO, $EST_MOVIMIENTO_MOVIMTO_DIARIO, $IND_APL_CARGO_MOVIMTO_DIARIO, $DES_MOVIMIENTO_MOVIMTO_DIARIO_RETRAIT, $SISTEMA_FUENTE_MOVIMTO_DIARIO, $NUM_MOV_FUENTE_MOVIMTO_DIARIO, $COD_AGENCIA, $COD_USUARIO, $DES_REFERENCIA);
            if($MOVIMTO_DIARIO['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $MOVIMTO_DIARIO['message'],
                ], $MOVIMTO_DIARIO['code']);
            }

            $IND_ESTADO_DEPOT_ENCA = 'A';
            $IND_DESGLOSE_DEPOT_ENCA = 'S';
            $TRAN_DIARIO_ENCA = $this->todoRepository->TRAN_DIARIO_ENCA($COD_EMPRESA, $COD_AGENCIA, $COD_USUARIO, $CC_CUENTA_EFECTIVO->COD_CLIENTE, $MONTANT, $MONTANT, $CC_CUENTA_EFECTIVO->NUM_CUENTA, $SOLDE_CLIENT_AVANT, $SOLDE_CLIENT_APRES, $ENCABEZADO, $CONS_ASTO, $BOLETA, $COD_MONEDA, $COD_SISTEMA, $TIP_TRANSACCION_RETRAIT, $SUB_TIP_TRANSAC, $FEC_MOVIMIENTO, $IND_ESTADO_RETRAIT_ENCA, $MONTANT_RETOURNE=0, $IND_DESGLOSE_RETRAIT_ENCA, $CC_CUENTA_EFECTIVO?->cf_producto->NOM_PRODUCTO, $MTO_COMISSION, $NUM_SEC_DEP_CC);
            if($TRAN_DIARIO_ENCA['code'] != 200){
                return response()->json([
                    'status' => 'failed',
                    'message' => $TRAN_DIARIO_ENCA['message'],
                ], $TRAN_DIARIO_ENCA['code']);
            }
            $COD_FORMA_PAGO_DEPOT_DETA = 1;
            $TIP_DOCUMENTO_DEPOT_DETA = 5;
            $TRAN_DIARIO_DETA = $this->todoRepository->TRAN_DIARIO_DETA($COD_EMPRESA, $COD_AGENCIA, $MONTANT, $ENCABEZADO, $DETALLE, $COD_MONEDA, $COD_FORMA_PAGO_RETRAIT_DETA, $TIP_DOCUMENTO_RETRAIT_DETA);

            if($TRAN_DIARIO_DETA['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $TRAN_DIARIO_DETA['message'],
                ], $TRAN_DIARIO_DETA['code']);
            }
            $DES_ASIENTO_DEPOT = "DEPÔT DE COMPTE EN ESPECES VIA APPLI WEB";
            $EST_ASIENTO_DEPOT_ASTO = 'P';
            $IND_LIQUIDACION_DEPOT_ASTO = 'N';
            $IND_POST_CIERRE_DEPOT_ASTO = 'N';
            $ASTO_RESUMEN = $this->todoRepository->ASTO_RESUMEN($CONS_ASTO, $COD_EMPRESA, $COD_AGENCIA, $TIP_TRANSACCION_RETRAIT, $SUB_TIP_TRANSAC, $COD_SISTEMA, $FEC_MOVIMIENTO, $DES_ASIENTO_RETRAIT, $EST_ASIENTO_RETRAIT_ASTO, $COD_USUARIO, $IND_LIQUIDACION_RETRAIT_ASTO, $IND_POST_CIERRE_RETRAIT_ASTO);

            if($ASTO_RESUMEN['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $ASTO_RESUMEN['message'],
                ], $ASTO_RESUMEN['code']);
            }

            $CUENTA_CONTABLE_PRODUCTO = $this->todoRepository->GetCuentaContableProducto($CC_CUENTA_EFECTIVO->COD_PRODUCTO);

            if($CUENTA_CONTABLE_PRODUCTO['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $CUENTA_CONTABLE_PRODUCTO['message'],
                ], $CUENTA_CONTABLE_PRODUCTO['code']);
            }
            $CUENTA_CONTABLE_CAJERO = $this->todoRepository->GetCuentaContableOperation($COD_USUARIO);

            if($CUENTA_CONTABLE_CAJERO['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $CUENTA_CONTABLE_CAJERO['message'],
                ], $CUENTA_CONTABLE_CAJERO['code']);
            }
            $DETAILE_ASTO_DEPOT_ASTO_CREDIT = "DEPÔT EN ESPECES";
            $DETAILE_ASTO_DEPOT_ASTO_DEBIT = "EFECTIVO DE CAJAS";
            $TIP_CAM_BASE_DEPOT = 1.00;
            $TIP_CAM_CTA_DEPOT = 1.00;
            $ASTO_DETALLE_PRODUCTO = $this->todoRepository->ASTO_DETALLE($CONS_ASTO, '1', $CUENTA_CONTABLE_PRODUCTO, '0.00', $MONTANT, $MAX_REFERENCIA, $FEC_MOVIMIENTO, $COD_EMPRESA, $COD_AGENCIA, $DETAILE_ASTO_DEPOT_ASTO_CREDIT, $TIP_CAM_BASE_RETRAIT, $TIP_CAM_CTA_RETRAIT);

            if($ASTO_DETALLE_PRODUCTO['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $ASTO_DETALLE_PRODUCTO['message'],
                ], $ASTO_DETALLE_PRODUCTO['code']);
            }

            $ASTO_DETALLE_CAJERO = $this->todoRepository->ASTO_DETALLE($CONS_ASTO, '2', $CUENTA_CONTABLE_CAJERO, $MONTANT, '0.00', $MAX_REFERENCIA, $FEC_MOVIMIENTO, $this->COD_EMPRESA, $this->COD_AGENCIA, $DETAILE_ASTO_DEPOT_ASTO_DEBIT, $TIP_CAM_BASE_RETRAIT, $TIP_CAM_CTA_RETRAIT);
            if($ASTO_DETALLE_CAJERO['code'] != 200) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $ASTO_DETALLE_CAJERO['message'],
                ], $ASTO_DETALLE_CAJERO['code']);
            }

            $CC_CUENTA_EFECTIVO->FEC_ULT_MOVIMIENTO = $FEC_MOVIMIENTO;
            $CC_CUENTA_EFECTIVO->update();
            DB::commit();
            if (env('APP_ENV') == 'local') {
                $tel = env('TEL_ADMIN');
            } else {
                $tel = "";
                $CL_CLIENTE = CL_CLIENTES::where('COD_CLIENTE', $CC_CUENTA_EFECTIVO->COD_CLIENTE)
                    ->where('COD_EMPRESA', $CC_CUENTA_EFECTIVO->COD_EMPRESA)->first();
                if($CL_CLIENTE){
                    $tel = $CL_CLIENTE->TEL_PRINCIPAL;
                }

            }
            if($tel != ""){
                $message = "Dépôt espèces, le " . date('Y-m-d H:i:s') . " compte " .  $CC_CUENTA_EFECTIVO->COD_CLIENTE . " du montant de " . Utils::getFormatMoney($MONTANT) . ". Nouveau solde: " . number_format($SOLDE_CLIENT_APRES, 0, ".", " ");
                //$this->todoRepository->sendSMS($tel, $message);
            }
            return response()->json([
                'status' => 'success',
                'message' => "Opération de dépot effectée avec succès!",
                'referenceTransaction' => $TRAN_DIARIO_ENCA->NUM_SECUENCIA_DOC,
            ]);
        }
        catch (\Exception $e){
            return [
                'code' => 500,
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }

    public function makeTransfer(Request $request)
    {
        $todo = Todo::find(0);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
    public function reservationDeFonds(Request $request)
    {
        $todo = Todo::find(0);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
    public function unReservationDeFonds(Request $request)
    {
        $todo = Todo::find(0);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
