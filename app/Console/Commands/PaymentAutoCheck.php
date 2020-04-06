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
        $thisMonday = '2020-03-09';
        $thisSunday = '2020-03-15';
//        $thisMonday = date("Y-m-d", strtotime("last monday"));
//        $thisSunday = date("Y-m-d", strtotime("this sunday"));

        $check_vouchers = CheckVoucher::with('company.sapServers', 'company.bankChecks')
            ->whereBetween('created_at', [$thisMonday, $thisSunday])
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
            $check_number = $this->checkNumber($connection, $company_code, $bankCheck);

            //api cv posting
            $posted_check = APIController::executeSapFunction($connection, 'ZFI_CHECKINFO', [
                'DOC_NUM' => $document_code,
                'COMP_CODE' => $company_code,
                'FISCAL_YEAR' => $fiscal_year,
                'HOUSE_BANK' => $house_bank,
                'ACCT_ID' => $account_id,
                'CHECK_NUM' => $check_number,
            ], [
                'RESULT' => 'result',
                'RETURN' => 'return'
            ]);

            //result
            if ($posted_check['result'] == 'S'){
                CheckInfo::create([
                    'document_code' => $document_code,
                    'company_code' => $company_code,
                    'fiscal_year' => $fiscal_year,
                    'house_bank' => $house_bank,
                    'account_id' => $account_id,
                    'check_number' => $check_number,
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
        return;
    }

    private function checkNumber($sap_connection, $company_code, $bankCheck){
        $check_info = APIController::readSapTableApi($sap_connection, [
            'table' => ['PCEC' => 'check_info'],
            'fields' => [
                'CHECF' => 'check_number_from',
                'CHECL' => 'check_number_status',
            ],
            'options' => [
                ['TEXT' => "ZBUKR = '$company_code' AND "],
                ['TEXT' => "HBKID = '$bankCheck->house_bank' AND "],
                ['TEXT' => "HKTID = '$bankCheck->account_id' AND "],
                ['TEXT' => "STAPL = '$bankCheck->check_lot'"],
            ]
        ])->first();

        if ($check_info->check_number_status == ''){
            return $check_info->check_number_from;
        }
        else{
            return $check_info->check_number_status + 1;
        }
    }


}
