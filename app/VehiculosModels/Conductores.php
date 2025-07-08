<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class Conductores extends Model
{
    protected $table = "choferes";
    protected $fillable = [
        "empleado_id",
        "licencia",
        "tipo",
        "vigencia",
        "empleado_registra_id",
        "estado",
        "condicion",
    ];
    public $timestamps = true;
}
