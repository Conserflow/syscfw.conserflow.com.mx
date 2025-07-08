<?php

namespace App\AlmaceModels;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        "fecha",
        "articulo_id",
        "proyecto_id",
        "existecia_sistema",
        "existencia_real",
        "observaciones",
        "empleado_id" // Empleado que registra
    ];
    protected $table = "alm_inventario";
}
