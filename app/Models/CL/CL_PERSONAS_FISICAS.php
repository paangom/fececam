<?php

namespace App\Models\CL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CL_PERSONAS_FISICAS extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_PERSONAS_FISICAS';
    public $incrementing = false;
    protected $primaryKey = 'COD_CLIENTE';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_CLIENTE',
        'COD_PROFESION',
        'COD_ACTIVIDAD',
        'COD_SECTOR',
        'PRIMER_NOMBRE',
        'SEGUNDO_NOMBRE',
        'PRIMER_APELLIDO',
        'SEGUNDO_APELLIDO',
        'EST_CIVIL',
        'IND_SEXO',
        'NOM_CONYUGUE',
        'NACIONALIDAD',
        'LUGAR_NACIMIENTO',
        'NUM_HIJOS',
        'TENENCIA_VIVIENDA',
        'ANTIGUEDAD_RESIDENCIA',
        'COD_CTE_CONYUGE',
        'TENENCIA_PUESTO',
        'ANTIGUEDAD_PUESTO',
        'APELLIDO_PADRE',
        'APELLIDO_MADRE',
        'NOMBRE_MADRE',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:5',
        'COD_PROFESION' => 'required|string|max:5',
        'COD_ACTIVIDAD' => 'required|string|max:5',
        'COD_SECTOR' => 'required|string|max:5',
        'PRIMER_NOMBRE' => 'required|string|max:20',
        'SEGUNDO_NOMBRE' => 'required|string|max:20',
        'PRIMER_APELLIDO' => 'required|string|max:20',
        'SEGUNDO_APELLIDO' => 'required|string|max:20',
        'EST_CIVIL' => 'required|string|max:1',
        'IND_SEXO' => 'required|string|max:1',
        'NOM_CONYUGUE' => 'required|string|max:60',
        'NACIONALIDAD' => 'required|string|max:30',
        'LUGAR_NACIMIENTO' => 'required|string|max:30',
        'NUM_HIJOS' => 'required|string|max:2',
        'APELLIDO_PADRE' => 'nullable|string|max:80',
        'APELLIDO_MADRE' => 'nullable|string|max:80',
        'NOMBRE_MADRE' => 'nullable|string|max:60',
        'TENENCIA_VIVIENDA' => 'required|string|max:2',
        'ANTIGUEDAD_RESIDENCIA' => 'required|float|digits:3,1',
        'COD_CTE_CONYUGE' => 'required|string|max:15',
        'TENENCIA_PUESTO' => 'required|string|max:2',
        'ANTIGUEDAD_PUESTO' => 'required|float|digits:3,1',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }

    public function cl_sector_economico() : BelongsTo{
        return $this->belongsTo(CL_SECTOR_ECONOMICO::class, 'COD_SECTOR', 'COD_SECTOR');
    }
}
