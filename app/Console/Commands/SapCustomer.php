<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\Company;
use App\CustomerCode;
use App\CustomerCompany;
use App\CustomerSaleArea;
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
        set_time_limit(0);
        $date = '0000-00-00';
        // $date = now()->subDays(1)->toDateString();
        $caf_api = 'http://10.97.70.38/api/customers/';
        $companies_arr = [];
        foreach(SAPServer::get() as $server){
            $sap_server = $server['sap_server'];
            $client = new Client();
            $request = $client->get($caf_api .$sap_server.'/'.$date);
            $response = json_decode($request->getBody(), true);
            
            if($response){
                
                foreach($response['companies'] as $companies){
                    $customer_code;
                    $company_code;
                    $payment_terms;

                    if($sap_server == 'HANA'){
                        $customer_code = $companies['business_partner'];
                        $company_code = $companies['company_code'];
                        $payment_terms = $companies['payment_terms'];
                    }
                    else{
                        $customer_code = $companies['customer_code'];
                        $company_code = $companies['company']['code'];
                        $payment_terms = $companies['payment_terms'];
                    }

                    $validate_customer_company = CustomerCompany::where([['customer_code',$customer_code],['company_code',$company_code]])->first();
                    if(empty($validate_customer_company)){
                        $validate_customer_company = new CustomerCompany;
                    }

                    $validate_customer_company->customer_code = $customer_code;
                    $validate_customer_company->company_code = $company_code;
                    $validate_customer_company->payment_terms = $payment_terms;
                    $validate_customer_company->sap_server = $sap_server;
                    $validate_customer_company->save();
                }

                foreach($response['sales'] as $sales){
                    $customer_code; $sales_organization; $distribution_channel;
                    $division; $payment_terms; $order_block; $delivery_block;
                    $billing_block;

                    if($sap_server == 'HANA'){
                        $customer_code = $sales['business_partner'];
                        $sales_organization = $sales['sales_organization'];
                        $distribution_channel = $sales['distribution_channel'];
                        $division = $sales['division'];
                        $payment_terms = $sales['payment_terms'];
                        $order_block = $sales['order_block'];
                        $delivery_block = $sales['delivery_block'];
                        $billing_block = $sales['billing_block'];
                    }
                    else{
                        $customer_code = $sales['customer_code'];
                        $sales_organization = $sales['sales_organization']['code'];
                        $distribution_channel = $sales['distribution_channel']['code'];
                        $division = $sales['division']['code'];
                        $payment_terms = $sales['payment_terms'];
                        $order_block = $sales['order_block'];
                        $delivery_block = $sales['delivery_block'];
                        $billing_block = $sales['billing_block'];
                    }

                    $validate_customer_sales_area = CustomerSaleArea::where([
                        ['customer_code',$customer_code],
                        ['sales_organization',$sales_organization],
                        ['distribution_channel',$distribution_channel],
                        ['division',$division]
                        ])->first();
                    if(empty($validate_customer_sales_area)){
                        $validate_customer_sales_area = new CustomerSaleArea;
                    }

                    $validate_customer_sales_area->customer_code = $customer_code;
                    $validate_customer_sales_area->sales_organization = $sales_organization;
                    $validate_customer_sales_area->distribution_channel = $distribution_channel;
                    $validate_customer_sales_area->division = $division;
                    $validate_customer_sales_area->payment_terms = $payment_terms;
                    $validate_customer_sales_area->order_block = $order_block;
                    $validate_customer_sales_area->delivery_block = $delivery_block;
                    $validate_customer_sales_area->billing_block = $billing_block;
                    $validate_customer_sales_area->sap_server = $sap_server;
                    $validate_customer_sales_area->save();

                }

                $user_divisions =[];
                foreach($response['generals'] as $generals){
                    $customer_code; $name; $street; $city; $account_group; 
                    $name2; $name3; $name4; $street4; $street5; $postal_code; $region;
                    $country; $county; $township; $customer_company_id;$telephone_1;$telephone_2;
                    $fax_number;$customer_sales_area;

                    if($sap_server == 'HANA'){
                        $customer_code = $generals['business_partner'];
                        $name = $generals['name1'];
                        $street = $generals['street'];
                        $city = $generals['city'];
                        $account_group = $generals['grouping'];
                        $name2 = $generals['name2']; 
                        $name3 = $generals['name3']; 
                        $name4 = $generals['name4']; 
                        $street4 = $generals['street4']; 
                        $street5 = $generals['street5']; 
                        $postal_code = $generals['postal_code']; 
                        $region = $generals['region']; 
                        $country = $generals['country']; 
                        $county = $generals['county']; 
                        $township = $generals['township']; 
                        $telephone_1 = $generals['telephone_number1']; 
                        $telephone_2 = $generals['telephone_number2']; 
                        $fax_number = null;
                    }
                    else{
                        $customer_code = $generals['customer_code'];
                        $name = $generals['customer_name1'];
                        $street = $generals['street'];
                        $city = $generals['city'];
                        $account_group = $generals['account_group']['code'];
                        $name2 = $generals['customer_name2']; 
                        $name3 = $generals['customer_name3']; 
                        $name4 = $generals['customer_name4']; 
                        $street4 = $generals['street2']; 
                        $street5 = null; 
                        $postal_code = $generals['postal_code']; 
                        $region = $generals['region']['code']; 
                        $country = $generals['country']['code']; 
                        $county = null; 
                        $township = null; 
                        $telephone_1 = $generals['tel_nos']; 
                        $telephone_2 = $generals['tel_nos2']; 
                        $fax_number = $generals['fax_nos'];
                    }

                    $validate_customer_code = CustomerCode::where([['customer_code',$customer_code],['server',$sap_server]])->first();

                    if(empty($validate_customer_code)){
                        $validate_customer_code = new CustomerCode;
                    }
                    
                    $validate_customer_code->customer_code = $customer_code;
                    $validate_customer_code->server = $sap_server;
                    $validate_customer_code->name = $name;
                    $validate_customer_code->street = $street;
                    $validate_customer_code->city = $city;
                    $validate_customer_code->account_group = $account_group;

                    //added fields
                    $validate_customer_code->name2 = $name2;
                    $validate_customer_code->name3 = $name3;
                    $validate_customer_code->name4 = $name4;
                    $validate_customer_code->street4 = $street4;
                    $validate_customer_code->street5 = $street5;
                    $validate_customer_code->postal_code = $postal_code;
                    $validate_customer_code->region = $region;
                    $validate_customer_code->country = $country;
                    $validate_customer_code->county = $county;
                    $validate_customer_code->township = $township;
                    $validate_customer_code->telephone_1 = $telephone_1;
                    $validate_customer_code->telephone_2 = $telephone_2;
                    $validate_customer_code->save();
                }
            }
        }
    }
}
