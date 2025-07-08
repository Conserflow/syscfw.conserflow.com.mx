<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class EdoCivilController extends Controller
{
    /**
     * Obtiene todos los estados civiles registrados
     */
    public function index()
    {
        try
        {
            $estados = DB::table('estados_civiles')
                ->select('estados_civiles.*')
                ->orderBy('estados_civiles.nombre')
                ->get();
            return Status::Success("estados", $estados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los estados civiles");
        }
    }
}
