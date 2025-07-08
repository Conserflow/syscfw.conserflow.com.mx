<?php

namespace App\Traits;

use App\Http\Helpers\Utilidades;
use Exception;

trait StatusResponse
{
    /**
     * Regresa un response correcto
     */
    public function emptyResponse()
    {
        return response()->json(["success" => "ok"], 200);
    }

    /**
     * Regresa un response correcto
     */
    public function successResponse($nombre, $data = [])
    {
        return response()->json(["success" => true, $nombre => $data], 200);
    }

    /**
     * Regresa un response correcto
     * @param  Exception  $e Exception
     * @param String $mensaje Mensaje de error
     */
    public function errorResponse($e, $mensaje)
    {
        Utilidades::errors($e);
        return response()->json(["success" => false, "message" => "Error al " . $mensaje], 200);
    }

    /**
     * Retorna un response con error sin excepcion
     *
     * @param  string   $mensaje Mensaje de error
     */
    public static function errorResponse2($mensaje)
    {
        return response()->json(["status" >= false, "message" => $mensaje]);
    }
}
