<?php

namespace App\Http\Controllers\TI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\PartidasTISalidaSitio;
use App\TIModels\TiAccesorio;
use App\TIModels\TiComputo;
use App\TIModels\TiImpresion;
use App\TIModels\TiVideo;
use App\TIModels\TiRed;
use App\TIModels\TISalidaSitio;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValesSitioController extends Controller
{
    /**
     * Obtiene todas las salidas a sitio
     */
    public function ObtenerSalidasSitio($id)
    {
        $ti_material_salida = TISalidaSitio::join('empleados AS e', 'e.id', 'ti_salida_sitio.solicita')
            ->join('proyectos AS p', 'p.id', 'ti_salida_sitio.proyecto_id')
            ->select(
                'ti_salida_sitio.*',
                'p.nombre_corto',
                DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS solicita_empleado")
            )
            ->where('ti_salida_sitio.empresa', $id)
            ->get();

        return response()->json($ti_material_salida);
    }

    /**
     * Obtener los articulos de la salida
     */
    public function ObtenerEquiposSitio($id)
    {
        $data = PartidasTISalidaSitio::where('ti_salida_sitio_id', $id)->get();

        $arreglo = [];
        foreach ($data as $key => $value)
        {
            $des = '';

            if ($value->tipo == 1)
            {
                $cdata = TiComputo::where('id', $value->material_id)
                    ->select(DB::raw("CONCAT(no_serie,' ',marca_modelo,' ',cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS descripcion"))
                    ->first();
                $des = $cdata->descripcion;
            }
            else if ($value->tipo == 2)
            {
                $cdata = TiAccesorio::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            else if ($value->tipo == 3)
            {
                $cdata = TiImpresion::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            else if ($value->tipo == 4)
            {
                $cdata = TiVideo::where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            else
            {
                $cdata = TiRed::select(DB::raw("CONCAT(descripcion,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            $arreglo[] = [
                'data' => $value,
                'descripcion' => $des,
            ];
        }

        return response()->json($arreglo);
    }

    /**
     * Guardar salida a sitio
     */
    public function GuardarValeSitio(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $user = Auth::user();
            if ($request->id == null) // Nuevo
            {
                $ti_material_salida = new TISalidaSitio();
                $ti_material_salida->fecha_salida = $request->fecha_salida;
                $ti_material_salida->proyecto_id = $request->proyecto;
                $ti_material_salida->solicita = $request->solicita;
                $ti_material_salida->entrega = $user->empleado_id;
                $ti_material_salida->empresa = $request->empresa;
                Utilidades::auditar($ti_material_salida, $ti_material_salida->id);
                $ti_material_salida->save();
            }
            else
            {
                $ti_material_salida = TISalidaSitio::find($request->id);
            }

            $data = json_decode($request->data, true);
            foreach ($data as $key => $value)
            {
                $partidas = new PartidasTISalidaSitio();
                $partidas->tipo = $value['tipo'];
                $partidas->cantidad = $value['cantidad'];
                $partidas->material_id = $value['id'];
                $partidas->unidad = $value['unidad'];
                $partidas->ti_salida_sitio_id = $ti_material_salida->id;
                Utilidades::auditar($partidas, $partidas->id);
                $partidas->save();

                if ($value['tipo'] == 1)
                {
                    $cdata = TiComputo::where('id', $value['id'])->first();
                    $total = $cdata->cantidad - $value['cantidad'];
                    $cdata->cantidad = $total;
                    $cdata->condicion = 3;
                    if ($total < 0)
                    {
                        DB::rollBack();
                        return Status::Error2("Stock insuficiente");
                    }
                    Utilidades::auditar($cdata, $cdata->id);
                    $cdata->save();
                }
                if ($value['tipo'] == 2)
                {
                    $cdata = TiAccesorio::where('id', $value['id'])->first();
                    $total = $cdata->cantidad - $value['cantidad'];
                    $cdata->cantidad = $total;
                    $cdata->condicion = 3;
                    if ($total < 0)
                    {
                        DB::rollBack();
                        return Status::Error2("Stock insuficiente");
                    }
                    Utilidades::auditar($cdata, $cdata->id);
                    $cdata->save();
                }
                if ($value['tipo'] == 3)
                {
                    $cdata = TiImpresion::where('id', $value['id'])->first();
                    $total = $cdata->cantidad - $value['cantidad'];
                    $cdata->cantidad = $total;
                    $cdata->condicion = 3;
                    if ($total < 0)
                    {
                        DB::rollBack();
                        return Status::Error2("Stock insuficiente");
                    }
                    Utilidades::auditar($cdata, $cdata->id);
                    $cdata->save();
                }
                if ($value['tipo'] == 4)
                {
                    $cdata = TiVideo::where('id', $value['id'])->first();
                    $total = $cdata->cantidad - $value['cantidad'];
                    $cdata->cantidad = $total;
                    $cdata->condicion = 3;
                    if ($total < 0)
                    {
                        DB::rollBack();
                        return Status::Error2("Stock insuficiente");
                    }
                    Utilidades::auditar($cdata, $cdata->id);
                    $cdata->save();
                }
                if ($value['tipo'] == 5)
                {
                    $cdata = TiRed::where('id', $value['id'])->first();
                    $total = $cdata->cantidad - $value['cantidad'];
                    $cdata->cantidad = $total;
                    $cdata->condicion = 3;
                    if ($total < 0)
                    {
                        DB::rollBack();
                        return Status::Error2("Stock insuficiente");
                    }
                    Utilidades::auditar($cdata, $cdata->id);
                    $cdata->save();
                }
            }
            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar el vale");
        }
    }

    /**
     * Obtener las partidas pendientes por retornar de la salida
     */
    public function ObtenerPendientes($id)
    {
        $data = PartidasTISalidaSitio::where('ti_salida_sitio_id', $id)->where('retornado', '1')->get();

        $arreglo = [];
        foreach ($data as $key => $value)
        {
            $des = '';

            if ($value->tipo == 1)
            {
                $cdata = TiComputo::where('id', $value->material_id)
                    ->select(DB::raw("CONCAT(no_serie,' ',marca_modelo,' ',cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS descripcion"))
                    ->first();
                $des = $cdata->descripcion;
            }
            if ($value->tipo == 2)
            {
                $cdata = TiAccesorio::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            if ($value->tipo == 3)
            {
                $cdata = TiImpresion::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            if ($value->tipo == 4)
            {
                $cdata = TiVideo::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }
            if ($value->tipo == 5)
            {
                $cdata = TiRed::select(DB::raw("CONCAT(descripcion,' ',no_serie) AS descripcion"))
                    ->where('id', $value->material_id)
                    ->first();
                $des = $cdata->descripcion;
            }

            $arreglo[] = [
                'data' => $value,
                'descripcion' => $des,
            ];
        }

        return response()->json($arreglo);
    }

    /**
     * Retorna una partida
     */
    public function RegresarPartidaSitio($id)
    {
        try
        {
            $user = Auth::user();

            $valores = explode('&', $id);
            DB::beginTransaction();
            $partida = PartidasTISalidaSitio::where('material_id', $valores[0])->where('ti_salida_sitio_id', $valores[1])->first();
            $partida->retornado = $valores[2];
            Utilidades::auditar($partida, $partida->id);
            $partida->save();


            $tablas = ["ti_computo", "ti_accesorios", "ti_impresoras", "ti_video", "ti_red"];
            $tbl = $tablas[$partida->tipo - 1];

            if ($valores[2] == 2)
            {

                // Buscar equipo en inventario
                $n = DB::table($tbl)->where("id", $partida->material_id)->first()->cantidad;

                $equipo = DB::table($tbl)->where("id", $partida->material_id)
                    ->update([
                        "cantidad" => ($n + $partida->cantidad),
                        "condicion" => 1
                    ]);
            }
            elseif ($valores[2] == 0)
            {
                // Desactivamos el articulo
                // Buscar equipo en inventario
                $n = DB::table($tbl)->where("id", $partida->material_id)->first()->cantidad;

                $equipo = DB::table($tbl)->where("id", $partida->material_id)
                    ->update([
                        "cantidad" => ($n + $partida->cantidad),
                        "condicion" => 0
                    ]);
            }

            $partida_totales = PartidasTISalidaSitio::where('ti_salida_sitio_id', $valores[1])
                ->count();

            $partida_retornadas = PartidasTISalidaSitio::where('ti_salida_sitio_id', $valores[1])
                ->where('retornado', '!=', '1')
                ->count();

            if ($partida_retornadas == $partida_totales)
            {
                $salida = TISalidaSitio::where('id', $valores[1])->first();
                $salida->condicion = 2;
                $salida->retorno = $user->empleado_id;
                $salida->fecha_retorno = date('Y-m-d');
                Utilidades::auditar($salida, $salida->id);
                $salida->save();
            }
            DB::commit();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "regresar el artículo");
        }
    }

    /**
     * DEscargar el vale de sitio
     */
    public function DescargarValeSitio($id)
    {
        try
        {
            $data = TISalidaSitio::join('empleados AS e', 'e.id', 'ti_salida_sitio.solicita')
                ->join('empleados AS ea', 'ea.id', 'ti_salida_sitio.entrega')
                ->leftjoin('empleados AS er', 'er.id', 'ti_salida_sitio.retorno')
                ->join('proyectos AS p', 'p.id', 'ti_salida_sitio.proyecto_id')
                ->select(
                    'ti_salida_sitio.*',
                    'p.nombre_corto',
                    'p.ciudad',
                    DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_r"),
                    DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS empleado_e"),
                    DB::raw("CONCAT(er.nombre,' ',er.ap_paterno,' ',er.ap_materno) AS empleado_retorno")
                )
                ->where('ti_salida_sitio.id', $id)
                ->first();

            $fecha_salida = $this->getFechaNombre($data->fecha_salida);
            $fecha_retorno = $this->getFechaNombre($data->fecha_retorno);

            $partidas = DB::table('ti_salida_sitio_partidas')->where('ti_salida_sitio_id', $id)->get();

            $arreglo = [];
            foreach ($partidas as $key => $value)
            {
                $des = '';

                if ($value->tipo == 1)
                {
                    $cdata = TiComputo::where('id', $value->material_id)
                        ->select(DB::raw("CONCAT(no_serie,' ',marca_modelo,' ',cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS descripcion"))
                        ->first();
                    $des = $cdata->descripcion;
                }
                if ($value->tipo == 2)
                {
                    $cdata = TiAccesorio::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                        ->where('id', $value->material_id)
                        ->first();
                    $des = $cdata->descripcion;
                }
                if ($value->tipo == 3)
                {
                    $cdata = TiImpresion::select(DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS descripcion"))
                        ->where('id', $value->material_id)
                        ->first();
                    $des = $cdata->descripcion;
                }
                if ($value->tipo == 4)
                {
                    $cdata = TiVideo::where('id', $value->material_id)
                        ->first();
                    $des = $cdata->descripcion;
                }
                if ($value->tipo == 5)
                {
                    $cdata = TiRed::select(DB::raw("CONCAT(descripcion,' ',no_serie) AS descripcion"))
                        ->where('id', $value->material_id)
                        ->first();
                    $des = $cdata->descripcion;
                }

                $arreglo[] = [
                    'data' => $value,
                    'descripcion' => $des,
                ];
            }

            $pdf = Facade::loadView('pdf.ti.valesalidati', compact('data', 'fecha_salida', 'fecha_retorno', 'arreglo'));
            $pdf->setPaper('letter', 'portrait');
            return $pdf->stream();
        }
        catch (Exception $e)
        {
            dd($e);
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    public function getFechaNombre($date)
    {

        $anio = substr($date, 0, 4);
        $mes = substr($date, 5, -3);
        $dia = substr($date, 8);

        $fecha_salida = $dia . ' / ' . Utils::NombreMesNumero($mes) . ' / ' . $anio;

        return $fecha_salida;
    }
}
