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
            //  '20th_week' => ['2025-01-27','2025-02-02',true],
            //  '21st_week' => ['2025-02-03','2025-02-09',true],
            //  '22nd_week' => ['2025-02-10','2025-02-16',true],
            //  '23rd_week' => ['2025-02-17','2025-02-23',true],
            //  '24th_week' => ['2025-02-24','2025-03-02',true],
            //  '25th_week' => ['2025-03-03','2025-03-09',true],
            //  '26th_week' => ['2025-03-10','2025-03-16',true],
            //  '27th_week' => ['2025-03-17','2025-03-23',true],
            //  '28th_week' => ['2025-03-24','2025-03-30',true],
            // '29th_week' => ['2025-03-31','2025-04-06',true],
            // '30th_week' => ['2025-04-07','2025-04-13',true],
            // '31st_week' => ['2025-04-14','2025-04-20',true],
            // '32nd_week' => ['2025-04-21','2025-04-27',true],
            // '33rd_week' => ['2025-04-28','2025-05-04',true],
            // '34th_week' => ['2025-05-05','2025-05-11',true],
            // '35th_week' => ['2025-05-12','2025-05-18',true],
            // '36th_week' => ['2025-05-19','2025-05-25',true],
            // '37th_week' => ['2025-05-26','2025-06-01',true],
            // '38th_week' => ['2025-06-02','2025-06-08',true],
            // '39th_week' => ['2025-06-09','2025-06-15',true],
            // '40th_week' => ['2025-06-16','2025-06-22',true],
                '41st_week' => ['2025-06-30','2025-07-06',true],
                '42nd_week' => ['2025-07-07','2025-07-13',true],
                '43rd_week' => ['2025-07-14','2025-07-20',true]
        ];

        foreach($back_dates as $back_date){
            $paymentAutoPosting = new PaymentAutoPosting();
            $paymentAutoPosting->generateExpense($back_date[0],$back_date[1], $sap_server,$back_date[2]);
        }

        echo $sap_server.' payment postings reprocessed.';
    }
}
