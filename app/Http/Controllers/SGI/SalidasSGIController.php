<?php

namespace App\Http\Controllers\SGI;

use App\Exports\SGI\BitacoraSalidasNCExport;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\SGIModels\Departamento;
use App\SGIModels\SalidaNC;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SalidasSGIController extends Controller
{
    /**
     * Registrar
     */
    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            if ($request->id)
                return $this->update($request, $request->id);
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                ["descripcion", "tratamiento_otro",  "cliente_proveedor"]
            );
            $salidanc = new SalidaNC($datos);
            $folio = $this->generarFolio($request->fecha_deteccion);
            $salidanc->empleado_registra_id = Auth::user()->empleado_id;
            $salidanc->folio = $folio;
            $salidanc->save();
            Auditoria::AuditarCambios($salidanc);
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la salida no conforme");
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                ["descripcion", "tratamiento_otro", "cliente_proveedor"]
            );
            $salidanc = SalidaNC::find($id);
            $salidanc->fill($datos);
            Auditoria::AuditarCambios($salidanc);
            $salidanc->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "actualizar la salida no conforme");
        }
    }

    /**
     * 
     */
    public function obtener($anio)
    {
        try
        {
            $salidas = SalidaNC::porAnio($anio)->todos()->get();
            return Status::Success("salidanc", $salidas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las salidas no conformes");
        }
    }

    /**
     * Generar folio de la salida
     */
    private function generarFolio($fecha)
    {
        $aux_fecha = Carbon::parse($fecha);
        $anio = $aux_fecha->year;
        $n = SalidaNC::whereYear("fecha_elaboracion", $anio)->count() + 1;
        $folio = "CSFW-SNC-$anio-" . str_pad($n, 3, "0", STR_PAD_LEFT);
        return $folio;
    }

    /**
     * Generar documento de la salida
     */
    public function Descargar($id)
    {
        $salida = SalidaNC::todos()->find($id);
        $pdf = PDF::loadView("pdf.sgi.salidanc", compact("salida"));
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream();
    }

    public function DepartamentosSalidas()
    {
        $departamentos = Departamento::all();
        return Status::Success("departamentos", $departamentos);
    }

    /**
     * Generar la bitacora de las salida
     */
    public function DescargarBitacora($anio)
    {
        ob_end_clean();
        ob_start();
        return Excel::download(new BitacoraSalidasNCExport($anio), "PCC-14_F-02_BITÁCORA DE SALIDAS NO CONFORMES_$anio.xlsx");
    }
}
