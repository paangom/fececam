<?php

namespace App\Models\CL;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CL_CLASES_SOCIEDAD extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_CLASES_SOCIEDAD';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'CLASE_SOCIEDAD'];

    protected $fillable = [
        'COD_EMPRESA',
        'CLASE_SOCIEDAD',
        'DES_SOCIEDAD'
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'CLASE_SOCIEDAD' => 'required|string|max:5',
        'DES_SOCIEDAD' => 'required|string|max:60',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('CLASE_SOCIEDAD', $this->getAttribute('CLASE_SOCIEDAD'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }

}
