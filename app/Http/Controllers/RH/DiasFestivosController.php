<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\RHModels\DiaFestivo;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiasFestivosController extends Controller
{
    /**
     * Registrar día festivo
     */
    public function GuardarDiasFestivos(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["descripcion"]);
            if ($request->id == null)
            {
                $diasfestivos = new DiaFestivo($datos);
                $diasfestivos->empleado_registra_id = Auth::user()->empleado_id;
                $diasfestivos->save();
            }
            else
            {
                $diasfestivos = DiaFestivo::find($request->id);
                $diasfestivos->fill($datos);
                $diasfestivos->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el día");
        }
    }

    /**
     * Obtener todos los días festivos regitrados
     */
    public function ObtenerDiasFestivos()
    {
        try
        {
            $DiasFestivos = DB::table("rh_dias_festivos")
                ->select("id", "dia", "descripcion")
                ->get();
            return Status::Success("diasfestivos", $DiasFestivos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los días festivos");
        }
    }
}
