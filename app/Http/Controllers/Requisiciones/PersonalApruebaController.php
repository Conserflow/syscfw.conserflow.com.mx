<?php

namespace App\Http\Controllers\Requisiciones;

use App\Http\Controllers\Controller;
use App\RequisicionModels\PersonaAprueba;
use App\Traits\StatusResponse;
use Exception;

class PersonalApruebaController extends Controller
{
    use StatusResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $empleados = PersonaAprueba::activos()
                ->get();
            return $this->successResponse("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "obtener el personal que aprueba");
        }
    }
}
