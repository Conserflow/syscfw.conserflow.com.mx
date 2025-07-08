<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\TiAccesorio;
use Exception;
use Illuminate\Http\Request;

class TiAccesoriosController extends Controller
{

    /**
     * Obtiene todos los accesorios registrados
     */
    public function Obtener()
    {
        try
        {
            $accesorios = TiAccesorio::where('eliminado', '1')->get();
            return Status::Success("accesorios", $accesorios);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los accesorios");
        }
    }

    /**
     * Obtiene todos los accesorios registrados
     */
    public function ObtenerActivos()
    {
        try
        {
            $accesorios = TiAccesorio::where('cantidad', '>', '0')
                ->where('eliminado', '1')
                ->get();

            return Status::Success("accesorios", $accesorios);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los accesorios activos");
        }
    }

    /**
     * Registra un nuevo accesorio
     */
    public function Guardar(Request $request)
    {
        try
        {
            $existe = TiAccesorio::find($request->id);
            if ($existe == null)
            {
                // nueva
                $nuevo = new TiAccesorio($request->all());
                $nuevo->save();
                Auditoria::AuditarCambios($nuevo);
            }
            else
            {
                $existe->fill($request->all());
                Auditoria::AuditarCambios($existe);
                $existe->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar los accesorios");
        }
    }

    /**
     * Actualizar
     */
    public function Actualizar(Request $request)
    {
        try
        {
            $equipo = TiAccesorio::find($request->id);
            $equipo->fill($request->all());
            $equipo->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return Status::Error($e, "actualizar el accesorio");
        }
    }

    public function Activar(Request $request)
    {
        try
        {
            $equipo = TiAccesorio::find($request->id);
            $equipo->condicion = $request->condicion;
            $equipo->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "activar el accesorio");
        }
    }

    public function Eliminar($id)
    {
        try
        {
            $computo = TiAccesorio::where('id', $id)->first();
            $computo->eliminado = 0;
            Utilidades::auditar($computo, $computo->id);
            $computo->save();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "eliminar el accesorio");
        }
    }
}
