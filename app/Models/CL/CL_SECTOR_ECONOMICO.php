<?php

namespace App\Models\CL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CL_SECTOR_ECONOMICO extends Model
{
   use HasFactory;
    protected $table = 'CL.CL_SECTOR_ECONOMICO';
    public $incrementing = false;
    protected $primaryKey = ['COD_EMPRESA', 'COD_SECTOR'];

    protected $fillable = [
        'COD_EMPRESA',
        'COD_SECTOR',
        'DES_SECTOR'
    ];

    public static $rules = [
        'COD_EMPRESA' => 'required|string|max:5',
        'COD_SECTOR' => 'required|string|max:5',
        'DES_SECTOR' => 'required|string|max:60',
    ];


    protected function setKeysForSaveQuery($query)
    {
        return $query->where('COD_SECTOR', $this->getAttribute('COD_SECTOR'))
            ->where('COD_EMPRESA', $this->getAttribute('COD_EMPRESA'));
    }


}
