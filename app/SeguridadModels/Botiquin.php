<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class Botiquin extends Model
{
    protected $table = "seguridad_inspeccion_botiquin_botiquin";
    public $fillable = [
        "sib_id",
        "material",
        "existencia",
        "reposicion",
        "fecha_vencimiento",
        "observacion",
        "apoyo",
    ];
    public $timestamps = true;
}