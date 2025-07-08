<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class InspeccionEppParticipante extends Model
{
    protected $table = "seguridad_inspeccion_epp_participantes";
    public $fillable = [
        "empleado_id",
        "sip_id",
        "epp_overol",
        "epp_calzado",
        "epp_casco",
        "epp_guantes",
        "epp_ocular",
        "epp_respiratoria",
        "epp_auditiva",
        "epp_barbiquejo",
        "epa_respiratoria",
        "epa_arnes",
        "epa_careta",
        "epa_mangas",
        "epa_mascarilla",
        "empleado_registra_id"
    ];

    public $timestamps = true;

}
