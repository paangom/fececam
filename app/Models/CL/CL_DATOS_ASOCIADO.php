<?php

namespace App\Models\CL;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CL_DATOS_ASOCIADO extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_DATOS_ASOCIADO';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_CLIENTE'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_CLIENTE',
        'IND_ESTADO',
        'FECH_INGRESO',
        'FECH_INACTIVACION',
        'FECH_RENUNCIA',
        'COD_MOT_RENUNCIA',
        'COD_PLANILLA',
        'TIP_ASOCIADO',
        'LUGAR_TRABAJO',
        'SALARIO',
        'CANT_DEPENDIENTES',
        'NOM_BENEFICIARIO',
        'RELAC_BENEFICIARIO',
        'FECH_NACIMIENTO',
        'NUM_SESION',
        'NUM_ARTICULO',
        'TIPO_UNION',
        'TIPO_PLANILLA',
        'PUESTO',
        'IND_EMPLEADO',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:15',
        'IND_ESTADO' => 'required|string|max:1',
        'FECH_INGRESO' => 'nullable|string',
        'FECH_INACTIVACION' => 'nullable|string',
        'FECH_RENUNCIA' => 'nullable|string',
        'COD_MOT_RENUNCIA' => 'nullable|string|max:5',
        'COD_PLANILLA' => 'nullable|string|max:5',
        'TIP_ASOCIADO' => 'nullable|string|max:1',
        'LUGAR_TRABAJO' => 'nullable|string|max:80',
        'TIP_TRABAJO' => 'nullable|string|max:40',
        'SALARIO' => 'nullable|string',
        'CANT_DEPENDIENTES' => 'nullable|string',
        'DIR_TRABAJO' => 'nullable|string|max:240',
        'NOM_BENEFICIARIO' => 'nullable|string|max:40',
        'RELAC_BENEFICIARIO' => 'nullable|string|max:20',
        'FECH_NACIMIENTO' => 'nullable|string',
        'NUM_SESION' => 'nullable|string',
        'NUM_ARTICULO' => 'nullable|string',
        'TIPO_UNION' => 'nullable|string|max:1',
        'TIPO_PLANILLA' => 'nullable|string|max:1',
        'PUESTO' => 'nullable|string|max:40',
        'IND_EMPLEADO' => 'nullable|string|max:1',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }

    public function cf_cliente() : BelongsTo{
        return $this->belongsTo(CL_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }


}
