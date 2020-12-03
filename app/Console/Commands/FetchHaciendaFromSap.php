<?php

namespace App\Console\Commands;

use App\Http\Controllers\APIController;
use Illuminate\Console\Command;
use App\SapServer;
use App\SapUser;
use App\PlanterHacienda;
use DB;
use Carbon\Carbon;

class FetchHaciendaFromSap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetch-haciendas-sap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch hacienda from SAP';

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
     * Fetch function
     */
    public function fetchFromSap()
    {
        $sap_server = SapServer::where('sap_server', 'PFMC')->first();
        $sap_user = SapUser::where('user_id', 175)->where('sap_server', 'PFMC')->first();

        // $connection = [
        //     'ashost' => $sap_server->app_server,
        //     'sysnr' => $sap_server->system_number,
        //     'client' => $sap_server->client,
        //     'user' => $sap_user->sap_id,
        //     'passwd' => $sap_user->sap_password,
        // ];
        $connection = [
            'ashost' => "172.17.1.34",
            'sysnr' => "00",
            'client' => "778",
            'user' => "payproject",
            'passwd' => "welcome69+",
        ];

        $planters = APIController::executeSapFunction($connection, 'ZFM_CMS', [], null);

        $collecPlanters = collect($planters['CMS_OUT'])->sortBy('PL_ID', SORT_NATURAL);

        $this->info("Storing planter hacienda" . "\n");
        $this->output->progressStart(count($collecPlanters));

        foreach ($collecPlanters as $planter) {
            sleep(1);

            // $this->info(dd($planter));

            // $planterHacienda = PlanterHacienda::firstOrCreate(
            //     [
            //         'hacienda_code' => $planter->H_HCODE,
            //     ],
            //     [
            //         // 'planter_id' => 0,
            //         'mobile_number' => $planter->PL_MOBILE,
            //         'planter_code' => $planter->PL_CODE,
            //         'name' => $planter->PL_SNAME,
            //         'planter_audit_no' => $planter->H_PAN,
            //         'address' =>  $planter->BG_NAME . " " . $planter->DI_NAME . " " . $planter->PR_NAME,
            //         'area' => $planter->H_AREA,
            //         'created_at' => Carbon::now(),
            //         'updated_at' => Carbon::now()
            //     ]
            // );

            $planterHacienda = new PlanterHacienda;
            $planterHacienda->mobile_number = $planter->PL_MOBILE;
            $planterHacienda->planter_code = $planter->PL_CODE;
            $planterHacienda->hacienda_code = $planter->H_HCODE;
            $planterHacienda->name = $planter->PL_SNAME;
            $planterHacienda->planter_audit_no = $planter->H_PAN;
            $planterHacienda->address =  $planter->BG_NAME . " " . $planter->DI_NAME . " " . $planter->PR_NAME;
            $planterHacienda->area = $planter->H_AREA;
            $planterHacienda->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("Storing Done: " . "\n");

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Fetch from sap dev server');
        // dd($this->fetchFromSap());
        // $this->info($this->fetchFromSap());
        $this->fetchFromSap();
    }
}
