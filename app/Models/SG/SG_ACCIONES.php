<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_ACCIONES extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_ACCIONES';
    protected $primaryKey = 'COD_ACCION';
    public $incrementing = false;
    protected $keyType = "string" ;

    protected $fillable = [
        'COD_ACCION',
        'DES_ACCION'
    ];
    public static $rules = [
        'COD_ACCION'   => 'required|string|max:5|unique:sqlsrv.SG.SG_ACCIONES,COD_ACCION',
        'DES_ACCION'   => 'required|string|max:60|unique:sqlsrv.SG.SG_ACCIONES,DES_ACCION'
    ];
}
