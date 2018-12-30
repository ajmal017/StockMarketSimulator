<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
//use App\Http\Controllers\MarketController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\MarketRank::class,
        Commands\InvestorRank::class,
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
        //$schedule->call('MarketController@update_market_all_cache')->hourlyAt(10);
        //$schedule->call('App\Http\Controllers\MarketController@update_market_all_cache')->everyMinute();
        $schedule->command('marketrank:update all')->hourlyAt(10);
        $schedule->command('investorrank:update active_shares true')->hourlyAt(5);
        $schedule->command('investorrank:update new_price')->cron('*/5 * * * * *');
        $schedule->command('investorrank:update rank')->hourlyAt(15);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');


    }
}
