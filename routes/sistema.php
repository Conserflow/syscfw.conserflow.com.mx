<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function ()
{
    // Permisos
    Route::get("sistema/usuariosactivos", "Sistema\UsuariosController@GetUsers");
    Route::put("sistema/desactivarmenu", "Sistema\UsuariosController@DesactivarMenu");
   });
