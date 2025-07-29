<?php

namespace App\Console\Commands;

use App\CheckVoucher;
use App\CheckVoucherError;
use App\Http\Controllers\APIController;
use App\PaymentHeader;
use App\SapUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PaymentAutoCv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:autocv';

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

        $payment_headers = PaymentHeader::with('company.sapServers', 'company.bankGls')
            ->whereBetween('created_at', [$thisMonday, $thisSunday . ' 23:59:59'])
            ->whereDoesntHave('checkVoucher') // payment header without CV
            ->get();

        foreach ($payment_headers as $payment_header){
            //sap credentials
            $sap_server = $payment_header->company->sapServers[0];
            $sap_user = SapUser::where('user_id', 175)->where('sap_server', $sap_server->sap_server)->first();
            $connection = [
                'ashost' => $sap_server->app_server,
                'sysnr' => $sap_server->system_number,
                'client' => $sap_server->client,
                'user' => $sap_user->sap_id,
                'passwd' => $sap_user->sap_password
            ];

            $company_code = $payment_header->company_code;
            $date_today = Carbon::now()->format('Ymd');
            $reference = $payment_header->reference_number;
            $header_text = $payment_header->header_text;
            $bank_account = $payment_header->company->bankGls->where('bank_id', '1')->first()->gl_account; //revise bank id 1 = BPI
            $amount = $payment_header->payable->amount * -1;
//            $business_area = $payment_header->payable->business_area;
            $business_area = ''; // todo:: should be table
            $vendor_code = $payment_header->vendor_code;
            $document_code = $payment_header->document_code;

            //api cv posting
            $posted_cv = APIController::executeSapFunction($connection, 'ZFI_SF_CV_POST', $params = [
                'COMP_CODE' => $company_code,
                'DOC_DATE' => $date_today,
                'POST_DATE' => $date_today,
                'REFERENCE' => $reference,
                'DOC_HDR_TXT' => $header_text,
                'BANK_ACCT' => strval($bank_account),
                'AMOUNT:double' => $amount,
                'BUS_AREA' => $business_area,
                'VEN_CODE' => $vendor_code,
                'DOC_NUMBER' => $document_code,
            ], [
                'E_DOC_NUM' => 'document_number',
                'RESULT' => 'result',
                'RETURN' => 'return'
            ],$sap_server->sap_server);

            //result
            if ($posted_cv['result'] == 'S'){
                $check_voucher = new CheckVoucher();
                $check_voucher->company_code = $company_code;
                $check_voucher->document_date = $date_today;
                $check_voucher->posting_date = $date_today;
                $check_voucher->reference_number = $reference;
                $check_voucher->header_text = $header_text;
                $check_voucher->bank_account = $bank_account;
                $check_voucher->amount = $amount;
                $check_voucher->business_area = $business_area;
                $check_voucher->vendor_code = $vendor_code;
                $check_voucher->document_code = $posted_cv['document_number'];
                $check_voucher->apv = $document_code;
                $check_voucher->save();
            }
            else{
                $errDescription = json_encode($posted_cv['return']);
                 CheckVoucherError::create([
                     'payment_header_id' => $payment_header->id,
                     'description' => $errDescription,
                 ]);
            }
        }
        echo 'Command successful! '.count($payment_headers).' entries processed between '.$thisMonday.' and '.$thisSunday;
        return;
    }
}
