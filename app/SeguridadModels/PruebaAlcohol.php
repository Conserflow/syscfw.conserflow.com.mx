<?php
namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class PruebaAlcohol extends Model
{
    protected $table = "seguridad_pruebas_alcohol";
    public $fillable = [
        "ubicacion",
        "fecha",
        "responsable_id",
        "observaciones",
        "empleado_registra_id"
    ];
    public $timestamps = true;
}