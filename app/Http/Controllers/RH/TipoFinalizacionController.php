<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class TipoFinalizacionController extends Controller
{
    /**
     * Obtener los tipos de finalización de contratos
     */
    public function ObtenerTipos()
    {
        try
        {
            $tipos = DB::table("tipo_fin_contrato")->select("id", "nombre")->get();
            return Status::Success("tipos", $tipos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los tipos de finalización");
        }
    }
}
