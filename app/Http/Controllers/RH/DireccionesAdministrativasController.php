<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class DireccionesAdministrativasController extends Controller
{

    /**
     * Obtener todas las direcciones
     */
    public function Obtener()
    {
        try
        {
            $direccionesAdministrativas = DB::table("direcciones_administrativas as da")
                ->select("da.id", "da.nombre")
                ->orderBy("nombre")
                ->get();
            return Status::Success("direcciones", $direccionesAdministrativas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las direcciones");
        }
    }
}
