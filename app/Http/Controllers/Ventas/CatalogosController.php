<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class CatalogosController extends Controller
{
    /**
     * Obtener el catalogo de regimen fiscal
     */
    public function ObtenerRegimen()
    {
        try
        {
            $regimen = DB::table("ventas_catalogos_regimen_fiscal")->get();
            return Status::Success("list_regimen", $regimen);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el régimen fiscal");
        }
    }
}
