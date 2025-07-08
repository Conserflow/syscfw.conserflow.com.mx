<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post ("controlw/auth", "APISistema\ControlW@Auth");

Route::middleware('auth:api')->get('/user', function (Request $request)
{
    return $request->user();
});
