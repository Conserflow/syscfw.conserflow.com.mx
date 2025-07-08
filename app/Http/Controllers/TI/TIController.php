<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use App\TIModels\TiAccesorio;
use App\TIModels\TiComputo;
use App\TIModels\TiImpresion;
use App\TIModels\TiVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TIController extends Controller
{

    
    /**
     * Obtiene los equipos
     */
    public function getPorTipoPrograma(Request $request)
    {
        try
        {
            if ($request->tipo == 1)
            {
                // Equipo de cómputo que contenga la descripción ingresada y que esté activo
                $cdata = TiComputo::where(function ($query) use ($request)
                {
                    $query->Where('marca_modelo', 'LIKE', '%' . $request->des . '%')
                        ->Orwhere('no_serie', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('cpu', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('ram', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('almacenamiento', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('tarjeta_video', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('tarjeta_red', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('observaciones', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('mac', 'LIKE', '%' . $request->des . '%');
                })
                    // ->where("condicion", 1)
                    // ->where('cantidad', '>', '0')
                    ->select(
                        'id',
                        'marca_modelo AS marca',
                        'marca_modelo AS modelo',
                        'no_serie',
                        DB::raw("CONCAT(no_serie,' ',marca_modelo,' ',cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS descripcion"),
                        'cantidad'
                    )
                    ->get();
            }

            if ($request->tipo == 2)
            { //Acesorios
                $cdata = TiAccesorio::where(function ($query) use ($request)
                {
                    $query->where('descripcion', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('modelo', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('marca', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('no_serie', 'LIKE', '%' . $request->des . '%');
                })
                    // ->where('cantidad', '>', '0')
                    // ->where("condicion",1)
                    ->select('id', DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"), 'cantidad')
                    ->get();
            }

            if ($request->tipo == 3)
            { //Impresion
                $cdata = TiImpresion::where(function ($query) use ($request)
                {
                    $query->where('descripcion', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('modelo', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('marca', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('no_serie', 'LIKE', '%' . $request->des . '%');
                })
                    // ->where('cantidad', '>', '0')
                    // ->where('condicion',1)
                    ->select(
                        'id',
                        'marca',
                        'modelo',
                        'no_serie',
                        DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"),
                        'cantidad'
                    )
                    ->get();
            }

            if ($request->tipo == 4)
            { //Video
                $cdata = TiVideo::where(function ($query) use ($request)
                {
                    $query->where('descripcion', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('no_serie', 'LIKE', '%' . $request->des . '%');
                })
                    // ->where('cantidad', '>', '0')
                    // ->where('condicion',1)
                    ->select('id', DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"), 'cantidad')
                    ->get();
            }

            return response()->json($cdata);
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
}
