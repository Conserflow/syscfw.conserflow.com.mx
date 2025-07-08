<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\RHModels\Departamento;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\Auth;

class DepartamentoController extends Controller
{

    /**
     * Obtener todos los departamentos
     */
    public function ObtenerDepartamentos()
    {
        try
        {
            $deptos = DB::table("departamentos as dp")
                ->leftJoin("direcciones_administrativas as da", "dp.direccion_administrativa_id", "da.id")
                ->select(
                    "dp.id",
                    "dp.nombre",
                    "da.id AS direccion_administrativa_id",
                    "da.nombre AS direccion"
                )
                ->where("condicion", 1)
                ->orderBy("dp.nombre")
                ->get();
            return Status::Success("departamentos", $deptos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtenr los departamentos");
        }
    }

    /**
     * Guardar departamento
     */
    public function GuardarDepartamentos(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["nombre"]);
            if ($request->id == null)
            {
                $departamento = new Departamento($datos);
                $departamento->empleado_registra_id = Auth::user()->empleado_id;
                $departamento->save();
            }
            else
            {
                $departamento = Departamento::find($request->id);
                $departamento->fill($datos);
                $departamento->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el departamento");
        }
    }

    /**
     * Desactivar el depto
     */
    public function Desactivar(Request $request)
    {
        try
        {
            $departamento = Departamento::find($request->id);
            $departamento->condicion = 0;
            Auditoria::AuditarCambios($departamento);
            $departamento->update();
            return Status::Success("");
        }
        catch (Exception $e)
        {
            return Status::Error($e, "desactivar el departamento");
        }
    }
}
