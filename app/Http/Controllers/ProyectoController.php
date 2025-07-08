<?php

namespace App\Http\Controllers;

use App\Exports\Proyectos\ProyectosExport;
use Illuminate\Http\Request;
use App\Proyecto;
use App\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use \App\Http\Helpers\Utilidades;
use App\Requisicion;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ProyectoController extends Controller
{
  /**
   * [protected Se definen las reglas para guardado y actualizacion de registro]
   * @var [type]
   */
  protected $rules = array(
    'nombre' => 'required|max:500',

  );
  /**
   * [index Obtiene todos los registros del modelo Proyectos]
   * @return Response [lista los proyectos por categoria y subcategoria]
   */
  public function index()
  {
    $arreglo = [];
    /*Obtiene los registros de Proyectos*/
    $proyectos = DB::table('proyectos')
      ->join('usuario_categoria as s', 'proyectos.proyecto_subcategorias_id', '=', 's.proyecto_subcategoria_id')
      ->select('proyectos.*')
      ->where(['s.user_id' => Auth::user()->id])
->where("proyectos.condicion",1)
      ->orderBy('proyectos.id', 'asc')->get();

    foreach ($proyectos as $key => $proyecto)
    {

      $arreglo[] =
        [
          'proyecto' => $proyecto,
        ];
    }

    return response()->json($arreglo);
    /***********************************/
  }

  /**
   ** OBTIENE TODOS LOS PROYECTOS Y DEVUELVE ID Y NOMBRE CORTO PARA SE AGREGADO EN UN VUE SELECT EN LA VISTA
   **/
  public function getAll()
  {
    $proyectos = DB::table('proyectos')
      ->select('proyectos.id', 'proyectos.nombre_corto AS name')
      ->orderBy('proyectos.id', 'asc')
      ->get();

    return response()->json($proyectos);
  }
  /**
   * [index Obtiene todos los registros del modelo Proyectos]
   * @return Response [lista los proyectos por categoria y subcategoria]
   */
  public function proyectosPermisos()
  {
    /*Obtiene los registros de Proyectos*/
    $proyectos = DB::table('proyectos')
      ->join('usuario_categoria as s', 'proyectos.proyecto_subcategorias_id', '=', 's.proyecto_subcategoria_id')
      ->select('proyectos.*')
      ->where(['s.user_id' => Auth::user()->id])
      ->orderBy('proyectos.id', 'asc')->get();

    return response()->json($proyectos);
    /***********************************/
  }
  /**
   * [todos Obtiene todos los registros del modelo Proyectos]
   * @return Response [lista todos los proyectos]
   */
  public function todos()
  {
    $proyectos = Proyecto::orderBy('id', 'asc')->get();
    $arreglo = [];
    foreach ($proyectos as $key => $proyecto)
    {
      $arreglo[] =
        [
          'proyecto' => $proyecto,
        ];
    }

    return response()->json($arreglo);
  }

  /**
   * [listar Obtiene todos los registros del modelo Proyectos]
   * @return Response [lista los proyectos por categoria y subcategoria]
   */
  public function listar()
  {

    try
    {
      $aux_proyectos = DB::table('proyectos as p')
        ->join('usuario_categoria as s', 'p.proyecto_subcategorias_id', 's.proyecto_subcategoria_id')
        ->select('p.*')
        ->where('s.user_id', Auth::user()->id)
        // ->where("p.condicion", 1) // Activos
        ->orderBy('p.nombre')
        ->get();
      $proyectos = [];
      foreach ($aux_proyectos as $p)
      {
        // Buscar si tiene requi
        $n_requi = Requisicion::where("proyecto_id", $p->id)
          ->count();
        if ($n_requi > 0)
          $proyectos[] = $p;
      }
      return response()->json($proyectos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proyectos");
    }
  }

  public function listarTodos()
  {
    try
    {
      $proyectos = Proyecto::leftJoin('proyecto_subcategorias AS PS', 'PS.id', '=', 'proyectos.proyecto_subcategorias_id')
        ->leftJoin('proyecto_categorias AS PC', 'PC.id', '=', 'PS.proyecto_categoria_id')
        ->select('proyectos.*', 'PC.nombre AS nombre_categoria')->orderBy('proyectos.id')
        ->get();
      return response()->json($proyectos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proyectos");
    }
  }

  /**
   * Obtiene todos los proyectos con el total de oc creadas
   */
  public function ObtenerProyectos()
  {
    try
    {
      // Proyectos
      $proyectos = DB::table("proyectos as p")
        ->leftJoin('proyecto_subcategorias as ps', 'ps.id', 'p.proyecto_subcategorias_id')
        ->leftJoin('proyecto_categorias as pc', 'pc.id', 'ps.proyecto_categoria_id')
        ->select(
          'p.*',
          'pc.nombre as nombre_categoria',
          DB::raw("0 as total_oc"),
          DB::raw("0 as total_req"),
          DB::raw("condicion=1 as activo")
        )
        ->orderBy("activo","desc")
        ->orderBy('p.nombre_corto')
        ->get();
      foreach ($proyectos as $p)
      {
        $total_requis = DB::table("requisiciones as r")
          ->where("r.proyecto_id", $p->id)->count();
        $total_oc = DB::table("ordenes_compras as oc")
          ->where("oc.proyecto_id", $p->id)->count();
        $p->total_oc = $total_oc;
        $p->total_req = $total_requis;
      }
      return Status::Success("proyectos", $proyectos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proyectos");
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  public function subirDocumento(Request $request)
  {
    try
    {
      //obtiene el nombre del archivo y su extension
      $DocumentoNE = $request->file('documento_po')->getClientOriginalName();
      //Obtiene el nombre del archivo
      $DocumentoNombre = pathinfo($DocumentoNE, PATHINFO_FILENAME);
      //obtiene la extension
      $DocumentoExt = $request->file('documento_po')->getClientOriginalExtension();
      //nombre que se guarad en BD
      $DocumentoStore = $DocumentoNombre . '_' . uniqid() . '.' . $DocumentoExt;
      //Subida del archivo al servidor ftp
      Storage::disk('local')->put('Archivos/' . $DocumentoStore, fopen($request->file('documento_po'), 'r+'));

      $proyecto = new \App\ProyectoDocumentos();
      $proyecto->proyecto_id = $request->id;
      $proyecto->documento = $DocumentoStore;
      Utilidades::auditar($proyecto, $proyecto->id);
      $proyecto->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function getDocumentosProyectos($id)
  {
    $proyecto = \App\ProyectoDocumentos::where('proyecto_id', $id)->where('condicion', '1')->get();
    return response()->json($proyecto);
  }
  public function deleteDocTempProyectos($id)
  {
    $proyecto = \App\ProyectoDocumentos::where('proyecto_id', $id)->where('condicion', '1')->get();
    foreach ($proyecto as $key => $value)
    {
      Storage::disk('temporal')->delete($value->documento);
    }
    return response()->json(['status' => true]);
  }

  public function deleteDocumentosProyectos($id)
  {
    $proyecto = \App\ProyectoDocumentos::where('id', $id)->first();
    $proyecto->condicion = 0;
    Utilidades::auditar($proyecto, $proyecto->id);
    $proyecto->save();
    return response()->json(['status' => true]);
  }
  /**
   * [Guarda en BD los registros de la tabla proyectos y en la tabla stock guarda el nombre_corto de la tabla proyectos,
   * guarda el documento_po en la tabla proyectos]
   * @param Request   $request [Objeto de datos del POST]
   * @return Response          [Array con estatus true]
   */

  public function store(Request $request)
  {
    try
    {
      DB::beginTransaction();
      if ($request->metodo == 0)
      {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails())
        {
          return response()->json(array(
            'status' => false,
            'errors' => $validator->errors()->all()
          ));
        }
        else
        {
          /*Inserta un nuevo registro en la BD*/
          $proyecto = new Proyecto();
          $datos = LimpiarInput::LimpiarCampos($request->all(), [
            "nombre", "nombre_corto", "cotizacion", "ciudad",  "pm_cliente", "pm_interno", "clave"
          ]);
          $proyecto->fill($datos);
          $proyecto->empleado_registra_id = Auth::user()->empleado_id;
          Utilidades::auditar($proyecto, $proyecto->id);
          $proyecto->save();
          Auditoria::AuditarCambios($proyecto);

          $stock = new Stock();
          $stock->nombre = $proyecto->nombre_corto;
          $stock->proyecto_id = $proyecto->id;
          Utilidades::auditar($stock, $stock->id);
          $stock->condicion = '1';
          $stock->save();
          Auditoria::AuditarCambios($stock);

          // Folio
          $n = DB::table("proyectos")
            ->where("created_at", ">", "2023-09-25")
            ->whereNotNull("folio")
            ->count() + 1;
          $anio = substr(date("Y"), -2);
          $folio = "CSFW-$anio-" . $request->cliente_id . "-" . str_pad($n, 3, "0", STR_PAD_LEFT);
          $proyecto->folio = $folio;
          $proyecto->update();
        }
      }

      if ($request->metodo == 1)
      {
        $DocumentoStore = null;
        //*Variables para actualizar nuevos archivos y eliminar existentes
        $ValorDocumento = null;
        $proyectos = Proyecto::where('id', $request->id)->get();

        foreach ($proyectos as $key => $item)
        {
          $ValorDocumento = $item->documento_po;

          $DocumentoStore = $item->documento_po;
        }
        //*FIN

        //obtiene el nombre del archivo y su extension
        $DocumentoNE = $request->file('documento_po')->getClientOriginalName();
        //Obtiene el nombre del archivo
        $DocumentoNombre = pathinfo($DocumentoNE, PATHINFO_FILENAME);
        //obtiene la extension
        $DocumentoExt = $request->file('documento_po')->getClientOriginalExtension();
        //nombre que se guarad en BD
        $DocumentoStore = $DocumentoNombre . '_' . uniqid() . '.' . $DocumentoExt;
        //Subida del archivo al servidor ftp
        Storage::disk('local')->put('Archivos/' . $DocumentoStore, fopen($request->file('documento_po'), 'r+'));
        if ($ValorDocumento != null)
        {
          //Elimina el archivo en el servidor si requiere ser actualizado
          Utilidades::ftpSolucionEliminar($ValorDocumento);
        }
        $proyecto = Proyecto::findOrFail($request->id);
        $proyecto->documento_po = $DocumentoStore;
        $proyecto->save();
      }

      if ($request->metodo == 2)
      {
        $DocumentoStore = null;
        //*Variables para actualizar nuevos archivos y eliminar existentes
        $ValorDocumento = null;
        $proyectos = Proyecto::where('id', $request->id)->get();

        foreach ($proyectos as $key => $item)
        {
          $ValorDocumento = $item->documento_po;

          $DocumentoStore = $item->documento_po;
        }
        //*FIN

        //obtiene el nombre del archivo y su extension
        $DocumentoNE = $request->file('documento_po')->getClientOriginalName();
        //Obtiene el nombre del archivo
        $DocumentoNombre = pathinfo($DocumentoNE, PATHINFO_FILENAME);
        //obtiene la extension
        $DocumentoExt = $request->file('documento_po')->getClientOriginalExtension();
        //nombre que se guarad en BD
        $DocumentoStore = $DocumentoNombre . '_' . uniqid() . '.' . $DocumentoExt;
        //Subida del archivo al servidor ftp
        Storage::disk('local')->put('Archivos/' . $DocumentoStore, fopen($request->file('documento_po'), 'r+'));
        if ($ValorDocumento != null)
        {
          //Elimina el archivo en el servidor si requiere ser actualizado
          Utilidades::ftpSolucionEliminar($ValorDocumento);
        }
        $proyecto = Proyecto::findOrFail($request->id);
        $proyecto->documento_po = $DocumentoStore;
        $proyecto->save();
      }
      DB::commit();

      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "registrar el proyecto");
    }
  }

  /**
   * [Consulta en BD de la tabla proyecto donde id = $id proporcionado]
   * @param  Int      $id [id del GET]
   * @return Response     [Array en formato JSON]
   */
  public function show($id)
  {
    return response()->json(Proyecto::findOrFail($id));
  }

  /**
   * [Consulta en BD que condicion tiene el Proyecto]
   *
   * @param  Int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $proyecto = Proyecto::findOrFail($id);
    if ($proyecto->condicion == 0 || $proyecto->condicion == 2 || $proyecto->condicion == 3)
    {
      $proyecto->condicion = 1;
    }
    else
    {
      $proyecto->condicion = 0;
      $this->cierreProyecto($proyecto);
    }
    Auditoria::AuditarCambios($proyecto);
    $proyecto->update();


    return $proyecto;
  }

  /**
   * [Consulta en BD si el proyecto es condicion=2 esta en Pausa]
   *
   * @param  Int  $id
   * @return \Illuminate\Http\Response
   */

  public function pausar($id)
  {
    $proyecto = Proyecto::findOrFail($id);
    $proyecto->condicion = 2;
    // $this->cierreProyecto($proyecto);
    Auditoria::AuditarCambios($proyecto);
    $proyecto->update();
    return $proyecto;
  }
  /**
   * [Consulta en BD si el proyecto es condicion=3 fue rechazado]
   *
   * @param  Int  $id
   * @return \Illuminate\Http\Response
   */
  public function rechazar($id)
  {
    $proyecto = Proyecto::findOrFail($id);
    $proyecto->condicion = 3;
    $this->cierreProyecto($proyecto);
    Auditoria::AuditarCambios($proyecto);
    $proyecto->update();
    return $proyecto;
  }

  /**
   * [cierreProyecto Pasa al stoke general al  desactivar el proyecto]
   * @param  Datos   $datos [description]
   * @return Response        [description]
   */
  public function cierreProyecto($datos)
  {
    $proyecto_id = $datos->id;
    $Requisicion = \App\Requisicion::where('proyecto_id', '=', $proyecto_id)->get()->first();
    if (!is_null($Requisicion))
    {
      if ($Requisicion->condicion == 0)
      {
        $Requisicion->condicion = 1;
      }
      else
      {
        $Requisicion->condicion = 0;

        $stocke = \App\Stock::where('proyecto_id', '=', $proyecto_id)->get()->first();
        $stocke_id = $stocke->id;
        $stock_articulos = \App\StockArticulo::where('stocke_id', '=', $stocke_id)->get()->first();
        if (!is_null($stock_articulos))
        {
          $stock_articulos->stocke_id = 1; //por default debe ser 1 al perteccer al proyecto General
          $stock_articulos->update();
          $hoy = date("Y-m-d");
          $hora = date("H:i:s");
          $movimientos = \App\Movimiento::where('stocke_id', '=', $stocke_id)->get();
          foreach ($movimientos as $key => $movimiento)
          {
            $movientos_traspaso = new \App\Movimiento();
            $movientos_traspaso->cantidad = $movimiento->cantidad;
            $movientos_traspaso->fecha = $hoy;
            $movientos_traspaso->hora = $hora;
            $movientos_traspaso->tipo_movimiento = 'TraspasoSG';
            $movientos_traspaso->folio = 'Traspaso -' . $movimiento->proyecto_id;
            $movientos_traspaso->lote_id = $movimiento->lote_id;
            $movientos_traspaso->stocke_id = 1; //por default debe ser 1 al pertenecer al proyecto General
            $movientos_traspaso->almacene_id = $movimiento->almacene_id;
            $movientos_traspaso->stand_id = $movimiento->stand_id;
            $movientos_traspaso->nivel_id = $movimiento->nivel_id;
            $movientos_traspaso->articulo_id = $movimiento->articulo_id;
            $movientos_traspaso->save();
            Auditoria::AuditarCambios($movientos_traspaso);
          }
        }
      }
      Auditoria::AuditarCambios($Requisicion);
      $Requisicion->update();
    }




    return response()->json(array(
      'status' => true
    ));
  }

  /**
   * [Actualiza en BD los registros de la tabla proyectos apartir de un id proporcionado respetando
   * las reglas definidas]
   * @param  Request  $request [Objeto de datos del PUT]
   * @param  Int      $id      [id del PUT]
   * @return Response          [Array con estatus true]
   */
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), $this->rules);

    if ($validator->fails())
    {
      return response()->json(array(
        'status' => false,
        'errors' => $validator->errors()->all()
      ));
    }
    else
    {

      $proyecto = Proyecto::findOrFail($id);
      $datos = LimpiarInput::LimpiarCampos($request->all(), [
        "nombre", "nombre_corto", "cotizacion", "ciudad",  "pm_cliente", "pm_interno",
      ]);
      $proyecto->fill($datos);
      Auditoria::AuditarCambios($proyecto);
      $proyecto->update();

      //$stock = Stock::where('proyecto_id','=',$id)->first();
      //$stock->nombre = $request->nombre_corto;
      //$stock->save();

      return response()->json(array('status' => true));
    }

    // $stock = Stock::findOrFail($id);
    // $stock->nombre = $proyecto->nombre_corto;
    // $stock->proyecto_id = $proyecto->id;
    // $stock->condicion = '1';
    // $stock->update();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }


  /**
   * [Ordena y limita los datos de la busqueda con los filtros ]
   * @param  Arrray $datos            [Array recibido en la función]
   * @return String                    [filtra los datos]
   */
  public function busqueda($datos)
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = $datos;

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

  /**
   * [Obtiene los archivos del servidor ftp id = $id proporcionado]
   * @param  Int      $id [id del GET]
   * @return Response     [Array en formato JSON]
   */

  public function obtenerarchivos(Request $request)
  {
    // $valores = explode("&",$id);
    // $validador = $valores[0];
    // $nombre_archivo = $valores[1];

    // if ($validador == 1) {
    // Se obtiene el archivo del FTP serve
    $proyecto = \App\ProyectoDocumentos::where('id', $request->id)->first();

    $archivo = Utilidades::ftpSolucion($proyecto->documento);
    // Se coloca el archivo en una ruta local
    Storage::disk('temporal')->put($proyecto->documento, $archivo);
    // }

    // if ($validador == 2) {
    //elimina de la ruta local el archivo descargado
    // Storage::disk('cotizaciones')->delete($nombre_archivo);
    // }
    return response()->json($proyecto->documento);
  }

  public function proyectosMaster()
  {
    // code...
    $master = DB::table('proyectos')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'proyectos.proyecto_subcategorias_id')
      ->select('proyectos.*')
      ->whereIn('ps.proyecto_categoria_id', ['1', '2'])
      ->get();
    return response()->json($master);
  }

  public function proyectocompra()
  {
    $proyectos = DB::table('proyectos')
      ->join('usuario_categoria as s', 'proyectos.proyecto_subcategorias_id', '=', 's.proyecto_subcategoria_id')
      ->select('proyectos.*')
      ->where(['s.user_id' => Auth::user()->id])
      ->orderBy('proyectos.id', 'asc')->get();


    return response()->json($proyectos);
  }

  public function listarTodosCompras()
  {
    $proyectos = DB::table('proyectos')
      ->leftJoin('proyecto_subcategorias AS PS', 'PS.id', '=', 'proyectos.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS PC', 'PC.id', '=', 'PS.proyecto_categoria_id')
      ->select('proyectos.*', 'PC.nombre AS nombre_categoria')->orderBy('proyectos.fecha_inicio', 'desc');
    return $proyectos;
  }

  public function buscarcompras()
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = $this->listarTodosCompras();
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
    $results = $data->get();
    // $count = sizeof($results);


    return [
      'data' => $results,
      'count' => $count,
    ];
  }

  public function buscar()
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = $this->Joins();
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
    $results = $data->get();
    // $count = sizeof($results);


    return [
      'data' => $results,
      'count' => $count,
    ];
  }

  /**
   * [Aquis e definen todos los JOINS para cada consilata partiendo de la tabla padre que esta almacenado en la variable $data]
   * @param Array  $data [Array recibido en la función]
   * @param String $arreglo_final  [Cadena concatenada]
   */


  public function Joins()
  {
    // $arreglo_final = [];

    $proyectos = DB::table('proyectos')
      ->join('usuario_categoria as s', 'proyectos.proyecto_subcategorias_id', '=', 's.proyecto_subcategoria_id')
      ->select('proyectos.*')
      ->where(['s.user_id' => Auth::user()->id]);

    return $proyectos;
  }

  public function centrocostos()
  {
    $proyectos = Proyecto::orderBy('id', 'asc')
      // ->leftJoin('proyectos as p','p.id','=','proyectos.proyecto_id')
      ->select('proyectos.*');

    return $proyectos;
  }

  public function Descargar()
  {
    try
    {
      ob_end_clean(); // this
      ob_start(); // and this
      return Excel::download(new ProyectosExport(), 'Proyectos.xlsx');
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
