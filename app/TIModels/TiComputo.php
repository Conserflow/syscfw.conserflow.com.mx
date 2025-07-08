<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class TiComputo extends Model
{
    protected $fillable = [
        "no_serie",
        "marca_modelo",
        "cpu",
        "ram",
        "almacenamiento",
        "tarjeta_video",
        "tarjeta_red",
        "observaciones",
        "mac",
        "codigo_barras",
        "cantidad",
        "so",
        "empresa",
        "color",
    ];
    protected $table = 'ti_computo';
    public $timestamps = false;
}
