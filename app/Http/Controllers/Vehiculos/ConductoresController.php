<?php

namespace App\Http\Controllers\Vehiculos;

use Illuminate\Http\Request;
use App\VehiculosModels\Conductores;
use App\VehiculosModels\ConductoresImg;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConductoresController extends Controller
{
  /**
   * Obtiene los conductores activos
   */
  public function index()
  {
    try
    {
      // Obtener los conductores
      $conductores = DB::table("choferes as c")
        ->join("empleados as e", "e.id", "c.empleado_id")
        ->select(
          "c.id",
          "c.empleado_id",
          DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
          DB::raw("'-' as licencia_doc"),
          "c.licencia",
          "c.tipo",
          "c.vigencia",
          "c.estado",
          "e.condicion"
        )
        ->orderBy("e.nombre")
        ->get();
      foreach ($conductores as $c)
      {
        $licencia = DB::table("conductores_img as ci")
          ->select("ci.nombre")
          ->where("ci.conductor_id", $c->id)
          ->orderBy("id", "desc")
          ->first();
        if ($licencia)
        {
          $c->licencia_doc = $licencia->nombre;
        }
      }
      return Status::Success("conductores", $conductores);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los conductores");
    }
  }

  /**
   * Registra o actualiza el conductor
   */
  public function create(Request $request)
  {
    try
    {
      DB::beginTransaction();
      if ($request->id == null) // Nuevo
      {
        $conductor = new Conductores();
        $conductor->empleado_registra_id = Auth::user()->empleado_id;
        $conductor->condicion = 1;
        $conductor->fill(LimpiarInput::LimpiarCampos($request->all(), ["licencia", "tipo", "estado"]));
        Utilidades::auditar($conductor, 0);
        $conductor->save();
      }
      else // Actualizar
      {
        $conductor = Conductores::find($request->id);
        $conductor->fill(LimpiarInput::LimpiarCampos($request->all(), ["licencia", "tipo", "estado"]));
        Utilidades::auditar($conductor, $conductor->id);
        $conductor->save();
      }

      // Registrar comprobante
      if ($request->hasFile("comprobante"))
      {
        // Generar nombre unico de archivo
        $doc = $request->file("comprobante");
        $nombre_archivo = "L_" . uniqid() . ".pdf";
        Storage::disk("local")->put("Trafico/" . $nombre_archivo, fopen($doc, "r+"));

        $comprobante = new ConductoresImg();
        $comprobante->conductor_id = $conductor->id;
        $comprobante->nombre = $nombre_archivo;
        Utilidades::auditar($comprobante, $conductor->id);
        $comprobante->save();
      }
      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "guardar el conductor");
    }
  }

  /**
   * Elimina de la ruta temporal el archivo descargado
   */
  public function editar($nombre)
  {
    //elimina de la ruta local el archivo descargado
    Storage::disk("descarga")->delete($nombre);
    Storage::disk("local")->delete($nombre);
  }

  /**
   *Descarga el documento ingresado
   */
  public function descargar($nombre)
  {
    // Se coloca el archivo en una ruta local
    $archivo = $this->ftpSolucion($nombre);
    Storage::disk("descarga")->put($nombre, $archivo);
    return response()->download(storage_path() . "/app/descargas/" . $nombre);
  }

  public static function ftpSolucion($nombre)
  {
    // Se obtiene el archivo del local serve
    //Se busca en disk o carpeta -----
    if (Storage::exists("Trafico/" . $nombre))
    {
      // Se coloca el archivo en una ruta local
      $archivo = Storage::disk("local")->get("Trafico/" . $nombre);
    }
    return $archivo;
  }
}
