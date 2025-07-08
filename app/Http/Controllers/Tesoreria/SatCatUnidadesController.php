<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\SatCatUnidades;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SatCatUnidadesController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarSatCatUnidades(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = $request->all();
            if ($request->id == null)
            {
                $satcatunidades = new SatCatUnidades($datos);
                $satcatunidades->empleado_registra_id = Auth::user()->empleado_id;
                $satcatunidades->save();
            }
            else
            {
                $satcatunidades = SatCatUnidades::find($request->id);
                $satcatunidades->fill($datos);
                $satcatunidades->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            dd($e);
            return Status::Error($e, "guardar ...");
        }
    }

    /**
     * 
     */
    public function ObtenerSatCatUnidades()
    {
        try
        {
            $unidaes = DB::table("sat_cat_unidades")->get();
            return Status::Success("unidades", $unidaes);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar ...");
        }
    }
}
