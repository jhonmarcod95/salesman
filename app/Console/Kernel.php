<?php

namespace App\Console;

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
        Commands\PaymentAutoPosting::class,
        Commands\PaymentAutoCv::class,
        Commands\PaymentAutoPostingMonthEnd::class,
        Commands\EmailCustomerVisit::class,
        Commands\GetCustomerOrder::class,
        Commands\SapCustomerCode::class,
        Commands\FetchHaciendaFromSap::class,
        Commands\UpdateIODetails::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /* begin:: every tuesday *******************/

        // auto posting
        $schedule->command('payment:autopostingreprocessing LFUG')
            ->weekly()
            ->tuesdays()
            ->at('15:30');
        
        $schedule->command('payment:autopostingreprocessing HANA')
            ->weekly()
            ->tuesdays()
            ->at('15:50');

        $schedule->command('payment:autoposting LFUG')
            ->weekly()
            ->tuesdays()
            ->at('16:10');

        $schedule->command('payment:autoposting HANA')
            ->weekly()
            ->tuesdays()
            ->at('16:30');

        // auto cv
        $schedule->command('payment:autocv')
            ->weekly()
            ->tuesdays()
            ->at('16:45');

        // auto check
        $schedule->command('payment:autocheck')
            ->weekly()
            ->tuesdays()
            ->at('17:00');

        /* end:: every tuesday **********************/


        /* begin:: every month end *******************/

        $schedule->command('payment:autopostingmonthend HANA')
            ->monthlyOn(date('t'), '23:15');

        $schedule->command('payment:autopostingmonthend LFUG')
            ->monthlyOn(date('t'), '23:30');

        $schedule->command('payment:autocv')
            ->monthlyOn(date('t'), '23:45');

        $schedule->command('payment:autocheck')
            ->monthlyOn(date('t'), '23:50');

        /* end:: every month end *********************/


        /* begin:: daily *****************************/

        //Get Customer Order
        $schedule->command('command:customer_order')->dailyAt('6:00');

        //Get Customer Order
        $schedule->command('command:sap_customer_codes')->dailyAt('23:59');

        /* end:: daily *******************************/

        // update IO details
        $schedule->command('update:IODetails')
            ->weekly()
            ->mondays();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
