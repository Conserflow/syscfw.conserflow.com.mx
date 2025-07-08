<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class PrestamoUnidad extends Model
{
    protected $fillable = [
        "unidad_id",
        "fecha_prestamo",
        "fecha_devolucion",
        "empleado_registra_id",
        "motivo_prestamo",
        "tipo",
        "motivo_devolucion"
    ];
    protected $table = "unidades_prestamos";
    public $timestamps = true;
}
