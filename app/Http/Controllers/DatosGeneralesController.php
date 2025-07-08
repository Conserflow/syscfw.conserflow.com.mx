<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Http\Helpers\Utilidades;

class DatosGeneralesController extends Controller
{
  //
  /**
   * [index funcion que lista todos los registros del modelo DatosGenerales]
   */
  public function index($value = '')
  {
    $datos = \App\DatosGenerales::get();
    return response()->json($datos);
  }

  public function show($id)
  {
    $factura = \App\DatosGenerales::where('id', $id)->first();
    return response()->json($factura);
  }
}
