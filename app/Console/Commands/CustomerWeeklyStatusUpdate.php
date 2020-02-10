<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Auth;
use DB;
use Carbon\Carbon;
use App\{
    User,
    Customer,
    CustomerActivity,
};

use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;


class CustomerWeeklyStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:weekly_status_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer status updated successfully!';

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
  
        $all_customers = Customer::select('id','customer_code','name')->where('company_id','4')->get();
        
        if($all_customers){
                foreach($all_customers as $customer){
                    $customer_sap = [];
                    //Get Customer Info
                    $get_sap_customer_lfug = $this->get_customer_lfug($customer);
                    if(!$get_sap_customer_lfug){
                        $get_sap_customer_pmfc = $this->get_customer_pfmc($customer);
                    }
            }
        }
    }

    public function save_sap_request($customer,$customer_sap){
        if($customer_sap)
        {
            $first_date = $customer_sap['created_date'];

            //Active
            if($first_date){
                $activities = [];
                $activities['customer_id'] = $customer['id'];        
                $activities['activity_description'] = 'Active';        
                $activities['activity_date'] = $first_date;

                $check_status_exist = CustomerActivity::
                                        where('customer_id',$customer['id'])
                                        ->where('activity_description','Active')
                                        ->where('activity_date',$first_date)
                                        ->first();

                if(!$check_status_exist){
                    //add
                    CustomerActivity::create($activities);

                    //Update Customer
                    $customer_data = [];
                    $customer_data['status'] = '1';
                    Customer::where('id', $customer['id'])->update($customer_data);
                    
                }
            }
            //Inactive Status
            $check_status_inactive = substr($customer_sap['name'], 0, 4);
            if($check_status_inactive == 'XXX_'){
                $activities = [];
                $activities['customer_id'] = $customer['id'];        
                $activities['activity_description'] = 'Inactive';        
                $activities['activity_date'] = date('Y-m-d');
                
                $check_status_exist = CustomerActivity::
                                        where('customer_id',$customer['id'])
                                        ->where('activity_description','Inactive')
                                        ->first();

                if(!$check_status_exist){
                    //add
                    CustomerActivity::create($activities);
                }else{
                    CustomerActivity::find($check_status_exist->id)->delete();
                    CustomerActivity::create($activities);
                }

                //Update Customer
                $customer_data = [];
                $customer_data['status'] = '2';
                Customer::where('id', $customer['id'])->update($customer_data);

            }else{
                $inactive_status = CustomerActivity::
                                        where('customer_id',$customer['id'])
                                        ->where('activity_description','Inactive')
                                        ->first();
                if($inactive_status){
                    CustomerActivity::find($inactive_status->id)->delete();
                }
            }

            //Closed Status
            $check_status_closed = $customer_sap['closed'];
        
            if($check_status_closed == 'X'){
                $activities = [];
                $activities['customer_id'] = $customer['id'];        
                $activities['activity_description'] = 'Closed';        
                $activities['activity_date'] = date('Y-m-d');

                $check_status_exist = CustomerActivity::
                                        where('customer_id',$customer['id'])
                                        ->where('activity_description','Closed')
                                        ->first();

                if(!$check_status_exist){
                    CustomerActivity::create($activities);
                }else{
                    CustomerActivity::find($check_status_exist->id)->delete();
                    CustomerActivity::create($activities);
                }

                //Update Customer
                $customer_data = [];
                $customer_data['status'] = '4';
                Customer::where('id', $customer['id'])->update($customer_data);

                
            }else{
                $close_status = CustomerActivity::
                                        where('customer_id',$customer['id'])
                                        ->where('activity_description','Closed')
                                        ->first();
                if($close_status){
                    CustomerActivity::find($close_status->id)->delete();
                }
            }
            return 'saved';
        }else{
            return 'failed';
        }
        
    }

    public function get_customer_lfug($customer){

        $customer_code = $customer['customer_code'];
        $client = new Client();
        $connection = Config::get('constants.sap_api.connection_lfug');

        try {

            $response = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
            ['query' => 
                ['connection' => $connection,
                    'table' => [
                        'table' => ['KNA1' => 'customers'],
                        'fields' => [
                            'KUNNR' => 'customer_code',
                            'NAME1' => 'name',
                            'CASSD' => 'closed',
                            'ERDAT' => 'created_date',
                        ],
                        'options' => [
                            ['TEXT' => "KUNNR = '$customer_code'"]
                        ],
                    ]
                ]
            ],
            ['timeout' => 60],
            ['delay' => 10000]
            );
        
            $customers_sap= json_decode($response->getBody(), true);

            $customer_data = [];
            if($customers_sap){
                $customer_data['customer_code'] =  $customers_sap[0]['customer_code'];
                $customer_data['name'] =  $customers_sap[0]['name'];
                $customer_data['closed'] =  $customers_sap[0]['closed'];
                $customer_data['created_date'] =  $customers_sap[0]['created_date'] ? date('Y-m-d',strtotime($customers_sap[0]['created_date'])) : "";
            }

            $save_sap_request = $this->save_sap_request($customer,$customer_data);

            if($save_sap_request == 'saved'){
                return $customer_data;
            }else{
                return [];
            }

        }catch (BadResponseException $ex) {
            return [];
        }
        catch (ConnectException $ex) {
            return [];
        }
        catch (RequestException $ex) {
            return [];
        }
    }

    public function get_customer_pfmc($customer){

        $customer_code = $customer['customer_code'];

        $client = new Client();
        $connection = Config::get('constants.sap_api.connection_pfmc');

        try {
            $response = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
            ['query' => 
                ['connection' => $connection,
                    'table' => [
                        'table' => ['KNA1' => 'customers'],
                        'fields' => [
                            'KUNNR' => 'customer_code',
                            'NAME1' => 'name',
                            'CASSD' => 'closed',
                            'ERDAT' => 'created_date',
                        ],
                        'options' => [
                            ['TEXT' => "KUNNR = '$customer_code'"]
                        ],
                    ]
                ]
            ],
            ['timeout' => 60],
            ['delay' => 10000]
            );
            
            $customers_sap= json_decode($response->getBody(), true);

            $customer_data = [];
            if($customers_sap){
                $customer_data['customer_code'] =  $customers_sap[0]['customer_code'];
                $customer_data['name'] =  $customers_sap[0]['name'];
                $customer_data['closed'] =  $customers_sap[0]['closed'];
                $customer_data['created_date'] =  $customers_sap[0]['created_date'] ? date('Y-m-d',strtotime($customers_sap[0]['created_date'])) : "";
            }

            $save_sap_request = $this->save_sap_request($customer,$customer_data);

            if($save_sap_request == 'saved'){
                return $customer_data;
            }else{
                return [];
            }

        }catch (BadResponseException $ex) {
            return [];
        }
        catch (ConnectException $ex) {
            return [];
        }
        catch (RequestException $ex) {
            return [];
        }
    }

}
