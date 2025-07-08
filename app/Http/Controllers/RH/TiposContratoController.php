<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\RHModels\TipoContrato;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TiposContratoController extends Controller
{
    /**
     * obtener los tipos de contrato
     */
    public function ObtenerTipoContrato()
    {
        try
        {
            $TipoContrato = DB::table("tipo_contratos")
            ->where("condicion",1)
                ->select("id", "nombre")
                ->get();
            return Status::Success("tipocontrato", $TipoContrato);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los tipos de contrato");
        }
    }
}
