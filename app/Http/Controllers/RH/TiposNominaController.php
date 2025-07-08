<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\TipoNomina;
use Exception;

class TiposNominaController extends Controller
{
    public function Obtener()
    {
        try
        {
            $tiposNomina = TipoNomina::select("id", "nombre")->get();
            return Status::Success("tiposnomina", $tiposNomina);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los tipos de nomina");
        }
    }
}
