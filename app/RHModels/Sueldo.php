<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Sueldo extends Model
{
  protected $fillable = [
    "sueldo_diario_integral", //SDI
    "sueldo_diario_neto", // Diario
    "contrato_id", // Contrato
    "sueldo_mensual", // Del alta
    "infonavit", //0
    "viaticos_mensuales", //0
    "sueldo_diario_real", //0
  ];
  protected $table = "sueldos";
}
