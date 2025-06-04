<?php

namespace App\Http\Controllers;

use App\Models\CC\CC_CUENTA_EFECTIVO;
use App\Models\CL\CL_CLIENTES;
use App\Models\CL\CL_PERSONAS_FISICAS;
use App\Models\CL\CL_PERSONAS_JURIDICAS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Routing\Controller;

class TodoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['customerCountService']]);
    }

    public function customerCountService()
    {
        $total = CL_CLIENTES::all()->count();
        return response()->json([
            'status' => 'success',
            'nombreDeClients' => $total,
        ]);
    }

    public function customerInfosService($codeCliente)
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

    public function show($id)
    {
        $todo = Todo::find($id);
        return response()->json([
            'status' => 'success',
            'todo' => $todo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $todo = Todo::find($id);
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
