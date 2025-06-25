<?php

namespace App\Models\CF;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CF_CALENDARIOS extends Model
{
    use HasFactory;
    protected $table = 'CF.CF_CALENDARIOS';
    protected $primaryKey = ['COD_EMPRESA', 'COD_SISTEMA', 'COD_AGENCIA'];
    public $incrementing = false;

    protected $fillable = [
        'COD_EMPRESA',
        'COD_SISTEMA',
        'COD_AGENCIA',
        'FEC_HOY',
        'NOM_DIA',
        'FEC_ANTERIOR',
        'PRIMER_DIA_MES',
        'PRIMER_HABIL_MES',
        'ULT_DIA_MES',
        'ULT_HABIL_MES',
    ];

    public static $rules = [
        'COD_EMPRESA'   => 'required|string|max:5',
        'COD_SISTEMA'   => 'required|string|max:2',
        'COD_AGENCIA'   => 'required|string|max:5',
        'FEC_HOY'   => 'required|date',
        'NOM_DIA'   => 'required|string|max:20',
        'FEC_ANTERIOR'   => 'nullable|date',
        'PRIMER_DIA_MES'   => 'nullable|date',
        'PRIMER_HABIL_MES'   => 'nullable|date',
        'ULT_DIA_MES'   => 'nullable|date',
        'ULT_HABIL_MES'   => 'nullable|date',
    ];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'))
            ->where('COD_AGENCIA', $this->getAttribute('COD_AGENCIA'))
            ->where('COD_SISTEMA', $this->getAttribute('COD_SISTEMA'));
    }

    public static function getDateBy($COD_SISTEMA, $COD_AGENCIA)
    {
        $matches = [
            "COD_AGENCIA" => $COD_AGENCIA,
            "COD_SISTEMA" => $COD_SISTEMA
        ];
        $cf_calendarios = self::where($matches)->first();
        $date = strtotime($cf_calendarios->FEC_HOY);
        $DATE = date('Y-m-d', $date);

        return $DATE;
    }
}
