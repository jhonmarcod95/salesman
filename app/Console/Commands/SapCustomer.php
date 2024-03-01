<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\Company;
use App\SAPServer;
use GuzzleHttp\Client;
class SapCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sap_customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get SAP customer';

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
        $startdate = now()->subDays(2)->toDateString();
        $enddate = now()->subDays(1)->toDateString();
        $sap_server = SAPServer::get();
        foreach($sap_server as $server){
            $client = new Client();
            $request = $client->get('http://10.97.70.38/api/new-customers/'.$server['sap_server'].'/'.$startdate.'/'. $enddate);
            $customer_data = json_decode($request->getBody(), true);
            
            if($customer_data){
                foreach($customer_data as $customers){

                        $company_id = '';
                        foreach($customers['customer_company'] as $company){
                            $company_id =  Company::where('code',$company['company']['code'])->first(['id'])->id;
                        }

                        $customer_validation = Customer::where('customer_code',$customers['customer_code'])->first();
                        $customer;

                        if(empty($customer_validation)){
                            $customer = new Customer;
                        }
                        else{
                            $customer = $customer_validation;
                        }
                            $customer->company_id = $company_id;
                            $customer->customer_status = $customers['status'];
                            $customer->customer_code = $customers['customer_code'];
                            $customer->name = $customers['customer_name1'];
                            $customer->street = $customers['street'];
                            $customer->town_city = $customers['city'];
                            $customer->telephone_1 = $customers['tel_nos'];
                            $customer->telephone_2 = $customers['tel_nos2'];
                            $customer->fax_number = $customers['fax_nos'];
                            $customer->account_group = $customers['account_group']['name'];
                            $customer->save();
                    
                }
            }
        }


    }
}
