<?php

namespace App\Http\Controllers\Compras;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ComprasModels\CatServicios;
use App\CatVehiculos;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use \App\Http\Helpers\Utilidades;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class CatServiciosController extends Controller
{

  /**
   * Obtiene todos los servicios registrados
   */
  public function index()
  {
    try
    {
      $servicios = CatServicios::get();
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

      $catServicio = new CatServicios();
      $catServicio->fill($request->all());
      $catServicio->empleado_registra_id = Auth::user()->empleado_id;
      Utilidades::auditar($catServicio, 0);
      $catServicio->save();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el servicio");
    }
  }

  // TODO: De donde es???
  /**
   * Muestra
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

      $comentarios = DB::table('incidencias_requisiciones')->where('requisicion_id', $value->requisicione_id)
        ->where('pda', $value->pda)->where('articulo_servicio', '0')
        ->where('articulo_servicio_id', $value->servicio_id)
        ->where('activo', '1')
        ->first();

      $arreglo[] = [
        'req' => $value,
        'doc' => $documentos,
        'correccion' => $comentarios,
      ];
    }
    return response()->json($arreglo);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Actualiza el servicio ingresado
   */
  public function update(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');

      $catServicio = CatServicios::findOrFail($request->id);
      $catServicio->fill($request->all());
      Utilidades::auditar($catServicio, $catServicio->id);
      $catServicio->save();

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "actualizar el servicio");
    }
  }


  /**
   * Busqueda 
   */
  public function busqueda()
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = CatServicios::select(
      [
        'id', 'nombre_servicio', 'proveedor_marca', 'unidad_medida', 'centro_costos_id'
      ]
    );

    if (isset($query) && $query)
    {
      $data = $byColumn == 1 ?
        $this->busqueda_filterByColumn($data, $query) :
        $this->busqueda_filter($data, $query, $fields);
    }

    $count = $data->count();

    $data->limit($limit)
      ->skip($limit * ($page - 1));

    if (isset($orderBy))
    {
      $direction = $ascending == 1 ? 'ASC' : 'DESC';
      $data->orderBy($orderBy, $direction);
    }

    $results = $data->get()->toArray();

    return [
      'data' => $results,
      'count' => $count,
    ];
  }

  public function busquedav()
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = CatVehiculos::select(
      [
        'id', 'descripcion', 'centro_costo_id'
      ]
    );

    if (isset($query) && $query)
    {
      $data = $byColumn == 1 ?
        $this->busqueda_filterByColumn($data, $query) :
        $this->busqueda_filter($data, $query, $fields);
    }

    $count = $data->count();

    $data->limit($limit)
      ->skip($limit * ($page - 1));

    if (isset($orderBy))
    {
      $direction = $ascending == 1 ? 'ASC' : 'DESC';
      $data->orderBy($orderBy, $direction);
    }

    $results = $data->get()->toArray();

    return [
      'data' => $results,
      'count' => $count,
    ];
  }

  protected function busqueda_filterByColumn($data, $queries)
  {
    $queries = json_decode($queries, true);

    return $data->where(function ($q) use ($queries)
    {
      foreach ($queries as $field => $query)
      {
        $_field = $field;

        if (is_string($query))
        {
          $q->where($_field, 'LIKE', "%{$query}%");
        }
        else
        {
          $start = Carbon::createFromFormat('Y-m-d', substr($query['start'], 0, 10))->startOfDay();
          $end = Carbon::createFromFormat('Y-m-d', substr($query['end'], 0, 10))->endOfDay();

          $q->whereBetween($_field, [$start, $end]);
        }
      }
    });
  }

  protected function busqueda_filter($data, $query, $fields)
  {
    return $data->where(function ($q) use ($query, $fields)
    {
      foreach ($fields as $index => $field)
      {
        $method = $index ? 'orWhere' : 'where';
        $q->{$method}($field, 'LIKE', "%{$query}%");
      }
    });
  }
}
