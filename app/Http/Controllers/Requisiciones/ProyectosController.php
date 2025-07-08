<?php

namespace App\Http\Controllers\Requisiciones;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Helpers\Tables;
use App\RequisicionModels\PartidaMaterial;
use App\RequisicionModels\Requisicion;
use App\RequisicionModels\Tipo;
use App\Traits\StatusResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\ExceptionCode;

class ProyectosController extends Controller
{
    use StatusResponse;

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index(Request $request)
    {
        try
        {
            $requisiciones = Requisicion::withRelations()
                ->leftJoin("proyectos as proyecto", "requisiciones2.proyecto_id", "proyecto.id")
                ->leftJoin("empleados as solicita", "empleado_solicita_id", "solicita.id")
                ->leftJoin("empleados as aprueba", "empleado_aprueba_id", "aprueba.id")
                ->leftJoin("sgi_salidas_deptos as areas", "area_id", "areas.id")
                ->leftJoin("requisiciones_tipos as tipo", "tipo_id", "tipo.id")
                ->select(
                    "requisiciones2.*",
                    "requisiciones2.folio as requisiciones2__folio",
                )
                ->pendientesSupervisor();

            return Tables::search($requisiciones, $request, []);
        }
        catch (ExceptionCode $e)
        {
            return $this->errorResponse($e, "obtener las requisiciones");
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $partida = PartidaMaterial::findOrFail($id);
            $partida->cantidad_almacen = $request->cantidad;
            Auditoria::AuditarCambios($partida);
            $partida->update();

            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "actualizar la cantidad");
        }
    }

    /**
     * Mostar los datos de la requisicion 
     */
    public function show($id)
    {
        try
        {
            $requi = Requisicion::with("tipo")->findOrFail($id);
            $partidas = [];

            switch ($requi->tipo->ruta)
            {
                case Tipo::$MATERIALES:
                    $partidas = PartidaMaterial::with("unidadMedida")
                        ->byRequisicion($requi->id)->get();
                    break;
                default:
                    throw new Exception($requi->tipo->ruta . ": Tipo No implementado", 501);
                    break;
            }

            // Obtener los detalles
            return $this->successResponse("info", ["requi" => $requi, "partidas" => $partidas]);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "obtener los datos de la requisicón");
        }
    }

    public function rechazar(Request $request)
    {
        try
        {
            $motivo_rechazo = LimpiarInput::LimpiarCampo($request->motivo);
            $requisicion = Requisicion::findOrFail($request->id);
            $requisicion->motivo_rechazo = $motivo_rechazo;
            $requisicion->condicion = Requisicion::$RECHAZADO_SUPERVISA;

            $requis_controller = new RequisicionesController();
            // TODO: agregar persona que rechaza
            $requis_controller->enviarNotificacionRechazo($requisicion);

            Auditoria::AuditarCambios($requisicion);
            $requisicion->update();

            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "rechazar la requisición");
        }
    }

    /**
     * Aprobar la requisicion por parte de almacén
     */

    public function aprobar(Request $request)
    {
        try
        {
            $requisicion = Requisicion::findOrFail($request->id);

            $partidas_pendientes = 0;
            switch ($requisicion->tipo->ruta)
            {
                case Tipo::$MATERIALES:
                    $partidas_pendientes = PartidaMaterial::byRequisicion($requisicion->id)
                        ->pendienteAlmacen()->count();
                    break;
                default:
                    throw new Exception($requisicion->tipo->ruta . ": Tipo No implementado", 501);
                    break;
            }

            if ($partidas_pendientes > 0)
            {
                return $this->errorResponse2("Existen partidas pendientes de revisar");
            }

            $requisicion->condicion = Requisicion::$APROBADO;
            $requisicion->aprueba_almacen_id = Auth::user()->empleado_id;
            $requis_controller = new RequisicionesController();
            // TODO: cambiar correo
            $requis_controller->enviarNotificacionSupervisa($requisicion);
            Auditoria::AuditarCambios($requisicion);
            $requisicion->update();

            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "aprobar la requisición");
        }
    }
}
