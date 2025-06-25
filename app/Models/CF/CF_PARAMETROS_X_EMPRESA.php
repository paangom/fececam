<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CF_PARAMETROS_X_EMPRESA extends Model
{
    use HasFactory;
    protected $table = 'CF.CF_PARAMETROS_X_EMPRESA';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_SISTEMA', 'COD_PARAMETRO'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_SISTEMA',
        'COD_PARAMETRO',
        'DES_PARAMETRO',
        'VAL_PARAMETRO',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_SISTEMA' => 'required|string|max:2',
        'COD_PARAMETRO' => 'required|string|max:15',
        'DES_PARAMETRO' => 'required|string|max:60',
        'VAL_PARAMETRO' => 'required|string|max:100',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_PARAMETRO', $this->getAttribute('COD_PARAMETRO'));
    }

}
