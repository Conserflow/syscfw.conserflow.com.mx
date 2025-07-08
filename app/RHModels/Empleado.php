<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    protected $fillable = [
        "nombre",
        "ap_paterno",
        "ap_materno",
        "sexo",
        "lug_nac",
        "fech_nac",
        "ine",
        "curp",
        "rfc",
        "nss_imss",
        "fech_alta_imss",
        "fech_ing",
        "tipo_sangre",
        "talla_overol",
        "talla_botas",
        "amortizacion",
        "numero_credito",
        "edo_civil_id",
        "ubicacion_id",
        "puesto_id",
        "id_checador",
        "fiscal_cp",
        "fiscal_estado",
        "talla_camisa",
        "salario_neto" // Semanal/Quincenal
    ];
    protected $table = "empleados";
}
