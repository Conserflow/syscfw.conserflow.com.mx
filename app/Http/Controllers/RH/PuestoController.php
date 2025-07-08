<?php

namespace App\Http\Controllers\RH;

use App\Exports\RH\PuestosExport;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\RHModels\Puesto;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PuestoController extends Controller
{

    /**
     * Obtener todos los puestos
     */
    public function ObtenerPuestos()
    {
        try
        {
            $puestos = DB::table("puestos as p")
                ->leftJoin("departamentos as d", "p.departamento_id", "d.id")
                ->leftJoin("direcciones_administrativas as da", "d.direccion_administrativa_id", "da.id")
                ->select(
                    "p.id",
                    "p.nombre",
                    "p.area",
                    "p.departamento_id",
                    "p.nivel_o",
                    "d.nombre as departamento",
                    "da.nombre AS direccion"
                )
                ->orderBy("p.nombre")
                ->where("p.condicion", 1)
                ->get();
            return Status::Success("puestos", $puestos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los puestos");
        }
    }

    /**
     * Obtener solo el nombre y el id del puesto
     */
    public function ObtenerPuestosNombre()
    {
        try
        {
            $puestos = DB::table("puestos as p")
                ->select("id", "nombre")
                ->orderBy("p.nombre")
                ->where("p.condicion", 1)
                ->get();
            return Status::Success("puestos", $puestos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los puestos");
        }
    }

    /**
     * Guardar el puesto
     */
    public function GuardarPuestos(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect("/");
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["nombre", "area"]);
            if ($request->id == null)
            {
                $puesto = new Puesto($datos);
                $puesto->condicion = 1;
                $puesto->empleado_registra_id = Auth::user()->empleado_id;
                $puesto->save();
                Auditoria::AuditarCambios($puesto);
            }
            else
            {
                $puesto = Puesto::find($request->id);
                $puesto->fill($datos);
                Auditoria::AuditarCambios($puesto);
                $puesto->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el puesto");
        }
    }

    /**
     * Obtener el puesto y por el ID
     */
    public function show($p_id)
    {
        try
        {
            $puesto = Puesto::where("id", $p_id)
                ->where("condicion", 1)
                ->select("id", "nombre")->first();
            if ($puesto == null) return Status::Error2("Puesto no encontrado");
            return Status::Success("puesto", $puesto);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el puesto");
        }
    }

    public function descargar()
    {
        return Excel::download(new PuestosExport(), "Puestos.xlsx");
    }
}
