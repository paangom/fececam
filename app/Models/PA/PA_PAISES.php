<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Model;
use App\Models\CL\CL_PERSONAS_FISICAS;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PA_PAISES extends Model
{
    protected $table = 'PA.PA_PAISES';
    protected $primaryKey = 'COD_PAIS';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = ['COD_PAIS', 'ABR_PAIS', 'NOM_PAIS', 'NACIONALIDAD'];


    public function provincias(): HasMany
    {
        return $this->hasMany(PA_PROVINCIAS::class, "COD_PAIS");
    }

    public function cl_personas_fisica(): HasMany
    {
        return $this->hasMany(CL_PERSONAS_FISICAS::class, "NACIONALIDAD", "COD_PAIS");
    }
}
