<?php

namespace App\Models\CL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CL_ID_CLIENTES extends Model
{
    use HasFactory;
    protected $table = 'CL.CL_ID_CLIENTES';
    public $incrementing = false;
    protected $primaryKey = ['COD_TIPO_ID', 'COD_CLIENTE'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_TIPO_ID',
        'COD_CLIENTE',
        'NUM_ID',
        'FEC_VENCIM',
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_CLIENTE' => 'required|string|max:15',
        'COD_TIPO_ID' => 'required|string|max:5',
        'NUM_ID' => 'required|string|max:30',
        'FEC_VENCIM' => 'required|string',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_TIPO_ID', $this->getAttribute('COD_TIPO_ID'))->where('COD_CLIENTE', $this->getAttribute('COD_CLIENTE'));
    }

    public function cl_tipos_id(){
        return $this->belongsTo(CL_TIPOS_ID::class, 'COD_TIPO_ID', 'COD_TIPO_ID');
    }

    public function cliente(){
        return $this->belongsTo(CL_CLIENTES::class, 'COD_CLIENTE', 'COD_CLIENTE');
    }
}
