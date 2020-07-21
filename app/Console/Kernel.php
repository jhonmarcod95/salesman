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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //auto posting
        $schedule->command('payment:autoposting')
            ->weekly()->tuesdays()->at('00:01');

        //auto cv
        $schedule->command('payment:autocv')
            ->weekly()->tuesdays()->at('00:21');

        //auto cv
        $schedule->command('payment:autocheck')
            ->weekly()->tuesdays()->at('00:31');

        //auto posting month end
        $schedule->command('payment:autopostingmonthend')->monthlyOn(date('t'), '23:00');
        $schedule->command('payment:autocv')->monthlyOn(date('t'), '23:21');
        $schedule->command('payment:autocheck')->monthlyOn(date('t'), '23:31');

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
