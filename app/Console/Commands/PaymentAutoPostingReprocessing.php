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
            // '6th_week' => ['2024-10-07','2024-10-13',true],
            //  '7th_week' => ['2024-10-28','2024-11-03',true]
            //  '8th_week' => ['2024-11-04','2024-11-10',true],
            //  '9th_week' => ['2024-11-11','2024-11-17',true],
            //  '10th_week' => ['2024-11-18','2024-11-24',true],
            //  '11th_week' => ['2024-11-25','2024-12-01',true],
            //  '12th_week' => ['2024-12-02','2024-12-08',true],
            //  '13th_week' => ['2024-12-09','2024-12-15',true],
            //  '14th_week' => ['2024-12-16','2024-12-22',true],
            //  '15th_week' => ['2024-12-23','2024-12-29',true],
            //  '16th_week' => ['2024-12-30','2025-01-05',true],
            //  '17th_week' => ['2025-01-06','2025-01-12',true],
            //  '18th_week' => ['2025-01-13','2025-01-19',true],
            //  '19th_week' => ['2025-01-20','2025-01-26',true],
             '20th_week' => ['2025-01-27','2025-02-02',true],
             '21st_week' => ['2025-02-03','2025-02-09',true],
             '22nd_week' => ['2025-02-10','2025-02-16',true]
        ];

        foreach($back_dates as $back_date){
            $paymentAutoPosting = new PaymentAutoPosting();
            $paymentAutoPosting->generateExpense($back_date[0],$back_date[1], $sap_server,$back_date[2]);
        }
    }
}
