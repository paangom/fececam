<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CF_PARAMETROS_GENERALES extends Model
{
    use HasFactory;
    protected $table = 'CF.CF_PARAMETROS_GENERALES';
    public $incrementing = false;
    protected $primaryKey = 'COD_PARAMETRO';
    //protected $primaryKey = ['COD_SISTEMA', 'COD_PARAMETRO'];

    protected $fillable = [
        'COD_SISTEMA',
        'COD_PARAMETRO',
        'DES_PARAMETRO',
        'VAL_PARAMETRO',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_PARAMETRO', $this->getAttribute('COD_PARAMETRO'));
    }

}
