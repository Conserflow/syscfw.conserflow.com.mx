<?php

namespace App\Http\Controllers;

use App\SistemaModels\AuditoriaCambio;
use Illuminate\Support\Facades\Auth;

class Auditoria
{
    /**
     * Registra los cambios (Registro/Actualización) realizados en los modelos
     */
    public static function AuditarCambios($obj)
    {
        $fecha_hora = date("Y-m-d H:i:s"); // Hora
        $usuario = Auth::user()->name;
        $modelo = get_class($obj);
        $nuevo = $obj->wasRecentlyCreated; // Es registro (true) o actualización 
        if ($nuevo) // Nuevo: 0 cambios
        {
            $cambios = "REGISTRO NUEVO";
            $model_id = 0;
        }
        else // se registran los cambios
        {
            // Obtenr solo los campos cambiados
            $actual = $obj->getAttributes();
            $original = $obj->getoriginal();
            $dif=array_diff_assoc($actual, $original);
            $campos_cambiados = array_keys($dif);
            if (count($campos_cambiados) == 0) return false; // Sin cambios
            $model_id = isset($original["id"]) ? $original["id"] : 0; // Obtener Id
            $cambios = "";
            // Obtener valor anterior y actual
            foreach ($campos_cambiados as $c)
            {
                $cambios .= $c . ": " . $original[$c] . " --> " . $actual[$c] . PHP_EOL;
            }
        }

        // Guardar registro
        $auditoria = new AuditoriaCambio(
            [
                "model_id" => $model_id,
                "fecha_hora" => $fecha_hora,
                "usuario" => $usuario,
                "modelo" => $modelo,
                "nuevo" => $nuevo,
                "cambios" => $cambios,
            ]
        );
        $auditoria->save();
        return true;
    }
}
