<?php

namespace App\Http\Controllers\Enfermeria;

use App\EnfermeriaModels\AtencionMedica;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AtencionMedicaController extends Controller
{
    private $empleados = [];
    /**
     * Obtener todas las atenciones del empleado ingresado
     */
    public function ObtenerAtenciones()
    {
        try
        {
            $atenciones = DB::table("enfermeria_atencion_medica as eam")
                ->join("empleados as e", "e.id", "eam.empleado_id")
                ->join("enfermeria_motivo_atencion as ema", "ema.id", "eam.motivo_id")
                ->select(
                    "eam.id",
                    "eam.fecha",
                    "eam.empleado_id",
                    "eam.medicamentos",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "ema.nombre as motivo",
                    "ema.tipo"
                )
                ->get();
            return Status::Success("atenciones", $atenciones);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las atenciones médicas");
        }
    }

    /**
     * Guarda la asistenica medica
     */
    public function GuardarAtencion(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["medicamentos"]);
            if ($request->id == null)
            {
                $atencion = new AtencionMedica($datos);
                $atencion->empleado_registra_id = Auth::user()->empleado_id;
                $atencion->save();
                Auditoria::AuditarCambios($atencion);
            }
            else
            {
                $atencion = AtencionMedica::find($request->id);
                $atencion->fill($datos);
                Auditoria::AuditarCambios($atencion);
                $atencion->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la atención médica");
        }
    }

    /**
     * 
     */
    public function Yolo()
    {
        try
        {
            $this->empleados = $this->Empleado();
            $MAX = 50;
            $medicamentos = [
                "aspirina", "paracetamol", "ibuprofeno", "suero", "diclofenaco", "tamizol",
                "gotas", "jabon", "agua", "acohol"
            ];
            $MAX_Motivos = DB::table("enfermeria_motivo_atencion")->count("*");
            DB::beginTransaction();
            $asd=0;
            for ($i = 0; $i < $MAX; $i++)
            {
                $asd++;
                $index = random_int(0, count($this->empleados)-1);
                $emp=$this->empleados[$index];
                $emp_id = $emp["emp_id"];
                $puesto_id = $emp["puesto_id"];
                $asd = new AtencionMedica(
                    [
                        "fecha" => Carbon::create(2022, random_int(1, 8), random_int(1, 29)),
                        "empleado_id" => $emp_id, // Empleado
                        "motivo_id" => random_int(1, $MAX_Motivos), // Motivo
                        "puesto_id" => $puesto_id,
                        "medicamentos" => $medicamentos[random_int(0, count($medicamentos) - 1)] // Medicamento
                    ]
                );
                $asd->empleado_registra_id = Auth::user()->empleado_id;
                $asd->save();
            }
            DB::commit();
            return "ok";
        }
        catch (Exception $e)
        {
            DB::rollBack();
        }
    }

    public function Empleado()
    {
        $empleados_aux = DB::table("empleados as e")
            ->select(
                "e.id as emp_id"
            )
            ->where("e.condicion", 1)
            ->get();

        // Obtener el puesto (primero activo)
        $empleados = [];
        foreach ($empleados_aux as $e)
        {
            $contrato = DB::table("contratos as c")
                ->join("puestos as p", "p.id", "c.puesto_id")
                ->select(
                    "p.id as puesto_id"
                )
                ->where("c.empleado_id", $e->emp_id)
                ->where("c.condicion", 1)
                ->first();
            if ($contrato == null) continue; // Sin contrato
            $empleados[] = array_merge((array)$e, (array)$contrato);
        }
        return $empleados;
    }
}
