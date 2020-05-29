<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PaymentAutoPostingMonthEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autopostingmonthend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $thisMonday = date("Y-m-d", strtotime("last monday"));
        $lastDayMonth = date("Y-m-d", strtotime("last day of this month"));

        $paymentAutoPosting = new PaymentAutoPosting();
        $paymentAutoPosting->generateExpense($thisMonday, $lastDayMonth);
    }
}
