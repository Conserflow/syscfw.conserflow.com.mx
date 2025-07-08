<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class CotizacionEquipo extends Model
{
    protected $fillable = [
        "proveedor",
		"marca",
        "propuesta_id",
		"costo",
		"forma_pago",
        "empleado_registra_id",
        "created_at",
        "updated_at"
    ];
    protected $table = "ti_propuestas_cotizaciones";
    public $timestamps = true;
}