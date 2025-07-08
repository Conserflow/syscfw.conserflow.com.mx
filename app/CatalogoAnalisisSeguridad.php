<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogoAnalisisSeguridad extends Model
{
    //
    protected $fillable = ['secuencia','riesgo','recomendacion',"condicion"];
    protected $table = 'catalogo_analisis_seguridad';
    public $timestamps = false;
}
