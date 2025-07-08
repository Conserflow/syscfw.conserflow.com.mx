<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\RHModels\RegChecador;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecadorController extends Controller
{
    /**
     ** Crea un registro de asistencia con los datos ingresados
     ** funciona sin que el usuario este autentificado,
     **/
    public function Guardar(Request $request)
    {
        try
        {
            $user_id = 0;
            // Obtener Usuario
            $user = Auth::user();
            if ($user != null)
                $user_id = $user->empleado_id;

            $checdador = new RegChecador();
            $checdador->empleado = $request->empleado;
            $checdador->empleado_id = $request->empleado_id;
            $checdador->fecha = $request->fecha;
            $checdador->hora = $request->hora;
            $checdador->empleado_registra_id = $user_id;
            $checdador->ubicacion = strtoupper($request->ubicacion);
            $checdador->save();
            return Status::Success();
        }
        catch (Exception $e)
        {
            Utilidades::errors($e, 2);
        }
    }
}
