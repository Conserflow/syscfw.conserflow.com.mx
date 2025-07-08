<?php

namespace App\Http\Controllers\Seguridad;

use App\SeguridadModels\FolioProyecto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoliosProyectosController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarFolio(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["nombre"]);

            // Nombre repetido
            $existe = DB::table("seguridad_folios_proyectos as sfp")
                ->where("sfp.nombre", $datos["nombre"])
                ->orWhere("sfp.proyecto_id", $datos["proyecto_id"])
                ->first();
            if ($existe)
                return Status::Error2("Proyecto ya registrado");

            if ($request->id == null)
            {
                $folios_permisos = new FolioProyecto($datos);
                $folios_permisos->empleado_registra_id = Auth::user()->empleado_id;
                $folios_permisos->save();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el folio del proyecto");
        }
    }

    /**
     * 
     */
    public function ObtenerFolios()
    {
        try
        {
            $folios = DB::table("seguridad_folios_proyectos as sfp")
                ->join("proyectos as p", "p.id", "sfp.proyecto_id")
                ->select(
                    "sfp.id",
                    "sfp.nombre",
                    "p.nombre_corto as proyecto"
                )
                ->get();
            return Status::Success("folios_proyectos", $folios);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obener los folios de proyectos");
        }
    }
}
