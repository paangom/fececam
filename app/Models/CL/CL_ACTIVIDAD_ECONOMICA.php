<?php

namespace App\Models\CL;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CL_ACTIVIDAD_ECONOMICA extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_ACTIVIDAD_ECONOMICA';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_ACTIVIDAD'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_ACTIVIDAD',
        'DES_ACTIVIDAD'
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_ACTIVIDAD' => 'required|string|max:5',
        'DES_ACTIVIDAD' => 'required|string|max:60',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_ACTIVIDAD', $this->getAttribute('COD_ACTIVIDAD'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }


}
