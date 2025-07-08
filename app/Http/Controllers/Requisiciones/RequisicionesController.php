<?php

namespace App\Http\Controllers\Requisiciones;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Helpers\Tables;
use App\Http\Helpers\Utilidades;
use App\RequisicionModels\PartidaMaterial;
use App\RequisicionModels\Requisicion;
use App\RequisicionModels\Tipo;
use App\Traits\StatusResponse;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Constraint\ExceptionCode;

class RequisicionesController extends Controller
{
    use StatusResponse;

    public function __construct()
    {
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
                    "requisiciones2.folio as requisiciones2__id",
                )
                ->withTrashed();

            return Tables::search($requisiciones, $request, []);
        }
        catch (ExceptionCode $e)
        {
            return $this->errorResponse($e, "obtener las requisiciones");
        }
    }

    /**
     * Mostar los datos de la requisicion 
     */
    public function show($id)
    {
        try
        {
            $requi = Requisicion::with("tipo")
                ->findOrFail($id);
            return $this->successResponse("info", $requi);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "obtener los datos de la requisicón");
        }
    }

    public function store(Request $request)
    {
        try
        {
            $notas = LimpiarInput::LimpiarCampo($request->notas);
            $requisicion = new Requisicion($request->all());
            $requisicion->folio = Utilidades::folioRequisicion("REQ", $request->proyecto_id);
            $requisicion->revision = "-1";
            $requisicion->notas = $notas;
            $requisicion->condicion = Requisicion::$NUEVO;
            $requisicion->proyecto_id = $request->proyecto_id;
            $requisicion->tipo_id = $request->tipo_id;
            $requisicion->empleado_solicita_id = Auth::user()->empleado_id;
            $requisicion->empleado_aprueba_id = $request->empleado_aprueba_id;
            $requisicion->fecha_emision = date("Y-m-d");
            $requisicion->save();
            Auditoria::AuditarCambios($requisicion);
            $requisicion->load("tipo");

            return $this->successResponse("requisicion", $requisicion);
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "guardar la requisición");
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $notas = LimpiarInput::LimpiarCampo($request->notas);
            $requisicion = Requisicion::find($id);
            $requisicion->fill($request->all());
            $requisicion->notas = $notas;
            $requisicion->empleado_aprueba_id = $request->empleado_aprueba_id;
            Auditoria::AuditarCambios($requisicion);
            $requisicion->update();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "actualizar la requisición");
        }
    }

    /**
     * Eliminar la requi
     */
    public function eliminar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $motivo = $request->motivo;
            $requi = Requisicion::find($request->id);
            $requi->motivo_eliminacion = $motivo;
            $requi->condicion = Requisicion::$ELIMINADO;
            $requi->update();
            $requi->delete();
            Auditoria::AuditarCambios($requi);

            DB::commit();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse($e, "eliminar la requisición");
        }
    }

    /**
     * Cerrar la requisicion
     */
    public function cerrar(Request $request)
    {
        try
        {
            $requisicion = Requisicion::find($request->id);

            if ($this->requiereValidacionAlmacen($requisicion))
            {
                $this->enviarNotificacionAlmacen($requisicion);
                $requisicion->condicion = Requisicion::$VALIDACION_ALMACEN;
            }
            else
            {
                $requisicion->condicion = Requisicion::$VALIDACION_SUPERVISA;
                $this->enviarNotificacionSupervisa($requisicion);
            }

            $requisicion->fecha_emision = date("Y-m-d");
            $requisicion->cambiarRevision();
            Auditoria::AuditarCambios($requisicion);
            $requisicion->update();
            return $this->emptyResponse();
        }
        catch (Exception $e)
        {
            return $this->errorResponse($e, "cerrar la requisición");
        }
    }

    /**
     * Mandar notificacion cuando se cierra la requisicions
     * @param Requisicion $requi Requisición a cerrar
     */
    public function enviarNotificacionAlmacen($requi)
    {
        $correo = "";
        $partidas = [];
        $total_partidas = 0;

        switch ($requi->tipo->ruta)
        {
            case Tipo::$MATERIALES:
                $correo = "almacen.tehuacan@conserflow.com";
                $partidas = PartidaMaterial::byRequisicion($requi->id)
                    ->soloMateriales()->limit(10)->get();
                $total_partidas = PartidaMaterial::byRequisicion($requi->id)
                    ->soloMateriales()->count();
                break;
        }

        if (config("app.debug"))
        {
            $correo = explode(",", config("app.mail_errores"));
        }

        $solicita = $requi->solicita;

        $data = [
            "partidas" => $requi->partidas,
            "folio" => $requi->folio,
            "area_solicita" => $requi->area->nombre,
            "proyecto" => $requi->proyecto->nombre_corto,
            "solicita" => "$solicita->nombre $solicita->ap_paterno $solicita->ap_materno",
            "total_partidas" => $total_partidas,
            "partidas" => $partidas,
        ];

        Mail::send("emails.requisicion.validar_almacen", $data, function ($message) use ($correo)
        {
            $message->to($correo)->subject("NOTIFICACIÓN DE REQUISICIÓN");
            $message->from("webmaster@conserflow.com", "Conserflow");
        });
    }

    /**
     * Mandar notificacion cuando se cierra la requisicions
     * @param Requisicion $requi Requisición a cerrar
     */
    public function enviarNotificacionSupervisa($requi)
    {
        $correo = User::byEmpleado($requi->empleado_aprueba_id)->first()->email;
        $partidas = PartidaMaterial::byRequisicion($requi->id)->limit(10)->get();
        $total_partidas = PartidaMaterial::byRequisicion($requi->id)
            ->soloMateriales()->count();

        if (config("app.debug"))
        {
            $correo = explode(",", config("app.mail_errores"));
        }

        $solicita = $requi->solicita;

        $data = [
            "partidas" => $requi->partidas,
            "folio" => $requi->folio,
            "area_solicita" => $requi->area->nombre,
            "proyecto" => $requi->proyecto->nombre_corto,
            "solicita" => "$solicita->nombre $solicita->ap_paterno $solicita->ap_materno",
            "total_partidas" => $total_partidas,
            "partidas" => $partidas,
        ];

        Mail::send("emails.requisicion.validar_supervisa", $data, function ($message) use ($correo)
        {
            $message->to($correo)->subject("NOTIFICACIÓN DE REQUISICIÓN");
            $message->from("webmaster@conserflow.com", "Conserflow");
        });
    }

    /**
     * Mandar notificacion cuando se cierra la requisicions
     * @param Requisicion $requi Requisición a cerrar
     */
    public function enviarNotificacionRechazo($requi)
    {
        $correo = User::byEmpleado($requi->empleado_aprueba_id)->first()->email;
        $partidas = PartidaMaterial::byRequisicion($requi->id)->limit(10)->get();
        $total_partidas = PartidaMaterial::byRequisicion($requi->id)
            ->soloMateriales()->count();

        if (config("app.debug"))
        {
            $correo = explode(",", config("app.mail_errores"));
        }

        $solicita = $requi->solicita;

        $data = [
            "partidas" => $requi->partidas,
            "folio" => $requi->folio,
            "area_solicita" => $requi->area->nombre,
            "proyecto" => $requi->proyecto->nombre_corto,
            "solicita" => "$solicita->nombre $solicita->ap_paterno $solicita->ap_materno",
            "total_partidas" => $total_partidas,
            "partidas" => $partidas,
        ];

        Mail::send("emails.requisicion.rechazado", $data, function ($message) use ($correo)
        {
            $message->to($correo)->subject("NOTIFICACIÓN DE REQUISICIÓN RECHAZADA");
            $message->from("webmaster@conserflow.com", "Conserflow");
        });
    }

    /**
     * Comprueba si la requisicion requiere revisión de almacen
     */
    public function requiereValidacionAlmacen($requi)
    {
        // De tipo material
        if (!in_array($requi->tipo->ruta, [Tipo::$MATERIALES])) return false;

        if ($requi->tipo->ruta == Tipo::$MATERIALES)
        {
            return PartidaMaterial::byRequisicion($requi->id)
                ->soloMateriales()->count() > 0;
        }
        return false;
    }
}
