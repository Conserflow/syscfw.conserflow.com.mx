<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\SatCatProdSer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SatCatProdserController extends Controller
{
    /**
     * Obtener todos los productos/servicios del SAT
     */
    public function index()
    {
        try
        {
            $productoServicio = DB::table("sat_cat_prodser as scp")
                ->select(
                    "scp.id",
                    "scp.clave",
                    "scp.descripcion",
                    DB::raw("CONCAT_WS(' ',scp.clave,scp.descripcion) as nombre")
                )
                ->orderBy('scp.clave')->get()->toArray();
            return Status::Success("productoServicio", $productoServicio);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los productos");
        }
    }

    /**
     * [Guarda en BD los registros en la tabla sat_cat_prodser respetando las reglas establecidas]
     * @param  Request  $request [Objeto de datos del POST]
     * @return Response          [Array con estatus true]
     */
    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $productoServicio = new SatCatProdSer($request->all());
            $productoServicio->empleado_registra = Auth::user()->empleado_id;
            Utilidades::auditar($productoServicio, $productoServicio->id);
            $productoServicio->save();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "registrar el producto/servicio");
        }
    }

    /**
     * [Actualiza en BD los registros de la tabla sat_cat_prodser respetando las reglas establecidas]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Respomse          [Array con estatus true]
     */
    public function update(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $productoServicio = SatCatProdSer::findOrFail($request->id);
            $productoServicio->fill($request->all());
            Utilidades::auditar($productoServicio, $productoServicio->id);
            $productoServicio->update();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "actualizar el actículo/servicio");
        }
    }
}
