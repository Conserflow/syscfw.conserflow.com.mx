<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\RHModels\Contacto;
use Exception;

class ContactoController extends Controller
{
    /**
     * Mostrar el contacto del empleado ingreado
     */
    public function Obtener($emp_id)
    {
        try
        {
            $contacto = Contacto::where("empleado_id", $emp_id)
                ->first();
            return Status::Success("contacto", $contacto);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos de contacto");
        }
    }

    /**
     * Guardar el contacto seleccionado
     */
    public function Guardar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect("/");
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["contacto_emergencia"]);
            if ($request->id == null)
            {
                $contactoemp = new Contacto($datos);
                $contactoemp->save();
                Auditoria::AuditarCambios($contactoemp);
            }
            else
            {
                $contactoemp = Contacto::find($request->id);
                $contactoemp->fill($datos);
                Auditoria::AuditarCambios($contactoemp);
                $contactoemp->update();
            }

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "actualizar el contacto");
        }
    }
}
