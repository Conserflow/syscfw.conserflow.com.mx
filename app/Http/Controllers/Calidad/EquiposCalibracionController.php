<?php

namespace App\Http\Controllers\Calidad;

use App\Articulo;
use App\CalidadModels\EquiposCatalogo;
use App\CalidadModels\ServiciosEquiposCalibracion;
use App\Existencia;
use App\Exports\CalibracionReporteExport;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Utilidades;
use App\Lote;
use App\LoteAlmacen;
use App\Movimiento;
use App\PartidaEntrada;
use App\Requisicionhasordencompras;
use App\SistemaModels\AuditoriaCambio;
use App\StockArticulo;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Servicio - Estado
 * 0. Nuevo
 * 1. fecha_servicio Modificado
 * 2. proxima_fecha Modificado
 * 3. Renovar
 */
class EquiposCalibracionController extends Controller
{
  /**
   * Obtener todos los equipos de calibración y el último servicio
   */
  public function index()
  {
    try
    {
      $equipos_calib = DB::table('equipos_catalogo')->get();

      $equipos = [];
      foreach ($equipos_calib as $e)
      {
        $servicio = DB::table('servicios_equipos_calibracion')
          ->where('equipos_catalogo_id', $e->id)->orderBy('id', 'DESC')
          ->first();

        $equipos[] = [
          'equipos' => $e,
          'servicios' => $servicio,
        ];
      }

      return Status::Success("equipos", $equipos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los equipos");
    }
  }

  /**
   * Guardar el equipo de calibración
   */
  public function guardar(Request $request)
  {
    DB::beginTransaction();
    try
    {
      $datos = LimpiarInput::LimpiarCampos($request->all(), [
        "descripcion",
        "marca",
        "modelo",
        "rango_medicion",
        "numero_serie",
        "frecuencia",
        "resguardo",
        "observaciones",
      ]);
      $equipos = new EquiposCatalogo($datos);
      $equipos->condicion = 1; // Activo
      $equipos->empleado_registra_id = Auth::user()->empleado_id;
      $equipos->save();
      Auditoria::AuditarCambios($equipos);

      // Guardar fecha de servicio
      if ($request->fecha_servicio != null || $request->proxima_fecha != null)
      {
        $servicios = new ServiciosEquiposCalibracion();
        $servicios->equipos_catalogo_id = $equipos->id;
        $servicios->fecha_servicio = $request->fecha_servicio;
        $servicios->proxima_fecha = $request->proxima_fecha;
        $servicios->estado = 0;
        $servicios->save();
        Auditoria::AuditarCambios($servicios);
      }

      // Registrar equipo de calib en articulos
      $articulo = new Articulo();
      $articulo->nombre = $request->descripcion . ' Modelo: ' . $request->modelo .
        ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;

      $articulo->descripcion = $request->descripcion . ' Modelo: ' . $request->modelo .
        ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;
      $articulo->marca = $request->marca;
      $articulo->comentarios = $request->descripcion . ' Modelo: ' . $request->modelo .
        ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;
      $articulo->unidad = "EQUIPO";
      $articulo->empleado_registra_id = Auth::user()->empleado_id;
      $articulo->save();
      Auditoria::AuditarCambios($articulo);

      $articulo_id = $articulo->id;
      $cantidad = 1;
      $precio = 0;
      // Registrar id de artículo
      $equipos->articulo_id = $articulo->id;
      $equipos->update();

      // Registrar entrada interna
      $rhoc = new Requisicionhasordencompras();
      $rhoc->requisicione_id = 1;
      $rhoc->orden_compra_id = 1;
      $rhoc->articulo_id = $articulo_id;
      $rhoc->cantidad = $cantidad;
      $rhoc->precio_unitario = $precio;
      $rhoc->tipo_entrada = 'Interna';
      $rhoc->condicion = 0;
      $rhoc->antigua = 0;
      $rhoc->cantidad_entrada = 0;
      $rhoc->save();

      // PartidaEntrada
      $partidaEntrada = new PartidaEntrada();
      $partidaEntrada->entrada_id = 1;
      $partidaEntrada->req_com_id = $rhoc->id;
      $partidaEntrada->articulo_id = $articulo_id;
      $partidaEntrada->validacion_calidad = 0;
      $partidaEntrada->cantidad = $cantidad;
      $partidaEntrada->almacene_id = 1;
      $partidaEntrada->pendiente = 0;
      $partidaEntrada->status = 0;
      $partidaEntrada->precio_unitario = $precio;
      $partidaEntrada->stocke_id = 1;
      Utilidades::auditar($partidaEntrada, $partidaEntrada->id);
      $partidaEntrada->save();

      // Lote
      $lote = new Lote();
      $lote->nombre = "lote 0002-" . $articulo_id;
      $lote->entrada_id = $partidaEntrada->id;
      $lote->articulo_id = $articulo_id;
      $lote->cantidad = 1;
      $lote->save();
      $lote->nombre = ("lote 0002-" . $articulo_id . "-" . $lote->id);
      Utilidades::auditar($lote, $lote->id);
      $lote->save();

      // LoteAlmacen
      $lote_almacen = new LoteAlmacen();
      $lote_almacen->lote_id = $lote->id;
      $lote_almacen->almacene_id = 1;
      $lote_almacen->cantidad = $cantidad;
      $lote_almacen->stocke_id = 1;
      $lote_almacen->articulo_id = $articulo_id;
      $lote_almacen->condicion = 1;
      $lote_almacen->codigo_barras = 'MCF 0001 ' . $articulo_id . ' ' . $lote->id;
      Utilidades::auditar($lote_almacen, $lote_almacen->id);
      $lote_almacen->save();

      // Existencia ??? Crear nueva existencia para cada articulo? (Repetidos?)
      $existencia = new Existencia();
      $existencia->articulo_id = $articulo_id;
      $existencia->almacene_id = 1;
      $existencia->id_lote = $lote->id;
      $existencia->cantidad = $cantidad;
      Utilidades::auditar($existencia, $existencia->id);
      $existencia->save();

      // Movimiento
      $movimiento = new Movimiento();
      $movimiento->cantidad = $cantidad;
      $movimiento->fecha = date("y-m-d");
      $movimiento->hora = date("H:i:s");
      $movimiento->tipo_movimiento = "INV";
      $movimiento->folio = "Entrada-" . 1 . 1;
      $movimiento->proyecto_id = 1;
      $movimiento->lote_id = $lote_almacen->id;
      $movimiento->stocke_id = 1;
      $movimiento->almacene_id = 1;
      $movimiento->articulo_id = $articulo_id;
      Utilidades::auditar($movimiento, $movimiento->id);
      $movimiento->save();

      $stock_articulo = StockArticulo::where("articulo_id", "=", $articulo_id)
        ->where("stocke_id", "=", 1)->first();
      if ($stock_articulo == null)
      {
        // Registrar nuevo
        $nuevo_stock = new StockArticulo();
        $nuevo_stock->cantidad = $cantidad;
        $nuevo_stock->articulo_id = $articulo_id;
        $nuevo_stock->stocke_id = 1;
        Utilidades::auditar($nuevo_stock, $nuevo_stock->id);
        $nuevo_stock->save();
      }
      else
      {
        // Sumar cantidad
        $n = $stock_articulo->cantidad + $cantidad;
        $stk = StockArticulo::where("articulo_id", $articulo_id)
          ->where("stocke_id", 1)->first();
        $stk->cantidad = $n;
        $stk->update();
      }
      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "registrar equipo de calibración");
    }
  }

  /**
   * Actualiza el equipo con los datos ingesados
   */
  public function actualizar(Request $request)
  {
    if (!$request->ajax()) return;
    try
    {
      DB::beginTransaction();
      // Actualizar equipo catalogo
      $equipo = EquiposCatalogo::where('id', $request->id)->first();
      $datos = LimpiarInput::LimpiarCampos($request->all(), [
        "descripcion",
        "marca",
        "modelo",
        "rango_medicion",
        "numero_serie",
        "frecuencia",
        "resguardo",
        "observaciones",
      ]);
      $equipo->fill($datos);
      Auditoria::AuditarCambios($equipo);
      $equipo->update();

      // Actualizar articulo (almacen)
      $articulo = Articulo::where('id', $equipo->articulo_id)->first();
      if ($articulo != null)
      {
        $articulo->nombre = $equipo->descripcion . ' Modelo: ' . $equipo->modelo .
          ' con rango de medición ' . $equipo->rango_medicion . ' Num. Serie ' . $equipo->numer_serie;
        $articulo->descripcion = $equipo->descripcion  . 'Modelo: ' . $equipo->modelo .
          ' con rango de medición ' . $equipo['Rango de Medición'] . ' Num. Serie ' . $equipo->numero_serie;
        $articulo->update();
      }
      else
      {
        $articulo = new Articulo();
        $articulo->nombre = $request->descripcion . ' Modelo: ' . $request->modelo .
          ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;

        $articulo->descripcion = $request->descripcion . ' Modelo: ' . $request->modelo .
          ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;
        $articulo->marca = $request->marca;
        $articulo->comentarios = $request->descripcion . ' Modelo: ' . $request->modelo .
          ' con rango de medición ' . $request->rango_medicion . ' Num. Serie ' . $request->numero_serie;
        $articulo->unidad = "EQUIPO";
        $articulo->empleado_registra_id = Auth::user()->empleado_id;
        Auditoria::AuditarCambios($articulo);
        $articulo->save();
      }
      $equipo->articulo_id = $articulo->id;
      $equipo->update();

      $servicios_buscar = ServiciosEquiposCalibracion::where('id', $request->servicio_id)
        ->first();
      if ($servicios_buscar == null)
      {
        // Nuevo
        $servicio = new ServiciosEquiposCalibracion();
        $servicio->equipos_catalogo_id = $equipo->id;
        $servicio->fecha_servicio = $request->fecha_servicio;
        $servicio->proxima_fecha = $request->proxima_fecha;
        $servicio->estado = 0;
        $servicio->save();
        Auditoria::AuditarCambios($servicio);
      }
      else
      {
        if ($servicios_buscar->fecha_servicio != $request->fecha_servicio)
          $servicios_buscar->estado = 1;
        if ($servicios_buscar->proxima_fecha != $request->proxima_fecha & $servicios_buscar->estado == 1)
          $servicios_buscar->estado = 2;
        if ($servicios_buscar->estado == 2)
        {
          // Nuevo
          $servicios_buscar->estado = 3;
        }
        $servicios_buscar->save();
      }

      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "actualizar el equipo");
    }
  }

  /**
   * Desactivar el equipo
   */
  public function Eliminar(Request $request)
  {
    try
    {
      // Desactivar equipo
      $equipo = EquiposCatalogo::find($request->id);
      $equipo->condicion = 0; // Eliminado
      Auditoria::AuditarCambios($equipo);
      $equipo->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "eliminar el equipo");
    }
  }

  /**
   * Obtener los historiales de calibración del equipo ingresado 
   */
  public function ObtenerResguardos($a_id)
  {
    try
    {
      $resguardos = DB::table('partidas AS p')
        ->join('salidasresguardo AS sr', 'sr.id', 'p.salida_id')
        ->join('proyectos AS pr', 'pr.id', 'sr.proyecto_id')
        ->join('lote_almacen AS la', 'la.id', 'p.lote_id')
        ->join('articulos AS a', 'la.articulo_id', 'a.id')
        ->join("empleados as e1", "e1.id", "sr.empleado_entrega_id")
        ->join("empleados as e2", "e2.id", "sr.empleado_solicita_id")
        ->select(
          "p.cantidad as solicitado",
          "p.cantidad_retorno as retornado",
          "sr.fecha as fecha_solicitud",
          "sr.empleado_entrega_id",
          "sr.empleado_solicita_id",
          "pr.nombre_corto as proyecto",
          DB::raw("concat_ws(' ',e1.nombre,e1.ap_materno,e1.ap_materno) as empleado_entrega"),
          DB::raw("concat_ws(' ',e2.nombre,e2.ap_materno,e2.ap_materno) as empleado_solicita")
        )
        ->where('p.tiposalida_id', '3')
        ->where("a.id", $a_id)
        ->get();
      return Status::Success("resguardos", $resguardos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los resguardos");
    }
  }

  /**
   * Obtiene todos los equipos de calibración con su ultima fecha de servicio
   */
  public function Obtener()
  {
    try
    {
      $equipos = EquiposCatalogo::get();
      foreach ($equipos as $equipo)
      {
        $servicio = DB::table("servicios_equipos_calibracion as sec")
          ->where("equipos_catalogo_id", $equipo->id)
          ->orderBy("id", "desc")
          ->first();
        $proxima_fecha = "N/D";
        $fecha_servicio = "N/D";
        $id = 0;
        $estado = $servicio != null ? $servicio->estado : 0;
        if ($servicio != null)
        {
          $proxima_fecha = $servicio->proxima_fecha;
          $fecha_servicio = $servicio->fecha_servicio;
          $id = $servicio->id;
        }

        $equipo->proxima_fecha = $proxima_fecha;
        $equipo->fecha_servicio = $fecha_servicio;
        $equipo->estado_equipo = $estado;
        $equipo->servicio_id = $id;
        // $aux = (array)$equipo;
        // $aux["estado"] = $servicio->estado;
        // $equipo = (object)$aux;
      }
      return response()->json(["status" => true, "equipos" => $equipos]);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return response()->json(["status" => false, "mensaje" => "Error al obtener los equipos de calibración"]);
    }
  }

  /**
   * Descarga el reporte general de calibración
   */
  public function DescargarReporte()
  {
    try
    {
      ob_end_clean();
      ob_start();
      return Excel::download(new CalibracionReporteExport(), 'PCC-03_F-01 CONTROL DE CALIBRACIÓN DE EQUIPOS.xlsx');
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return "<h5>Error al general el reporte</<h5>";
    }
  }
}
