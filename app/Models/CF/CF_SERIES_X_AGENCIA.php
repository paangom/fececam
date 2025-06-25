<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CF_SERIES_X_AGENCIA extends Model
{
    use HasFactory;
    protected $table = 'CF.CF_SERIES_X_AGENCIA';

    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_AGENCIA', 'COD_SISTEMA', 'COD_SERIE'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_AGENCIA',
        'COD_SISTEMA',
        'COD_SERIE',
        'DES_SERIE',
        'VAL_SIGUIENTE',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_AGENCIA' => 'required|string|max:5',
        'COD_SISTEMA' => 'required|string|max:2',
        'COD_SERIE' => 'required|string|max:10',
        'DES_SERIE' => 'required|string|max:60',
        'VAL_SIGUIENTE' => 'required|digits:8',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_AGENCIA', $this->getAttribute('COD_AGENCIA'))
            ->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_SERIE', $this->getAttribute('COD_SERIE'));
    }

}
