<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class ParticipantePlatica extends Model
{
    protected $table = "seguridad_platicas_participantes";
    public $fillable = [
        "platica_id",
        "empleado_id",
        "empleado_registra_id"
    ];
    public $timestamps = true;
}
