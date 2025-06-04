<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class CF_PRODUCTOS extends Model
{
    use HasFactory;
    protected $primaryKey = 'COD_PRODUCTO';
    protected $table = 'CF.CF_PRODUCTOS';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'COD_EMPRESA',
        'COD_SISTEMA',
        'COD_PRODUCTO',
        'COD_MONEDA',
        'NOM_PRODUCTO',
        'DES_PRODUCTO',
        'REQUISITOS',
        'COD_COLUMNA_REPORTE',
    ];

    public static $rules = [
        'COD_EMPRESA'   => 'required|string|max:5',
        'COD_PRODUCTO'   => 'required|string|max:5',
        'COD_SISTEMA'   => 'required|string|max:5',
        'COD_MONEDA'   => 'nullable|string|max:5',
        'NOM_PRODUCTO'   => 'nullable|string|max:100',
        'DES_PRODUCTO'   => 'nullable|string|max:255',
        'REQUISITOS'   => 'nullable|string|max:255',
        'COD_COLUMNA_REPORTE'   => 'nullable|string|max:5',
    ];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'))
            ->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_PRODUCTO', $this->getAttribute('COD_PRODUCTO'));
    }

}
