<?php

namespace App\Http\Controllers;
use App\Schedule;
use Carbon\Carbon;
use DB;
use Auth;
use App\Customer;
use App\User;
use App\Message;
use App\CustomerActivity;
use Illuminate\Http\Request;
use Spatie\Geocoder\Facades\Geocoder;
use Illuminate\Support\Facades\Input;

use Config;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class CustomerController extends Controller
{
    /**
     * Display customer index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Customers']);

        $search  = Input::get('search');

        if($search){
            $customers = Customer::with('provinces','classifications')
                                ->when(!empty($search), function($q) use($search){
                                    $q->where('customer_code', 'like', '%' . $search . '%');
                                    $q->orWhere('name', 'like', '%' . $search . '%');
                                })
                                ->whereIn('company_id', Auth::user()->companies->pluck('id'))
                                ->paginate(50);
        }else{
            $customers = Customer::with('provinces','classifications')
                                ->whereIn('company_id', Auth::user()->companies->pluck('id'))
                                ->paginate(50);
        }

        return view('customer.index', compact('customers','search'));
        

        // $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        // $notification = 0;  
        // foreach($message as $notif){

        //     $ids = collect(json_decode($notif->seen, true))->pluck('id');
        //     if(!$ids->contains(Auth::user()->id)){
        //         $notification++;
        //     }
        // }

        
    }

    /**
     * Get all customer
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  Customer::orderBy('customers.id', 'desc')
            ->when(Auth::user()->level() < 8, function($q){
                $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            })
            ->leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->leftJoin('customer_classifications', 'customer_classifications.id', '=', 'customers.classification')
            ->get([
                'customers.id',
                'customers.area',
                'customers.classification',
                'customers.customer_code',
                'customers.name',
                'customers.deleted_at',
                'customers.street',
                'customers.town_city',
                'customers.province_id',
                'customers.google_address',
                'customers.telephone_1',
                'customers.telephone_2',
                'customers.fax_number',
                'customers.remarks',
                'customers.created_at',
                'customers.updated_at',
                'provinces.name AS province',
                'customer_classifications.description AS customer_classification',
            ]);
    }
    
    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('customer.create', compact('notification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        if($request->status == 3){
            $request->validate([
                'name' => 'required',
                'classification' => 'required',
                'street' => 'required',
                'google_address' => 'required',
                'town_city' => 'required',
                'province' => 'required',
                'telephone_1' => 'required',
            ]);
        }else{
            $request->validate([
                'customer_code' => 'required|unique:customers,customer_code',
                'name' => 'required',
                'classification' => 'required',
                'street' => 'required',
                'google_address' => 'required',
                'town_city' => 'required',
                'province' => 'required',
                'telephone_1' => 'required',
            ]);
        }
      
        
        $customers = new Customer;

        $customers->company_id = Auth::user()->companies->pluck('id')[0];
        $customers->classification = $request->classification;
        $customers->status = $request->status;
        $customers->customer_code = $request->customer_code;
        $customers->name = $request->name;
        $customers->street = $request->street;
        $customers->town_city = $request->town_city;
        $customers->province_id = $request->province;
        if($request->google_address){
            $customers->google_address = $request->google_address;
            $customers->lat = $request->lat;
            $customers->lng = $request->lng;
        }
       
        $customers->telephone_1 = $request->telephone_1;
        $customers->telephone_2 = $request->telephone_2;
        $customers->fax_number = $request->fax_number;
        $customers->remarks = $request->remarks;

        if($request->meet_up_location_address){
            $customers->meet_up_location_address = $request->meet_up_location_address;
            $customers->meet_up_lat = $request->meet_up_lat;
            $customers->meet_up_lng = $request->meet_up_lng;
        }

        $customers->customer_dealer_id = $request->customerDealer ? $request->customerDealer['id'] : ""; 

        if($customers->save()){    
             //generate prospect id 
            $prospect_id = '';
            if($request->status == 3){
                $classification_customer_id = str_pad($customers->classification, 1, '0', STR_PAD_RIGHT);
                $prospect_id = 'P'  . $classification_customer_id . str_pad($customers->id, 9, '0', STR_PAD_LEFT);
                Customer::where('id', $customers->id)->update([
                    'customer_code' => $prospect_id,
                    'prospect_id' => $prospect_id
                ]);

                $customer_activity = [];
                $customer_activity['customer_id'] = $customers->id;
                $customer_activity['activity_description'] = 'Prospect';
                $customer_activity['activity_date'] = Carbon::now();
                CustomerActivity::create($customer_activity);
            }

          
            return ['redirect' => route('customers_list')];
        }   
    }

    /**
     * Show the edit form
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('customer.edit', compact('id', 'notification'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Customer::findOrFail($id);
    }

     /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_code' => 'required|unique:customers,customer_code,'. $customer->id,
            'name' => 'required',
            'classification' => 'required',
            'street' => 'required',
            'town_city' => 'required',
            'province' => 'required',
            'google_address' => 'required',
            'telephone_1' => 'required',
        ]);

        $customer->company_id = Auth::user()->companies->pluck('id')[0];
        $customer->classification = $request->classification;
        $customer->status = $request->status;
        $customer->customer_code = $request->customer_code;
        $customer->name = $request->name;
        $customer->street = $request->street;
        $customer->town_city = $request->town_city;
        $customer->province_id = $request->province;

        $customer->google_address = $request->google_address;
        $customer->lat = $request->lat;
        $customer->lng = $request->lng;

        $customer->telephone_1 = $request->telephone_1;
        $customer->telephone_2 = $request->telephone_2;
        $customer->fax_number = $request->fax_number;
        $customer->remarks = $request->remarks;
        
        if($request->meet_up_location_address){
            $customer->meet_up_location_address = $request->meet_up_location_address;
            $customer->meet_up_lat = $request->meet_up_lat;
            $customer->meet_up_lng = $request->meet_up_lng;
        }else{
            $customer->meet_up_location_address = "";
            $customer->meet_up_lat = "";
            $customer->meet_up_lng = "";     
        }
        
        
        $customer->customer_dealer_id = $request->customerDealer ? $request->customerDealer['id'] : ""; 
        
        // apply changes in schedule
        $date_now = Carbon::now();
        $date_now = date("Y-m-d", strtotime($date_now));

        Schedule::where('code', $request->customer_code)
            ->where('date', '>=', $date_now)
            ->update([
                'lat' =>  $request->lat,
                'lng' => $request->lng
            ]);

        if($customer->save()){
            return ['redirect' => route('customers_list')];
        }
    }

    /**
     * Return Auto generated customer code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkCustomerCode(Request $request){
        $request->validate([
            'classification' => 'required',
            'company_id' => 'required'
        ]);

        if($request->classification == 8 && $request->company_id == 5){
            $customer = Customer::withTrashed()->where('classification',8)->where('company_id', 5)->orderBy('id','asc')->get();
            $customer_code = $customer->last()->customer_code;
            return $customer_code;

        }else{
            $customer = Customer::withTrashed()->whereNotIn('classification', [1,2,8])->orderBy('id','asc')->get();
            $customer_code = $customer->last()->customer_code;

            //generate until to become unique
            generate:
            if(Customer::withTrashed()->where('customer_code', $customer_code)->exists()){
                $customer_code = $customer_code + 1;
                goto generate;
            }

            //pad zeros at left
            $customer_code = str_pad($customer_code, 10, "0");
            return $customer_code;
        }
    }

    public function getGeocodeCustomer($address){
        $geocode = Geocoder::getCoordinatesForAddress($address);
        return $geocode;
    }

    /**
     * Return geocode base on customer's address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getGeocode($address){
        $geocode = Geocoder::getCoordinatesForAddress($address);
        return $geocode['lat'].','.$geocode['lng'];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Customer $customer)
    {
        if($customer->delete()){
            return $customer;
        }else{
            return 'Unable to delete Customer.';
        }
    }

    /**
     * Display customer visited page
     *
     * @return \Illuminate\Http\Response
     */

    public function customerVisitedIndex(){
        session(['header_text' => 'Visited Customer']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('customer.visited', compact('notification'));
    }

    /**
     * Fetch all customer visited
     * 
     * @return \Illuminate\Http\Response
     */
    public function customerVisitedIndexData(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);
        
        $companyId = Auth::user()->companies[0]->id;

        return DB::select("call p_customer_visited('$request->startDate','$request->endDate' , '$companyId')");
    }

    public function getCustomerDetails($customer){
       return Customer::findOrFail($customer);
    }

    public function customerSalesReport(){
        session(['header_text' => 'Sales Report']);
        return view('sales-report.index');
    }


    public function customersSalesReportData(Request $request){

        $companyId = Auth::user()->companies[0]->id;
        $params = $request->all(); 

        $request->validate([
            'selectedCompanies' => 'required',
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $selected_company_ids = [];
        if($request->selectedCompanies){
            foreach ($request->selectedCompanies as $id){
                array_push($selected_company_ids,$id['id']);
             }
        }
       
        return $customers = Customer::with(['schedules' => function ($query) use($params) {
                        $query->where('date', '>=', $params['startDate']);
                        $query->where('date', '<=', $params['endDate']);
                        $query->where('type', '1');
                        $query->where('status', '1');
                        $query->orderBy('date', 'DESC');
                        $query->with('attendances','user');
                    }])
                    ->with('last_visited','company')
                    ->whereIn('company_id',$selected_company_ids)
                    ->orderBy('id', 'DESC')
                    ->get();

    }

    public function customerAppointmentDurationReport(){
        session(['header_text' => 'Appointment Duration Report']);
        return view('appointment-duration-report.index');
    }

    public function customerAppointmentDurationReportData(Request $request){

        $params = $request->all(); 

        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required',
            'selectedUsers' => 'required',
        ]);

        $selected_user_ids = [];
        if($request->selectedUsers){
            foreach ($request->selectedUsers as $id){
                array_push($selected_user_ids,$id['id']);
             }
        }

        $users_schedules_data = User::with(['schedules' => function ($query) use($params) {
                                    $query->with('attendances');
                                    $query->leftjoin('attendances',function($query){
                                        $query->on('attendances.schedule_id','=','schedules.id');
                                    });
                                    $query->where('date', '>=', $params['startDate']);
                                    $query->where('date', '<=', $params['endDate']);
                                    $query->select('attendances.sign_in','schedules.*');
                                    $query->orderBy('sign_in','ASC');
                                }])
                                ->whereIn('id',$selected_user_ids)
                                ->get();

        return $users_schedules_data;
                                  
    }

    public function salesActivityCustomerReport(){
        session(['header_text' => 'Sales Activity Customer Report']);
        return view('sales-activity-customer-report.index');
    }

    public function salesActivityCustomerReportAll(Request $request){

        $request->validate([
            'selectedCompany' => 'required',
        ]);

        $company = $request->selectedCompany;

        $selected_status_ids = [];
        if($request->selectedStatuses){
            foreach ($request->selectedStatuses as $id){
                array_push($selected_status_ids,$id['id']);
             }
        }

        return $customers = Customer::with('statuses','customer_activity')->select('id','prospect_id','customer_code','name','google_address','status')
                    ->where('company_id',$company['id'])
                    ->when(!empty($request->selectedStatuses), function($q) use($selected_status_ids) {
                        $q->whereIn('status',  $selected_status_ids);
                    })
                    ->orderBy('id', 'DESC')
                    ->get();
    }
    
    public function salesCustomerActivities($customer_code){

        $customer = Customer::where('customer_code' , '=',$customer_code )->first();

      

        $activities_data = [];

        $customer_activity = CustomerActivity::
                            where('customer_id', '=',$customer->id)
                            ->orderBy('id','ASC')
                            ->get();
                            
        return $customer_activity;
        // if(!empty($customer_activity)){
        //     //Prospect
        //     $date = $customer_activity['activity_date'] ? Carbon::parse($customer_activity['activity_date'])->format('Y-m-d') : '';

        //     $activities_data = [
        //         [
        //             'activity'=> 'Prospect',
        //             'activity_date'=> $date,
        //         ]
        //     ];
        // }
                            
        // $get_sap_customer_lfug = $this->get_customer_lfug($customer_code);
        // $get_sap_purchase_orders_lfug = $this->get_customer_purchase_order_lfug($customer_code);
        
        // //LFUG Server
        // if($get_sap_customer_lfug){
        //     //Active Status
        //     $first_date = '';
        //     $last_date = '';
        //     if($get_sap_purchase_orders_lfug){
        //         $activities = [];
        //         $key = count($get_sap_purchase_orders_lfug) - 1;
        //         $first_date = $get_sap_purchase_orders_lfug[0]['purchase_date'] ? Carbon::parse($get_sap_purchase_orders_lfug[0]['purchase_date'])->format('Y-m-d') : '';
        //         $last_date = $get_sap_purchase_orders_lfug[$key]['purchase_date'] ? Carbon::parse($get_sap_purchase_orders_lfug[$key]['purchase_date'])->format('Y-m-d') : '';
        //         $activities['activity'] = 'Active';        
        //         $activities['activity_date'] = $first_date;
        //         array_push($activities_data, $activities);
        //     }
        //     //Inactive Status
        //     $check_status_inactive = substr($get_sap_customer_lfug['name'], 0, 4);
        //     if($check_status_inactive == 'XXX_'){
        //         $activities = [];
        //         $activities['activity'] = 'Inactive';        
        //         $activities['activity_date'] = $last_date;
        //         array_push($activities_data, $activities);
        //     }
        //     //Closed Status
        //     $check_status_closed = $get_sap_customer_lfug['closed'];
           
        //     if($check_status_closed == 'X'){
        //         $activities = [];
        //         $activities['activity'] = 'Closed';        
        //         $activities['activity_date'] = $last_date;
        //         array_push($activities_data, $activities);
        //     }

        //     return $activities_data;
        // }else{
        //     //PFMC Server
        //     $get_sap_customer_pfmc = $this->get_customer_pfmc($customer_code);
        //     $get_sap_purchase_orders_pfmc = $this->get_customer_purchase_order_pfmc($customer_code);

        //     if($get_sap_customer_pfmc){
        //         //Active Status
        //         $first_date = '';
        //         $last_date = '';
        //         if($get_sap_purchase_orders_pfmc){
        //             $activities = [];
        //             $key = count($get_sap_purchase_orders_pfmc) - 1;
        //             $first_date = $get_sap_purchase_orders_pfmc[0]['purchase_date'] ? Carbon::parse($get_sap_purchase_orders_pfmc[0]['purchase_date'])->format('Y-m-d') : '';
        //             $last_date = $get_sap_purchase_orders_pfmc[$key]['purchase_date'] ? Carbon::parse($get_sap_purchase_orders_pfmc[$key]['purchase_date'])->format('Y-m-d') : '';
        //             $activities['activity'] = 'Active';        
        //             $activities['activity_date'] = $first_date;
        //             array_push($activities_data, $activities);
        //         }
        //         //Inactive Status
        //         $check_status_inactive = substr($get_sap_customer_pfmc['name'], 0, 4);
        //         if($check_status_inactive == 'XXX_'){
        //             $activities = [];
        //             $activities['activity'] = 'Inactive';        
        //             $activities['activity_date'] = $last_date;
        //             array_push($activities_data, $activities);
        //         }
        //         //Closed Status
        //         $check_status_closed = $get_sap_customer_pfmc['closed'];
               
        //         if($check_status_closed == 'X'){
        //             $activities = [];
        //             $activities['activity'] = 'Closed';        
        //             $activities['activity_date'] = $last_date;
        //             array_push($activities_data, $activities);
        //         }
    
        //         return $activities_data;
        //     }

        // }
    
        return $customer_activity;
    }

    private function get_customer_lfug($customer_code){

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
                        ]
                    ]
                ]
            ]);
            
            $customer = json_decode($response->getBody(), true);

            $key = array_search($customer_code,array_column($customer, 'customer_code'));
            $customer_data = [];
            if($key){
                if($customer_code == $customer[$key]['customer_code']){
                    $customer_data['customer_code'] =  $customer[$key]['customer_code'];
                    $customer_data['name'] =  $customer[$key]['name'];
                    $customer_data['closed'] =  $customer[$key]['closed'];
                }
            }
            return $customer_data;

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse()->getBody();
            return json_decode($response, true);
        }
    }

    private function get_customer_purchase_order_lfug($customer_code){

        $client = new Client();
        $connection = Config::get('constants.sap_api.connection_lfug');
        $table_customer_first_date = Config::get('constants.sap_api.table_customer_first_date');
        try {
            $response_do_number = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                        ['query' => 
                            ['connection' => $connection,
                                'table' =>  [
                                    'table' => ['VBAK' => 'customer_first_date'],
                                    'fields' => [
                                        'KUNNR' => 'customer_code',
                                        'VBELN' => 'do_number',
                                        'ERDAT' => 'purchase_date',
                                    ],
                                    'options' => [
                                        ['TEXT' => "KUNNR = '$customer_code' AND VBTYP = 'C'"]
                                    ]
                                ]
                            ]
                        ]);

            $customer_purchase_orders = json_decode($response_do_number->getBody(), true);

            return $customer_purchase_orders;

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse()->getBody();
            return json_decode($response, true);
        }
    }
    
    private function get_customer_pfmc($customer_code){

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
                        ]
                    ]
                ]
            ]);
            
            $customer = json_decode($response->getBody(), true);

            $key = array_search($customer_code,array_column($customer, 'customer_code'));
            $customer_data = [];
            if($key){
                if($customer_code == $customer[$key]['customer_code']){
                    $customer_data['customer_code'] =  $customer[$key]['customer_code'];
                    $customer_data['name'] =  $customer[$key]['name'];
                    $customer_data['closed'] =  $customer[$key]['closed'];
                }
            }
            return $customer_data;

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse()->getBody();
            return json_decode($response, true);
        }
    }

    private function get_customer_purchase_order_pfmc($customer_code){

        $client = new Client();
        $connection = Config::get('constants.sap_api.connection_pfmc');
        try {
            $response_do_number = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                        ['query' => 
                            ['connection' => $connection,
                                'table' =>  [
                                    'table' => ['VBAK' => 'customer_first_date'],
                                    'fields' => [
                                        'KUNNR' => 'customer_code',
                                        'VBELN' => 'do_number',
                                        'ERDAT' => 'purchase_date',
                                    ],
                                    'options' => [
                                        ['TEXT' => "KUNNR = '$customer_code' AND VBTYP = 'C'"]
                                    ]
                                ]
                            ]
                        ]);

            $customer_purchase_orders = json_decode($response_do_number->getBody(), true);

            return $customer_purchase_orders;

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse()->getBody();
            return json_decode($response, true);
        }
    }

    public function get_customer_pfmc_all($customer_code){

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
            ['timeout'=>30]
            );
            
            $customers_sap= json_decode($response->getBody(), true);

            $customer_data = [];
            if($customers_sap){
                $customer_data['customer_code'] =  $customers_sap[0]['customer_code'];
                $customer_data['name'] =  $customers_sap[0]['name'];
                $customer_data['closed'] =  $customers_sap[0]['closed'];
                $customer_data['created_date'] =  $customers_sap[0]['created_date'];

                return $customer_data;
            }else{
                return 'Customer Code: ' . $customer_code . ' not found!';
            }
            

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse()->getBody();
            return 'Customer Code: ' . $customer_code . ' not found! Error Exception';
        }
    }

    public function customerDealers(){
        return Customer::select(
                'id',
                'name',
                'company_id'
            )
            ->with('company')
            ->where('classification','=','16')
            ->whereIn('company_id', Auth::user()->companies->pluck('id'))
            ->orderBy('id', 'desc')
            ->get();
    }

}
