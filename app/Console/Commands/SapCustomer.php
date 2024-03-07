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
        // $startdate = now()->subDays(2)->toDateString();
        // $enddate = now()->subDays(1)->toDateString();
        // $sap_server = SAPServer::get();
        // $caf_api = 'http://10.97.70.38/api/new-customers/';

        // foreach($sap_server as $server){
        //     $client = new Client();
        //     $request = $client->get($caf_api .$server['sap_server'].'/'.$startdate.'/'. $enddate);
        //     $customer_data = json_decode($request->getBody(), true);
            
        //     if($customer_data){
        //         foreach($customer_data as $customers){

        //                 $company_id;

        //                 foreach($customers['customer_company'] as $company){
        //                     $company_id =  Company::where('code',$company['company']['code'])->first();
        //                 }

        //                 $customer_code = CustomerCode::where('customer_code',$customers['customer_code'])
        //                                         ->where('server',$server['sap_server'])
        //                                         ->first();

        //                 $customer = Customer::where('customer_code',$customer_code['customer_code'])->first();

        //                 if(empty($customer)){
        //                     $customer = new Customer;
        //                 }

        //                 if(empty($customer_code)){
        //                     $customer_code = new CustomerCode;
        //                 }

        //                 //Insert customers to customers table
        //                     $customer->company_id = $company_id['id'];
        //                     $customer->customer_status = $customers['status'];
        //                     $customer->customer_code = $customers['customer_code'];
        //                     $customer->name = $customers['customer_name1'];
        //                     $customer->street = $customers['street'];
        //                     $customer->town_city = $customers['city'];
        //                     $customer->telephone_1 = $customers['tel_nos'];
        //                     $customer->telephone_2 = $customers['tel_nos2'];
        //                     $customer->fax_number = $customers['fax_nos'];
        //                     $customer->account_group = $customers['account_group']['code'];
        //                     $customer->save();

        //                 //Insert customer code in customer_codes table
        //                     $customer_code->customer_code = $customers['customer_code'];
        //                     $customer_code->server = $customers['sap_server'];
        //                     $customer_code->name = $customers['customer_name1'];
        //                     $customer_code->street = $customers['street'];
        //                     $customer_code->city = $customers['city'];
        //                     $customer_code->account_group = $customers['account_group']['code'];
        //                     $customer_code->save();
        //         }
        //     }
        // }

        // $date = '2024-03-06';
        // $sap_server = SAPServer::get();
        // $caf_api = 'http://10.97.70.38/api/customers/';
        // $server = 'LFUG';

        //     $client = new Client();
        //     $request = $client->get($caf_api .$server.'/'.$date);
        //     $response = json_decode($request->getBody(), true);
            
        //     if($response){
        //         foreach($response['generals'] as $generals){
        //             $customer_code;
        //             if($server == 'HANA'){
        //                 $customer_code = $generals['business_partner'];
        //             }
        //             else{
        //                 $customer_code = $generals['customer_code'];
        //             }

        //             $validate_customer_code = CustomerCode::where([['customer_code',$customer_code],['server',$server]])->first();

        //             if(empty($validate_customer_code)){
        //                 $validate_customer_code = new CustomerCode;
        //             }

        //             $validate_customer_code->customer_code = $customer_code;
        //             $validate_customer_code->server = $server;
        //             $validate_customer_code->name = $generals['name1'];
        //             $validate_customer_code->street = $generals['street'];
        //             $validate_customer_code->city = $generals['city'];
        //             $validate_customer_code->account_group = $generals['grouping'];

        //             //added fields
        //             $validate_customer_code->name2 = $generals['name2'];
        //             $validate_customer_code->name3 = $generals['name3'];
        //             $validate_customer_code->name4 = $generals['name4'];
        //             $validate_customer_code->street4 = $generals['street4'];
        //             $validate_customer_code->street5 = $generals['street5'];
        //             $validate_customer_code->postal_code = $generals['postal_code'];
        //             $validate_customer_code->region = $generals['region'];
        //             $validate_customer_code->country = $generals['country'];
        //             $validate_customer_code->county = $generals['county'];
        //             $validate_customer_code->township = $generals['township'];
        //             $validate_customer_code->save();


                    
        //         }

        //         foreach($response['companies'] as $companies){

        //             $customer_code;
        //             $company_code = $companies['company_code'];
        //             if($server == 'HANA'){
        //                 $customer_code = $companies['business_partner'];
        //             }
        //             else{
        //                 $customer_code = $companies['customer_code'];
        //             }

        //             $validate_customer_company = CustomerCompany::where([['customer_code',$customer_code],['company_code',$company_code]])->first();
        //             if(empty($validate_customer_company)){
        //                 $validate_customer_company = new CustomerCompany;
        //             }

        //             $validate_customer_company->customer_code = $customer_code;
        //             $validate_customer_company->company_code = $company_code;
        //             $validate_customer_company->payment_terms = $companies['payment_terms'];
        //             $validate_customer_company->save();
        //         }

        //         foreach($response['sales'] as $sales){

        //             $customer_code;
        //             $sales_organization = $sales['sales_organization'];
        //             $distribution_channel = $sales['distribution_channel'];
        //             $division = $sales['division'];

        //             if($server == 'HANA'){
        //                 $customer_code = $sales['business_partner'];
        //             }
        //             else{
        //                 $customer_code = $sales['customer_code'];
        //             }

        //             $validate_customer_sales_area = CustomerSaleArea::where([
        //                 ['customer_code',$customer_code],
        //                 ['sales_organization',$sales_organization],
        //                 ['distribution_channel',$distribution_channel],
        //                 ['division',$division]
        //                 ])->first();

        //             if(empty($validate_customer_sales_area)){
        //                 $validate_customer_sales_area = new CustomerSaleArea;
        //             }

        //             $validate_customer_sales_area->customer_code = $customer_code;
        //             $validate_customer_sales_area->sales_organization = $sales_organization;
        //             $validate_customer_sales_area->distribution_channel = $distribution_channel;
        //             $validate_customer_sales_area->division = $division;
        //             $validate_customer_sales_area->payment_terms = $sales['payment_terms'];
        //             $validate_customer_sales_area->order_block = $sales['order_block'];
        //             $validate_customer_sales_area->delivery_block = $sales['delivery_block'];
        //             $validate_customer_sales_area->billing_block = $sales['billing_block'];
        //             $validate_customer_sales_area->save();

        //         }

                

        //     }

        $date = '2024-03-06';
        $sap_server = SAPServer::get();
        $caf_api = 'http://10.97.70.38/api/customers/';
        $server = 'HANA';

            $client = new Client();
            $request = $client->get($caf_api .$server.'/'.$date);
            $response = json_decode($request->getBody(), true);
            
            if($response){
                foreach($response['generals'] as $generals){
                    $customer_code; $name; $street; $city; $account_group; 
                    $name2; $name3; $name4; $street4; $street5; $postal_code; $region;
                    $country; $county; $township;
                    if($server == 'HANA'){
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
                    }
                    else{
                        $customer_code = $generals['customer_code'];
                        $name = $generals['customer_name1'];
                        $street = $generals['street'];
                        $city = $generals['city'];
                        $account_group = '';
                        $name2 = $generals['customer_name2']; 
                        $name3 = $generals['customer_name3']; 
                        $name4 = $generals['customer_name4']; 
                        $street4 = $generals['street2']; 
                        $street5 = ''; 
                        $postal_code = $generals['postal_code']; 
                        $region = $generals['region']; 
                        $country = $generals['country']; 
                        $county = $generals['county']; 
                        $township = $generals['township']; 
                    }

                    $validate_customer_code = CustomerCode::where([['customer_code',$customer_code],['server',$server]])->first();

                    if(empty($validate_customer_code)){
                        $validate_customer_code = new CustomerCode;
                    }

                    $validate_customer_code->customer_code = $customer_code;
                    $validate_customer_code->server = $server;
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
                    $validate_customer_code->save();


                    
                }

                foreach($response['companies'] as $companies){
                    $customer_code;
                    $company_code;
                    $payment_terms;

                    if($server == 'HANA'){
                        $customer_code = $companies['business_partner'];
                        $company_code = $companies['company_code'];
                        $payment_terms = $companies['payment_terms'];
                    }
                    else{
                        $customer_code = $companies['customer_code'];
                        $company_code = $companies['company_code'];
                        $payment_terms = $companies['payment_terms'];
                    }

                    $validate_customer_company = CustomerCompany::where([['customer_code',$customer_code],['company_code',$company_code]])->first();
                    if(empty($validate_customer_company)){
                        $validate_customer_company = new CustomerCompany;
                    }

                    $validate_customer_company->customer_code = $customer_code;
                    $validate_customer_company->company_code = $company_code;
                    $validate_customer_company->payment_terms = $payment_terms;
                    $validate_customer_company->save();
                }

                foreach($response['sales'] as $sales){

        

                    $customer_code; $sales_organization; $distribution_channel;
                    $division; $payment_terms; $order_block; $delivery_block;
                    $billing_block;

                    if($server == 'HANA'){
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
                        $sales_organization = $sales['sales_organization'];
                        $distribution_channel = $sales['distribution_channel'];
                        $division = $sales['division'];
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
                    $validate_customer_sales_area->save();

                }

            }


    }
}
