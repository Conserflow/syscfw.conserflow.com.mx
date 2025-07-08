<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Http\Helpers\Utilidades;

class PartidasSalidasController extends Controller
{
  /**
   * [index Consulta de la tabla partidas_requisiciones todos los registros qure contiene]
   * @return Response [description]
   */
  public function index()
  {
    $data = DB::table('partidas_requisiciones')
      ->leftJoin('articulos AS a', 'a.id', '=', 'partidas_requisiciones.articulo_id')
      ->leftJoin('requisiciones AS r', 'r.id', '=', 'partidas_requisiciones.requisicione_id')
      ->leftJoin('proyectos AS p', 'p.id', '=', 'r.proyecto_id')
      ->select('partidas_requisiciones.*', 'a.id AS ida', 'r.id AS rid', 'a.descripcion AS descripciona', 'p.nombre AS proyecton', 'p.id AS proyectoi', 'r.folio AS rf', 'r.fecha_solicitud AS rfs')
      ->where('partidas_requisiciones.condicion', '=', '1')->get();
    return response()->json($data);
  }

  /**
   * [show Consulta en la tabla lote_almacen todos los registros que coincidan con el id del proyecto o con stoke general
   * ademas de verificar la ubicación del usuario logeado para selecionar los registro que coincidan con su ubicación]
   * @param  Int $id [description]
   * @return Response     [description]
   */
  public function show($id)
  {
    $partidas = DB::table('lote_almacen as la')
      ->leftJoin('lotes as l', 'l.id', 'la.lote_id')
      ->leftJoin('stocks as s', 's.id', 'la.stocke_id')
      ->leftJoin('proyectos as p', 'p.id', 's.proyecto_id')
      ->leftJoin('articulos as a', 'a.id', 'la.articulo_id')
      ->select(
        'la.cantidad',
        'a.id as a_id',
        'a.descripcion as descripcion',
        'a.marca as a_marca',
        'a.unidad as a_unidad',
        'p.nombre_corto as proyecto'
      )
      ->where('la.cantidad', '>', '0')
      ->get();
    return Status::Success("partidas", $partidas);
  }

  /**
   * [show Consulta en la tabla lote_almacen todos los registros que coincidan con el id del proyecto o con stoke general
   * ademas de verificar la ubicación del usuario logeado para selecionar los registro que coincidan con su ubicación]
   * @param  Int $id [description]
   * @return Response     [description]
   */
  private function ObtenerPartidasAux()
  {
    $partidas = DB::table('lote_almacen as la')
      ->leftJoin('lotes as l', 'l.id', 'la.lote_id')
      ->leftJoin('stocks as s', 's.id', 'la.stocke_id')
      ->leftJoin('proyectos as p', 'p.id', 's.proyecto_id')
      ->leftJoin('articulos as a', 'a.id', 'la.articulo_id')
      ->leftJoin('entradas as e', 'e.id', 'l.entrada_id')
      ->leftJoin("partidas_entradas as pe", "l.entrada_id", "pe.id")
      ->leftJoin("requisicion_has_ordencompras as rhoc", "rhoc.id", "pe.req_com_id")
      ->select(
        'la.id as la_id',
        'a.id as a__id',
        'la.cantidad as la__cantidad',
        'a.nombre as a__nombre',
	DB::raw("IF(rhoc.comentario is null,a.descripcion, concat(a.descripcion,' - ',rhoc.comentario)) as a__descripcion"),
        // DB::raw("concat(a.descripcion,' - ',rhoc.comentario) as a__descripcion"),
        'a.marca as a__marca',
        'a.unidad as a__unidad',
        'p.nombre_corto as p__nombre_corto'
      )
      ->where('la.cantidad', '>', 0);
    return $partidas;
  }

  public function ObtenerPartidasServer($id)
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = $this->ObtenerPartidasAux();
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
      $aux_order = str_replace("__", ".", $orderBy);
      $direction = $ascending == 1 ? 'ASC' : 'DESC';
      $data->orderBy($aux_order, $direction);
    }
    $results = $data->get();
    return [
      'data' => $results,
      'count' => $count,
    ];
  }

  protected function busqueda_filterByColumn($data, $queries)
  {
    $queries = json_decode($queries, true);

    foreach ($queries as $field => $query)
    {
      $_field = str_replace("__", ".", $field);
      $data->where($_field, 'LIKE', "%{$query}%");
    }
    return $data;
  }

  protected function busqueda_filter($data, $query, $fields)
  {
    foreach ($fields as $index => $field)
    {
      $method = $index ? 'orWhere' : 'where';
      $data->{$method}($field, 'LIKE', "%{$query}%");
    }
  }

  /**
   * [getLoteTemporal Obtiene todos los registros de la tabla lote_temporal que coincidan con el id de proyecto recibido]
   * @param   Int $id [description]
   * @return Response     [description]
   */
  public function getLoteTemporal($id)
  {
    $ubicacion = Utilidades::ubicacion();
    if ($ubicacion == null)
    {
      $lote_temporal = DB::table('lote_temporal')
        ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'lote_temporal.lote_almacen_id')
        ->leftJoin('requisiciones AS R', 'R.id', '=', 'lote_temporal.requisicion_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'LA.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('lotes AS L', 'L.id', '=', 'LA.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'LA.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'LA.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'LA.stand_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'LA.articulo_id')
        ->select(
          'LA.id',
          'S.nombre AS nombre_stock',
          'lote_temporal.id AS lt_id',
          'lote_temporal.cantidad',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('R.proyecto_id', '=', $id)
        ->where('lote_temporal.cantidad', '>', '0')->get();

      return response()->json($lote_temporal);
    }
    else
    {
      $lote_temporal = DB::table('lote_temporal')
        ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'lote_temporal.lote_almacen_id')
        ->leftJoin('requisiciones AS R', 'R.id', '=', 'lote_temporal.requisicion_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'LA.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('lotes AS L', 'L.id', '=', 'LA.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'LA.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'LA.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'LA.stand_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'LA.articulo_id')
        ->select(
          'LA.id',
          'S.nombre AS nombre_stock',
          'lote_temporal.id AS lt_id',
          'lote_temporal.cantidad',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('R.proyecto_id', '=', $id)
        ->where('AL.ubicacion_id', '=', $ubicacion)
        ->where('lote_temporal.cantidad', '>', '0')->get();

      return response()->json($lote_temporal);
    }
  }

  /**
   * [edit Consulta en la tabla lote_almacen del stoke general y del proyecto que coincide con el id recibido]
   * @param  Int $id [description]
   * @return Response     [description]
   * Pendiente por revisar (uso)
   */
  public function edit($id)
  {
    $LoteAlmacen1 = DB::table('lote_almacen')
      ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
      ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
      ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
      ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
      ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
      ->select(
        'lote_almacen.*',
        'A.id AS ida',
        'A.nombre AS anombre',
        'A.descripcion AS adescripcion',
        'A.codigo AS acodigo',
        'A.marca AS amarca',
        'A.unidad AS aunidad',
        'P.nombre AS proyecton',
        'P.id AS proyectoi'
      )
      ->where('P.id', '=', $id)
      ->where('lote_almacen.cantidad', '>', '0')->get();

    $LoteAlmacen2 = DB::table('lote_almacen')
      ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
      ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
      ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
      ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
      ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
      ->select(
        'lote_almacen.*',
        'A.id AS ida',
        'A.nombre AS anombre',
        'A.descripcion AS adescripcion',
        'A.codigo AS acodigo',
        'A.marca AS amarca',
        'A.unidad AS aunidad',
        'P.nombre AS proyecton',
        'P.id AS proyectoi'
      )
      ->where('lote_almacen.stocke_id', '=', 1)
      ->where('A.tipo_resguardo_id', '>', '1')
      ->where('lote_almacen.cantidad', '>', '0')->get();

    $LoteAlmacen = $LoteAlmacen1->merge($LoteAlmacen2);


    return response()->json($LoteAlmacen);
  }

  /**
   * [ver Consulta en la tabla partidas todos los registro que coincidan con el id de salida_id recbido]
   * @param  String $id [description]
   * @return Response     [description]
   */
  public function ver($id)
  {
    $valores = explode('&', $id);
    $partidasalida = DB::table('partidas')
      ->leftJoin('salidas AS S', 'S.id', '=', 'partidas.salida_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->leftJoin('tipo_salidas AS TS', 'TS.id', '=', 'S.tiposalida_id')
      ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'partidas.lote_id')
      ->leftJoin('almacenes AS AL', 'AL.id', '=', 'LA.almacene_id')
      ->leftJoin('niveles AS N', 'N.id', '=', 'LA.nivel_id')
      ->leftJoin('stands AS ST', 'ST.id', '=', 'LA.stand_id')
      ->leftJoin('stocks AS SK', 'SK.id', '=', 'LA.stocke_id')
      ->leftJoin('articulos AS A', 'A.id', '=', 'LA.articulo_id')
      ->select(
        'partidas.*',
        'S.ubicacion AS subicacion',
        'P.nombre AS pnombre',
        'TS.nombre AS tsnombre',
        'AL.nombre AS alnombre',
        'SK.nombre AS sknombre',
        'A.nombre AS anombre',
        'A.codigo AS acodigo',
        'A.descripcion AS adescripcion',
        'A.marca AS amarca',
        'A.unidad AS aunidad'
      )
      ->where('partidas.salida_id', '=', $valores[0])
      ->where('partidas.tiposalida_id', '=', $valores[1])
      ->get();

    return response()->json($partidasalida);
  }

  /**
   * [store Guardado de un registro en el modelo Partidas y llenado de sus respectivas tablas relacionadas ]
   * @param  Request $request [description]
   * @return Response           [description]
   */
  public function store(Request $request)
  {
    try
    {
      $partida = \App\Partidas::where('salida_id', '=', $request->salida_id)
        ->where('lote_id', '=', $request->lote_id)->first();
      if ($partida != null)
      {
        $partidas = \App\Partidas::where('salida_id', '=', $request->salida_id)->where('lote_id', '=', $request->lote_id)->first();
        $partidas->salida_id = $request->salida_id;
        $partidas->tiposalida_id = $request->tiposalida_id;
        $partidas->lote_id = $request->lote_id;
        $partidas->cantidad = $partidas->cantidad + $request->cantidad;
        Utilidades::auditar($partidas, $partidas->id);
        $partidas->save();
      }
      else
      {
        $partidas = new \App\Partidas();
        $partidas->fill($request->all());
        Utilidades::auditar($partidas, $partidas->id);
        $partidas->save();
      }

      $tipo_salida_nombre = \App\TipoSalida::where('id', '=', $request->tiposalida_id)->first();

      $lote_almacen = \App\LoteAlmacen::where('id', '=', $partidas->lote_id)->first();
      $cantidadresta = $lote_almacen->cantidad - $request->cantidad;
      $lote_almacen->cantidad = $cantidadresta;
      $lote_almacen->update();

      $stokearticulo = \App\StockArticulo::where('articulo_id', '=', $lote_almacen->articulo_id)->where('stocke_id', '=', $lote_almacen->stocke_id)->first();
      $cantidadrestastoke = $stokearticulo->cantidad - $request->cantidad;
      $stokearticulo->cantidad = $cantidadrestastoke;
      $stokearticulo->update();

      $existencias = \App\Existencia::where('id_lote', '=', $lote_almacen->lote_id)->where('articulo_id', '=', $lote_almacen->articulo_id)->first();
      $cantidadrestaexistencia = $existencias->cantidad - $request->cantidad;
      $existencias->cantidad = $cantidadrestaexistencia;
      $existencias->update();

      $stocks = \App\Stock::where('id', '=', $stokearticulo->stocke_id)->first();
      $proyectos = \App\Proyecto::where('id', '=', $stocks->proyecto_id)->first();

      $hoy = date("Y-m-d");
      $hora = date("H:i:s");
      $movimiento = new \App\Movimiento();
      $movimiento->cantidad = $request->cantidad;
      $movimiento->fecha = $hoy;
      $movimiento->hora = $hora;
      $movimiento->tipo_movimiento = 'Salida ';
      $movimiento->folio = 'Salida-' . $lote_almacen->articulo_id . ' a ' . $tipo_salida_nombre->nombre;
      $movimiento->proyecto_id = $proyectos->id;
      $movimiento->lote_id =  $lote_almacen->id;
      $movimiento->stocke_id =  $stokearticulo->stocke_id;
      $movimiento->almacene_id = $lote_almacen->almacene_id;
      $movimiento->stand_id = ($lote_almacen->stand_id == 'null') ? null : $lote_almacen->stand_id;
      $movimiento->nivel_id = ($lote_almacen->nivel_id == 'null') ? null : $lote_almacen->nivel_id;
      $movimiento->articulo_id = $lote_almacen->articulo_id;
      Utilidades::auditar($movimiento, $movimiento->id);
      $movimiento->save();

      return response()->json(array('status' => true));
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * [requisicionbusqueda Consulta en la tabla lote_almacen los registros que coincidan con el proyecto y el articulo recibido
   * ademas de verificar la ubicación del usuario logeado para selecionar los registro que coincidan con su ubicación]
   * @param   String $id [description]
   * @return Response     [description]
   */
  public function requisicionbusqueda($id)
  {
    $valores = explode('&', $id);
    $ubicacion = Utilidades::ubicacion();
    if ($ubicacion == null)
    {
      $LoteAlmacen1 = DB::table('lote_almacen')
        ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
        ->select(
          'lote_almacen.*',
          'S.nombre AS stock_nombre',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('P.id', '=', $valores[0])
        ->where('A.id', '=', $valores[1])
        ->where('lote_almacen.cantidad', '>', '0')->orderBy('S.id', 'desc')->get();

      $LoteAlmacen2 = DB::table('lote_almacen')
        ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
        ->select(
          'lote_almacen.*',
          'S.nombre AS stock_nombre',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('lote_almacen.stocke_id', '=', 1)
        ->where('A.id', '=', $valores[1])
        ->where('lote_almacen.cantidad', '>', '0')->orderBy('S.id', 'desc')->get();

      $LoteAlmacen = $LoteAlmacen1->merge($LoteAlmacen2);
    }
    else
    {
      $LoteAlmacen1 = DB::table('lote_almacen')
        ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
        ->select(
          'lote_almacen.*',
          'S.nombre AS stock_nombre',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('P.id', '=', $valores[0])
        ->where('A.id', '=', $valores[1])
        ->where('AL.ubicacion_id', '=', $ubicacion)
        ->where('lote_almacen.cantidad', '>', '0')->orderBy('S.id', 'desc')->get();

      $LoteAlmacen2 = DB::table('lote_almacen')
        ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
        ->leftJoin('almacenes AS AL', 'AL.id', '=', 'lote_almacen.almacene_id')
        ->leftJoin('niveles AS N', 'N.id', '=', 'lote_almacen.nivel_id')
        ->leftJoin('stands AS ST', 'ST.id', '=', 'lote_almacen.stand_id')
        ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
        ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
        ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')
        ->select(
          'lote_almacen.*',
          'S.nombre AS stock_nombre',
          'L.nombre AS lote_nombre',
          'A.id AS ida',
          'A.nombre AS anombre',
          'A.descripcion AS adescripcion',
          'A.codigo AS acodigo',
          'A.marca AS amarca',
          'A.unidad AS aunidad',
          'P.nombre AS proyecton',
          'P.id AS proyectoi'
        )
        ->where('lote_almacen.stocke_id', '=', 1)
        ->where('AL.ubicacion_id', '=', $ubicacion)
        ->where('A.id', '=', $valores[1])
        ->where('lote_almacen.cantidad', '>', '0')->orderBy('S.id', 'desc')->get();

      $LoteAlmacen = $LoteAlmacen1->merge($LoteAlmacen2);
    }


    return response()->json($LoteAlmacen);
  }

  public function buscarlotenombre(Request $request)
  {
    // $lote_almacen =DB::select("SELECT lote_almacen.id FROM lote_almacen
    //   LEFT JOIN lotes l ON l.id = lote_almacen.lote_id
    //   LEFT JOIN stocks s ON s.id = lote_almacen.stocke_id
    //   LEFT JOIN proyectos p ON p.id = s.proyecto_id
    //   WHERE l.nombre = '$request->lotenombreinput' AND (p.id = '$request->proyecto_id' OR s.id = 1)
    //    AND lote_almacen.cantidad > 0");

    $lote_almacen_uno = DB::table('lote_almacen')
      ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
      ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->select('lote_almacen.id', 'P.nombre')
      ->where('L.nombre', '=', $request->lotenombreinput)
      ->where('P.id', '=', $request->proyecto_id)
      ->where('lote_almacen.cantidad', '>', '0')->get();

    $lote_almacen_dos = DB::table('lote_almacen')
      ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
      ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->select('lote_almacen.id', 'P.nombre')
      ->where('L.nombre', '=', $request->lotenombreinput)
      ->where('S.id', '=', '1')
      ->where('lote_almacen.cantidad', '>', '0')->get();

    $lote_almacen = $lote_almacen_uno->merge($lote_almacen_dos);

    $lote_temporal = DB::table('lote_temporal')
      ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'lote_temporal.lote_almacen_id')
      ->leftJoin('lotes AS L', 'L.id', '=', 'LA.lote_id')
      ->leftJoin('requisiciones AS R', 'R.id', '=', 'lote_temporal.requisicion_id')
      ->select('lote_temporal.id', 'lote_temporal.comentario AS nombre')
      ->where('L.nombre', '=', $request->lotenombreinput)
      ->where('R.proyecto_id', '=', $request->proyecto_id)
      ->where('lote_temporal.cantidad', '>', '0')->get();

    $lotes = $lote_almacen->merge($lote_temporal);

    return response()->json($lotes);
  }

  public function obtenerarticulog($id)
  {
    $lote_almacen = DB::table('lote_almacen')
      ->leftJoin('lotes AS L', 'L.id', '=', 'lote_almacen.lote_id')
      ->leftJoin('stocks AS S', 'S.id', '=', 'lote_almacen.stocke_id')
      ->leftJoin('proyectos AS P', 'P.id', '=', 'S.proyecto_id')
      ->leftJoin('articulos AS A', 'A.id', '=', 'lote_almacen.articulo_id')

      ->select('lote_almacen.id', 'P.nombre', 'A.descripcion AS adescripcion', 'lote_almacen.cantidad')
      ->where('lote_almacen.id', '=', $id)
      ->first();
    return response()->json($lote_almacen);
  }

  public function obtenerarticuloa($id)
  {
    $lote_temporal = DB::table('lote_temporal')
      ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'lote_temporal.lote_almacen_id')
      ->leftJoin('lotes AS L', 'L.id', '=', 'LA.lote_id')
      ->leftJoin('requisiciones AS R', 'R.id', '=', 'lote_temporal.requisicion_id')
      ->leftJoin('articulos AS A', 'A.id', '=', 'LA.articulo_id')
      ->select('lote_temporal.lote_almacen_id AS id', 'lote_temporal.cantidad', 'lote_temporal.id AS lt_id', 'lote_temporal.comentario AS nombre', 'A.descripcion AS adescripcion')
      ->where('lote_temporal.id', '=', $id)->first();
    return response()->json($lote_temporal);
  }

  public function eliminarPartida($id)
  {

    try
    {



      $partidas_salidas = \App\Partidas::join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
        ->select('la.*', 'partidas.cantidad AS cantidad_salida')
        ->where('partidas.id', $id)->first();

      $stock_articulos = \App\StockArticulo::where('articulo_id', $partidas_salidas->articulo_id)
        ->where('stocke_id', $partidas_salidas->stocke_id)->first();
      $cantidadrestastoke = $stock_articulos->cantidad + $partidas_salidas->cantidad_salida;
      $stock_articulos->cantidad = $cantidadrestastoke;
      $stock_articulos->update();

      $existencias = \App\Existencia::where('id_lote', '=', $partidas_salidas->lote_id)->where('articulo_id', '=', $partidas_salidas->articulo_id)->first();
      $cantidadrestaexistencia = $existencias->cantidad + $partidas_salidas->cantidad_salida;
      $existencias->cantidad = $cantidadrestaexistencia;
      $existencias->update();

      $movimiento = \App\Movimiento::where('lote_id', $partidas_salidas->id)
        ->where('tipo_movimiento', 'Salida')
        ->where('cantidad', $partidas_salidas->cantidad_salida)->delete();

      $lote_almacen = \App\LoteAlmacen::where('id', $partidas_salidas->id)->first();
      $lote_almacen->cantidad = $lote_almacen->cantidad + $partidas_salidas->cantidad_salida;
      $lote_almacen->update();

      $partidas_salidas = \App\Partidas::where('id', $id)->delete();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }
}
