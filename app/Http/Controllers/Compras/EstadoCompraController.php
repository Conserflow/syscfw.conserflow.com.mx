<?php

namespace App\Http\Controllers\Compras;

use Illuminate\Http\Request;
use App\ComprasModels\EstadoCompra;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use \App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;

class EstadoCompraController extends Controller
{
    /**
     * Obtener todos los estados de compra
     */
    public function index()
    {
        try
        {
            $estados = EstadoCompra::get();
            return Status::Success("estados", $estados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los estados");
        }
    }

    /**
     * Guarda un nuevo estado de compra
     */
    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $edoCompra = new EstadoCompra();
            $edoCompra->empleado_registra_id = Auth::user()->empleado_id;
            $edoCompra->fill($request->all());
            Utilidades::auditar($edoCompra, 0);
            $edoCompra->save();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el estado de compra");
        }
    }

    /**
     * Actualiza el estado ingresado
     */
    public function update(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $edoCompra = EstadoCompra::findOrFail($request->id);
            $edoCompra->fill($request->all());
            Utilidades::auditar($edoCompra, $edoCompra->id);
            $edoCompra->update();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "actualizar el estado");
        }
    }
}
