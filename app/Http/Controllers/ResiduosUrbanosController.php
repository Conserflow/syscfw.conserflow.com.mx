<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Http\Helpers\Utilidades;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class ResiduosUrbanosController extends Controller
{
  //
  public function get()
  {
    $data = DB::table('residuos_urbanos as ru')
      ->join('empleados as e', 'e.id', 'ru.empleado_entrega_id')
      ->select(
        'ru.*',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) as entrega")
      )
      ->orderBy('ru.fecha_salida', 'DESC')
      ->get();

    return response()->json($data);
  }

  public function guardar(Request $request)
  {
    try
    {
      $fechas = '';
      foreach ($request->fechas as $key => $value)
      {
        $fechas .= $value . ', ';
      }

      $data = new \App\ResiduosUrbanos();
      $data->residuo = $request->residuo;
      $data->cantidad = $request->cantidad;
      $data->unidad = $request->unidad;
      $data->fecha_salida = $request->fecha_salida;
      $data->empleado_entrega_id = $request->empleado_entrega;
      $data->proveedor = $request->proveedor;
      $data->fechas = $fechas;
      $data->folio = $request->folio;
      Utilidades::auditar($data, $data->id);
      $data->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function actualizar(Request $request)
  {
    try
    {
      $fechas = '';
      foreach ($request->fechas as $key => $value)
      {
        $fechas .= $value . ',';
      }

      $data = \App\ResiduosUrbanos::where('id', $request->id)->first();
      $data->residuo = $request->residuo;
      $data->cantidad = $request->cantidad;
      $data->unidad = $request->unidad;
      $data->fecha_salida = $request->fecha_salida;
      $data->empleado_entrega_id = $request->empleado_entrega;
      $data->proveedor = $request->proveedor;
      $data->fechas = $fechas;
      $data->folio = $request->folio;
      Utilidades::auditar($data, $data->id);
      $data->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function descargar($anio)
  {
    try
    {
      $data = DB::table('residuos_urbanos as ru')
        ->join('empleados as e', 'e.id', 'ru.empleado_entrega_id')
        ->select(
          'ru.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) as entrega")
        )
        ->orderBy('ru.fecha_salida', 'ASC')
        ->whereRaw("year(fecha_salida)=?", [$anio])
        ->get();
      $pdf = PDF::loadView('pdf.bitacoraurbanos', compact('data'));
      error_reporting(E_ALL ^ E_DEPRECATED);
      $pdf->setPaper('letter', 'portrait');
      return $pdf->stream("PSE-01_F-01 BITACORA DE RESIDUOS SOLIDOS URBANOS");
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
