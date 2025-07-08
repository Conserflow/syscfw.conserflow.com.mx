<?php

namespace App\Http\Controllers\Seguridad;

use App\Exports\Seguridad\ReporteEppExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ReporteConsumoController extends Controller
{

    public function Reporte()
    {
        try
        {
            ob_end_clean();
            return Excel::download(new ReporteEppExport(), 'Reporte EPP.xlsx');
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
