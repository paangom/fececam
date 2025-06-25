<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_ROLES_X_PERMISSION extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_ROLES_X_PERMISSION';


    protected $fillable = [
        'COD_EMPRESA',
        'COD_ROL',
        'COD_PERM',
    ];

}
