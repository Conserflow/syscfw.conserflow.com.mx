<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\CatalogoBanco;
use App\RHModels\DatosBancariosEmpleado;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DatosBancariosEmpleadoController extends Controller
{

    /**
     * Guarda un banco del empleado ingresado
     */
    public function GuardarBanco(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect("/");
            if ($request->id == null)
            {
                $banco = new DatosBancariosEmpleado($request->all());
                $banco->save();
                Auditoria::AuditarCambios($banco);
            }
            else
            {
                $banco = DatosBancariosEmpleado::find($request->id);
                $banco->fill($request->all());
                Auditoria::AuditarCambios($banco);
                $banco->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar los datos bancarios");
        }
    }

    /**
     * Desactiva un registro bancario del empleado ingresado
     */
    public function Desactivar(Request $request)
    {
        try
        {
            $banco = DatosBancariosEmpleado::where("empleado_id", $request->empleado_id)
                ->where("id", $request->banco_id)
                ->first();
            $banco->condicion = 0; // Eliminar
            Auditoria::AuditarCambios($banco);
            $banco->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "eliminar el banco");
        }
    }

    /**
     * Obtiene todos los tipos de banco registrados
     */
    public function ObtenerBancos()
    {
        try
        {
            $bancos = CatalogoBanco::orderBy("nombre")->get();
            return Status::Success("bancos", $bancos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los bancos");
        }
    }

    /**
     * Obtener los bancos del empleado
     */
    public function ObtenerBancosEmpleado($emp_id)
    {
        try
        {
            $bancos = DB::table("datos_bancarios_empleados as dbe")
                ->leftJoin("catalogo_bancos as cb", "cb.id", "dbe.banco_id")
                ->select(
                    "dbe.id",
                    "dbe.banco_id",
                    "dbe.empleado_id",
                    "dbe.numero_tarjeta",
                    "dbe.numero_cuenta",
                    "dbe.clabe",
                    "cb.nombre as bnombre"
                )
                ->where("empleado_id", $emp_id)
                ->where("dbe.condicion", 1)
                ->get();
            return Status::Success("bancos", $bancos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos bancarios");
        }
    }

    /**
     * [get Obtencion del un registro especifico en la BD por id recibido]
     * @param  Request $request [description]
     * @param  Int  $id      [description]
     * @return Response           [description]
     */
    public function ObtenerDatos($emp_id)
    {
        $datos_bancarios = DB::table("datos_bancarios_empleados as dbe")
            ->leftJoin("catalogo_bancos as cb", "cb.id", "dbe.banco_id")
            ->select("dbe.*", "cb.nombre as bnombre")
            ->where("empleado_id", $emp_id)
            ->where("dbe.condicion", 1)
            ->get()->toArray();
        return response()->json($datos_bancarios);
    }
}
