<?php

namespace App\Http\Controllers\Compras;

use Illuminate\Http\Request;
use App\Compras;
use App\ComprasModels\Proveedor;
use App\CondicionPago;
use App\Proyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Utilidades;
use Maatwebsite\Excel\Facades\Excel;
use App\Requisicionhasordencompras;
use App\Partidas;
use App\Exports\GeneralComprasExport;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Impuesto;
use App\PartidaRe;
use App\PartidasDatosBancariosOC;
use App\RHModels\Empleado;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class ComprasController extends Controller
{
  /**
   * [protected Reglas para el guardado y actualizacion en la BD]
   * @var [type]
   */
  protected $rules = array(
    // 'folio' => 'required|max:30',
    'condicion_pago_id' => 'required|max:30',
    'periodo_entrega' => 'required|max:50',
    'lugar_entrega' => 'required|max:125',
    'proyecto_id' => 'required',
    'proveedore_id' => 'required',
    'moneda' => 'required',
    // 'referencia' => 'required|max:30',
  );

  /**
   * [index consulta de la tabla requisicion_has_ordencompras]
   * @param  Request $request [description]
   * @param  [int]  $id      [description]
   * @return [Response]           [description]
   */
  public function index(Request $request, $id)
  {
    $proyecto = Compras::join('proyectos AS p', 'p.id', '=', 'ordenes_compras.proyecto_id')
      ->where('ordenes_compras.id', '=', $id)->first();
    $compras_vehi = [];
    $compras_art = DB::table('requisicion_has_ordencompras')
      ->leftJoin('requisiciones AS req', 'req.id', '=', 'requisicion_has_ordencompras.requisicione_id')
      ->leftJoin('ordenes_compras AS oc', 'oc.id', '=', 'requisicion_has_ordencompras.orden_compra_id')
      ->leftJoin('articulos AS a', 'a.id', '=', 'requisicion_has_ordencompras.articulo_id')
      ->leftJoin('proyectos AS p', 'p.id', '=', 'req.proyecto_id')
      ->leftJoin('costos_proyectos_servicios AS CPS', 'CPS.requisicion_has_ordencompra_id', '=', 'requisicion_has_ordencompras.id')
      ->join('partidas_requisiciones', function ($join)
      {
        $join->on('requisicion_has_ordencompras.requisicione_id', '=', 'partidas_requisiciones.requisicione_id')
          ->on('requisicion_has_ordencompras.articulo_id', '=', 'partidas_requisiciones.articulo_id');
      })
      ->select(
        'requisicion_has_ordencompras.*',
        DB::raw('requisicion_has_ordencompras.cantidad * requisicion_has_ordencompras.precio_unitario as total'),
        'req.id AS rid',
        'req.folio AS rf',
        'requisicion_has_ordencompras.comentario',
        'req.fecha_solicitud AS rfs',
        'p.nombre_corto AS proyecton',
        'a.id AS aid',
        DB::raw("concat_ws(' ',a.nombre,a.descripcion) as ad"),
        'CPS.catalogo_centro_costos_id'
      )
      ->where('requisicion_has_ordencompras.orden_compra_id', '=', $id)
      ->where('requisicion_has_ordencompras.articulo_id', '!=', 'null')
      ->distinct()
      ->get()->toArray();
    $compras_serv = DB::table('requisicion_has_ordencompras')
      ->leftJoin('requisiciones AS req', 'req.id', '=', 'requisicion_has_ordencompras.requisicione_id')
      ->leftJoin('ordenes_compras AS oc', 'oc.id', '=', 'requisicion_has_ordencompras.orden_compra_id')
      ->leftJoin('servicios AS s', 's.id', '=', 'requisicion_has_ordencompras.servicio_id')
      ->leftJoin('proyectos AS p', 'p.id', '=', 'req.proyecto_id')
      ->leftJoin('costos_proyectos_servicios AS CPS', 'CPS.requisicion_has_ordencompra_id', '=', 'requisicion_has_ordencompras.id')
      ->join('partidas_requisiciones', function ($join)
      {
        $join->on('requisicion_has_ordencompras.requisicione_id', '=', 'partidas_requisiciones.requisicione_id')
          ->on('requisicion_has_ordencompras.servicio_id', '=', 'partidas_requisiciones.servicio_id');
      })
      ->select(
        'requisicion_has_ordencompras.*',
        DB::raw('requisicion_has_ordencompras.cantidad * requisicion_has_ordencompras.precio_unitario as total'),
        'req.id AS rid',
        'req.folio AS rf',
        'req.fecha_solicitud AS rfs',
        'p.nombre_corto AS proyecton',
        's.id AS aid',
        'requisicion_has_ordencompras.comentario',
        's.nombre_servicio AS ad',
        'CPS.catalogo_centro_costos_id'
      )
      ->where('requisicion_has_ordencompras.orden_compra_id', '=', $id)
      ->where('requisicion_has_ordencompras.servicio_id', '!=', 'null')
      ->get()->toArray();

    if ($proyecto->nombre_corto === 'MANTENIMIENTO VEHICULAR')
    {
      $compras_vehi = DB::table('requisicion_has_ordencompras')
        ->leftJoin('requisiciones AS req', 'req.id', '=', 'requisicion_has_ordencompras.requisicione_id')
        ->leftJoin('ordenes_compras AS oc', 'oc.id', '=', 'requisicion_has_ordencompras.orden_compra_id')
        ->leftJoin('cat_mantenimiento_vehiculos AS v', 'v.id', '=', 'requisicion_has_ordencompras.vehiculo_id')
        ->leftJoin('proyectos AS p', 'p.id', '=', 'req.proyecto_id')
        ->leftJoin('costos_proyectos_servicios AS CPS', 'CPS.requisicion_has_ordencompra_id', '=', 'requisicion_has_ordencompras.id')
        ->select(
          'requisicion_has_ordencompras.*',
          DB::raw('requisicion_has_ordencompras.cantidad * requisicion_has_ordencompras.precio_unitario as total'),
          'req.id AS rid',
          'req.folio AS rf',
          'req.fecha_solicitud AS rfs',
          'p.nombre_corto AS proyecton',
          'v.id AS aid',
          'v.descripcion AS ad',
          'CPS.catalogo_centro_costos_id'
        )
        ->where('requisicion_has_ordencompras.orden_compra_id', '=', $id)
        ->where('requisicion_has_ordencompras.vehiculo_id', '!=', 'null')
        ->get()->toArray();
      // $compras = array_merge($compras_art,$compras_serv,$compras_vehi);

    }
    elseif ($proyecto->nombre_corto === 'VEHÍCULOS')
    {
      $compras_vehi = DB::table('requisicion_has_ordencompras')
        ->leftJoin('requisiciones AS req', 'req.id', '=', 'requisicion_has_ordencompras.requisicione_id')
        ->leftJoin('ordenes_compras AS oc', 'oc.id', '=', 'requisicion_has_ordencompras.orden_compra_id')
        ->leftJoin('vehiculos AS v', 'v.id', '=', 'requisicion_has_ordencompras.vehiculo_id')
        ->leftJoin('proyectos AS p', 'p.id', '=', 'req.proyecto_id')
        ->leftJoin('costos_proyectos_servicios AS CPS', 'CPS.requisicion_has_ordencompra_id', '=', 'requisicion_has_ordencompras.id')
        ->select(
          'requisicion_has_ordencompras.*',
          DB::raw('requisicion_has_ordencompras.cantidad * requisicion_has_ordencompras.precio_unitario as total'),
          'req.id AS rid',
          'req.folio AS rf',
          'req.fecha_solicitud AS rfs',
          'p.nombre_corto AS proyecton',
          'v.id AS aid',
          'v.descripcion AS ad',
          'CPS.catalogo_centro_costos_id'
        )
        ->where('requisicion_has_ordencompras.orden_compra_id', '=', $id)
        ->where('requisicion_has_ordencompras.vehiculo_id', '!=', 'null')
        ->get()->toArray();
      // $compras = array_merge($compras_art,$compras_serv,$compras_vehi);

    }

    $compras = array_merge($compras_art, $compras_serv, $compras_vehi);

    $total = $this->ObtenerTotal($id);
    $ordenCompra = Compras::findOrFail($id);
    $ordenCompra->total = $total;
    $ordenCompra->total_aux = (floatval(str_replace(',', '', $total)));
    Utilidades::auditar($ordenCompra, $ordenCompra->id);
    $ordenCompra->save();

    return response()->json($compras);
  }

  /*
  * Función que recibe un parámetro id y retorna un total, que es la suma de los precios de los articulos de cada partida y sus respectivos impuestos
  * pertenecientes a la orden de compra del id que recibe
  */
  /**
   * [ObtenerTotal description]
   * @param [type] $id [description]
   */
  public function ObtenerTotal($id)
  {
    $subtotal_info = 0;
    $subtotal_info_dos = 0;
    $suma_partidas_compras = Requisicionhasordencompras::where('orden_compra_id', '=', $id)->select(DB::raw("SUM(cantidad * precio_unitario) AS subtotal"))->first();
    $subtotal_info_dos = $suma_partidas_compras->subtotal;

    $orden_compras  = Compras::select('descuento')->where('id', $id)->first();

    if ($orden_compras->descuento > 0)
    {
      $subtotal_info = (($suma_partidas_compras->subtotal / 100) * $orden_compras->descuento);
      $subtotal_info_dos =  $suma_partidas_compras->subtotal - (($suma_partidas_compras->subtotal / 100) * $orden_compras->descuento);
    }

    $impuestos = Impuesto::where('orden_compra_id', '=', $id)->get();

    $totalesimpuestos = array();
    $totalesimpuestos_retenidos = array();
    if (count($impuestos) != 0)
    {
      foreach ($impuestos as $key => $value)
      {
        if ($value->retenido == 1)
        {
          $totalim = ($subtotal_info_dos / 100) * $value->porcentaje;
          array_push($totalesimpuestos_retenidos, $totalim);
        }
        if ($value->retenido == 0)
        {
          $totalim = ($subtotal_info_dos / 100) * $value->porcentaje;
          array_push($totalesimpuestos, $totalim);
        }
      }
    }
    $total = ($subtotal_info_dos + array_sum($totalesimpuestos)) - array_sum($totalesimpuestos_retenidos);
    $total = number_format($total, 2);
    return $total;
  }

  /*
  * Función que recibe un parámetro id y retorna un arreglo en el cual se asocia el total de la compra consultada y el proveedor de la compra
  * pertenecientes a la orden de compra del id que recibe
  */
  public function ConsultarTotal($id)
  {
    $compra = Compras::where('id', '=', $id)->first();
    $proveedor = Proveedor::where('id', '=', $compra->proveedore_id)->first();
    $arreglo[] = [
      'compra' => $this->ObtenerTotal($id),
      'limitecredito' => $proveedor->limite_credito,
    ];
    return response()->json($arreglo);
  }

  /*
  * Función que recibe un request donde es acedido y envia parametro del post para poder descargar el archivo que se esta solicitando en la peticiónote
  * y guardarlo de manera local
  */
  public function update(Request $request)
  {
    if ($request->metodo == 1)
    {
      // Se obtiene el archivo del  serve

      $archivo = Utilidades::ftpSolucion($request->archivo);
      // Se coloca el archivo en una ruta local
      Storage::disk('descarga')->put($request->archivo, $archivo);
      //--Devuelve la respuesta y descarga el archivo--//
      return response()->download(storage_path() . '/app/descargas/' . $request->archivo);
    }

    if ($request->metodo == 0)
    {
      //elimina de la ruta local el archivo descargado
      Storage::disk('descarga')->delete($request->archivo);
      Storage::disk('local')->delete($request->archivo);
    }
  }

  /*
  * Función que recibe un parámetro que es una petición request por la cual es accedido y se envian los parametro del post retornando una status true
  * si las condiciones son cumplidas correctamente, en este caso el de agregar o actualizar una compra, dependiendo del metodo que se envia en el request
  * ademas de agregar impuestos si se tiene, guardar la compra como paga no recurrente y de guardar el formato correspondiente solo en la opción de actualizar
  */
  public function store(Request $request)
  {
    try
    {
      // dd($request->proveedore_id);
      $proyecto = Proyecto::findOrFail($request->proyecto_id);
      if ($proyecto->condicion != 1)
      {
        return response()->json(array(
          'status' => false,
          'errors' => ['El proyecto no esta activo.']
        ));
      }

      $tipos = explode(',', $request->tipos);
      $porcentaje = explode(',', $request->porcentaje);
      $retenido = explode(',', $request->retenido);
      if (!$request->ajax()) return redirect('/');

      $validator = Validator::make($request->all(), $this->rules);

      // Activar proveedor si estaba desactivado
      $proveedor = Proveedor::find($request->proveedore_id);
      if ($proveedor->condicion == 0)
      {
        $proveedor->condicion = 1;
        $proveedor->update();
      }

      // if ($validator->fails())
      // {
      //   return response()->json(array(
      //     'status' => false,
      //     'errors' => $validator->errors()->all()
      //   ));
      // }

      if ($request->metodo == "Nuevo")
      {
        DB::beginTransaction();
        // Obtener el ID del proyecto
        $folio = Utilidades::getFolio('OC-CONSERFLOW', $request->proyecto_id);
        $compra = new Compras();
        $compra->folio = $folio;
        $compra->proyecto_id = $request->proyecto_id;
        $compra->condicion_pago_id = $request->condicion_pago_id;
        $compra->periodo_entrega = $request->periodo_entrega;
        $compra->fecha_orden = $request->fecha_orden;
        $compra->lugar_entrega = $request->lugar_entrega;
        $compra->observaciones = $request->observaciones;
        $compra->descuento = $request->descuento;
        $compra->total = $request->total;
        $compra->tipo_cambio = $request->tipo_cambio;
        $compra->moneda = $request->moneda;
        $compra->referencia = $request->referencia;
        $compra->cie = $request->cie;
        $compra->sucursal = $request->sucursal;
        $compra->proveedore_id = $request->proveedore_id;
        $compra->proveedore_csct_id = $request->proveedore_csct_id == '' ? $request->proveedore_id :  $request->proveedore_csct_id;
        $compra->elabora_empleado_id = $request->elabora_empleado_id;
        $compra->autoriza_empleado_id = $request->autoriza_empleado_id;
        $compra->comentario_condicion_pago = $request->comentario_condicion_pago;
        $compra->conrequisicion = $request->conrequisicion;
        if ($request->fecha_probable_pago != '')
        {
          $compra->fecha_probable_pago = $request->fecha_probable_pago;
          $compra->prioridad = $request->prioridad;
        }
        Utilidades::auditar($compra, $compra->id);
        $compra->save();
        Auditoria::AuditarCambios($compra);

        $pdboc = new PartidasDatosBancariosOC();
        $pdboc->banco = $request->banco;
        $pdboc->clabe = $request->clabe;
        $pdboc->cuenta = $request->cuenta;
        $pdboc->orden_compra_id = $compra->id;
        $pdboc->titular = $request->titular;
        Utilidades::auditar($pdboc, $pdboc->id);
        $pdboc->save();
        //
        // if () {
        //   // code...
        // }

        for ($i = 0; $i < $request->tamanio; $i++)
        {
          $impueto = new Impuesto();
          $impueto->orden_compra_id = $compra->id;
          $impueto->tipo = $tipos[$i];
          $impueto->porcentaje = $porcentaje[$i];
          $impueto->retenido = $retenido[$i];
          Utilidades::auditar($impueto, $impueto->id);
          $impueto->save();
        }

        $total = $this->ObtenerTotal($compra->id);
        $ordenCompra_tot = Compras::findOrFail($compra->id);
        $ordenCompra_tot->total = $total;
        $ordenCompra_tot->total_aux = (floatval(str_replace(',', '', $total)));
        $ordenCompra_tot->save();
        Auditoria::AuditarCambios($ordenCompra_tot);

        DB::commit();
        return response()->json(array(
          'status' => true,
          'CONTENIDO' => $request->cotizacionesID,
          'compra' => $compra,
        ));
      }

      if ($request->metodo == "Actualizar")
      {
        DB::beginTransaction();
        /*ACTUALIZAR REGISTRO*/
        //Variables de archivo
        $FacturaStore = null;
        //*Variables para actualizar nuevos archivos y eliminar existentes
        $ValorFactura = null;
        $compras = Compras::where('id', $request->id)->get();

        foreach ($compras as $key => $item)
        {
          $ValorFactura = $item->ordenes_formato;
          $FacturaStore = $item->ordenes_formato;
        }
        //*FIN

        //--Si el request no contiene archivos, solo se actualizan los campos listados--//
        if (!$request->hasFile('ordenes_formato'))
        {

          $ordenCompra = Compras::findOrFail($request->id);
          $ordenCompra->folio = $request->folio;
          $ordenCompra->ordenes_formato = $FacturaStore;
          $ordenCompra->condicion_pago_id = $request->condicion_pago_id;
          $ordenCompra->periodo_entrega = $request->periodo_entrega;
          $ordenCompra->fecha_orden = $request->fecha_orden;
          $ordenCompra->lugar_entrega = $request->lugar_entrega;
          $ordenCompra->observaciones = $request->observaciones === 'null' ? NULL : $request->observaciones;
          $ordenCompra->descuento = $request->descuento;
          $ordenCompra->tipo_cambio = $request->tipo_cambio;
          $ordenCompra->moneda = $request->moneda;
          $ordenCompra->referencia = $request->referencia === 'null' ? NULL : $request->referencia;
          $ordenCompra->cie = $request->cie === 'null' ? NULL : $request->cie;
          $ordenCompra->sucursal = $request->sucursal === 'null' ? NULL : $request->sucursal;
          $ordenCompra->proveedore_id = $request->proveedore_id;
          $ordenCompra->proveedore_csct_id = $request->proveedore_csct_id == '' ? $request->proveedore_id :  $request->proveedore_csct_id;
          // $ordenCompra->proveedore_csct_id = $request->proveedore_csct_id;
          $ordenCompra->estado_id = $request->estado_id;
          $ordenCompra->elabora_empleado_id = $request->elabora_empleado_id;
          $ordenCompra->autoriza_empleado_id = $request->autoriza_empleado_id;
          $ordenCompra->comentario_condicion_pago = $request->comentario_condicion_pago === 'null' ? NULL : $request->comentario_condicion_pago;
          if ($request->fecha_probable_pago != '')
          {
            $ordenCompra->fecha_probable_pago = $request->fecha_probable_pago;
            $ordenCompra->prioridad = $request->prioridad;
          }
          Utilidades::auditar($ordenCompra, $ordenCompra->id);
          Auditoria::AuditarCambios($ordenCompra);
          Auditoria::AuditarCambios($ordenCompra);
          $ordenCompra->update();

          $pdboc_ = PartidasDatosBancariosOC::where('orden_compra_id', $request->id)->first();
          // $pdboc_ = new PartidasDatosBancariosOC();
          if (isset($pdboc_) == true)
          {
            // code...
            $pdboc_->banco = $request->banco;
            $pdboc_->clabe = $request->clabe;
            $pdboc_->cuenta = $request->cuenta;
            $pdboc_->orden_compra_id = $request->id;
            $pdboc_->titular = $request->titular;
            Utilidades::auditar($pdboc_, $pdboc_->id);
            $pdboc_->update();
          }
          // return response()->json($pdboc_);

          for ($i = 0; $i < $request->tamanio; $i++)
          {
            $impueto = new Impuesto();
            $impueto->orden_compra_id = $ordenCompra->id;
            $impueto->tipo = $tipos[$i];
            $impueto->porcentaje = $porcentaje[$i];
            $impueto->retenido = $retenido[$i];
            Utilidades::auditar($impueto, $impueto->id);
            $impueto->save();
          }

          $total = $this->ObtenerTotal($ordenCompra->id);
          $ordenCompra_tot = Compras::findOrFail($ordenCompra->id);
          $ordenCompra_tot->total = $total;
          $ordenCompra_tot->total_aux = (floatval(str_replace(',', '', $total)));
          Utilidades::auditar($ordenCompra, $ordenCompra->id);
          $ordenCompra_tot->save();

          DB::commit();
          return response()->json(array(
            'status' => true,
            // 'cuenta' => count($cotizaciones),
            'observaciones' => $request->cie
          ));
        }
      }
    }
    catch (\Throwable $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
      return response()->json(array('status' => 'error', "mensaje" => $e->getMessage()));
    }
  }

  /*
  * Función que recibe un parametro id y retorna un status true si las condiciones son ejecutadas correctamente
  */
  public function edit($id)
  {
    $compra = Compras::findOrFail($id);
    if ($compra->condicion == 0)
    {
      $compra->condicion = 1;
    }
    else
    {
      $compra->condicion = 0;
    }
    $compra->update();
    return response()->json(array('status' => true));
  }

  /*
  * Función que recibe un objeto request que contiene la ruta de acceso a ala funcion que retorna un arreglo de la consulta de las
  * todas las compras que existen
  */
  public function show($id)
  {
    $arreglo = [];
    $compras = Compras::orderBy('id', 'asc')
      ->join('proveedores AS p', 'p.id', '=', 'ordenes_compras.proveedore_id')
      // ->join('cotizaciones AS c', 'c.id', '=', 'ordenes_compras.cotizacione_id')
      ->join('estado_compras AS ec', 'ec.id', '=', 'ordenes_compras.estado_id')
      ->join('empleados AS ee', 'ee.id', '=', 'ordenes_compras.elabora_empleado_id')
      ->join('empleados AS ea', 'ea.id', '=', 'ordenes_compras.autoriza_empleado_id')
      ->join('condicion_pago AS CP', 'CP.id', '=', 'ordenes_compras.condicion_pago_id')
      ->join('proyectos AS pro', 'pro.id', '=', 'ordenes_compras.proyecto_id')
      ->select(
        'ordenes_compras.*',
        'p.nombre as pnom',
        'p.razon_social as prz',
        'CP.nombre AS nombre_condicion_pago',
        'pro.nombre as nombre_proyecto',
        'pro.nombre_corto AS nombre_corto_proyecto',
        DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS nombre_empleado_elabora"),
        DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS nombre_empleado_autoriza")
      )
      ->where('ordenes_compras.proyecto_id', '=', $id)
      ->get();
    dd($compras);
    foreach ($compras as $key => $compra)
    {
      $arreglo[] = [
        'compra' => $compra,
      ];
    }
    return response()->json($arreglo);
  }

  /*
  * Función que recibe un parametro id y retorna un json de los impuestos que pertenecientes a la orden de compra del id que recibe
  */
  public function impuesto($id)
  {
    $impuestos = Impuesto::where('orden_compra_id', '=', $id)->get();
    return response()->json($impuestos);
  }

  /*
  * Función que recibe un parametro id y retorna un status true al eliminar impuestos que pertenecientes al del id que recibe
  */
  public function impuestoeliminar($id)
  {
    $impuestos = Impuesto::where('id', '=', $id)->first();
    $impuestos->delete();
    return response()->json(array('status' => true));
  }

  /*
  * Función que retorna un json de todas la condiciones de pago existentes
  */
  public function condicionpago()
  {
    $condicionpago = DB::table('condicion_pago')->select('condicion_pago.*')->get();
    return response()->json($condicionpago);
  }

  /**
   * Busca en las requisiciones con estado_id = 6 (Autorizar por compras)
   */
  public function Requisicionesrecibir()
  {
    try
    {
      $requisiciones = DB::table('requisiciones')
        ->leftJoin('proyectos AS p', 'p.id', 'requisiciones.proyecto_id')
        ->leftJoin('empleados AS es', 'es.id', 'requisiciones.solicita_empleado_id')
        ->leftJoin('empleados AS ea', 'ea.id', 'requisiciones.autoriza_empleado_id')
        ->select(
          'requisiciones.*',
          'p.nombre_corto AS nombrep',
          DB::raw("CONCAT_WS(' ',es.nombre,es.ap_paterno,es.ap_materno) AS nombre_solicita"),
          DB::raw("CONCAT_WS(' ',ea.nombre,ea.ap_paterno,ea.ap_materno) AS nombre_autoriza")
        )
        ->where('requisiciones.estado_id', '6')
        ->get();
      return Status::Success("requisiciones", $requisiciones);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las requisiciones por autorizar");
    }
  }

  /**
   * [Query del lado del servidor de el modelo Articulo]
   * @return Array [Array que contiene data y count]
   */
  public function busqueda($id)
  {
    extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

    $data = $this->getDataC($id);
    // Compras::select(
    //   [
    //     'id','folio','ordenes_formato','condicion_pago_id','periodo_entrega','fecha_orden','lugar_entrega','observaciones','descuento','total','moneda','tipo_cambio','referencia','cie','sucursal',
    //     'proyecto_id','proveedore_id','estado_id','elabora_empleado_id','autoriza_empleado_id','condicion','comentario_condicion_pago','conrequisicion','fecha_probable_pago','prioridad'
    //     ])->where('proyecto_id',$id)->orderBy('folio', 'ASC');
    // $data->orderBy('folio', 'ASC');

    if (isset($query) && $query)
    {
      $data = $byColumn == 1 ?
        $this->busqueda_filterByColumn($data, $query) :
        $this->busqueda_filter($data, $query, $fields);
    }

    $count = $data->count();

    $data->limit($limit)
      ->skip($limit * ($page - 1));

    // if (isset($orderBy)) {
    //
    //   $direction = $ascending == 1 ? 'ASC' : 'DESC';
    //   $data->orderBy($orderBy, $direction);
    // }


    // leftJoin('tipo_calidad AS TC','TC.id','=','articulos.calidad_id')
    // ->
    $results = $this->Joins($data->get());
    //$results = $data->get();
    return [
      'data' => $results,
      'count' => $count,
      // 'orderby' => $d,
    ];
  }
  /**
   * [Consulta en la BD el nombre y descripcion del tipo de calidad]
   * @param Array  $data [Array recibido en la función]
   * @param String $arreglo_final  [Cadena concatenada]
   */

  public function getDataC($id)
  {
    $compras = Compras::leftJoin('pagos_no_recurrentes AS pnr', 'pnr.ordenes_comp_id', '=', 'ordenes_compras.id')
      ->leftJoin('proveedores AS p', 'p.id', '=', 'ordenes_compras.proveedore_id')
      ->leftJoin('proveedores AS pcsct', 'pcsct.id', '=', 'ordenes_compras.proveedore_csct_id')
      ->leftJoin('condicion_pago AS cp', 'cp.id', '=', 'ordenes_compras.condicion_pago_id')
      ->leftJoin('proyectos AS pro', 'pro.id', '=', 'ordenes_compras.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'pro.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->leftJoin('empleados AS ee', 'ee.id', '=', 'ordenes_compras.elabora_empleado_id')
      ->leftJoin('empleados AS ea', 'ea.id', '=', 'ordenes_compras.autoriza_empleado_id')
      ->select(
        'ordenes_compras.*',
        'pnr.rango_dias',
        'pnr.eventos',
        'p.nombre AS proveedor_nombre',
        'p.razon_social AS proveedor_razon_social',
        'cp.nombre AS nombre_condicion_pago',
        'pro.nombre AS nombre_proyeto',
        'pc.nombre as nombre_categoria',
        'pro.nombre_corto as nombre_corto_proyecto',
        'pcsct.razon_social AS proveedor_razon_social_csct',
        DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS nombre_empleado_elabora"),
        DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS nombre_empleado_autoriza")
      )
      ->where('ordenes_compras.proyecto_id', $id)->orderBy('ordenes_compras.id', 'DESC');

    return $compras;
  }

  public function Joins($data)
  {
    $arreglo_final = [];
    foreach ($data as $key => $value)
    {
      $pdboc = PartidasDatosBancariosOC::where('orden_compra_id', $value->id)->first();
      $proveedores = Proveedor::where('id', $value->proveedore_id)->first();
      $condicion_pago = CondicionPago::where('id', $value->condicion_pago_id)->first();
      $proyecto = Proyecto::leftJoin('proyecto_subcategorias AS PS', 'PS.id', '=', 'proyecto_subcategorias_id')
        ->leftJoin('proyecto_categorias AS PC', 'PC.id', '=', 'PS.proyecto_categoria_id')
        ->select('proyectos.*', 'PC.nombre AS nombre_categoria')
        ->where('proyectos.id', $value->proyecto_id)->first();
      $empleado_elabora = Empleado::select(DB::raw("CONCAT(nombre,' ',ap_paterno,' ',ap_materno) AS nombre_empleado_elabora"))->where('id', $value->elabora_empleado_id)->first();
      $empleado_autoriza = Empleado::select(DB::raw("CONCAT(nombre,' ',ap_paterno,' ',ap_materno) AS nombre_empleado_autoriza"))->where('id', $value->autoriza_empleado_id)->first();

      $arreglo_final[] = array_merge($value->toArray(), [
        'rango_dias' =>  0,
        'pagos' => 0,
        'proveedor_nombre' => $proveedores->nombre,
        'proveedor_razon_social' => $proveedores->razon_social,
        'nombre_condicion_pago' => isset($condicion_pago) == false ? '' : $condicion_pago->nombre,
        'nombre_proyeto' => $proyecto->nombre,
        'nombre_categoria' => $proyecto->nombre_categoria,
        'nombre_corto_proyecto' => $proyecto->nombre_corto,
        'nombre_empleado_elabora' => isset($empleado_elabora) == true ? $empleado_elabora->nombre_empleado_elabora : 0,
        'nombre_empleado_autoriza' => isset($empleado_autoriza) == true ? $empleado_autoriza->nombre_empleado_autoriza : 0,
        'datos_bancarios' => $pdboc,
      ]);
    }
    return $arreglo_final;
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

  public function calculo_dia_entrega($data)
  {
    try
    {
      $fecha_orden = $data->fecha_orden;
      $periodo_entrega = $data->periodo_entrega;
      $valores = explode(' ', $periodo_entrega);
      $dias = 0;

      if (count($valores) == 2)
      {
        if ($valores[1] === 'día' || $valores[1] === 'días' || $valores[1] === 'dias' || $valores[1] === 'dia' || $valores[1] === 'DIAS' || $valores[1] === 'DIA')
        {
          $dias = $valores[0];
        }
        elseif ($valores[1] === 'semana' || $valores[1] === 'semanas' || $valores[1] === 'SEMANAS' || $valores[1] === 'SEMANA')
        {
          $dias = $valores[0] * 7;
        }
      }

      $fecha = date("Y-m-d", strtotime($fecha_orden . "+ " . $dias . " days"));

      return $fecha;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function buscarocpf($id)
  {
    $compras = Compras::where('proyecto_id', $id)->orderBy('folio', 'asc')->get();
    $arreglo = [];
    foreach ($compras as $key => $value)
    {
      $facturas = DB::table('facturas_entradas')->where('orden_compra_id', $value->id)->where('total_factura', '!=', '')->get();

      $pagos = DB::table('pagos_no_recurrentes')
        ->Join('pagos_compras AS pc', 'pc.pagos_no_recurrentes_id', '=', 'pagos_no_recurrentes.id')
        ->select('pc.*')
        ->where('pagos_no_recurrentes.ordenes_comp_id', $value->id)
        ->get();

      $arreglo[] = [
        'oc' => $value,
        'facturas' => $facturas,
        'pagos' => $pagos,
      ];
    }
    return response()->json($arreglo);
  }
  public function compraArticulo(Request $request)
  {

    $acfw = Requisicionhasordencompras::leftJoin('articulos AS a', 'a.id', '=', 'requisicion_has_ordencompras.articulo_id')
      ->join('ordenes_compras AS oc', 'oc.id', '=', 'requisicion_has_ordencompras.orden_compra_id')
      ->join('proveedores AS p', 'p.id', 'oc.proveedore_id')
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('requisicion_has_ordencompras.comentario', 'LIKE', '%' . $request->articulo . '%')
      ->select(
        'oc.id AS ids',
        'oc.fecha_orden AS fecha_orden',
        'p.razon_social',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        'requisicion_has_ordencompras.comentario AS comentario',
        'oc.folio AS folio',
        'requisicion_has_ordencompras.id AS rhc_id',
        'requisicion_has_ordencompras.cantidad AS cantidad',
        'requisicion_has_ordencompras.precio_unitario',
        'oc.moneda'
      )
      ->get()->toArray();

    $compras = array_merge($acfw);

    return response()->json($compras);
  }


  public function LoteArticulo(Request $request)
  {
    $la = DB::table('lote_almacen AS la')
      ->join('articulos AS a', 'a.id', 'la.articulo_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras As rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', '=', 'rhoc.orden_compra_id')
      ->where('la.cantidad', '>', 0)
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('rhoc.comentario', 'LIKE', '%' . $request->articulo . '%')
      ->select(
        'oc.id AS ids',
        'oc.fecha_orden AS fecha_orden',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        'rhoc.comentario AS comentario',
        'oc.folio AS folio',
        'rhoc.id AS rhc_id',
        'la.cantidad',
        'rhoc.precio_unitario',
        'la.id AS lote_id',
        'a.id AS articulo_id',
        'rhoc.requisicione_id'
      )
      ->get();

    // if ($request->tipo == 1) {
    //   // code...
    //   $la = DB::select("SELECT oc.id AS ids,oc.fecha_orden AS fecha_orden,a.nombre AS nombre, a.descripcion AS descr ,a.unidad AS unidad,rhoc.comentario AS comentario,oc.folio AS folio,rhoc.id AS rhc_id,la.cantidad,rhoc.precio_unitario,la.id AS lote_id,l.id AS lotes_id,a.id AS articulo_id,rhoc.requisicione_id FROM lote_almacen AS la join articulos AS a on a.id = la.articulo_id join lotes AS l on l.id = la.lote_id join partidas_entradas AS pe on pe.id = l.entrada_id join requisicion_has_ordencompras AS rhoc on rhoc.id = pe.req_com_id join ordenes_compras AS oc on oc.id = rhoc.orden_compra_id where la.cantidad > 0 AND (a.nombre LIKE '%$request->articulo%' OR a.descripcion LIKE '%$request->articulo%' OR rhoc.comentario LIKE '%$request->articulo%')");
    // }elseif ($request->tipo == 2) {
    //   $la = DB::select("SELECT oc.id AS ids,oc.fecha_orden AS fecha_orden,a.nombre AS nombre, a.descripcion AS descr ,a.unidad AS unidad,rhoc.comentario AS comentario,oc.folio AS folio,rhoc.id AS rhc_id,la.cantidad,rhoc.precio_unitario,la.id AS lote_id,l.id AS lotes_id,a.id AS articulo_id,rhoc.requisicione_id FROM lote_almacen AS la join articulos AS a on a.id = la.articulo_id join lotes AS l on l.id = la.lote_id join partidas_entradas AS pe on pe.id = l.entrada_id join requisicion_has_ordencompras AS rhoc on rhoc.id = pe.req_com_id join ordenes_compras AS oc on oc.id = rhoc.orden_compra_id join proyectos AS p on p.id = oc.proyecto_id where la.cantidad > 0 AND p.id = '$request->proyecto' ");
    // }


    return response()->json($la);
  }

  public function requiArticulo(Request $request)
  {
    $ar = PartidaRe::leftJoin('articulos AS a', 'a.id', '=', 'partidas_requisiciones.articulo_id')
      ->Join('requisiciones AS r', 'r.id', '=', 'partidas_requisiciones.requisicione_id')
      ->Join('proyectos AS p', 'p.id', '=', 'r.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->join('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->whereIn('p.nombre', ['Proyectos', 'Servicios'])
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->where('r.estado_id', '!=', '11')
      ->select('r.id as ids', 'r.folio as folio', 'a.nombre AS nombre', 'a.descripcion AS desc', 'a.unidad as unidad')
      ->get();

    return response()->json($ar);
  }

  public function salidasArticulo(Request $request)
  {
    // Salidas taller
    $st = Partidas::join('salidas AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->Join('proyectos AS p2', 'p2.id', '=', 's.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->whereIn('p.nombre', ['Proyectos', 'Servicios'])
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->where('partidas.tiposalida_id', '1')
      ->select(
        'partidas.cantidad AS cantidad_salida',
        's.id as ids',
        DB::raw("('Taller') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        "p2.nombre_corto as p_salida",
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    // Salidas sitio
    $ss = Partidas::join('salidassitio AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_solicita_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->join('proyectos AS p2', 'p2.id', 's.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->whereIn('p.nombre', ['Proyectos', 'Servicios'])
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->where('partidas.tiposalida_id', '2')
      ->select(
        'partidas.cantidad AS cantidad_salida',
        's.id as ids',
        DB::raw("('Sitio') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        'p2.nombre_corto AS p_salida',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_solicita_id AS empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    $array_uno = array_merge($st, $ss);

    $sr = Partidas::join('salidasresguardo AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_solicita_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->whereIn('p.nombre', ['Proyectos', 'Servicios'])
      ->where('a.nombre', 'LIKE', '%' . $request->articulo . '%')
      ->orWhere('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
      ->where('partidas.tiposalida_id', '3')
      ->select(
        's.id as ids',
        DB::raw("('Resguardo') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_solicita_id AS empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    $array = array_merge($array_uno, $sr);

    return response()->json($array);
  }


  public function salidasProyecto(Request $request)
  {
    // Salidas taller
    $st = Partidas::join('salidas AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->Join('proyectos AS p2', 'p2.id', '=', 's.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->where('s.proyecto_id', $request->proyecto)
      // ->whereIn('p.nombre',['Proyectos','Servicios'])
      // ->where('a.nombre','LIKE','%'.$request->articulo.'%')
      // ->orWhere('a.descripcion','LIKE','%'.$request->articulo.'%')
      ->where('partidas.tiposalida_id', '1')
      ->select(
        'partidas.cantidad AS cantidad_salida',
        's.id as ids',
        DB::raw("('Taller') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        "p2.nombre_corto as p_salida",
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    // Salidas sitio
    $ss = Partidas::join('salidassitio AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_solicita_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->join('proyectos AS p2', 'p2.id', 's.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->where('s.proyecto_id', $request->proyecto)
      // ->whereIn('p.nombre',['Proyectos','Servicios'])
      // ->where('a.nombre','LIKE','%'.$request->articulo.'%')
      // ->orWhere('a.descripcion','LIKE','%'.$request->articulo.'%')
      ->where('partidas.tiposalida_id', '2')
      ->select(
        'partidas.cantidad AS cantidad_salida',
        's.id as ids',
        DB::raw("('Sitio') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        'p2.nombre_corto AS p_salida',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_solicita_id AS empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    $array_uno = array_merge($st, $ss);

    $sr = Partidas::join('salidasresguardo AS s', 's.id', 'partidas.salida_id')
      ->join('empleados AS e', 'e.id', 's.empleado_solicita_id')
      ->join('lote_almacen AS la', 'la.id', 'partidas.lote_id')
      ->join('lotes AS l', 'l.id', 'la.lote_id')
      ->join('partidas_entradas AS pe', 'pe.id', 'l.entrada_id')
      ->join('requisicion_has_ordencompras AS rhoc', 'rhoc.id', 'pe.req_com_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'rhoc.orden_compra_id')
      ->join('articulos AS a', 'a.id', '=', 'la.articulo_id')
      ->join('stocks AS ss', 'ss.id', 'la.stocke_id')
      ->Join('proyectos AS p', 'p.id', '=', 'ss.proyecto_id')
      ->leftJoin('proyecto_subcategorias AS ps', 'ps.id', '=', 'p.proyecto_subcategorias_id')
      ->leftJoin('proyecto_categorias AS pc', 'pc.id', '=', 'ps.proyecto_categoria_id')
      ->where('s.proyecto_id', $request->proyecto)
      // ->whereIn('p.nombre',['Proyectos','Servicios'])
      // ->where('a.nombre','LIKE','%'.$request->articulo.'%')
      // ->orWhere('a.descripcion','LIKE','%'.$request->articulo.'%')
      ->where('partidas.tiposalida_id', '3')
      ->select(
        's.id as ids',
        DB::raw("('Resguardo') AS tipo"),
        's.folio as folio',
        'p.nombre_corto',
        'a.nombre AS nombre',
        'a.descripcion AS desc',
        'a.unidad as unidad',
        's.empleado_solicita_id AS empleado_id',
        DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_solicita"),
        'oc.folio AS oc_folio'
      )
      ->get()->toArray();

    $array = array_merge($array_uno, $sr);

    return response()->json($array);
  }

  /**
   * Descarga el reporte general de las compra del proeycto ingresado
   */
  public function ReportGeneral($ids)
  {
    try
    {
      $ids = explode("&", $ids);
      array_pop($ids);
      ob_end_clean();
      ob_start();
      return Excel::download(new GeneralComprasExport($ids), 'Historico de Compras.xlsx');
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  /**
   * Obtener las OC cerradas para correción de almacén
   */
  public function ObtenerOCCorreccion($p_id)
  {
    try
    {
      $ocs = DB::table("ordenes_compras as oc")
        ->join("proveedores as p", "p.id", "oc.proveedore_id")
        ->select(
          "oc.id",
          "oc.id as aux_id",
          "oc.folio",
          "p.nombre as proveedor",
          "oc.folio",
          "oc.condicion"
        )
        ->where("oc.proyecto_id", $p_id) // Proyecto
        ->where("oc.folio", "like", "OC-%") // NOrmal
        ->orderBy("oc.folio", "desc")
        ->get();
      return Status::Success("ocs", $ocs);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las OC's");
    }
  }

  /**
   * Cerrar la OC
   */
  public function CerrarOC(Request $request)
  {
    try
    {
      $compra = Compras::find($request->id);
      $compra->condicion = 2;
      Auditoria::AuditarCambios($compra);
      $compra->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "cerrar la Orden de Compra");
    }
  }
}
