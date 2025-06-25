<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\CF\{CF_IDIOMAS, CF_EMPRESAS, CF_AGENCIAS, CF_CATAL_TRANSACCIONES, CF_SISTEMAS, CF_TRANSAC_X_EMPRESA};

class SG_PERMISSIONS extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_PERMISSIONS';
    protected $primaryKey = 'COD_PERM' ;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $with = [];
    //protected $with = ['sg_roles', 'sg_usuarios'];


    protected $fillable = [
        'COD_PERM',
        'DES_PERMISSION',
        'CONTROLEUR',
        'DES_CONTROLEUR',
        'METHOD',
        'COD_EMPRESA',
        'COD_SISTEMA',
        "MODULE"
    ];


}
