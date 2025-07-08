<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Auditoria;
use App\VentasModels\Clientes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;

class ClientesController extends Controller
{
  /**
   * Obtiene los clientes registrados y el ejecutibo asignado
   */
  public function index()
  {
    try
    {
      $clientes = DB::table("clientes")
        ->leftJoin("empleados AS E", "E.id", "clientes.ejecutivo_asignado_id")
        ->select(
          "clientes.*",
          DB::raw("CONCAT_WS(' ',E.nombre,E.ap_paterno,E.ap_materno) AS nombre_empleado")
        )
        ->orderBy("nombre")
        ->get();
      return Status::Success("clientes", $clientes);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los clientes");
    }
  }

  /**
   * Funcion que consulta un registro de un id especifico
   */
  public function show($id)
  {
    try
    {
      $cliente = DB::table("clientes as c")
        ->where("c.id", $id)
        ->leftJoin("ventas_catalogos_regimen_fiscal as vcrf", "c.regimen_fiscal", "vcrf.clave")
        ->select(
          "c.*",
          "vcrf.nombre as regimen_fiscal_nombre"
        )
        ->first();
      return Status::Success("cliente", $cliente);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener el cliente");
    }
  }

  /**
   * Crea un registo en la BD
   */
  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");

      $empleado_id = Auth::user()->empleado_id;
      $cliente = new Clientes();
      $cliente->empleado_registra_id = $empleado_id;
      $cliente->fill($request->all());
      $cliente->save();
      Auditoria::AuditarCambios($cliente);
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "registrar el cliente");
    }
  }

  /**
   * Actualiza un registro buscado por el id en la base de datos
   */
  public function update(Request $request, $id)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");

      $cliente = Clientes::find($id);
      $cliente->fill($request->all());
      Utilidades::auditar($cliente, $cliente->id);
      $cliente->save();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "actualizar el cliente");
    }
  }
}
