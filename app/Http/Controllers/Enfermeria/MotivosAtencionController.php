<?php

namespace App\Http\Controllers\Enfermeria;

use App\EnfermeriaModels\MotivoAtencion;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MotivosAtencionController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarMotivoAtencion(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["nombre",]);
            if ($request->id == null)
            {
                $motivoatencion = new MotivoAtencion($datos);
                $motivoatencion->empleado_registra_id = Auth::user()->empleado_id;
                $motivoatencion->save();
                Auditoria::AuditarCambios($motivoatencion);
            }
            else
            {
                $motivoatencion = MotivoAtencion::find($request->id);
                $motivoatencion->fill($datos);
                Auditoria::AuditarCambios($motivoatencion);
                $motivoatencion->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el motivo");
        }
    }

    /**
     * Obtener los motivos de atencion
     */
    public function ObtenerMotivoAtencion()
    {
        try
        {
            $MotivoAtencion = DB::table("enfermeria_motivo_atencion")
            ->select("id","nombre","tipo")
            ->orderBy("tipo")
            ->orderBy("nombre")
            ->get();
            return Status::Success("motivos", $MotivoAtencion);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los motivos de atención");
        }
    }
}
