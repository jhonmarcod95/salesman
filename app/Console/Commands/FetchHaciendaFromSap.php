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

        $connection = [
            'ashost' => $sap_server->app_server,
            'sysnr' => $sap_server->system_number,
            'client' => $sap_server->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];

        $planters = APIController::executeSapFunction($connection, 'ZFM_CMS', [], null);

        $collecPlanters = collect($planters['CMS_OUT'])
                    ->take(100);

        foreach ($collecPlanters as $planter) {
            DB::table('planter_haciendas')->insert([
                array(
                    'planter_id' => 0,
                    'planter_code' => $planter->PL_CODE,
                    'name' => $planter->PL_SNAME,
                    'mobile_number' => $planter->PL_MOBILE,
                    'hacienda_code' => $planter->H_HCODE,
                    'planter_audit_no' => $planter->H_PAN,
                    'address' =>  $planter->BG_NAME." ".$planter->DI_NAME." ".$planter->PR_NAME,
                    'area' => $planter->H_AREA,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                )
            ]);
        }

        $checkLastSave = PlanterHacienda::orderBy('id','desc')->first();

        return $checkLastSave;

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
        $this->info($this->fetchFromSap());
    }
}
