<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PA_DISTRITOS extends Model
{
    protected $table = 'PA.PA_DISTRITOS';
    protected $primaryKey = 'COD_DISTRITO';
    protected $keyType = 'string';

    protected $fillable = ["COD_PAIS", "COD_PROVINCIA", "COD_CANTON", "COD_DISTRITO", "DES_DISTRITO"] ;


    public function pais(): BelongsTo
    {
        return $this->belongsTo(PA_PAISES::class, "COD_PAIS", "COD_PAIS");
    }


    public function provincia(): BelongsTo
    {
        return $this->belongsTo(PA_PROVINCIAS::class, "COD_PROVINCIA", "COD_PROVINCIA");
    }


    public function canton(): BelongsTo
    {
        return $this->belongsTo(PA_CANTONES::class, "COD_CANTON", "COD_CANTON");
    }
}
