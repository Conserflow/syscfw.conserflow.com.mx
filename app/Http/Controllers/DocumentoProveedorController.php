<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class DocumentoProveedorController extends Controller
{
  public function index()
  {
    //    if (!$request->ajax()) return redirect('/');
    $documentos = DB::table('documentos_proveedores')->select('documentos_proveedores.*')->get();
    return response()->json($documentos);
  }
  public function show($id)
  {
    $valores = explode('&', $id);
    $documentos_partida = DB::table('partidarequisicion_documentos')
      ->leftJoin('documentos_proveedores', 'documentos_proveedores.id', '=', 'partidarequisicion_documentos.documento_id')
      ->leftJoin('partidas_requisiciones AS PR', function ($join)
      {
        $join->on('PR.requisicione_id', '=', 'partidarequisicion_documentos.partidarequisicione_req')
          ->on('PR.articulo_id', '=', 'partidarequisicion_documentos.partidarequisicione_art');
      })
      ->leftJoin('requisicion_has_ordencompras AS RHC', function ($join)
      {
        $join->on('RHC.articulo_id', '=', 'PR.articulo_id')
          ->on('RHC.requisicione_id', '=', 'PR.requisicione_id');
      })
      ->leftJoin('partidas_entradas AS PE', function ($join)
      {
        $join->on('PE.articulo_id', '=', 'RHC.articulo_id')
          ->on('PE.req_com_id', '=', 'RHC.id');
      })
      ->select('partidarequisicion_documentos.*', 'documentos_proveedores.nombre')
      ->where('PE.req_com_id', '=', $valores[0])
      ->where('PE.articulo_id', '=', $valores[1])
      ->where('partidarequisicion_documentos.condicion', '=', '0')
      ->get();
    return response()->json($documentos_partida);
  }
  public function requisicionesdocumentospendientes()
  {
    $requisiciones = DB::table('partidarequisicion_documentos')
      ->leftJoin('documentos_proveedores AS DC', 'DC.id', '=', 'partidarequisicion_documentos.documento_id')
      ->leftJoin('requisiciones AS R', 'R.id', '=', 'partidarequisicion_documentos.partidarequisicione_req')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'R.proyecto_id')
      ->select('R.*', 'P.nombre AS nombre_proyecto')
      ->where('partidarequisicion_documentos.condicion', '=', '0')
      ->where('R.estado_id', '=', '5')
      ->distinct()->get();

    return response()->json($requisiciones);
  }

  public function documentospendientes($id)
  {
    $documentos_partidas = DB::table('partidarequisicion_documentos')
      ->leftJoin('documentos_proveedores AS DC', 'DC.id', '=', 'partidarequisicion_documentos.documento_id')
      ->leftJoin('requisiciones AS R', 'R.id', '=', 'partidarequisicion_documentos.partidarequisicione_req')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'R.proyecto_id')
      ->select('R.*', 'P.nombre AS nombre_proyecto', 'DC.id  AS id_doc')
      ->where('partidarequisicion_documentos.condicion', '=', '0')
      ->where('R.estado_id', '=', '5')
      ->where('R.id', '=', $id)
      ->get();
    return response()->json($documentos_partidas);
  }

  public function descargardocumentos($id)
  {

    // Se obtiene el archivo del FTP serve

    $archivo = Utilidades::ftpSolucion($id);
    // Se coloca el archivo en una ruta local
    Storage::disk('descarga')->put($id, $archivo);
    //--Devuelve la respuesta y descarga el archivo--//
    return response()->download(storage_path() . '/app/descargas/' . $id);
  }
}
