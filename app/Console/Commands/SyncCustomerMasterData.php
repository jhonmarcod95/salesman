<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CustomerClassification;
use App\Customer;
use App\CustomerCode;
use App\Company;
use App\CustomerCompany;

class SyncCustomerMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SyncCustomerMasterData';

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
        $customer_list = Customer::whereIn('customer_code',CustomerCode::pluck('customer_code'))->where('status','1')->get();
        $classification_id = CustomerClassification::where('description','Invalid')->first();
        
        $update_inactive = Customer::whereNotIn('customer_code',$customer_list->pluck('customer_code'))->where('status','1')->update(['status' => $classification_id['id']]);
        
        foreach($customer_list as $with_customer_code){
            $customer_id = $with_customer_code['id'];
            $customer_code = $with_customer_code['customer_code'];
            $customer_company = Company::where('id',$with_customer_code['company_id'])->pluck('code');
            $validate_customer_companies = CustomerCompany::where([['customer_code',$customer_code],['company_code',$customer_company]])->first();

            if(empty($validate_customer_companies)){
                $update_active_customer_status = Customer::where('id',$customer_id)->update(['status'=>$classification_id['id']]);
            }
        }
    }
}
