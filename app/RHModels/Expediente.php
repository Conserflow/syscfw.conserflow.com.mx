<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $fillable =
    [
        "solicitud",
        "evaluacion_psicolaboral",
        "evaluacion_hab_tecnicas",
        "foto",
        "acta_nacimiento",
        "credencial_elector",
        "curp",
        "rfc",
        "nss_imss",
        "comprobante_domicilio",
        "cartilla_servicio_militar",
        "licencia_manejo",
        "acta_matrimonio",
        "carta_infonavit",
        "certificado_medico",
        "carta_no_enfermedades",
        "vales_resguardo",
        "comprobante_estudios",
        "carta_recomendacion",
        "retencion_credito_infonavit",
        "folio",
        "formato_expediente",
        "empleado_id"
    ];
    public $timestamps = true;
}
