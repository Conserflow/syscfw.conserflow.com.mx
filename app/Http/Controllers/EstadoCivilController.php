<?php

namespace App\Http\Controllers;

use App\EstadoCivil;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \App\Http\Helpers\Utilidades;


class EstadoCivilController extends Controller
{


  public function index()
  {

    $dias = DB::table('estados_civiles')
      ->select('estados_civiles.*')
      ->get();
    return response()->json(
      $dias->toArray()
    );
  }
}
