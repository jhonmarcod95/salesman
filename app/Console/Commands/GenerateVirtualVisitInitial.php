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
    }

    public function getCustomers($tsr_customer_code)
    {
        return  TsrSapCustomer::with('customer')
            ->where('tsr_customer_code', $tsr_customer_code)
            ->where('customer_code', '!=', $tsr_customer_code)
            ->whereHas('customer', function ($q) {
                $q->where('name', 'not like', '%X:%');
                // $q->orWhere('name','not like','%XXX_%');
            })
            ->get();
    }

    public function fetchSAPApi()
    {
        $this->info("\n\n" . "Fetch API SAP First...." . "\n");

        $tsr = User::whereNotNull('tsr_customer_code_pfmc')
            ->orWhereNotNull('tsr_customer_code_lfug')
            ->take(1)
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
                                'code' => $customer['customer_code'],
                                'name' => $customer['customer'] ? $customer['customer']['name'] : "",
                                'address' => $customer['customer'] ? $customer['customer']['city'] : "",
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
                                'customer_code' => $customer['customer_code'],
                                'customer_name' => $customer['customer'] ? $customer['customer']['name'] : "",
                                'address' => $customer['customer'] ? $customer['customer']['city'] : "",
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }
        }

        $this->finalArray = collect($tsr_customer_arr)
            ->groupBy('user_id')
            ->values();

        $this->info("End Fetch API SAP!" . "\n");

    }

    public function genearateInitialVisit()
    {
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
                }

                $this->output->progressFinish();
            });

            $this->info("Generate Done" . "\n");

    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fetchSAPApi();
        // dd($this->finalArray);
        $this->genearateInitialVisit();
    }
}
