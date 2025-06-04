<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PA_CANTONES extends Model
{
    protected $table = 'PA.PA_CANTONES';
    protected $primaryKey = 'COD_CANTON';
    protected $keyType = 'string';

    protected $fillable = ["COD_PAIS", "COD_PROVINCIA", "COD_CANTON", "DES_CANTON"];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_PAIS', $this->getAttribute('COD_PAIS'))
            ->where('COD_CANTON', $this->getAttribute('COD_CANTON'))
            ->where('COD_PROVINCIA', $this->getAttribute('COD_PROVINCIA'));
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(PA_PAISES::class, "COD_PAIS", "COD_PAIS");
    }


    public function provincia(): BelongsTo
    {
        return $this->belongsTo(PA_PROVINCIAS::class, "COD_PROVINCIA", "COD_PROVINCIA");
    }


    public function distritos(): HasMany
    {
        return $this->hasMany(PA_DISTRITOS::class, "COD_CANTON");
    }
}
