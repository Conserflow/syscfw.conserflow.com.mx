<?php

namespace App\Http\Controllers\Requisiciones;

use App\Exports\Requisiciones\MaterialPrincipalExport;
use App\Http\Controllers\Controller;
use App\RequisicionModels\Requisicion;
use App\RequisicionModels\Tipo;
use Maatwebsite\Excel\Facades\Excel;

class DescargarController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function descargar($id)
    {
        // Obtener la requi
        $requi = Requisicion::findOrFail($id);
        switch ($requi->tipo->ruta)
        {
            case Tipo::$MATERIALES:
                return Excel::download(new MaterialPrincipalExport($id), "$requi->folio.xlsx");
                break;
            default:
                return redirect("404");
        }
    }
}
