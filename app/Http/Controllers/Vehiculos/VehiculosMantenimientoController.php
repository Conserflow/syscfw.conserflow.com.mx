<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Helpers\Utilidades;
use App\VehiculosModels\MantenimientoVehiculo;
use Exception;
use Illuminate\Support\Facades\DB;


class VehiculosMantenimientoController extends Controller
{
  /**
   ** OBTIENE TODOS LOS REGISTROS DE MANTENIMIENTO EXISTENTES
   **/
  public function getAll()
  {
    try
    {
      $mantenimiento = MantenimientoVehiculo::join('empleados as es', 'es.id', 'mantenimiento_vehiculos.solicita')
        ->join('empleados as er', 'er.id', 'mantenimiento_vehiculos.recibe')
        ->join('empleados as ee', 'ee.id', 'mantenimiento_vehiculos.entrega')
        ->join('empleados as ere', 'ere.id', 'mantenimiento_vehiculos.recibe_empleado')
        ->join('unidades as u', 'u.id', 'mantenimiento_vehiculos.unidad_id')
        ->select(
          'mantenimiento_vehiculos.*',
          DB::raw("CONCAT(es.nombre,' ',es.ap_paterno,' ',es.ap_materno) as empleado_solicita"),
          DB::raw("CONCAT(er.nombre,' ',er.ap_paterno,' ',er.ap_materno) as empleado_recibe"),
          DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) as empleado_entrega"),
          DB::raw("CONCAT(ere.nombre,' ',ere.ap_paterno,' ',ere.ap_materno) as empleado_recibe_uno"),
          'u.unidad',
          'u.marca',
          'u.modelo',
          'u.placas',
          DB::raw("concat_ws(' ',u.unidad,u.marca,u.modelo,u.placas) as vehiculo_desc")
        )
        ->orderBy("mantenimiento_vehiculos.fecha_inicio","desc")
        ->get();

      return response()->json($mantenimiento);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   ** OBTIENE un registro de la base de datos por id
   **/
  public function getById($id)
  {
    try
    {
      $mantenimiento = MantenimientoVehiculo::join('empleados as es', 'es.id', 'mantenimiento_vehiculos.solicita')
        ->join('empleados as er', 'er.id', 'mantenimiento_vehiculos.recibe')
        ->join('empleados as ee', 'ee.id', 'mantenimiento_vehiculos.entrega')
        ->join('empleados as ere', 'ere.id', 'mantenimiento_vehiculos.recibe_empleado')
        ->join('unidades as u', 'u.id', 'mantenimiento_vehiculos.unidad_id')
        ->select(
          'mantenimiento_vehiculos.*',
          DB::raw("CONCAT(es.nombre,' ',es.ap_paterno,' ',es.ap_materno) as empleado_solicita"),
          DB::raw("CONCAT(er.nombre,' ',er.ap_paterno,' ',er.ap_materno) as empleado_recibe"),
          DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) as empleado_entrega"),
          DB::raw("CONCAT(ere.nombre,' ',ere.ap_paterno,' ',ere.ap_materno) as empleado_recibe_uno"),
          'u.unidad',
          'u.marca',
          'u.modelo',
          'u.placas'
        )
        ->where('mantenimiento_vehiculos.id', $id)->first();

      return response()->json($mantenimiento);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   ** Guarda un registro en la base de datoss
   **/
  public function store(Request $request)
  {
    try
    {
      $datos = LimpiarInput::LimpiarIngnorar($request->all());
      $mantenimiento = new MantenimientoVehiculo($datos);
      Utilidades::auditar($mantenimiento, $mantenimiento->id);
      $mantenimiento->save();

      return response()->json($mantenimiento);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   ** Acutualiza un registro en la base de datos apatertir de modelo
   **/
  public function update(Request $request)
  {
    try
    {

      $mantenimiento = MantenimientoVehiculo::where('id', $request->id)->first();
      $datos = LimpiarInput::LimpiarIngnorar($request->all());
      $mantenimiento->fill($datos);
      Utilidades::auditar($mantenimiento, $mantenimiento->id);
      $mantenimiento->save();

      return response()->json($mantenimiento);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   ** Elimina un registro en la base de datos por id
   **/
  public function delete($id)
  {
    try
    {
      $mantenimiento = MantenimientoVehiculo::where('id', $id)->delete();
      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Descargar el mmto ingresado
   */
  public function Descargar($id)
  {
    try
    {
      $mantenimiento = DB::table("mantenimiento_vehiculos as mv")
        ->join('empleados as es', 'es.id', 'mv.solicita')
        ->join('empleados as er', 'er.id', 'mv.recibe')
        ->join('empleados as ee', 'ee.id', 'mv.entrega')
        ->join('empleados as ere', 'ere.id', 'mv.recibe_empleado')
        ->join('unidades as u', 'u.id', 'mv.unidad_id')
        ->leftJoin("poliza_unidades as pu", "pu.unidad_id", "u.id")
        ->select(
          'mv.*',
          "pu.numero_poliza",
          DB::raw("CONCAT_WS(' ',es.nombre,es.ap_paterno,es.ap_materno) as empleado_solicita"),
          DB::raw("CONCAT_WS(' ',er.nombre,er.ap_paterno,er.ap_materno) as empleado_recibe"),
          DB::raw("CONCAT_WS(' ',ee.nombre,ee.ap_paterno,ee.ap_materno) as empleado_entrega"),
          DB::raw("CONCAT_WS(' ',ere.nombre,ere.ap_paterno,ere.ap_materno) as empleado_recibe_uno"),
          'u.unidad',
          'u.marca',
          'u.modelo',
          'u.placas',
          "u.numero_serie",
          "u.color",
          "u.no_motor",
          "u.capacidad",
          "u.cilindros",
          "ee.id as empleado_entrega_id",
          "ere.id as empleado_recibe_id",
          "u.numero_tarjeta_circulacion"
        )
        ->where("mv.id", $id)
        ->first();

      $revision = $mantenimiento->fecha_inicio >= "2023-04-28" ? "01" : "00";
      $emision = $mantenimiento->fecha_inicio >= "2023-04-28" ? "28.ABR.23" : "29.JUN.20";

      $pdf = "pdf.reportemttounidad";
      $pdf = PDF::loadView($pdf, compact("mantenimiento", "revision", "emision"));
      $pdf->setPaper('letter', 'portrait');
      $nombre = "Mantenimiento Vehicular - " . $mantenimiento->unidad;
      error_reporting(E_ALL ^ E_DEPRECATED);
      return $pdf->stream($nombre);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
