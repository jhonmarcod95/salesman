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
            // '55th_week' => ['2025-10-27','2025-11-02'],
            // '56th_week' => ['2025-11-03','2025-11-09'],
            // '57th_week' => ['2025-11-10','2025-11-16'],
            // '58th_week' => ['2025-11-17','2025-11-23'],
            // '59th_week' => ['2025-11-24','2025-11-30'],
            '60th_week' => ['2025-12-01','2025-12-07'],
            '61st_week' => ['2025-12-08','2025-12-14'],
        ];

        foreach($back_dates as $back_date){
            $paymentAutoPosting = new PaymentAutoPosting();
            $paymentAutoPosting->generateExpense($back_date[0],$back_date[1], $sap_server);
        }

        echo $sap_server.' payment postings reprocessed.';
    }
}
