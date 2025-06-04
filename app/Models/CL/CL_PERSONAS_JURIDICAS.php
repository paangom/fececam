<?php

namespace App\Models\CL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CL_PERSONAS_JURIDICAS extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_PERSONAS_JURIDICAS';
    public $incrementing = false;
    protected $primaryKey = 'COD_CLIENTE';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_CLIENTE',
        'COD_SECTOR',
        'CLASE_SOCIEDAD',
        'COD_ACTIVIDAD',
        'NOM_COMERCIAL',
        'RAZON_SOCIAL',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:15',
        'COD_SECTOR' => 'required|string|max:5',
        'CLASE_SOCIEDAD' => 'required|string|max:5',
        'COD_ACTIVIDAD' => 'required|string|max:5',
        'NOM_COMERCIAL' => 'required|string|max:60',
        'RAZON_SOCIAL' => 'required|string|max:120',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'));
    }
    public function cl_sector_economico() : BelongsTo{
        return $this->belongsTo(CL_SECTOR_ECONOMICO::class, 'COD_SECTOR', 'COD_SECTOR');
    }

    public function cl_cliente() : BelongsTo{
        return $this->belongsTo(CL_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }

    public function cl_actividado_economica() : BelongsTo{
        return $this->belongsTo(CL_ACTIVIDAD_ECONOMICA::class, 'COD_ACTIVIDAD', 'COD_ACTIVIDAD');
    }

    public function cl_clase_sociedad() : BelongsTo{
        return $this->belongsTo(CL_CLASES_SOCIEDAD::class, 'CLASE_SOCIEDAD', 'CLASE_SOCIEDAD');
    }

}
