<?php

namespace App\Http\Controllers\Vehiculos;

use App\VehiculosModels\Proveedor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{

    /**
     * Registrar el proveedor
     */
    public function GuardarProveedor(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;

            $datos = LimpiarInput::LimpiarCampos($request->all(), ["nombre", "razon_social"]);
            if ($request->id == null)
            {
                // Comprobar rfc
                $existe = Proveedor::where("rfc", $request->rfc)->first();
                if ($existe) return Status::Error2("Proveedor ya registrado");
                $proveedor = new Proveedor($datos);
                $proveedor->empleado_registra_id = Auth::user()->empleado_id;
                $proveedor->save();
            }
            else
            {
                $proveedor = Proveedor::find($request->id);
                $proveedor->fill($datos);
                $proveedor->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el proveedor");
        }
    }

    /**
     * Obtener todos los proveedores de vehiculos
     */
    public function ObtenerProveedor()
    {
        try
        {
            $proveedores = DB::table("vehiculos_proveedores")
                ->select("id", "nombre", "razon_social", "rfc")
                ->get();
            return Status::Success("proveedores", $proveedores);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los proveedores");
        }
    }
}
