<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class GradosController extends Controller
{

    /**
     * Obtener todos los grados registrados
     */
    public function index()
    {
        try
        {
            $grados = DB::table("grados")
                ->select("id", "nombre")->orderBy("nombre")->get();
            return Status::Success("grados", $grados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los grados");
        }
    }
}
