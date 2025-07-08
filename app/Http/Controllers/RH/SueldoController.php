<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\Sueldo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SueldoController extends Controller
{

  /**
   * Obtener los sueldos del contrato ingresado
   */
  public function ObtenerSueldos($c_id)
  {
    try
    {
      $sueldos = Sueldo::where("contrato_id", $c_id)
        ->select(
          "id",
          "sueldo_diario_integral",
          "sueldo_mensual",
          "infonavit",
          "viaticos_mensuales",
          "sueldo_diario_neto",
          "contrato_id",
          "sueldo_diario_real",
          "fecha_act"
        )
        ->get();
      return Status::Success("sueldos", $sueldos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los sueldos");
    }
  }

  /**
   * Obtener los sueldos del contrato ingresado
   */
  public function GuardarSueldo(Request $request)
  {
    try
    {
      if (!$request->ajax()) redirect("/");
      $sueldo = new Sueldo($request->all());
      $sueldo->fecha_act = date("Y-m-d");
      $sueldo->sueldo_mensual = 0;
      $sueldo->infonavit = 0;
      $sueldo->viaticos_mensuales = 0;
      $sueldo->empleado_registra_id = Auth::user()->empleado_id;
      $sueldo->save();
      Auditoria::AuditarCambios($sueldo);

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el sueldo");
    }
  }

  /**
   * Actualizar el SDI
   */
  public function ActualizarSDI(Request $request)
  {
    try
    {
      $sueldo = Sueldo::find($request->id);
      $sueldo->sueldo_diario_integral = $request->sueldo_diario_integral;
      $sueldo->update();
      Auditoria::AuditarCambios($sueldo);
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el SDI");
    }
  }
}
