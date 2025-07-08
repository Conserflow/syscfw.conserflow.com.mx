<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\TipoUbicacion;
use Exception;

class TipoUbicacionController extends Controller
{
    /**
     * Obtener todos las ubicaciones
     */
    public function ObtenerUbicaciones()
    {
        try
        {
            $ubicaciones = TipoUbicacion::get();
            return Status::Success("ubicaciones", $ubicaciones);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las ubicaciones");
        }
    }
}
