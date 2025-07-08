<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\TipoHorario;
use Exception;

class TiposHorarioController extends Controller
{
    public function ObtenerHorarios()
    {
        try
        {
            $tiposHorario = TipoHorario::get();
            return Status::Success("horarios", $tiposHorario);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los horarios");
        }
    }
}
