<?php

namespace App\Http\Controllers\Almacen;

use App\Exports\Almacen\ReporteContabilidadExport;
use App\Exports\Almacen\ExistenciasContabilidadExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ReportesContabilidadController extends Controller
{
    public function Salidas($anio, $mes)
    {
        try
        {
            ob_end_clean();
            ob_start();
            ini_set('max_execution_time', 3000);
            return Excel::download(new ReporteContabilidadExport($anio,$mes), 'Reporte_Salidas.xlsx');
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }
    
    public function Existencias($anio, $mes)
    {
        try
        {
            ob_end_clean();
            ob_start();
            ini_set('max_execution_time', 3000);
            return Excel::download(new ExistenciasContabilidadExport($anio,$mes), 'Reporte_Existencias.xlsx');
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }
}
