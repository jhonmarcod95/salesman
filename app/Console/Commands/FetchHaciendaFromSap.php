<?php

namespace App\Console\Commands;

use App\Http\Controllers\APIController;
use Illuminate\Console\Command;
use App\SapServer;
use App\SapUser;

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
        
        return $planters;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Fetch from sap dev server');
        $this->infor(' result: '. $this->fetchFromSap());
    }
}
