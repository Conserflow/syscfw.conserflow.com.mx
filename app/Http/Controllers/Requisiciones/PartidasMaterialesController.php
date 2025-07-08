<?php

namespace App\Http\Controllers\Requisiciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\RequisicionModels\PartidaMaterial;
use App\Traits\StatusResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartidasMaterialesController extends Controller
{
    use StatusResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $data = LimpiarInput::LimpiarCampos($request->all(), [
                "concepto",
                "comentarios",
                "marca",
                "documentos_requeridos"
            ]);
            $partida = new PartidaMaterial($data);
            $partida->empleado_registra_id = Auth::user()->empleado_id;
            $partida->save();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "guardar la partida");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $partidas = PartidaMaterial::with("unidadMedida")
                ->byRequisicion($id)
                ->select(
                    "requisicion_materiales_partidas.id",
                    "requi_id",
                    "concepto",
                    "comentarios",
                    "marca",
                    "cantidad",
                    "um_id",
                    "tipo",
                    "documentos_requeridos",
                )
                ->get();
            return $this->successResponse("partidas", $partidas);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "obtener las partidas");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            $data = LimpiarInput::LimpiarCampos($request->all(), [
                "concepto",
                "comentarios",
                "marca",
                "documentos_requeridos"
            ]);
            $partida = PartidaMaterial::findOrFail($id);
            $partida->fill($data);
            $partida->update();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "actualizar la partida");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $partida = PartidaMaterial::find($id);
            $partida->delete();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "eliminar la partida");
        }
    }
}
