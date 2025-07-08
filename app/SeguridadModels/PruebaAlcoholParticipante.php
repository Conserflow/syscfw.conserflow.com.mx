<?php
namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class PruebaAlcoholParticipante extends Model
{
    protected $table = "seguridad_pruebaalcohol_participantes";
    public $fillable = [
        "prueba_id",
        "empleado_id",
        "resultado",
        "empleado_registra_id"
    ];
    public $timestamps = true;
}