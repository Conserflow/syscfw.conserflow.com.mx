<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Exception;

class LimpiarInput
{
    /**
     * Elimina los acentos y convierte a mayuscula los campos ingresados
     * @param Array $request Datos del Request
     * @param Array $campos Campos a limpiar
     */
    public static function LimpiarCampos($request, $campos)
    {
        try
        {
            foreach ($campos as $campo)
            {
                $aux = mb_strtoupper($request[$campo]);
                $request[$campo] = str_replace(
                    ["Á", "É", "Í", "Ó", "Ú"],
                    ["A", "E", "I", "O", "U"],
                    $aux
                );
            }
            return $request;
        }
        catch (Exception $e)
        {
            Utilidades::errors($e, 2);
        }
    }

    /**
     * Elimina los acentos y convierte a mayuscula los campos ingresados
     * @param Array $request Datos del Request
     * @param Array $campos Campos a limpiar
     * @param Array $ignore Campos a ignorar
     */
    public static function LimpiarIngnorar($request, $ignore = [])
    {
        try
        {
            // Eliminar los campos a ingorar
            foreach ($ignore as $i => $campo)
            {
                unset($request[$campo]);
            }
            // Limpiar
            foreach ($request as $i => $campo)
            {
                $aux = strtoupper($request[$i]);
                $request[$i] = str_replace(
                    ["Á", "É", "Í", "Ó", "Ú"],
                    ["A", "E", "I", "O", "U"],
                    $aux
                );
            }
            return $request;
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }

    /**
     * Elimina los acentos y convierte a mayuscula el campos ingresado
     * @param Array $request Datos del Request
     * @param Array $campo Campo a limpiar
     */
    public static function LimpiarCampo($campo)
    {
        try
        {
            $aux = mb_strtoupper($campo);
            $campo = str_replace(
                ["Á", "É", "Í", "Ó", "Ú"],
                ["A", "E", "I", "O", "U"],
                $aux
            );
            return $campo;
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }
}
