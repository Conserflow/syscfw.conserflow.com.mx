<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/checador',
        // Lector -> Almacén
        "/api/almacen/ingresar",
        "/api/almacen/guardar",
        "/api/almacen/registrarentrada",
        "/api/almacen/salidas/registra",
        "/api/almacen/salidas/resguardo",
        "/api/almacen/salidas/actualizarcodigo",

        "rh/checador/guardar", // Evitar 419 en Checador
        "sistema/todospermisosmodulo"
    ];
}
