<?php

namespace App\Models\CL;

use App\Models\PA\PA_CANTONES;
use App\Models\PA\PA_DISTRITOS;
use App\Models\PA\PA_PAISES;
use App\Models\PA\PA_PROVINCIAS;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CL_DIR_CLIENTES extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_DIR_CLIENTES';
    public $incrementing = false;
    protected $primaryKey = 'COD_DIRECCION';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_DIRECCION',
        'COD_CLIENTE',
        'COD_PAIS',
        'COD_PROVINCIA',
        'COD_CANTON',
        'COD_DISTRITO',
        'TIP_DIRECCION',
        'APDO_POSTAL',
        'COD_POSTAL',
        'DET_DIRECCION',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:15',
        'COD_DIRECCION' => 'required|string|max:2',
        'NUM_ID' => 'required|string|max:30',
        'COD_PAIS' => 'required|string|max:5',
        'COD_PROVINCIA' => 'required|string|max:5',
        'COD_CANTON' => 'required|string|max:5',
        'COD_DISTRITO' => 'required|string|max:5',
        'TIP_DIRECCION' => 'required|string|max:10',
        'APDO_POSTAL' => 'nullable|string|max:10',
        'COD_POSTAL' => 'nullable|string|max:10',
        'DET_DIRECCION' => 'required|string|max:200',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_DIRECCION', $this->getAttribute('COD_DIRECCION'))->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'));
    }


    public function PA_PAIS(){
        return $this->belongsTo(PA_PAISES::class, 'COD_PAIS', 'COD_PAIS');
    }

    public function PA_PROVINCIA(){
        return $this->belongsTo(PA_PROVINCIAS::class, 'COD_PROVINCIA', 'COD_PROVINCIA');
    }

    public function PA_CANTON(){
        return $this->belongsTo(PA_CANTONES::class, 'COD_CANTON', 'COD_CANTON');
    }

    public function PA_DISTRITO(){
        return $this->belongsTo(PA_DISTRITOS::class, 'COD_DISTRITO', 'COD_DISTRITO');
    }
}
