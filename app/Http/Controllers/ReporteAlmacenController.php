<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReporteAlmacenExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAlmacenController extends Controller
{

  public function Descargar(Request $request)
  {
    ob_end_clean();
    ob_start();
    return Excel::download(new ReporteAlmacenExport(), "ReporteAlmacen - " .  date("d-m-Y") . " .xlsx");
  }
}
