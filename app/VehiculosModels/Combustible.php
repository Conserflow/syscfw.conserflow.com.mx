<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class Combustible extends Model
{

    protected $table = 'combustible';
    protected $fillable = [
        "cantidad_bidones",
        "proveedor_id",
        "ubicacion",
        "folio",
        "fecha",
        "proyecto_id",
        "operador_id",
        "factura",
        "unidad_id",
        "horas",
        "producto_id",
        "kilometraje",
        "cantidad",
        "precio",
        "subtotal",
        "iva",
        "total",
        "tipo_deposito",
        "condicion",
        "updated_at",
        "created_at",
        "empleado_registra_id"

    ];
}
