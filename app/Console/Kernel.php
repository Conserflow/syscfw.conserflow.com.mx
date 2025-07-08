<?php

namespace App\Console;

use App\Jobs\ProcessPeriodoVacaciones;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\EquiposCalibracionCommand::class
    ];



    /**
     * Define the application"s command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command("calidad:equiposcalibracion")
            ->runInBackground()->monthlyOn(1);

        $schedule->job(new ProcessPeriodoVacaciones)->weekly()->saturdays()->at("08:00");
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . "/Commands");
        require base_path("routes/console.php");
    }
}
