<?php

namespace App\EnfermeriaModels;

use Illuminate\Database\Eloquent\Model;

class MotivoAtencion extends Model
{
  protected $fillable = [
    "nombre",
    "tipo"
  ];
  protected $table = "enfermeria_motivo_atencion";
}
