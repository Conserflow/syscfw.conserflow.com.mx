<?php

namespace App;

use App\RHModels\Empleado;
use Illuminate\Database\Eloquent\Model;

class SolicitudViaticos extends Model
{
  protected $fillable = [
    'fecha_solicitud',
    'fecha_pago',
    'folio',
    'proyecto_id',
    'origen_destino',
    'fecha_salida',
    'hora_estimada_salida',
    'fecha_operacion',
    'fecha_retorno',
    'empleado_elabora_id',
    'empleado_revisa_id',
    'empleado_autoriza_id',
    'total_personas',
    'empleado_supervisor_id',
    'estado',
    'empleado_user_id',
    'tipo',
    'origen_destino_destino',
    'eliminado',
    'timbrado'
  ];
  protected $table = 'solicitud_viaticos';
  //
  public function beneficiario()
  {
    return $this->hasOne(BeneficiarioViatico::class, 'solicitud_viaticos_id');
  }

  public function proyecto()
  {
    return $this->belongsTo(Proyecto::class, 'proyecto_id');
  }
  public function empleadoAutoriza()
  {
    return $this->belongsTo(Empleado::class, 'empleado_autoriza_id');
  }

  // Relación con el empleado que elabora
  public function empleadoElabora()
  {
    return $this->belongsTo(Empleado::class, 'empleado_elabora_id');
  }

  // Relación con el empleado que revisa
  public function empleadoRevisa()
  {
    return $this->belongsTo(Empleado::class, 'empleado_revisa_id');
  }

  // Relación con el empleado supervisor
  public function empleadoSupervisor()
  {
    return $this->belongsTo(Empleado::class, 'empleado_supervisor_id');
  }
  public function detalles()
  {
    return $this->hasMany(DetalleViatico::class, 'solicitud_viaticos_id');
  }

  /******** SCOPES *********/

  public function scopeByProyecto($query, $proyecto_id)
  {
    return $query->where('proyecto_id', $proyecto_id);
  }
}
