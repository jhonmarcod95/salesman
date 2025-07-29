<?php

namespace App\Console\Commands;

use App\CheckInfo;
use App\CheckInfoError;
use App\CheckVoucher;
use App\Http\Controllers\APIController;
use App\SapUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PaymentAutoCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autocheck';

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
        $thisMonday = date("Y-m-d", strtotime("last monday"));
        $thisSunday = date("Y-m-d", strtotime("this sunday"));

        $check_vouchers = CheckVoucher::with('company.sapServers', 'company.bankChecks')
            ->whereBetween('created_at', [$thisMonday, $thisSunday . ' 23:59:59'])
            ->whereDoesntHave('checkInfo') // CV without check number
            ->get();

        foreach ($check_vouchers as $check_voucher){
            //sap credentials
            $sap_server = $check_voucher->company->sapServers[0];
            $sap_user = SapUser::where('user_id', 175)->where('sap_server', $sap_server->sap_server)->first();
            $connection = [
                'ashost' => $sap_server->app_server,
                'sysnr' => $sap_server->system_number,
                'client' => $sap_server->client,
                'user' => $sap_user->sap_id,
                'passwd' => $sap_user->sap_password
            ];

            $bankCheck = $check_voucher->company->bankChecks->where('bank_id', '1')->first(); //revise bank id 1 = BPI

            $document_code = $check_voucher->document_code;
            $company_code = $check_voucher->company_code;
            $fiscal_year = Carbon::now()->format('Y');
            $house_bank = $bankCheck->house_bank;
            $account_id = $bankCheck->account_id;
            $check_number = strval($this->checkNumber($connection, $company_code, $bankCheck,$sap_server));

            //api cv posting
            $posted_check = APIController::executeSapFunction($connection, 'ZFI_CHECKINFO', [
                'DOC_NUM' => $document_code,
                'COMP_CODE' => $company_code,
                'FISCAL_YEAR' => $fiscal_year,
                'HOUSE_BANK' => $house_bank,
                'ACCT_ID' => $account_id,
                'CHECK_NUM' => strval($check_number),
            ], [
                'RESULT' => 'result',
                'RETURN' => 'return'
            ],$sap_server->sap_server);

            //result
            if ($posted_check['result'] == 'S'){
                CheckInfo::create([
                    'check_voucher_id' => $check_voucher->id,
                    'document_code' => $document_code,
                    'company_code' => $company_code,
                    'fiscal_year' => $fiscal_year,
                    'house_bank' => $house_bank,
                    'account_id' => $account_id,
                    'check_number' => strval($check_number),
                ]);
            }
            else{
                $errDescription = json_encode($posted_check['return']);
                CheckInfoError::create([
                    'check_voucher_id' => $check_voucher->id,
                    'description' => $errDescription,
                ]);
            }
        }
        echo 'Command successful! '.count($check_vouchers).' entries processed between '.$thisMonday.' and '.$thisSunday;
        return;
    }

    private function checkNumber($sap_connection, $company_code, $bankCheck,$sap_server){

        $check_info = APIController::readSapTableApi($sap_connection, [
            'table' => ['PCEC' => 'check_info'],
            'fields' => [
                'CHECF' => 'check_number_from',
                'CHECT' => 'check_number_to',
                'CHECL' => 'check_number_status',
            ],
            'options' => [
                ['TEXT' => "ZBUKR = '$company_code' AND "],
                ['TEXT' => "HBKID = '$bankCheck->house_bank' AND "],
                ['TEXT' => "HKTID = '$bankCheck->account_id' AND "],
                ['TEXT' => "STAPL = '$bankCheck->check_lot'"],
            ]
            ],$sap_server)->first();

        if ($check_info->check_number_status == ''){
            return $check_info->check_number_from;
        }
        else{
            if (is_numeric($check_info->check_number_status)){ // numeric
                return $check_info->check_number_status + 1;
            }
            else{
                return $check_number = APIController::executeSapFunction($sap_connection, 'ZFI_LASTCHECKINFO', [
                    'COMP_CODE' => $company_code,
                    'HOUSE_BANK' => $bankCheck->house_bank,
                    'ACCT_ID' => $bankCheck->account_id,
                    'CHECK_FR' => $check_info->check_number_from,
                    'CHECK_TO' => $check_info->check_number_to,
                ], ['LATEST_CHECK' => 'check_number'],$sap_server)->first() + 1;
            }

        }
    }


}
