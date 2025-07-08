<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class InspeccionBotiquin extends Model
{
    protected $table = "seguridad_inspeccion_botiquin";
    public $fillable = [
        "area",
        "numero",
        "fecha",
        "inspector_id",
        "responsable_id",
        "tipo",
        "visible",
        "buen_estado",
        "recomendaciones",
    ];
    public $timestamps = true;
}
