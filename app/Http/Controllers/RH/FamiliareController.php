<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use App\Familiare;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;

class FamiliareController extends Controller
{

    public function ObtenerFamiliares($emp_id)
    {
        $familiar = Familiare::where('empleado_id', $emp_id)
            ->where('condicion', 1)
            ->get();
        return Status::Success("familiares", $familiar);
    }

    public function GuardarFamiliares(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            if ($request->id == null)
            {
                $familiar = new Familiare();
                $familiar->fill($request->all());
                $familiar->save();
                Auditoria::AuditarCambios($familiar);
            }
            else
            {
                $familiar = Familiare::find($request->id);
                $familiar->fill($request->all());
                Auditoria::AuditarCambios($familiar);
                $familiar->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el familiar");
        }
    }

    /**
     * Eiminar registro del familiar
     */
    public function EliminarFamiliar(Request $request)
    {
        try
        {
            $familiar = Familiare::find($request->id);
            $familiar->condicion = 0; // Eliminar
            Auditoria::AuditarCambios($familiar);
            $familiar->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "eliminar el familiar");
        }
    }
}
