<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\Expediente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    /**
     * Obtiene el expediente del empleado ingresado
     * Si no existe, se crea uno nuevo
     */
    public function ObtenerExpediente($emp_id)
    {
        try
        {
            $expediente = Expediente::where("empleado_id",$emp_id)->first();

            if ($expediente == null) // No tiene
            {
                $expediente =  new Expediente();
                $expediente->empleado_id = $emp_id;
                $expediente->empleado_registra_id = Auth::user()->empleado_id;
                $expediente->folio = "";
                $expediente->save();
                Auditoria::AuditarCambios($expediente);
            }
            return Status::Success("expediente", $expediente);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el expediente");
        }
    }

    /**
     * Guardar el expediente
     */
    public function GuardarExpediente(Request $request)
    {
        try
        {
            $expediente = Expediente::find($request->id);
            $expediente->fill($request->all());
            Auditoria::AuditarCambios($expediente);
            $expediente->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el expediente");
        }
    }
}
