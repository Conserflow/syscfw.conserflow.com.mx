<?php

namespace App\Http\Controllers\Vehiculos;

use App\VehiculosModels\CatalogoTrafico;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use App\VehiculosModels\Unidades;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Utilidades;
use App\VehiculosModels\PrestamoUnidad;
use App\VehiculosModels\UnidadPropio;
use App\VehiculosModels\UnidadRenta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

class UnidadesController extends Controller
{

  /**
   * Obtiene las unidades de la empresa ingresada
   */
  public function Obtener($emp)
  {
    try
    {
      $id = $emp;
      $comp = "=";
      if ($emp == 0)
      {
        $id = 0;
        $comp = "!=";
      }
      // Obtener unidades (todas)
      $unidades_aux = DB::table("unidades as u")
        ->leftJoin("unidad_propio as up", 'up.unidad_id', 'u.id')
        ->leftJoin('unidad_renta as ur', 'ur.unidad_id', 'u.id')
        ->select(
          'u.*',
          'up.id as unidad_propio_id',
          'up.propietario',
          'ur.id as unidad_renta_id',
          'ur.proveedor',
          'ur.tiempo',
          'ur.costo_renta'
        )
        ->orderBy("u.placas")
        ->where("u.empresa", $comp, $id)
        ->get();

      $unidades = [];
      foreach ($unidades_aux as $u)
      {
        // Buscar prestamos
        $prestamo = PrestamoUnidad::where("unidad_id", $u->id)
          ->where("devuelto", 0) // que no se hayan devolvido
          ->first();
        if ($prestamo != null)
        {
          $u = (array)$u;
          $u["prestamo_id"] = $prestamo->id;
          $u = (object)$u;
        }
        $unidades[] = $u;
      }
      return Status::Success("unidades", $unidades);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las unidades");
    }
  }

  /**
   * Elimina la unidad seleccionada
   */
  public function Eliminar(Request $request)
  {
    try
    {
      $unidad = Unidades::find($request->id);
      $unidad->condicion = 0;
      $unidad->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "cambiar el estado");
    }
  }

  /**
   * Registra un prestamo de la unidad ingresada
   */
  public function Prestamo(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');

      DB::beginTransaction();
      if ($request->condicion == 1) // Regreso
      {
        $prestamo = PrestamoUnidad::find($request->prestamo_id);
        $prestamo->fecha_devolucion = date("Y-m-d");
        $prestamo->devuelto = 1;
        $prestamo->motivo_devolucion = $request->motivo;
        $prestamo->update();
      }
      else // Prestamo
      {
        // Registrar prestamo
        $prestamo = new PrestamoUnidad($request->all());
        $prestamo->motivo_prestamo = $request->motivo;
        $prestamo->fecha_prestamo = date("Y-m-d");
        $prestamo->empleado_registra_id = Auth::user()->empleado_id;
        $prestamo->save();
      }

      // Actualizar estado de unidad
      $unidad = Unidades::find($request->unidad_id);
      $unidad->condicion = $request->tipo;
      $unidad->update();
      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "guardar el préstamo");
    }
  }

  /**
   * Obtiene los detalles del prestamo ingresado
   */
  public function DetallesPrestamo($id)
  {
    try
    {
      $prestamo = PrestamoUnidad::find($id);
      return Status::Success("prestamo", $prestamo);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los detalles del prestamo");
    }
  }

  // FIXME:
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $unidades = Unidades::orderBy('id', 'ASC')->get();

    return response()->json($unidades);
  }

  /**
   * Registra o actualiza una unidad
   */
  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');
      $datos = LimpiarInput::LimpiarIngnorar($request->all(), ["tarjeta", "factura"]);
      $datos["excento"] = $datos["excento"] == "FALSE" ? 0 : 1;

      if ($request->metodo == "Nuevo")
      {
        /*NUEVO REGISTRO*/
        //Variables de archivo
        $FacturaStore = '';
        $TarjetaStore = '';

        //--Si el request no contiene archivos, se guardan los campos listados--//
        if (!$request->hasFile('factura') && !$request->hasFile('tarjeta'))
        {
          $unidad = new Unidades($datos);
          $unidad->factura = '';
          $unidad->tarjeta = '';
          Utilidades::auditar($unidad, 0);
          $unidad->save();
          $this->completarLLenado($datos, $unidad->id);
        }

        //--valida que campos del request contienen archivos y realiza el guardado--//
        if ($request->hasFile('factura') || $request->hasFile('tarjeta'))
        {
          if ($request->hasFile('factura'))
          {
            //obtiene el nombre del archivo y su extension
            $FacturaNE = $request->file('factura')->getClientOriginalName();
            //Obtiene el nombre del archivo
            $FacturaNombre = pathinfo($FacturaNE, PATHINFO_FILENAME);
            //obtiene la extension
            $FacturaExt = $request->file('factura')->getClientOriginalExtension();
            //nombre que se guarad en BD
            $FacturaStore = 'Factura_' . uniqid() . '.' . $FacturaExt;
            //Subida del archivo al servidor ftp
            Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('factura'), 'r+'));
            $unidad->factura = $FacturaStore;
            $unidad->update();
          }
          if ($request->hasFile('tarjeta'))
          {
            //obtiene el nombre del archivo y su extension
            $TarjetaNE = $request->file('tarjeta')->getClientOriginalName();
            //Obtiene el nombre del archivo
            $TarjetaNombre = pathinfo($TarjetaNE, PATHINFO_FILENAME);
            //obtiene la extension
            $TarjetaExt = $request->file('tarjeta')->getClientOriginalExtension();
            //nombre que se guarad en BD
            $TarjetaStore = 'Tarjeta_' . uniqid() . '.' . $TarjetaExt;
            //Subida del archivo al servidor ftp
            Storage::disk('local')->put('Archivos/' . $TarjetaStore, fopen($request->file('tarjeta'), 'r+'));
          }
          $unidad = new Unidades($datos);
          $unidad->tarjeta = $TarjetaStore;
          Utilidades::auditar($unidad, 0);
          $unidad->save();
          $this->completarLLenado($datos, $unidad->id);
        }

        return response()->json(array(
          'status' => true,
        ));
        /*FIN NUEVO REGISTRO*/
      }

      if ($request->metodo == "Actualizar")
      {
        /*ACTUALIZAR REGISTRO*/
        //Variables de archivo
        $FacturaStore = '';
        $TarjetaStore = '';
        //*Variables para actualizar nuevos archivos y eliminar existentes
        $ValorPoliza = '';
        $ValorPoliz = '';

        $unidades = Unidades::where('id', $request->id)->get();

        foreach ($unidades as $key => $item)
        {
          $ValorPoliza = $item->factura;
          $ValorPoliz = $item->tarjeta;

          $FacturaStore = $item->factura;
          $TarjetaStore = $item->tarjeta;
        }
        //*FIN

        if (!$request->ajax()) return redirect('/');

        //--Si el request no contiene archivos, solo se actualizan los campos listados--//
        if (!$request->hasFile('factura') && !$request->hasFile('tarjeta'))
        {

          $unidad = Unidades::findOrFail($request->id);
          $unidad->fill($datos);
          Utilidades::auditar($unidad, $unidad->id);
          $unidad->update();
          $this->completarLLenado($datos, $unidad->id);
        }

        //--valida que campos del request contienen archivos y realiza el guardado--//
        if ($request->hasFile('factura') || $request->hasFile('tarjeta'))
        {
          if ($request->hasFile('factura'))
          {
            //obtiene el nombre del archivo y su extension
            $FacturaNE = $request->file('factura')->getClientOriginalName();
            //Obtiene el nombre del archivo
            $FacturaNombre = pathinfo($FacturaNE, PATHINFO_FILENAME);
            //obtiene la extension
            $FacturaExt = $request->file('factura')->getClientOriginalExtension();
            //nombre que se guarad en BD
            $FacturaStore = 'Factura_' . uniqid() . '.' . $FacturaExt;
            //Subida del archivo al servidor ftp
            Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('factura'), 'r+'));
            $unidad = Unidades::findOrFail($request->id);
            $unidad->factura = $FacturaStore;
            $unidad->update();
            if ($ValorPoliza != '')
            {
              //Elimina el archivo en el servidor si requiere ser actualizado
              Utilidades::ftpSolucionEliminar($ValorPoliza);
            }
          }
          if ($request->hasFile('tarjeta'))
          {
            //obtiene el nombre del archivo y su extensionfactura
            $TarjetaNE = $request->file('tarjeta')->getClientOriginalName();
            //Obtiene el nombre del archivo
            $TarjetaNombre = pathinfo($TarjetaNE, PATHINFO_FILENAME);
            //obtiene la extension
            $TarjetaExt = $request->file('tarjeta')->getClientOriginalExtension();
            //nombre que se guarad en BD
            $TarjetaStore = 'Tarjeta_' . uniqid() . '.' . $TarjetaExt;
            //Subida del archivo al servidor ftp
            Storage::disk('local')->put('Archivos/' . $TarjetaStore, fopen($request->file('tarjeta'), 'r+'));
            $unidad->tarjeta = $TarjetaStore;
            $unidad->update();
            if ($ValorPoliz != '')
            {
              //Elimina el archivo en el servidor si requiere ser actualizado
              Utilidades::ftpSolucionEliminar($ValorPoliz);
            }
          }
          $this->completarLLenado($datos, $unidad->id);
        }

        return response()->json(array(
          'status' => true
        ));
        /*FIN ACTUALIZAR REGISTRO*/
      }
    }
    catch (\Throwable $e)
    {
      return Status::Error($e, "guardar la unidad");
    }
  }

  /**
   * Rellena los datos faltantes del vehículo
   */
  public function completarLLenado($request, $id)
  {
    $request = (object)$request;
    if ($request->tipo == 1)
    {
      $unidad_propio_buscar = UnidadPropio::where('unidad_id', $id)->first();
      if ($unidad_propio_buscar != null)
      {
        $unidad_propio_buscar->unidad_id = $id;
        $unidad_propio_buscar->propietario = $request->propietario;
        $unidad_propio_buscar->update();
        return true;
      }
      else
      {
        $unidad_renta_buscar = UnidadRenta::where('unidad_id', $id)->first();
        if ($unidad_renta_buscar != null)
        {
          $unidad_renta_eliminar = UnidadRenta::where('unidad_id', $id)->delete();
        }
        $unidad_propio = new UnidadPropio();
        $unidad_propio->unidad_id = $id;
        $unidad_propio->propietario = $request->propietario;
        $unidad_propio->save();
        return true;
      }
    }
    elseif ($request->tipo == 2)
    {
      $unidad_renta_buscar = UnidadRenta::where('unidad_id', $id)->first();
      if ($unidad_renta_buscar != null)
      {
        $unidad_renta_buscar->unidad_id = $id;
        $unidad_renta_buscar->proveedor = $request->proveedor;
        $unidad_renta_buscar->tiempo = $request->tiempo;
        $unidad_renta_buscar->costo_renta = $request->costo_renta;
        $unidad_renta_buscar->save();
        return true;
      }
      else
      {
        $unidad_propio_buscar = UnidadPropio::where('unidad_id', $id)->first();
        if ($unidad_propio_buscar != null)
        {
          $unidad_propio_eliminar = UnidadPropio::where('unidad_id', $id)->delete();
        }
        $unidad_renta = new UnidadRenta();
        $unidad_renta->unidad_id = $id;
        $unidad_renta->proveedor = $request->proveedor;
        $unidad_renta->tiempo = $request->tiempo;
        $unidad_renta->costo_renta = $request->costo_renta;
        $unidad_renta->save();
        return true;
      }
    }
  }

  // FIXME:
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    // Se obtiene el archivo del FTP serve
    $archivo = Utilidades::ftpSolucion($id);
    // Se coloca el archivo en una ruta local
    Storage::disk('descarga')->put($id, $archivo);
    //--Devuelve la respuesta y descarga el archivo--//
    return response()->download(storage_path() . '/app/descargas/' . $id);
  }

  // FIXME:
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //elimina de la ruta local el archivo descargado
    Storage::disk('descarga')->delete($id);
    Storage::disk('local')->delete($id);
  }

  /**
   * Obtiene los tipos de vehiculos registrados
   */
  public function ObtenerClasetipo()
  {
    try
    {
      $tipos = DB::table('catalogo_tipo_vehiculo')
        ->orderBy("clase_tipo")->get();
      return Status::Success("tipos", $tipos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los tipos");
    }
  }

  /**
   * Obtiene el catalogo de trafico para registro de servicio de mantenimiento
   */
  public function Trafico()
  {
    try
    {
      $catalogo = CatalogoTrafico::get();
      return Status::Success("catalogo", $catalogo);
    }
    catch (Exception $e)
    {
      return Status::Error($e);
    }
  }

  /**
   * Obtiene los tipos de combustible usados en los vehiculos
   */
  public function ObtenerCombustibles()
  {
    try
    {
      $combustibles = DB::table('catalogo_tipo_gasolina')
        ->orderBy("nombre")->get();
      return Status::Success("combustibles", $combustibles);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los combustibles");
    }
  }

  /**
   * Descargar reporte de las unidades existentes
   */
  public function DescargarReporte($emp_id)
  {
    try
    {
      $arreglo = [];
      $unidades = DB::table('unidades')
        ->where("empresa", $emp_id)
        ->where("condicion", 1)->get();

      foreach ($unidades as $index => $unidad)
      {
        $fecha = '';
        $compania = '';
        $num_poliza = '';
        $inicio_p = '';
        $termino_p = '';

        $seguro = DB::table('poliza_unidades')->where('unidad_id', $unidad->id)
        ->orderBy("id","desc")
        ->first();

        if ($seguro != null)
        {
          $compania = $seguro->proveedor;
          $num_poliza = $seguro->numero_poliza;
          $inicio_p = $seguro->fecha_inicio;
          $termino_p = $seguro->fecha_termino;
        }
        $arreglo[] = [
          'unidad' => $unidad,
          'fecha_ad' => $fecha,
          'compania' => $compania,
          'poliza' => $num_poliza,
          'inicio_p' => $inicio_p,
          'termino_p' => $termino_p,
        ];
      }

      $pdf = PDF::loadView('pdf.inventariovehiculos', compact('arreglo'));
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('ledger', 'portrait');
      return $pdf->stream();
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  /**
   * Obtener todas las unidades para el vale de combustible
   */
  public function UnidadesParaCombustible()
  {
    try
    {
      $unidades_particular = DB::table("unidades")
        ->where("condicion", 1)
        ->where("empresa", ">=", 1) // Empresas
        ->select("id", "unidad", "placas", "modelo")
        ->get()->toArray();

      $unidades_empresa = DB::table("unidades")
        ->where("condicion", 1)
        ->where("empresa", -1)
        ->select("id", "unidad", "placas", DB::raw("concat_ws(' - ',modelo,'PARTICULAR') as modelo"))
        ->get()->toArray();
      $unidades = array_merge($unidades_empresa, $unidades_particular);
      return Status::Success("unidades", $unidades);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las unidades");
    }
  }
  /**
   * Obtiene todas las unidades para mostrar en viaticos
   */
  public function UnidadesViaticos()
  {
    try
    {
      $unidad = Unidades::where("condicion", 1)
        ->get();
      if ($unidad == null) return Status::Error2("La unidad no existe");
      return Status::Success("unidad", ["condicion" => $unidad->condicion]);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener la unidad");
    }
  }
}
