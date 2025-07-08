<?php

namespace App\Http\Controllers;

use App\Compras;
use App\Entrada;
use App\EntradasPendientes;
use App\Existencia;
use App\Exports\Almacen\EntradasPendientesExport;
use Illuminate\Http\Request;
use App\Http\Helpers\Utilidades;
use App\Lote;
use App\LoteAlmacen;
use App\Movimiento;
use App\PartidaEntrada;
use App\Requisicionhasordencompras;
use App\StockArticulo;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EntradasPendientesController extends Controller
{

  public function get()
  {
    try
    {
      $entradas_pendietes = DB::table('entradas_pendientes AS ep')
        ->join('articulos AS a', 'a.id', 'ep.articulo_id')
        ->join('lote_almacen AS la', 'la.id', 'ep.lote_almacen')
        ->join('stocks AS s', 's.id', 'la.stocke_id')
        ->join('proyectos AS p', 'p.id', 's.proyecto_id')
        ->join('proveedores AS pr', 'pr.id', 'ep.proveedor_id')
        ->select('ep.*', 'la.almacene_id', 'la.nivel_id', 'la.stand_id', 'p.nombre_corto', 'p.id AS proyecto_id', 
        DB::raw("concat_ws(' ',a.nombre,a.descripcion) as descripcion"),
        'pr.razon_social AS proveedor_name')
        ->orderBy('id', 'DESC')
        ->where('ep.cantidad', '>', '0')
        ->get();

      return response()->json($entradas_pendietes);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardar(Request $request)
  {
    try
    {
      DB::beginTransaction();
      // Obtener proyecto
      $db_stock = DB::table('stocks AS s')->where('s.id', $request->stock)->first();

      $ordencompra = 1;
      if ($request->stock != 1)
      {
        // Buscar OC EP
        $compra = Compras::where('folio', 'LIKE', 'EP-OC-' . $db_stock->nombre)->first();
        if ($compra)
        {
          $ordencompra = $compra->id;
        }
        else
        {
          $compra_new = new Compras();
          $compra_new->folio = 'EP-OC-' . $db_stock->nombre;
          $compra_new->proyecto_id = $db_stock->proyecto_id;
          $compra_new->proveedore_id = 1;
          $compra_new->estado_id = 3;
          $compra_new->elabora_empleado_id = 6;
          $compra_new->autoriza_empleado_id = 205;
          $compra_new->condicion = 2;
          Utilidades::auditar($compra_new, $compra_new->id);
          $compra_new->save();

          $ordencompra = $compra_new->id;
        }
      }

      $rhoc = new Requisicionhasordencompras();
      $rhoc->requisicione_id = 1;
      $rhoc->orden_compra_id = $ordencompra;
      $rhoc->articulo_id = $request->articulo_id;
      $rhoc->cantidad = $request->cantidad;
      $rhoc->precio_unitario = 0;
      $rhoc->tipo_entrada = 'Pend';
      $rhoc->condicion = 0;
      $rhoc->antigua = 0;
      $rhoc->cantidad_entrada = 0;
      Utilidades::auditar($rhoc, $rhoc->id);
      $rhoc->save();

      $entradaid = 1;
      if ($request->stock != 1)
      {
        $entrada = Entrada::where('comentarios', 'LIKE', 'EP-OC-' . $db_stock->nombre)->first();
        if (isset($entrada) == true)
        {
          $entradaid = $entrada->id;
        }
        else
        {
          $entrada_new = new Entrada();
          $entrada_new->fecha = date('Y-m-d');
          $entrada_new->comentarios = 'EP-OC-' . $db_stock->nombre;
          $entrada_new->formato_entrada = '0002';
          $entrada_new->tipo_entrada_id = 1; // Entrada pendiente
          $entrada_new->empleado_calidad_id = 3;
          $entrada_new->empleado_almacen_id = 3;
          $entrada_new->empleado_usuario_id = 147;
          $entrada_new->condicion = 1;
          $entrada_new->orden_compra_id = $ordencompra;
          Utilidades::auditar($entrada_new, $entrada_new->id);
          $entrada_new->save();

          $entradaid = $entrada_new->id;
        }
      }

      // PArtidaEntrada
      $partidaEntrada = new PartidaEntrada();
      $partidaEntrada->entrada_id = $entradaid;
      $partidaEntrada->req_com_id = $rhoc->id;
      $partidaEntrada->articulo_id = $request->articulo_id;
      $partidaEntrada->validacion_calidad = 0;
      $partidaEntrada->cantidad = $request->cantidad;
      $partidaEntrada->almacene_id = 1;
      $partidaEntrada->pendiente = 0;
      $partidaEntrada->status = 0;
      $partidaEntrada->precio_unitario = 0;
      $partidaEntrada->stocke_id = $db_stock->id;
      Utilidades::auditar($partidaEntrada, $partidaEntrada->id);
      $partidaEntrada->save();

      // Lote
      $lote = new Lote();
      $lote->nombre = "lote 0003-" . $request->articulo_id;
      $lote->entrada_id = $partidaEntrada->id;
      $lote->articulo_id = $request->articulo_id;
      $lote->cantidad = $request->cantidad;
      Utilidades::auditar($lote, $lote->id);
      $lote->save();
      $lote->nombre = ("lote 0003-" . $request->articulo_id . "-" . $lote->id);
      $lote->save();

      // LoteAlmacen
      $lote_almacen = new LoteAlmacen();
      $lote_almacen->lote_id = $lote->id;
      $lote_almacen->almacene_id = 1;
      $lote_almacen->cantidad = $request->cantidad;
      $lote_almacen->stocke_id = $db_stock->id;
      $lote_almacen->articulo_id = $request->articulo_id;
      $lote_almacen->condicion = 1;
      $lote_almacen->codigo_barras = 'MCF 0003 ' . $request->articulo_id . ' ' . $lote->id;
      Utilidades::auditar($lote_almacen, $lote_almacen->id);
      $lote_almacen->save();


      // Existencia ??? Crear nueva existencia para cada articulo? (Repetidos?)
      $existencia = new Existencia();
      $existencia->articulo_id = $request->articulo_id;
      $existencia->almacene_id = 1;
      $existencia->id_lote = $lote->id;
      $existencia->cantidad = $request->cantidad;
      Utilidades::auditar($existencia, $existencia->id);
      $existencia->save();

      // Movimiento
      $movimiento = new Movimiento();
      $movimiento->cantidad = $request->cantidad;
      $movimiento->fecha = date("y-m-d");
      $movimiento->hora = date("H:i:s");
      $movimiento->tipo_movimiento = "PEN";
      $movimiento->folio = "Entrada-" . 1 . 1;
      $movimiento->proyecto_id = 1;
      $movimiento->lote_id = $lote_almacen->id;
      $movimiento->stocke_id = $db_stock->id;
      $movimiento->almacene_id = 1;
      $movimiento->articulo_id = $request->articulo_id;
      Utilidades::auditar($movimiento, $movimiento->id);
      $movimiento->save();

      $entrada_pendiente = new EntradasPendientes();
      $entrada_pendiente->articulo_id = $request->articulo_id;
      $entrada_pendiente->rhoc_id = $rhoc->id; //Requisicionhasordencompras
      $entrada_pendiente->lote_almacen = $lote_almacen->id; //LoteAlmacen
      $entrada_pendiente->existencia_id = $existencia->id; //Existencia
      $entrada_pendiente->partida_entrada_id = $partidaEntrada->id; //PartidaEntrada
      $entrada_pendiente->movimiento_id = $movimiento->id;
      $entrada_pendiente->cantidad = $request->cantidad;
      $entrada_pendiente->proveedor_id = $request->proveedor_id;
      $entrada_pendiente->folio = $request->folio;
      $entrada_pendiente->save();
      Auditoria::AuditarCambios($entrada_pendiente);

      $stock_articulo = StockArticulo::where("articulo_id", "=", $request->articulo_id)
        ->where("stocke_id", "=", $db_stock->id)->first();
      if ($stock_articulo == null)
      {
        // Registrar nuevo
        $nuevo_stock = new StockArticulo();
        $nuevo_stock->cantidad = $request->cantidad;
        $nuevo_stock->articulo_id = $request->articulo_id;
        $nuevo_stock->stocke_id = $db_stock->id;
        Utilidades::auditar($nuevo_stock, $nuevo_stock->id);
        $nuevo_stock->save();
      }
      else
      {
        // Sumar cantidad
        $n = $stock_articulo->cantidad + $request->cantidad;
        $stk = StockArticulo::where("articulo_id", "=", $request->articulo_id)
          ->where("stocke_id", "=", $db_stock->id)->first();
        $stk->cantidad = $n;
        $stk->update();
      }

      DB::commit();
      return response()->json(['status' => true]);
    }
    catch (Exception $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
      return response()->json(["status" => false, "mensaje" => "Error al generar entrada interna"]);
    }
  }

  public function changeAlmacen(Request $request)
  {
    try
    {

      $entrada = DB::table('entradas_pendientes AS ep')->where('ep.id', $request->id)->first();

      DB::beginTransaction();
      $lote_almacen = LoteAlmacen::where('id', $entrada->lote_almacen)->first();
      $lote_almacen->almacene_id = $request->almacene_id;
      $lote_almacen->nivel_id = $request->nivel_id;
      $lote_almacen->stand_id = $request->stand_id;
      Utilidades::auditar($lote_almacen, $lote_almacen->id);
      $lote_almacen->save();

      $existencia = Existencia::where('id', $entrada->existencia_id)->first();
      $existencia->almacene_id = 1;
      $existencia->nivel_id = $request->nivel_id;
      $existencia->stand_id = $request->stand_id;
      Utilidades::auditar($existencia, $existencia->id);
      $existencia->save();

      $partidaEntrada = PartidaEntrada::where('id', $entrada->partida_entrada_id)->first();;
      $partidaEntrada->almacene_id = $request->almacene_id;
      $partidaEntrada->nivel_id = $request->nivel_id;
      $partidaEntrada->stand_id = $request->stand_id;
      Utilidades::auditar($partidaEntrada, $partidaEntrada->id);
      $partidaEntrada->save();
      DB::commit();

      return response()->json(['status' => true]);
    }
    catch (Exception $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
    }
  }

  public function delete($id)
  {
    try
    {
      $entrada = DB::table('entradas_pendientes AS ep')->where('ep.id', $id)->first();

      $lote_almacen = DB::table('lote_almacen AS la')->where('la.id', $entrada->lote_almacen)->first();

      $stock_articulo = StockArticulo::where("articulo_id", "=", $entrada->articulo_id)
        ->where("stocke_id", "=", $lote_almacen->stocke_id)->first();

      if ($lote_almacen->cantidad > 0)
      {

        $n = $stock_articulo->cantidad - $entrada->cantidad;
        DB::beginTransaction();
        $stk = StockArticulo::where("articulo_id", "=", $entrada->articulo_id)
          ->where("stocke_id", "=", $lote_almacen->stocke_id)->first();
        $stk->cantidad = $n;
        $stk->update();

        $rhoc = Requisicionhasordencompras::where('id', $entrada->rhoc_id)->delete();
        $lote = Lote::where('id', $lote_almacen->lote_id)->delete();
        $movimiento = Movimiento::where('lote_id', $entrada->lote_almacen)->delete();
        $lote_almacen_d = LoteAlmacen::where('id', $entrada->lote_almacen)->delete();
        $existencia = Existencia::where('id', $entrada->existencia_id)->delete();
        $partidaEntrada = PartidaEntrada::where('id', $entrada->partida_entrada_id)->delete();
        $entrada_pendiente = EntradasPendientes::where('id', $id)->delete();;

        DB::commit();

        return response()->json(['status' => true, 'estado' => 1]);
      }
      else
      {
        return response()->json(['status' => true, 'estado' => 2]);
      }
    }
    catch (Exception $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
    }
  }

  /**
   * Buscar conceptos de OC, para asociar con la EP
   */
  public function asociar($id)
  {
    $valores = explode('&', $id);

    $data = Requisicionhasordencompras:: //whereIn('oc.proveedore_id', [$valores[0], '1'])
      where('oc.proyecto_id', $valores[1])
      ->where('oc.proyecto_id', '!=', '1')
      // ->orWhere('oc.proveedore_id','1')
      ->join('articulos AS a', 'a.id', 'requisicion_has_ordencompras.articulo_id')
      ->join('ordenes_compras AS oc', 'oc.id', 'requisicion_has_ordencompras.orden_compra_id')
      ->select('requisicion_has_ordencompras.*', 'a.descripcion', 'oc.folio')
      ->where('requisicion_has_ordencompras.condicion', '1')
      ->get();

    return response()->json($data);
  }

  /**
   * Realizar asociacion de la EP con la OC
   */
  public function guardarAsc(Request $request)
  {
    try
    {
      DB::beginTransaction();
      // Crear nueva entrada y mover partidas_entradas a la nueva
      $rhoc = Requisicionhasordencompras::join('ordenes_compras AS oc', 'oc.id', 'requisicion_has_ordencompras.orden_compra_id')
        ->where('requisicion_has_ordencompras.id', $request->poc)
        ->select('requisicion_has_ordencompras.*', 'oc.folio')
        ->first();

      $cantidad = $rhoc->cantidad - $request->cantidad;
      if ($cantidad < 0)
      {
        DB::rollBack();
        return Status::Error2("Cantidad de compra superada: $cantidad");
      }
      elseif ($cantidad == 0)
      {
        $rhoc->cantidad_entrada = 0;
        $rhoc->condicion = 0;
        $rhoc->save();
      }
      elseif ($cantidad > 0)
      {
        $rhoc->cantidad_entrada = $cantidad;
        $rhoc->save();
      }

      // Buscar requi de EP y actualizar precio con datos de poc
      $ep = EntradasPendientes::where('id', $request->ep_id)->first();
      // Actualizar precio
      $requi_real = Requisicionhasordencompras::find($request->poc); // Real
      $requi_pend = Requisicionhasordencompras::find($ep->rhoc_id); // Pendiente
      $requi_pend->precio_unitario = $requi_real->precio_unitario;
      $requi_pend->update();

      $c = $ep->cantidad - $request->cantidad;
      $ep->folio = $rhoc->folio;
      $ep->rhoc_id = $request->poc; // actualizar rhoid -> request-rhoid
      $ep->cantidad = $c;
      Auditoria::AuditarCambios($ep);
      $ep->update();

      $folio_oc = DB::table('ordenes_compras AS oc')->where('oc.folio', 'LIKE', '%' . $rhoc->folio . '%')->first();
      $user = Auth::user();
      $lote_almacen = DB::table('lote_almacen AS la')->where('la.id', $ep->lote_almacen)->first();
      $date = date('Y-m-d');
      $hora = date("H:i:s");
      $entrada = Entrada::where('fecha', $date)->where('orden_compra_id', $folio_oc->id)->first();
      if ($entrada == null)
      {
        $entrada = new Entrada();
        $entrada->fecha = $date;
        $entrada->comentarios = 'Traspaso EP-OC';
        $entrada->formato_entrada = $this->getFolioEntrada($folio_oc->id);
        $entrada->tipo_entrada_id = 9; // Entrada por asociacion
        $entrada->empleado_almacen_id = $user->empleado_id;
        $entrada->empleado_usuario_id = 147;
        $entrada->orden_compra_id = $rhoc->orden_compra_id; // OC Real
        $entrada->save();
      }

      // Buscar partidaentrada de la ep
      $partidaentrada = PartidaEntrada::find($ep->partida_entrada_id);
      $partidaentrada->entrada_id = $entrada->id;
      // Actualizar entrada.id
      $partidaentrada->update();

      $existencias = new Existencia();
      $existencias->id_lote = $lote_almacen->lote_id;
      $existencias->articulo_id = $rhoc->articulo_id;
      $existencias->almacene_id = $lote_almacen->almacene_id;
      $existencias->nivel_id = $lote_almacen->nivel_id;
      $existencias->stand_id = $lote_almacen->stand_id;
      $existencias->cantidad = $request->cantidad;
      // $existencias->save();

      // Movimiento
      $movimiento = new Movimiento();
      $movimiento->cantidad = (float) $request->cantidad;
      $movimiento->fecha = $date;
      $movimiento->hora = $hora;
      $movimiento->tipo_movimiento = 'Entrada Pendiente';
      $movimiento->folio = 'EP-' . $entrada->id;
      $movimiento->proyecto_id =  0;
      $movimiento->lote_id =  $lote_almacen->id;
      $movimiento->stocke_id =  $lote_almacen->stocke_id;
      $movimiento->articulo_id = $rhoc->articulo_id;
      Utilidades::auditar($movimiento, $movimiento->id);
      // $movimiento->save();
      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "asociar la partida");
    }
  }


  public function getFolioEntrada($oc)
  {

    $proyecto = DB::table('ordenes_compras AS oc')
      ->select('oc.proyecto_id AS proyecto')
      ->where('oc.id', '149')
      ->first();

    $entra = DB::table('entradas AS e')
      ->leftjoin('ordenes_compras AS oc', 'oc.id', 'e.orden_compra_id')
      ->select('e.*')
      ->where('oc.proyecto_id', $proyecto->proyecto)
      ->where('e.condicion', '1')->orderBy('e.fecha')->get();

    $contador = count($entra) + 1;

    return str_pad(($contador), 4, "0", STR_PAD_LEFT);
  }

  public function actualizarPartida(Request $request)
  {
    try
    {
      $s = DB::table('stocks')->where('id', '=', $request->proyecto)->first();

      $ep = EntradasPendientes::where('id', $request->id)->first();
      $ep->proveedor_id = $request->proveedor;
      Utilidades::auditar($ep, $ep->id);
      $ep->save();

      $lote_almacen = LoteAlmacen::where('id', $ep->lote_almacen)->first();
      $lote_almacen->stocke_id = $s->id;
      Utilidades::auditar($lote_almacen, $lote_almacen->id);
      $lote_almacen->save();

      return response()->json(['status' => true]);
    }
    catch (\Exception $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Generar reporte de las Entradas Pendientes que faltan por ligar
   */
  public function Reporte()
  {
    try
    {
      ob_end_clean();
      ob_start();
      return Excel::download(new EntradasPendientesExport(), 'Entradas_Pendientes.xlsx');
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
