<?php

// FIXME: Arreglar código
namespace App\Http\Controllers\TI;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use App\TIModels\ProgramaTIPreventivo;
use App\TIModels\TiAccesorio;
use App\TIModels\TiComputo;
use App\TIModels\TiImpresion;
use App\TIModels\TiVideo;
use Barryvdh\DomPDF\Facade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramaTIController extends Controller
{
    // FIXME:
    public function getInicial($id)
    {
        $data = ProgramaTIPreventivo::where('empresa', $id)
            ->select('anio')
            ->groupBy('anio')
            ->get();
        return response()->json($data);
    }

    // FIXME:
    public function getDetalle($id)
    {
        $valores = explode('&', $id);
        $data = ProgramaTIPreventivo::where('anio', $valores[0])
            ->where('empresa', $valores[1])
            ->get();
        $arreglo = [];
        foreach ($data as $key => $value)
        {
            $des = '';

            if ($value->tipo == 1)
            {
                $cdata = TiComputo::where('id', $value->caiv)
                    ->select(DB::raw("CONCAT(no_serie,' ',marca_modelo,' ',cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS descripcion"))
                    ->first();
                $des = $cdata->descripcion;
            }
            if ($value->tipo == 3)
            {
                $cdata = TiImpresion::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->caiv)
                    ->first();
                $des = $cdata->descripcion;
            }

            $arreglo[] = [
                'data' => $value,
                'tipo' => $des,
            ];
        }

        return response()->json($arreglo);
    }

    // FIXME:
    public function guardar(Request $request)
    {
        try
        {
            $data_new = new ProgramaTIPreventivo();
            $data_new->tipo = $request->tipo;
            $data_new->caiv = $request->caiv;
            $data_new->marca = $request->marca;
            $data_new->modelo = $request->modelo;
            $data_new->num_serie = $request->num_serie;
            $data_new->mes = $request->mes;
            $data_new->anio = $request->anio;
            $data_new->empresa = $request->empresa;
            $data_new->save();
            Utilidades::auditar($data_new, $data_new->id);

            return response()->json(['status' => true]);
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }

    // FIXME:
    public function actualizar(Request $request)
    {
        try
        {

            $data_new = ProgramaTIPreventivo::where('id', $request->id)->first();
            $data_new->tipo = $request->tipo;
            $data_new->caiv = $request->caiv;
            $data_new->marca = $request->marca;
            $data_new->modelo = $request->modelo;
            $data_new->num_serie = $request->num_serie;
            $data_new->mes = $request->mes;
            $data_new->anio = $request->anio;
            $data_new->empresa = $request->empresa;
            Utilidades::auditar($data_new, $data_new->id);
            $data_new->save();

            return response()->json(['status' => true]);
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }

    // FIXME:
    public function descargar($id)
    {
        try
        {
            $valores = explode('&', $id);

            $arreglo = ProgramaTIPreventivo::where('anio', $valores[0])
                ->where('empresa', $valores[1])
                ->orderBy("mes")
                ->get();
            $anio = $valores[0];

            if ($valores[1] == 1)
            {
                $pdf = Facade::loadView('pdf.ti.programamtto', compact('arreglo', "anio"));
                // PUTO GOYO!!!!! 
            }
            elseif ($valores[1] == 2)
            {
                $pdf = Facade::loadView('pdf.programapticsct', compact('arreglo'));
            }
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('letter', 'landscape');
            return $pdf->stream();
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    // FIXME:
    public function delete($id)
    {
        try
        {
            $data_new = ProgramaTIPreventivo::where('id', $id)->delete();
            return response()->json(['status' => true]);
        }
        catch (\Exception $e)
        {
            Utilidades::errors($e);
        }
    }

    /**
     * Obtiene los equipos
     */
    // FIXME:
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
            { //Accesorios
                $cdata = TiAccesorio::where(function ($query) use ($request)
                {
                    $query->where('descripcion', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('modelo', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('marca', 'LIKE', '%' . $request->des . '%')
                        ->orWhere('no_serie', 'LIKE', '%' . $request->des . '%');
                })
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
