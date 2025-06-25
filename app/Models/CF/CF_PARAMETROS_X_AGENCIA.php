<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CF_PARAMETROS_X_AGENCIA extends Model
{
    use HasFactory;
    protected $table = 'CF.CF_PARAMETROS_X_AGENCIA';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_AGENCIA', 'COD_SISTEMA', 'COD_PARAMETRO'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_AGENCIA',
        'COD_SISTEMA',
        'COD_PARAMETRO',
        'DES_PARAMETRO',
        'VAL_PARAMETRO',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_AGENCIA' => 'required|string|max:5',
        'COD_SISTEMA' => 'required|string|max:2',
        'COD_PARAMETRO' => 'required|string|max:15',
        'DES_PARAMETRO' => 'required|string|max:60',
        'VAL_PARAMETRO' => 'required|string|max:100',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_AGENCIA', $this->getAttribute('COD_AGENCIA'))
            ->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_PARAMETRO', $this->getAttribute('COD_PARAMETRO'));
    }

    public static function checkCaisseOpened($codAgencia, $codEmpresa)
    {
        $parametro = self::where('COD_AGENCIA', $codAgencia)
            ->where('COD_PARAMETRO', 'CIERRE_GENERAL')
            ->where('COD_EMPRESA', $codEmpresa)
            ->where('VAL_PARAMETRO', 'N')
            ->first();

        // Vérifier si le résultat est trouvé
        if (!$parametro) {
            throw new ModelNotFoundException('Aucune caisse ouverte.');
        }

        // Retourner vrai si la valeur du paramètre est "N"
        return $parametro;
    }
}
