<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use \App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class BitacoraResiduosController extends Controller
{
  //

  public function store(Request $request)
  {
    try
    {

      $bre = new \App\BitacoraResiduosEntrada();
      $bre->bitacora_residuos_entrada_general_id = $request->bitacora_residuos_entrada_general_id;
      $bre->fecha = $request->fecha;
      $bre->nombre = $request->nombre;
      $bre->tipo = $request->tipo;
      $bre->cantidad = $request->cantidad;
      $bre->unidad = $request->unidad;
      $bre->area_proceso = $request->area_proceso;
      $bre->fecha_salida_rme = $request->fecha_salida_rme;
      $bre->crebit = $request->crebit;
      $bre->fecha_dos = $request->fecha_dos;
      $bre->clave = $request->clave;
      $bre->no_autorizacion = $request->no_autorizacion;
      $bre->proveedor = $request->proveedor;
      $bre->destino = $request->destino;
      $bre->num_manifiesto = $request->num_manifiesto;
      // $bre->localidad = $request->localidad;
      // $bre->responsable = $request->responsable;
      Utilidades::auditar($bre, $bre->id);
      $bre->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function update(Request $request)
  {
    try
    {
      $bre = \App\BitacoraResiduosEntrada::where('id', $request->id)->first();
      $bre->bitacora_residuos_entrada_general_id = $request->bitacora_residuos_entrada_general_id;
      $bre->fecha = $request->fecha;
      $bre->nombre = $request->nombre;
      $bre->tipo = $request->tipo;
      $bre->cantidad = $request->cantidad;
      $bre->unidad = $request->unidad;
      $bre->area_proceso = $request->area_proceso;
      $bre->fecha_salida_rme = $request->fecha_salida_rme;
      $bre->crebit = $request->crebit;
      $bre->fecha_dos = $request->fecha_dos;
      $bre->clave = $request->clave;
      $bre->no_autorizacion = $request->no_autorizacion;
      $bre->proveedor = $request->proveedor;
      $bre->num_manifiesto = $request->num_manifiesto;
      $bre->destino = $request->destino;
      // $bre->localidad = $request->localidad;
      // $bre->responsable = $request->responsable;
      Utilidades::auditar($bre, $bre->id);
      $bre->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function get($id)
  {
    try
    {

      $db = DB::table('bitacora_residuos_entrada AS bre')
        ->select('bre.*', 'cbr.id AS cbr_id', 'cbr.residuo', 'cbr.tipo AS tipo_cbr')
        ->join('catalogo_bitacora_residuos AS cbr', 'cbr.id', 'bre.nombre')
        ->where('bre.bitacora_residuos_entrada_general_id', $id)
        ->get();

      foreach ($db as $key => $value)
      {
        $bre = DB::table('catalogo_bitacora_residuos AS cbr')->first();
      }

      return response()->json($db);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function getGral()
  {
    try
    {

      $db = DB::table('bitacora_residuos_entrada_general AS bre')
        ->leftjoin('empleados AS e', 'e.id', 'bre.responsable')
        ->select(
          'bre.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS responsable_nombre")
        )
        ->where('bre.condicion', '1')
        ->get();
      return response()->json($db);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Descarga el formato de RP o RME, según sea el caso
   */
  public function descargar($id)
  {
    try
    {
      $gral = DB::table('bitacora_residuos_entrada_general AS bre')
        ->leftjoin('empleados AS e', 'e.id', 'bre.responsable')
        ->select(
          'bre.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS responsable_nombre")
        )
        ->where('bre.id', $id)->first();

      // Definir tipo
      $dts = explode("-", $gral->folio);
      if (count($dts) != 3)
      {
        // Folio incorrecto
        Utilidades::errors(new Exception("Folio incorrecto: " . $gral->folio, 1));
        return view("errors.500");
      }
      $tipo = $dts[1];
      $nombre = $tipo === "RP" ? "pdf.bitacora" : "pdf.bitacoranuevo";
      $titulo = $tipo === "RP" ?
        "BITÁCORA DE ENTRADAS Y SALIDAS DE RESIDUOS PELIGROSOS" :
        "BITACORA DE ENTRADA Y SALIDA DE RESIDUO DE MANEJO ESPECIAL";

      $bre = DB::table('bitacora_residuos_entrada')
        ->join('catalogo_bitacora_residuos AS cbr', 'cbr.id', 'bitacora_residuos_entrada.nombre')
        ->where('bitacora_residuos_entrada_general_id', $id)
        ->select('bitacora_residuos_entrada.*', 'cbr.residuo AS nr')->get();

      $pdf = PDF::loadView($nombre, compact('gral', 'bre'));
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('ledger', 'portrait');
      error_reporting(E_ALL ^ E_DEPRECATED);
      return $pdf->stream($titulo);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Descarga el nuevo formato
   */
  public function Descargarnuevo($id)
  {
    try
    {
      $gral = DB::table('bitacora_residuos_entrada_general AS bre')
        ->leftjoin('empleados AS e', 'e.id', 'bre.responsable')
        ->select(
          'bre.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS responsable_nombre")
        )
        ->where('bre.id', $id)->first();

      $bre = DB::table('bitacora_residuos_entrada')
        ->join('catalogo_bitacora_residuos AS cbr', 'cbr.id', 'bitacora_residuos_entrada.nombre')
        ->where('bitacora_residuos_entrada_general_id', $id)
        ->select('bitacora_residuos_entrada.*', 'cbr.residuo AS nr')->get();

      $pdf = PDF::loadView('pdf.bitacoranuevo', compact('gral', 'bre'));
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('ledger', 'portrait');
      error_reporting(E_ALL ^ E_DEPRECATED);
      return $pdf->stream();
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  public function storeGral(Request $request)
  {
    try
    {
      $breg = new \App\BitacoraResiduosEntradaGral();
      $breg->folio = $request->folio;
      $breg->localidad = $request->localidad;
      $breg->responsable = $request->responsable;
      Utilidades::auditar($breg, $breg->id);
      $breg->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function updateGral(Request $request)
  {
    try
    {
      $breg =  \App\BitacoraResiduosEntradaGral::where('id', $request->id)->first();
      $breg->folio = $request->folio;
      $breg->localidad = $request->localidad;
      $breg->responsable = $request->responsable;
      Utilidades::auditar($breg, $breg->id);
      $breg->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function eliminar($id)
  {
    try
    {

      $data = \App\BitacoraResiduosEntradaGral::where('id', $id)->first();
      $data->condicion = 0;
      Utilidades::auditar($data, $data->id);
      $data->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }
}
