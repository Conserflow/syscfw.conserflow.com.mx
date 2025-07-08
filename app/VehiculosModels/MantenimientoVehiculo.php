<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class MantenimientoVehiculo extends Model
{
    //
    protected $table = 'mantenimiento_vehiculos';
    protected $fillable = [
        "unidad_id",
        "tipo",
        "descripcion",
        "solicita",
        "recibe",
        "fecha_inicio",
        "fecha_salida",
        "proveedor",
        "detalle",
        "materiales",
        "quimicos",
        "entrega",
        "recibe_empleado",
        "empresa",
    ];
}
