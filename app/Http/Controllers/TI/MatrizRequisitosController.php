<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\MatrizRequisitos;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatrizRequisitosController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarMatrizRequisitos(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                [
                    "puesto","software", "equipo", "accesorios", "impresora", "otro"
                ]
            );
            if ($request->id == null)
            {
                $matrizrequisitos = new MatrizRequisitos($datos);
                $matrizrequisitos->empleado_registra_id = Auth::user()->empleado_id;
                $matrizrequisitos->puesto_id=0; // Se cambia el puesto por manual
                $matrizrequisitos->save();
                Auditoria::AuditarCambios($matrizrequisitos);
            }
            else
            {
                $matrizrequisitos = MatrizRequisitos::find($request->id);
                $matrizrequisitos->fill($datos);
                Auditoria::AuditarCambios($matrizrequisitos);
                $matrizrequisitos->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el registro");
        }
    }

    /**
     * Obtener los equipos por puesto
     */
    public function ObtenerMatrizRequisitos()
    {
        try
        {
            $MatrizRequisitos = $this->ObtenerMatrizRequisitosAux();
            return Status::Success("matrizrequisitos", $MatrizRequisitos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los registros");
        }
    }

    public function ObtenerMatrizRequisitosAux()
    {
        $matrizrequisitos = DB::table("ti_matriz_requisitos as tmr")
            ->join("puestos as p2", "p2.id", "tmr.puesto_jefe_id")
            ->select(
                "tmr.id",
                "p2.nombre as puesto_jefe",
                "tmr.puesto",
                "tmr.puesto_jefe_id",
                "tmr.software",
                "tmr.equipo",
                "tmr.accesorios",
                "tmr.impresora",
                "tmr.red",
                "tmr.otro"
            )
            ->orderBy("puesto")
            ->get();
        return $matrizrequisitos;
    }

    /**
     * Descargar el reporte
     */
    public function Descargar()
    {
        try
        {
            $matrizrequisitos = $this->ObtenerMatrizRequisitosAux();
            $pdf = Facade::loadView("pdf.ti.matrizrequisitos", compact("matrizrequisitos"));
            $pdf->setPaper("letter", "portrait");
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream(" PTI-01/F-01 MATRIZ DE REQUISITOS DE EQUIPO POR PUESTO");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
