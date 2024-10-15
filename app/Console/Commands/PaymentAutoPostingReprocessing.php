<?php

namespace App\Console\Commands;

use App\CronLog;
use Illuminate\Console\Command;

class PaymentAutoPostingReprocessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autopostingreprocessing {sap_server}';

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
        CronLog::create(['name' => $this->signature]);

        $sap_server = $this->argument('sap_server');
        $back_dates = [
            // '1st_week' => ['2024-09-02','2024-09-08',true],
            // '2nd_week' => ['2024-09-09','2024-09-15',true],
            // '3rd_week' => ['2024-09-16','2024-09-22',true],
            // '4th_week' => ['2024-09-23','2024-09-30',true],
            // '5th_week' => ['2024-09-30','2024-10-06',true],
            '6th_week' => ['2024-10-07','2024-10-13',true]
        ];

        foreach($back_dates as $back_date){
            $paymentAutoPosting = new PaymentAutoPosting();
            $paymentAutoPosting->generateExpense($back_date[0],$back_date[1], $sap_server,$back_date[2]);
        }
    }
}
