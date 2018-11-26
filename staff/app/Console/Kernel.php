<?php

namespace App\Console;

use App\Console\Commands\ImportCountryImages;
use App\Console\Commands\ImportDotaChampions;
use App\Console\Commands\SyncEarnings;
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
        'App\Console\Commands\CallRoute',
        ImportCountryImages::class,
        SyncEarnings::class,
        ImportDotaChampions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('route:call --uri=/cron/matches')->everyMinute();
        $schedule->command('route:call --uri=/cron/leagues/save')->hourly();
        $schedule->command('route:call --uri=/cron/save_backlog')->everyMinute();

        //Get Toutou matches
        $schedule->command('route:call --uri=/cron/get_toutou')->everyMinute();
    }
}
