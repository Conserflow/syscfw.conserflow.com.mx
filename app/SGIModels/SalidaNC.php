<?php

namespace App\SGIModels;

use App\Proyecto;
use App\SGIModels\Departamento;
use App\RHModels\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalidaNC extends Model
{
  protected $table = "sgi_salidas_nc";
  protected $fillable =
  [
    "empleado_elabora_id",
    "fecha_elaboracion",
    "empleado_detecta_id",
    "area_id",
    "fecha_deteccion",
    "folio",
    "descripcion",
    "no_oc",
    "proyecto_id",
    "cliente_proveedor",
    "no_comunicado",
    "tratamiento",
    "tratamiento_otro",
    "resultado",
    "acciones",
    "empleado_verifica_id",
    "fecha_verificacion",
    "require_correccion",
    "no_accion_correctiva",
    "empleado_registra_id",
  ];

  /******************************************************
   * Relaciones
   *****************************************************/

  public function empleadoElabora()
  {
    return $this->belongsTo(Empleado::class, "empleado_elabora_id");
  }

  public function empleadoDetecta()
  {
    return $this->belongsTo(Empleado::class, "empleado_detecta_id");
  }

  public function area()
  {
    return $this->belongsTo(Departamento::class, "area_id");
  }

  public function proyecto()
  {
    return $this->belongsTo(Proyecto::class, "proyecto_id");
  }

  public function empleadoVerifica()
  {
    return $this->belongsTo(Empleado::class, "empleado_verifica_id");
  }

  /******************************************************
   * Scopes
   *****************************************************/
  public function scopeTodos($query)
  {
    return $query->with([
      "empleadoElabora" => function ($query)
      {
        $query->select(
          "id",
          DB::raw("concat_ws(' ',nombre,ap_paterno,ap_materno) as nombre")
        );
      },
      "empleadoDetecta" => function ($query)
      {
        $query->select(
          "id",
          DB::raw("concat_ws(' ',nombre,ap_paterno,ap_materno) as nombre")
        );
      },
      "empleadoVerifica" => function ($query)
      {
        $query->select(
          "id",
          DB::raw("concat_ws(' ',nombre,ap_paterno,ap_materno) as nombre")
        );
      },
      "area",
      "proyecto",
    ]);
  }

  public function scopePorAnio($query, $anio)
  {
    return $query->whereYear("fecha_elaboracion", $anio);
  }
}
