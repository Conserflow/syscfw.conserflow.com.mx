<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Support\Facades\DB;

class CodigosQRController extends Controller
{
  /**
   * Generar el código QR del empleado
   */
  public function GenerarQR($id)
  {
    try
    {
      $empleado = DB::table("empleados as e")
        ->select(
          "e.id",
          DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre")
        )->where("e.id", $id)->first();

      $text = $empleado->id . "|" . $empleado->nombre;
      $nombre = $empleado->nombre;

      $pdf = PDF::loadView("pdf.rh.codigoqr", compact("nombre", "text"));
      $pdf->setPaper("letter", "portrait");
      $nombre = $empleado->nombre . "_QR.pdf";
      return $pdf->stream($nombre);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}