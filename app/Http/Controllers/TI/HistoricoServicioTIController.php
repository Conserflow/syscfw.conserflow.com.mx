<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\HistoricoServicioTI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class HistoricoServicioTIController extends Controller
{

  public function Obtener()
  {
    try
    {
      $historicos = DB::table("ti_historico_servicio as ths")
        ->join("empleados as e", "e.id", "ths.empleado_realiza_id")
        ->select(
          "ths.id",
          "ths.tipo",
          "ths.empleado_id",
          "ths.nombre_usuario",
          "ths.problema_servicio",
          "ths.fecha_reporte",
          "ths.solucion",
          "ths.fecha_solucion",
          "ths.reincidencia",
          "ths.condicion",
          "e.id as empleado_realiza_id",
          DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as empleado_realiza")
        )
        ->where("ths.fecha_reporte",">=","2020-01-01")
        ->where('ths.condicion', 1)
        ->get();

      return Status::Success("historico", $historicos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los registros");
    }
  }

  public function Guardar(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");
      $datos = LimpiarInput::LimpiarCampos(
        $request->all(),
        ["nombre_usuario", "problema_servicio", "solucion"]
      );
      if ($request->id == null)
      {
        $servicio = new HistoricoServicioTI();
        $servicio->fill($datos);
        $servicio->save();
        Auditoria::AuditarCambios($servicio);
      }
      else
      {
        $servicio = HistoricoServicioTI::find($request->id);
        $servicio->fill($datos);
        Auditoria::AuditarCambios($servicio);
        $servicio->update();
      }
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el servicio");
    }
  }

  /**
   * Elimina el registro del historial
   */
  public function Eliminar(Request $request)
  {
    try
    {
      $servicio = HistoricoServicioTI::find($request->id);
      $servicio->condicion = 0;
      Auditoria::AuditarCambios($servicio);
      $servicio->save();

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "eliminar el registro");
    }
  }

  public function Descargar($anio)
  {
    try
    {
      set_time_limit(100);
      ini_set('memory_limit', '400M');

      $servicios = DB::table("ti_historico_servicio as ths")
        ->join("empleados as e", "e.id", "ths.empleado_realiza_id")
        ->select(
          "ths.id",
          "ths.tipo",
          "ths.empleado_id",
          "ths.nombre_usuario",
          "ths.problema_servicio",
          "ths.fecha_reporte",
          "ths.solucion",
          "ths.fecha_solucion",
          "ths.reincidencia",
          "e.id as empleado_realiza_id",
          DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as empleado_realiza")
        )
        ->whereRaw("year(ths.fecha_reporte)=?", [$anio])
        ->where('ths.condicion', 1)
        ->orderBy("ths.fecha_reporte")
        ->get();

      $pdf = PDF::loadView('pdf.ti.historicoservicios', compact('servicios'));
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('letter', 'landscape');
      return $pdf->stream("Historico de servicios");
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view('errors.500');
    }
  }
}
