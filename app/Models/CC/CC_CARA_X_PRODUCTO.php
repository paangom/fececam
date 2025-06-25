<?php

namespace App\Models\CC;

use App\Models\CF\CF_PRODUCTOS;
use Illuminate\Database\Eloquent\Model;

class CC_CARA_X_PRODUCTO extends Model
{
    protected $table = 'CC.CC_CARA_X_PRODUCTO';
    protected $primaryKey = ['COD_EMPRESA', 'COD_SISTEMA', 'COD_PRODUCTO'];
    public $timestamps = false;
    public $incrementing = false;


    public function cf_producto(){
        return $this->belongsTo(CF_PRODUCTOS::class, 'COD_PRODUCTO', 'COD_PRODUCTO');
    }
}
