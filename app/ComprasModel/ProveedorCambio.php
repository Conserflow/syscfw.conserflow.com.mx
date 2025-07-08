<?php

namespace App\ComprasModel;

use Illuminate\Database\Eloquent\Model;

class ProveedorCambio extends Model
{
    protected $fillable = [
        "proveedor_id",
        "tipo_movimiento",
        "fecha",
        "modificacion",
        "anexos",
        "empleado_registro_id",
        "campos_cambios"
    ];
    protected $table = "compras_proveedores_cambios";
    public $timestamps = true;
}
