<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\TiVideo;
use Exception;
use Illuminate\Http\Request;

class TiVideoController extends Controller
{

    /**
     * Obtiene todos los accesorios registrados
     */
    public function Obtener()
    {
        try
        {
            $video = TiVideo::where('eliminado', '1')->get();
            return Status::Success("equipo_video", $video);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los equipos de video");
        }
    }

    /**
     * Registra un nuevo equipo de cómputo
     */
    public function Guardar(Request $request)
    {
        try
        {
            // dd($request->all());
            $existe = TiVideo::find($request->id);
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["descripcion"]);
            if ($existe == null)
            {
                // nueva
                $nuevo = new TiVideo($datos);
                $nuevo->save();
                Auditoria::AuditarCambios($nuevo);
            }
            else
            {
                $existe->fill($datos);
                Auditoria::AuditarCambios($existe);
                $existe->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el equipo de video");
        }
    }

    public function Activar(Request $request)
    {
        try
        {
            $equipo = TiVideo::find($request->id);
            $equipo->condicion = $request->condicion;
            $equipo->update();
            Auditoria::AuditarCambios($equipo);
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "activar el equipo");
        }
    }

    public function Eliminar(Request $request)
    {
        try
        {
            $computo = TiVideo::find($request->id);
            $computo->eliminado = 0;
            Auditoria::AuditarCambios($computo);
            $computo->save();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "elimiar el equipo de video");
        }
    }
}
