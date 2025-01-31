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
        Commands\MonthlyVerificationCapture::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /* begin:: every monday *******************/

        #Capture verified expenses
        $schedule->command('capture:monthly-verified')
            ->weekly()
            ->mondays()
            ->at('23:30');

        /* end:: every monday *********************/

        
        /* begin:: every month *********************/

        #Capture verified expenses
        $schedule->command('capture:monthly-verified')
            ->monthlyOn(9, '23:30');


        $schedule->command('capture:monthly-verified')
            ->monthlyOn(10, '23:50');

        /* end:: every month ***********************/


        /* begin:: every tuesday *******************/

        // auto posting
        $schedule->command('payment:autopostingreprocessing LFUG')
            ->weekly()
            ->tuesdays()
            ->at('00:01');
        
        $schedule->command('payment:autopostingreprocessing HANA')
            ->weekly()
            ->tuesdays()
            ->at('00:15');

        $schedule->command('payment:autoposting LFUG')
            ->weekly()
            ->tuesdays()
            ->at('00:30');

        $schedule->command('payment:autoposting HANA')
            ->weekly()
            ->tuesdays()
            ->at('00:45');

        // auto cv
        $schedule->command('payment:autocv')
            ->weekly()
            ->tuesdays()
            ->at('01:00');

        // auto check
        $schedule->command('payment:autocheck')
            ->weekly()
            ->tuesdays()
            ->at('01:15');

        /* end:: every tuesday **********************/


        /* begin:: every month end *******************/
        $schedule->command('payment:autopostingreprocessing HANA')
            ->monthlyOn(date('t'), '23:00');

        $schedule->command('payment:autopostingreprocessing LFUG')
            ->monthlyOn(date('t'), '23:07');

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
            ->mondays()
            ->at('22:30');
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
