<?php

namespace App\Models\SG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SG_PUESTOS extends Model
{
    use HasFactory;
    protected $table = 'SG.SG_PUESTOS';
    protected $primaryKey = 'COD_PUESTO';
    public $incrementing = false;
    protected $keyType = 'string';
}
