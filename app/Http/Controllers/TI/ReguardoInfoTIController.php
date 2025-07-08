<?php
// FIXME: arreglar
namespace App\Http\Controllers\TI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Utilidades;
use App\TIModels\ReguardoInfoTI;
use Barryvdh\DomPDF\Facade as PDF;

class ReguardoInfoTIController extends Controller
{
  //
  public function get()
  {
    $data = DB::table("ti_resguardo_informacion as tri")
      ->leftJoin('empleados AS e', 'e.id', 'tri.responsable_i')
      ->join('empleados AS ee', 'ee.id', 'tri.responsable_r')
      ->select(
        'tri.*',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_ii"),
        DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS empleado_ri")
      )
      ->orderBy("tri.fecha","desc")
      ->get();

    return response()->json($data);
  }

  public function guardar(Request $request)
  {
    try
    {
      $data = new ReguardoInfoTI();
      $data->responsable_i = $request->responsable_i;
      $data->ruta = $request->ruta;
      $data->tamanio = $request->tamanio;
      $data->fecha = $request->fecha;
      $data->ubicacion = $request->ubicacion;
      $data->responsable_r = $request->responsable_r;
      $data->observaciones = $request->observaciones;
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
      $data = ReguardoInfoTI::where('id', $request->id)->first();
      $data->responsable_i = $request->responsable_i;
      $data->ruta = $request->ruta;
      $data->tamanio = $request->tamanio;
      $data->fecha = $request->fecha;
      $data->ubicacion = $request->ubicacion;
      $data->responsable_r = $request->responsable_r;
      $data->observaciones = $request->observaciones;
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

      $data = DB::table("ti_resguardo_informacion as tri")
        ->leftJoin('empleados AS e', 'e.id', 'tri.responsable_i')
        ->join('empleados AS ee', 'ee.id', 'tri.responsable_r')
        ->select(
          'tri.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_ii"),
          DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS empleado_ri")
        )
        ->whereRaw("year(fecha)=?", [$anio])
        ->get();
      $pdf = PDF::loadView('pdf.ti.bitacorati', compact('data',"anio"));
      $pdf->setPaper('letter', 'landscape');
      $pdf->getDomPDF()->set_option("enable_php", true);
      return $pdf->stream();
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }
}
