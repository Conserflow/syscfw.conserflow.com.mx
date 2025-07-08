<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    protected $fillable = [
        "unidad",
        "marca",
        "modelo",
        "anio",
        "placas",
        "estado",
        "comentarios",
        "tipo",
        "factura",
        "clase_tipo",
        "color",
        "no_motor",
        "capacidad",
        "cilindros",
        "combustible",
        "numero_tarjeta_circulacion",
        "tarjeta",
        "excento",
        "primer_semestre",
        "segundo_semestre",
        "numero_serie",
        "empresa"
    ];
    protected $table = "unidades";
}
