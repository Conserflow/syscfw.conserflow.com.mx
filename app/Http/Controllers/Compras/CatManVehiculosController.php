<?php

namespace App\Http\Controllers\Compras;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ComprasModels\CatManVehiculos;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use \App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;

class CatManVehiculosController extends Controller
{

  /**
   * Obtiene todos los servicios registrados
   */
  public function index()
  {
    try
    {
      $servicios = CatManVehiculos::get();
      return Status::Success("servicios", $servicios);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los servicios");
    }
  }

  /**
   * Registra un nuevo servicio
   */
  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');
      $catServicio = new CatManVehiculos();
      $catServicio->fill($request->all());
      $catServicio->empleado_registra_id = Auth::user()->empleado_id;
      Utilidades::auditar($catServicio, 0);
      $catServicio->save();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "registrar el servicio");
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $arreglo = [];
    $requisiciones = DB::table('requisiciones')
      ->leftJoin('partidas_requisiciones AS pr', 'pr.requisicione_id', '=', 'requisiciones.id')
      ->leftJoin('servicios', 'servicios.id', 'pr.servicio_id')
      ->select(
        'requisiciones.*',
        'pr.peso AS peso',
        'pr.cantidad AS cantidades',
        'pr.equivalente',
        'pr.fecha_requerido AS frequerido',
        'pr.condicion AS condicion',
        'pr.requisicione_id',
        'pr.cantidad_compra',
        'pr.cantidad_almacen',
        'pr.articulo_id',
        'pr.servicio_id',
        'pr.pda',
        'pr.comentario AS comentario_partida',
        'servicios.nombre_servicio as nservicio',
        'servicios.proveedor_marca as nproveedor',
        'servicios.unidad_medida as umservicio'
      )
      ->where('pr.requisicione_id', '=', $id)
      ->where('pr.servicio_id', '!=', null)
      ->orderBy('requisiciones.id', 'ASC')
      ->get();

    foreach ($requisiciones as $key => $value)
    {

      $documentos = DB::table('partidarequisicion_documentos')
        ->leftJoin('documentos_proveedores AS DP', 'DP.id', '=', 'partidarequisicion_documentos.documento_id')
        ->select('partidarequisicion_documentos.*', 'DP.nombre', 'DP.nombre_corto')->where('partidarequisicion_documentos.partidarequisicione_serv', '=', $value->servicio_id)
        ->where('partidarequisicion_documentos.partidarequisicione_req', '=', $value->requisicione_id)->get();

      $arreglo[] = [
        'req' => $value,
        'doc' => $documentos,
      ];
    }
    return response()->json($arreglo);
  }

  /**
   * Actualiza el servicio ingresado
   */
  public function update(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');
      $catServicio = CatManVehiculos::findOrFail($request->id);
      $catServicio->fill($request->all());
      Utilidades::auditar($catServicio, $catServicio->id);
      $catServicio->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "actualizar el servicio");
    }
  }
}
