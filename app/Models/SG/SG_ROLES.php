<?php

namespace App\Models\SG;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_ROLES extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_ROLES';
    protected $primaryKey = 'COD_ROL';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $with = ['cf_empresa', 'cf_agencia'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_ROL',
        'DES_ROL',
    ];

    public static $rules = [
        'COD_ROL'   => 'required|string|max:5|unique:sqlsrv.SG.SG_ROLES,COD_ROL',
        'COD_EMPRESA'   => 'required|string|max:5',
        'DES_ROL'   => 'required|string|max:60'
    ];

}
