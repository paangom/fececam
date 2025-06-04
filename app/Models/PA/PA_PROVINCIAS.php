<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PA_PROVINCIAS extends Model
{
    protected $table = 'PA.PA_PROVINCIAS';
    protected $primaryKey = 'COD_PROVINCIA';
    protected $keyType = "string" ;
    protected $fillable = ["COD_PAIS", "COD_PROVINCIA", "DES_PROVINCIA"];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_PAIS', $this->getAttribute('COD_PAIS'))
            ->where('COD_PROVINCIA', $this->getAttribute('COD_PROVINCIA'));
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(PA_PAISES::class, "COD_PAIS", "COD_PAIS");
    }


    public function cantones(): HasMany
    {
        return $this->hasMany(PA_CANTONES::class, "COD_PROVINCIA");
    }
}
