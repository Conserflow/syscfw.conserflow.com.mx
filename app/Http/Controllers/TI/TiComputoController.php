<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\TiComputo;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;


class TiComputoController extends Controller
{

  /**
   * Obtiene todos los equipos de computo registrados
   */
  public function Obtener($id)
  {
    try
    {
      $equipos = TiComputo::where('empresa', $id)
        ->where('eliminado', '1')
        ->get();
      return Status::Success("equipos", $equipos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los equipos");
    }
  }

  /**
   * Registra un nuevo equipo de cómputo
   */
  public function Registrar(Request $request)
  {
    try
    {
      $equipo = new TiComputo();
      $equipo->fill($request->all());
      $equipo->save();
      Auditoria::AuditarCambios($equipo);
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el equipo");
    }
  }

  /**
   * Registra un nuevo equipo de cómputo
   */
  public function Actualizar(Request $request)
  {
    try
    {
      $equipo = TiComputo::find($request->id);
      $equipo->fill($request->all());
      Auditoria::AuditarCambios($equipo);
      $equipo->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "actualizar el equipo");
    }
  }

  public function Activar(Request $request)
  {
    try
    {
      $equipo = TiComputo::where('id', $request->id)->first();
      $equipo->condicion = $equipo->condicion == 0 ? 1 : 0;
      Auditoria::AuditarCambios($equipo);
      $equipo->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "activar el equipo");
    }
  }

  public function Descargar($id)
  {
    try
    {
      $equipos = TiComputo::where('empresa', $id)
        ->get();
      $arreglo = [];

      $estados = ["Inactivo", "Activo", "Resguardo", "Sitio"];
      foreach ($equipos as $e)
      {

        $material = DB::table('ti_material_resguardo')
          ->leftJoin('empleados AS e', 'e.id', 'ti_material_resguardo.empleado_recibe')
          ->select(
            'ti_material_resguardo.*',
            DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS nom_usr")
          )
          ->where('tipo', '1')
          ->where('caiv', $e->id)
          ->first();
        $estado = $estados[$e->condicion];

        $arreglo[] = [
          'computo' => $e,
          'resguardo' => $material,
          'estado' => $estado,
        ];
      }
      if ($id == 1)
      {
        $pdf = PDF::loadView('pdf.invcompcsct', compact('arreglo'));
      }
      elseif ($id == 2)
      {
        $pdf = PDF::loadView('pdf.invcompcsct', compact('arreglo'));
      }

      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A2', 'portrait');
      return $pdf->stream();
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  public function Eliminar($id)
  {
    try
    {
      $computo = TiComputo::where('id', $id)->first();
      $computo->eliminado = 0;
      Auditoria::AuditarCambios($computo);
      $computo->save();

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e);
    }
  }
}
