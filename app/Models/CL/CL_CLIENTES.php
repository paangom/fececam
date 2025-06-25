<?php

namespace App\Models\CL;

use App\Models\CC\CC_CUENTA_EFECTIVO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CL_CLIENTES extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_CLIENTES';
    public $incrementing = false;
    protected $primaryKey = 'COD_CLIENTE';

    protected $fillable = [
        'COD_EMPRESA',
        'CAT_CLIENTE',
        'COD_CLIENTE',
        'NOM_CLIENTE',
        'IND_PERSONA',
        'FEC_INGRESO',
        'TEL_PRINCIPAL',
        'TEL_SECUNDARIO',
        'TEL_OTRO',
        'IND_RELACION',
        'FEC_REACTIVACION',
        'COD_AGENCIA',
        'CODCTE_ASO_COM',
        'CODCTE_GRP_SOL',
        'PROV_SERV_DESTINO',
        'CORREO_ELECTRONICO',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'CAT_CLIENTE' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:15',
        'NOM_CLIENTE' => 'required|string|max:80',
        'IND_PERSONA' => 'required|string|max:1',
        'FEC_INGRESO' => 'nullable|string',
        'TEL_PRINCIPAL' => 'nullable|string|max:15',
        'TEL_SECUNDARIO' => 'nullable|string|max:15',
        'TEL_OTRO' => 'nullable|string|max:15',
        'IND_RELACION' => 'nullable|string|max:1',
        'FEC_REACTIVACION' => 'nullable|string',
        'COD_AGENCIA' => 'nullable|string|max:5',
        'CODCTE_ASO_COM' => 'nullable|string|max:15',
        'CODCTE_GRP_SOL' => 'nullable|string|max:15',
        'PROV_SERV_DESTINO' => 'nullable|string|max:15',
        'CORREO_ELECTRONICO' => 'nullable|string',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'));
    }

    public function clDirClientes()
    {
        return $this->belongsTo(CL_DIR_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }

    public function clIDsCliente(){
        return $this->hasMany(CL_ID_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }

    public function clDatosAssociado()
    {
        return $this->belongsTo(CL_DATOS_ASOCIADO::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }

    public function ccCuentasEfectivos(){
        return $this->hasMany(CC_CUENTA_EFECTIVO::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }
}
