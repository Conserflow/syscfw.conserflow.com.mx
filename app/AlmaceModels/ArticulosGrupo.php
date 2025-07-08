<?php

namespace App\AlmaceModels;

use Illuminate\Database\Eloquent\Model;

class ArticulosGrupo extends Model
{
    protected $fillable = [
        "grupo_id",
        "art_inv_id",
        "empleado_registra_id"
    ];
    protected $table = "alm_articulos_grupos";
}
