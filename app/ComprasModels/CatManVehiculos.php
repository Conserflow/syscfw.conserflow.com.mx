<?php

namespace App\ComprasModels;

use Illuminate\Database\Eloquent\Model;

class CatManVehiculos extends Model
{
    protected $fillable =
    [
        "descripcion",
        "codigo",
        "marca",
        "comentario",
        "centro_costo_id",
        "empleado_registra_id"
    ];
    protected $table = "cat_mantenimiento_vehiculos";
    public $timestamps = true;
}
