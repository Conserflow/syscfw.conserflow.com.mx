<?php

namespace App\CalidadModels;

use Illuminate\Database\Eloquent\Model;

class EquiposCatalogo extends Model
{
    protected $table = 'equipos_catalogo';
    public $timestamps = true;
    protected $fillable = [
        "descripcion",
        "marca",
        "modelo",
        "tipo",
        "fecha_servicio",
        "proxima_fecha",
        "rango_medicion",
        "numero_serie",
        "frecuencia",
        "resguardo",
        "observaciones"
    ];
}
