<?php

namespace App\Models\CL;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CL_TIPOS_ID extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_TIPOS_ID';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_TIPO_ID'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_TIPO_ID',
        'DES_TIPO_ID',
        'MASCARA',
        'IND_LARGO_FIJO',
        'IND_PERSONA',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_TIPO_ID' => 'required|string|max:5',
        'DES_TIPO_ID' => 'required|string|max:60',
        'MASCARA' => 'required|string|max:45',
        'IND_LARGO_FIJO' => 'required|string|max:1',
        'IND_PERSONA' => 'required|string|max:1',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_TIPO_ID', $this->getAttribute('COD_TIPO_ID'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }

    public function cl_id_clientes() : HasMany{
        return $this->hasMany(CL_ID_CLIENTES::class, 'COD_TIPO_ID', 'COD_TIPO_ID');
    }


}
