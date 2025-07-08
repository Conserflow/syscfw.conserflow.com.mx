<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    // Clientes
    Route::resource("clientes", "Ventas\ClientesController");

    // Catalogos
    Route::get("ventas/catalogos/regimenfiscal", "Ventas\CatalogosController@ObtenerRegimen");
});
