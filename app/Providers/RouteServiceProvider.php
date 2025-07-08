<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(function ()
            {
                require base_path('routes/vehiculos.php');
                require base_path('routes/ventas.php');
                require base_path('routes/compras.php');
                require base_path('routes/seguridad.php');
                require base_path('routes/tesoreria.php');
                require base_path('routes/generales.php');
                require base_path('routes/rh.php');
                require base_path('routes/calidad.php');
                require base_path('routes/sistema.php');
                require base_path('routes/ti.php');
                require base_path('routes/enfermeria.php');
                require base_path('routes/sgi.php');
                require base_path('routes/almacen.php');
                require base_path('routes/costos.php');
                require base_path('routes/salidassgi.php');
                require base_path('routes/requisicion.php');
                require base_path('routes/web.php'); // Resto
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
