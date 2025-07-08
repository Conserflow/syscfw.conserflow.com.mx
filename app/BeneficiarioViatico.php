<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiarioViatico extends Model
{
  protected $fillable = ['solicitud_viaticos_id','empleado_beneficiario_id','datos_bancarios_empleado_id','cuenta','clabe','tarjeta','beneficiario_externo','banco_nombre'];
  protected $table = 'beneficiario_viatico';
  public $timestamps = false;
}
