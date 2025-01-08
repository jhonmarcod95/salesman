<?php

namespace App\Console\Commands;

use App\GlAccount;
use App\Http\Controllers\APIController;
use App\SalesmanInternalOrder;
use App\SapServer;
use App\CronLog;
use App\SapUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Tests\Feature\ApiTest;

class UpdateIODetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:IODetails';

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
        $salesman_ios = SalesmanInternalOrder::get();
        foreach ($salesman_ios as $salesman_io){

            $sap_server = SapServer::where('sap_server', $salesman_io->sap_server)->first();
            $sap_user = SapUser::where('user_id', 175)->where('sap_server', $sap_server->sap_server)->first();
            $sap_connection = [
                'ashost' => $sap_server->app_server,
                'sysnr' => $sap_server->system_number,
                'client' => $sap_server->client,
                'user' => $sap_user->sap_id,
                'passwd' => $sap_user->sap_password
            ];

            $io_detail = APIController::executeSapFunction($sap_connection, 'ZFM_SUPBUD_INTEG', [
                'I_AUFNR' => $salesman_io->internal_order,
                'I_PERIOD' => Carbon::now()->format('m'),
                'I_YEAR' => Carbon::now()->format('Y'),
            ], null,$sap_server->sap_server);

            if ($io_detail['O_UNIT']){
                $gl_account = str_pad($io_detail['O_GLACCOUNT'], '10', '0', STR_PAD_LEFT);
                $gl_account_id = GlAccount::where('code', $gl_account)->pluck('id')->first();
                $uom = $this->getSapUomValueConversion($io_detail['O_UNIT'],$sap_connection,$sap_server->sap_server);

                $update_io = SalesmanInternalOrder::find($salesman_io->id);
                $update_io->gl_account_id = $gl_account_id;
                $update_io->uom = $uom ? $uom['OUTPUT'] : $io_detail['O_UNIT'];
                $update_io->save();
            }
        }

    }

    /**
     * Get UOM conversion in SAP
     *
     */
    private function getSapUomValueConversion($uom,$sap_connection,$sap_server){
        return APIController::executeSapFunction($sap_connection,'ZCONVERSION_EXIT_CUNIT_INPUT',[
            'INPUT' => $uom
        ],null,$sap_server);
    }
}
