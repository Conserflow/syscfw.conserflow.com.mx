<?php

namespace App\Http\Controllers\Requisiciones;

use App\Http\Controllers\Controller;
use App\RequisicionModels\Tipo;
use App\Traits\StatusResponse;
use Exception;

class TipoRequisicionController extends Controller
{
    use StatusResponse;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try
        {
            $tipos = Tipo::select("id", "nombre")
                ->orderBy("nombre")->get();
            return $this->successResponse("tipos", $tipos);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "obtener los tipos");
        }
    }
}
