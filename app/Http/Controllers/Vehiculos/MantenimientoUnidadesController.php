<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use App\VehiculosModels\PartidasMantenimientoUnidades;
use App\VehiculosModels\MantenimientoUnidades;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Utilidades;
use App\VehiculosModels\ServicioUnidades;
use Exception;
use Illuminate\Support\Facades\DB;

class MantenimientoUnidadesController extends Controller
{
  /**
   * Obtiene todos los mantenimientos y servicios de la unidad ingresda
   */
  public function Mttos($id)
  {
    try
    {
      $aux_mttos = [];
      // Mantenimientos
      $mttos = MantenimientoUnidades::where("unidad_id", $id)
        ->select(
          "id",
          "fecha_servicio as fecha",
          "proveedor",
          "responsable",
          DB::raw("1 as tipo") // Mantenimiento
        )
        ->get()->toArray();
      foreach ($mttos as $mmto)
      {
        $partidas = DB::table("partidas_mantenimiento_unidades as pmu")
          ->join("catalogo_trafico as ct", "ct.id", "catalogo_trafico_id")
          ->select("ct.nombre")
          ->where("unidad_id", $id)
          ->where("mantenimiento_id", $mmto["id"])->get();
        $mmto["partidas"] = $partidas;
        $aux_mttos[] = $mmto;
      }

      // Servicios
      $servicios = ServicioUnidades::where("unidad_id", $id)
        ->select(
          "id",
          "responsable",
          "fecha_servicio as fecha",
          "proveedor",
          DB::raw("2 as tipo") // Servicio
        )
        ->get()->toArray();
      foreach ($servicios as $servicio)
      {
        $partidas = DB::table("partidas_servicios_unidades as psu")
          ->join("catalogo_trafico as ct", "ct.id", "psu.catalogo_trafico_id")
          ->select("ct.nombre")
          ->where("unidad_id", $id)
          ->where("servicio_id", $servicio["id"])->get();
        $servicio["partidas"] = $partidas;
        $aux_mttos[] = $servicio;
      }
      return Status::Success("mantenimientos", $aux_mttos);
    }
    catch (Exception $e)
    {
      dd($e);
      return Status::Error($e, "obtener los mantenimientos");
    }
  }


  /**
   * Obtiene los mantenimientos de la unidad ingresada
   */
  public function ObtenerMtto($id)
  {
    try
    {
      $arreglo = [];
      $mantenimientoUnidades = MantenimientoUnidades::where('unidad_id', $id)->get();
      foreach ($mantenimientoUnidades as $key => $value)
      {
        $partida_mantenimiento = PartidasMantenimientoUnidades::join('catalogo_trafico AS CT', 'CT.id', '=', 'partidas_mantenimiento_unidades.catalogo_trafico_id')
          ->select('partidas_mantenimiento_unidades.*', 'CT.operacion_id', 'CT.nombre')
          ->where('unidad_id', $id)
          ->where('mantenimiento_id', $value->id)->get();
        $arreglo[] = [
          'mantenimiento' => $value,
          'partidas' => $partida_mantenimiento,
        ];
      }
      return response()->json($arreglo);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los mantenimientos");
    }
  }

  //
  public function show($id)
  {
    $arreglo = [];
    $mantenimientoUnidades = MantenimientoUnidades::where("unidad_id", $id)->get();
    foreach ($mantenimientoUnidades as $key => $value)
    {
      $partida_mantenimiento = PartidasMantenimientoUnidades::join("catalogo_trafico as ct", "ct.id", "=", "partidas_mantenimiento_unidades.catalogo_trafico_id")
        ->select("partidas_mantenimiento_unidades.*", "ct.operacion_id", "ct.nombre")
        ->where("unidad_id", $id)
        ->where("mantenimiento_id", $value->id)->get();
      $arreglo[] = [
        "mantenimiento" => $value,
        "partidas" => $partida_mantenimiento,
      ];
    }

    return response()->json($arreglo);
  }

  public function store(Request $request)
  {
    if (!$request->ajax()) return redirect("/");
    $valores = explode(",", $request->tipo_servicios);
    $tamanio = count($valores);

    $datos = LimpiarInput::LimpiarIngnorar($request->all(), ["factura"]);
    if ($request->metodo == 1)
    {
      /*NUEVO REGISTRO*/
      $mantenimientoUnidades = new MantenimientoUnidades($datos);
      $mantenimientoUnidades->save();
    }
    else
    {
      /*ACTUALIZAR REGISTRO*/
      $polizaes = MantenimientoUnidades::where("id", $request->id)->get();
      foreach ($polizaes as $key => $item)
      {
        $ValorPoliza = $item->factura;
        $FacturaStore = $item->factura;
      }

      $mantenimientoUnidades = MantenimientoUnidades::findorFail($request->id);
      $mantenimientoUnidades->fill($datos);
      $mantenimientoUnidades->update();
    }
    // Partidas
    $this->llenarCatalogo($request, $valores, $tamanio, $mantenimientoUnidades->id);

    // Guardar comprobante
    if ($request->hasFile("factura"))
    {
      //obtiene el nombre del archivo y su extension
      $FacturaNE = $request->file("factura")->getClientOriginalName();
      //Obtiene el nombre del archivo
      //obtiene la extension
      $FacturaExt = $request->file("factura")->getClientOriginalExtension();
      //nombre que se guarad en BD
      $FacturaStore = "Mante_" . uniqid() . "." . $FacturaExt;
      //Subida del archivo al servidor ftp
      Storage::disk("local")->put("Archivos/" . $FacturaStore, fopen($request->file("factura"), "r+"));
      $mantenimientoUnidades->factura = $FacturaStore;
      $mantenimientoUnidades->update();
    }

    return Status::Success();
  }

  public function llenarCatalogo($request, $valores, $tamanio, $mantenimiento_id)
  {
    try
    {
      $tipo = PartidasMantenimientoUnidades::where("mantenimiento_id", $mantenimiento_id)->first();
      if ($tipo!=null)
      {
        // Borrar la anterior ???????
        $data = PartidasMantenimientoUnidades::where("mantenimiento_id", $mantenimiento_id)->delete();
        for ($i = 0; $i < $tamanio; $i++)
        {
          $tipo_servicios = new PartidasMantenimientoUnidades();
          $tipo_servicios->catalogo_trafico_id = $valores[$i];
          $tipo_servicios->unidad_id = $request->unidad_id;
          $tipo_servicios->mantenimiento_id = $mantenimiento_id;
          $tipo_servicios->save();
        }
      }
      else
      {
        for ($i = 0; $i < $tamanio; $i++)
        {
          $tipo_servicios = new PartidasMantenimientoUnidades();
          $tipo_servicios->catalogo_trafico_id = $valores[$i];
          $tipo_servicios->unidad_id = $request->unidad_id;
          $tipo_servicios->mantenimiento_id = $mantenimiento_id;
          Utilidades::auditar($tipo_servicios, $tipo_servicios->id);
          $tipo_servicios->save();
        }
      }
      return true;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }
}
