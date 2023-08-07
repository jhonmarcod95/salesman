<?php

namespace App\Console\Commands;

use App\GlAccount;
use App\Http\Controllers\APIController;
use App\SalesmanInternalOrder;
use App\SapServer;
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
        //

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
            ], null);

            if ($io_detail){

                $gl_account = str_pad($io_detail['O_GLACCOUNT'], '10', '0', STR_PAD_LEFT);
                $gl_account_id = GlAccount::where('code', $gl_account)->pluck('id')->first();
                $uom = $this->toUnconverted($io_detail['O_UNIT']);

                $update_io = SalesmanInternalOrder::find($salesman_io->id);
                $update_io->gl_account_id = $gl_account_id;
                $update_io->uom = $uom;
                $update_io->save();
            }
        }

    }

    private function toUnconverted($uom){
        if ($uom == 'DAY'){
            $uom = 'TAG';
        }
        else if ($uom == 'D'){
            $uom = '10';
        }
        else if ($uom == 'PC'){
            $uom = 'ST';
        }
        return $uom;
    }
}
