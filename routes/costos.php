<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    Route::get("costos/proyecto/viaticos/{p_id}", "Costos\CostoProyectoController@ObtenerViaticos");
});
