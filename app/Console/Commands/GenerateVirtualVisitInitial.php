<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use App\Schedule;
use Carbon\Carbon;
use DB;
use App\User;
use App\TsrSapCustomer;

class GenerateVirtualVisitInitial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate-initial-visit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch initial virutal visit from SAP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->finalArray = [];
        $this->counter = 0;
    }

    // public function getCustomers($tsr_customer_code)
    // {
    //     return  TsrSapCustomer::with('customer')
    //         ->where('tsr_customer_code', $tsr_customer_code)
    //         ->where('customer_code', '!=', $tsr_customer_code)
    //         ->whereHas('customer', function ($q) {
    //             $q->where('name', 'not like', '%X:%');
    //             // $q->orWhere('name','not like','%XXX_%');
    //         })
    //         ->get();
    // }

    public function getCustomers($tsr_customer_code)
    {
        return $customer_lists = DB::select(DB::raw("
                SELECT
                tsr_sap_customers.id,
                tsr_sap_customers.customer_code,
                tsr_sap_customers.tsr_customer_code,
                tsr_sap_customers.sales_organization,
                tsr_sap_customers.common_division,
                tsr_sap_customers.division,
                tsr_sap_customers.partner_function,
                tsr_sap_customers.`server`,
                tsr_sap_customers.created_at,
                tsr_sap_customers.updated_at,
                tsr_valid_customers.deletion_flag,
                tsr_valid_customers.customer_order_block,
                customer_codes.`name`,
                customer_codes.street,
                customer_codes.city
                FROM
                tsr_sap_customers
                INNER JOIN tsr_valid_customers ON tsr_sap_customers.customer_code = tsr_valid_customers.customer_code AND tsr_sap_customers.sales_organization = tsr_valid_customers.sales_organization AND tsr_sap_customers.common_division = tsr_valid_customers.common_division AND tsr_sap_customers.division = tsr_valid_customers.division
                INNER JOIN customer_codes ON tsr_sap_customers.customer_code = customer_codes.customer_code
                WHERE
                tsr_valid_customers.deletion_flag = '' AND
                tsr_valid_customers.customer_order_block = '' AND
                customer_codes.`name` NOT LIKE '%X:%' AND
                customer_codes.`name` NOT LIKE '%XXX%' AND
                customer_codes.`name` NOT LIKE '%ONETIME%' AND
                tsr_sap_customers.tsr_customer_code = '$tsr_customer_code' AND
                tsr_sap_customers.customer_code != '$tsr_customer_code'
                "));
    }

    public function fetchSAPApi()
    {
        ini_set('max_execution_time', 600);

        $this->info("\n\n" . "Fetch API SAP First...." . "\n");

        $tsr = User::whereNotNull('tsr_customer_code_pfmc')
            ->orWhereNotNull('tsr_customer_code_lfug')
            // ->take(1)
            ->get(['id', 'name', 'tsr_customer_code_pfmc', 'tsr_customer_code_lfug']);

        $tsr_customer_arr = [];

        foreach ($tsr as $item) {

            // for PFMC Server
            if ($item->tsr_customer_code_pfmc != '' || $item->tsr_customer_code_pfmc != null) {
                // convert to array
                $tsr_customer_code_pfmc = explode(',', $item->tsr_customer_code_pfmc);
                for ($x = 0; $x < count($tsr_customer_code_pfmc); $x++) {
                    $get_customer_pfmc = $this->getCustomers($tsr_customer_code_pfmc[$x]);
                    if ($get_customer_pfmc) {
                        foreach ($get_customer_pfmc as $customer) {
                            $data = array(
                                'user_id' => $item->id,
                                'salesman_name' => $item->name,
                                'code' => $customer->customer_code,
                                'name' => $customer ? $customer->name : "",
                                'address' => $customer ? $customer->city : "",
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }

            // for LFUG server
            if ($item->tsr_customer_code_lfug != '' || $item->tsr_customer_code_lfug != null) {
                // convert to array
                $tsr_customer_code_lfug = explode(',', $item->tsr_customer_code_lfug);
                for ($x = 0; $x < count($tsr_customer_code_lfug); $x++) {
                    $get_customer_pfmc = $this->getCustomers($tsr_customer_code_lfug[$x]);
                    if ($get_customer_pfmc) {
                        foreach ($get_customer_pfmc as $customer) {
                            $data = array(
                                'user_id' => $item->id,
                                'salesman_name' => $item->name,
                                'code' => $customer->customer_code,
                                'name' => $customer ? $customer->name : "",
                                'address' => $customer ? $customer->city : "",
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }
        }

        $this->finalArray = collect($tsr_customer_arr)
            ->groupBy('user_id')
            ->map(function ($item, $key) {
                return collect($item)->unique('code')->values();
            })
            ->values();

        $this->info("End Fetch API SAP!" . "\n");

    }

    public function genearateInitialVisit()
    {

       ini_set('max_execution_time', 600);

       collect($this->finalArray)
            ->map(function ($item, $key) {
                return collect($item)
                    ->map(function ($x, $k) {
                        return $x;
                        return array(
                            'user_id' => $x->user_id,
                            'type' => $x->type,
                            'code' => $x->code,
                            'name' => $x->name,
                            'address' => $x->address,
                            // 'date' => $x->date,
                            // 'start_time' => $x->start_time,
                            // 'end_time' => $x->end_time,
                            'lat' => 0,
                            'lng' => 0,
                            'status' => 2,
                            'is_generated' => 1,
                            'km_distance' => 0,
                        );
                    })->all();
            })->each(function ($items, $key) {

                $getChunk = ceil(count($items) / 5);


                $this->info("Generate Virutual Visit Schedules" . "\n");
                $this->output->progressStart(count($items));

                foreach (array_chunk($items, $getChunk) as $key => $item) {

                    sleep(1);

                    foreach ($item as $i) {

                        $schedule = Schedule::firstOrNew(
                            [
                                'code' => $i['code'],
                                'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
                            ],
                            [
                                'user_id' => $i['user_id'],
                                'type' => 7,
                                'name' => $i['name'],
                                'address' => $i['address'],
                                // 'start_time' => $i['start_time'],
                                // 'end_time' => $i['end_time'],
                                'lat' => 0,
                                'lng' => 0,
                                'status' => 2,
                                'is_generated' => 1,
                                'km_distance' => 0,
                                'created_at' =>  Carbon::now(),
                                'updated_at' =>  Carbon::now()
                            ]
                        );
                        $schedule->save();
                    }


                    $this->output->progressAdvance();
                     $this->counter + 1;
                }

                $this->output->progressFinish();
            });

            $this->info("Generate Done: " ."\n");

    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fetchSAPApi();
        // dd(count($this->finalArray));
        $this->genearateInitialVisit();
    }
}
