<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequisicionporCompraController extends Controller
{

  public function listadoCompras(Request $request, $id)
  {
    $listado = DB::table('ordenes_compras')
      ->leftJoin('proyectos', 'proyectos.id', '=', 'ordenes_compras.proyecto_id')
      ->select('ordenes_compras.*', 'proyectos.nombre_corto as nombreCorto')
      ->where('ordenes_compras.proyecto_id', '=', $id)
      ->get();

    return response()->json($listado);
  }
}
