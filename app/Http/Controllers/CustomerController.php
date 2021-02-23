<?php

namespace App\Http\Controllers;
use App\Schedule;
use Carbon\Carbon;
use DB;
use Auth;
use App\Customer;
use App\CustomerOrder;
use App\User;
use App\Message;
use App\Attendance;
use Illuminate\Http\Request;
use Spatie\Geocoder\Facades\Geocoder;

use Illuminate\Validation\Rule;

use App\CustomerCode;

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

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('customer.index', compact('notification'));
    }

    /**
     * Get all customer
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        ini_set('memory_limit', '2048M'); // revise this

        return  Customer::orderBy('customers.id', 'desc')
            ->when(Auth::user()->level() < 8, function($q){
                $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            })
            ->leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->leftJoin('regions', 'regions.id', '=', 'provinces.region_id')
            ->leftJoin('customer_classifications', 'customer_classifications.id', '=', 'customers.classification')
            // ->where('verified_status',1)
            ->get([
                'customers.id',
                'customers.area',
                'customers.classification',
                'customers.customer_code',
                'customers.status',
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
                'customers.verified_status',
                'regions.code AS region_code',
                'regions.name AS region',
            ]);
    }

    public function indexDataFilter(Request $request){
        $verified_status = $request->verified_status;
        if($verified_status == "Verified"){
            $verified_status = [1];
        }else if($verified_status == "All"){
            $verified_status = [0,1];
        }else{
            $verified_status = [1];
        }
        return  Customer::orderBy('customers.id', 'desc')
            ->when(Auth::user()->level() < 8, function($q){
                $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
            })
            ->leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->leftJoin('customer_classifications', 'customer_classifications.id', '=', 'customers.classification')
            ->whereIn('verified_status', $verified_status)
            ->get([
                'customers.id',
                'customers.area',
                'customers.classification',
                'customers.customer_code',
                'customers.status',
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
                'customers.verified_status',
            ]);
    }

    public function changeVerifiedStatus(Request $request, Customer $customer){
       $customer->verified_status = $request->verified_status;
        if($customer->save()){
            return Customer::where('id',$customer->id)->first();
        }else{
            return $customer;
        }

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

        $company_id = Auth::user()->companies->pluck('id')[0];
        $request->validate([
            // 'customer_code' => 'required|unique:customers,customer_code',
            'customer_code' => [
                'required',Rule::unique('customers')->where(function($query) use($company_id) {
                  $query->where('company_id', '=', $company_id);
              })
            ],
            'name' => 'required',
            'classification' => 'required',
            'street' => 'required',
            'google_address' => 'required',
            'town_city' => 'required',
            'province' => 'required',
        ]);

        // $geocode = Geocoder::getCoordinatesForAddress($request->google_address);

        $customers = new Customer;

        $customers->company_id = Auth::user()->companies->pluck('id')[0];
        $customers->classification = $request->classification;
        $customers->status = $request->status;
        $customers->customer_code = $request->customer_code;
        $customers->name = $request->name;
        $customers->street = $request->street;
        $customers->town_city = $request->town_city;
        $customers->province_id = $request->province;
        $customers->google_address = $request->google_address;
        $customers->lat = $request->lat;
        $customers->lng = $request->lng;
        $customers->telephone_1 = $request->telephone_1;
        $customers->telephone_2 = $request->telephone_2;
        $customers->fax_number = $request->fax_number;
        $customers->remarks = $request->remarks;
        $customers->check_customer_code = $request->check_customer_code;
        $customers->distributor_name = $request->distributor_name;
        $customers->brand_used = $request->brand_used;
        $customers->monthly_volume = $request->monthly_volume;
        $customers->date_converted = $request->date_converted;

        // return $customers;

        if($customers->save()){
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
            // 'customer_code' => 'required|unique:customers,customer_code,'. $customer->id,
            'name' => 'required',
            'classification' => 'required',
            'street' => 'required',
            'town_city' => 'required',
            'province' => 'required',
            'google_address' => 'required',
        ]);

        // $customer->company_id = Auth::user()->companies->pluck('id')[0];
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
        $customer->distributor_name = $request->distributor_name;
        $customer->brand_used = $request->brand_used;
        $customer->monthly_volume = $request->monthly_volume;
        $customer->date_converted = $request->date_converted;

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
            // $customer = Customer::withTrashed()->whereNotIn('classification', [1,2,8])->orderBy('id','asc')->get();
            // $customer_code = $customer->last()->customer_code;

            // //generate until to become unique
            // generate:
            // if(Customer::withTrashed()->where('customer_code', $customer_code)->exists()){
            //     $customer_code = $customer_code + 1;
            //     goto generate;
            // }

            // $validate_customer_code = Customer::withTrashed()->where('check_customer_code','!=','1')->orWhereNull('check_customer_code')->orderBy('id', 'DESC')->first();

            // if($validate_customer_code){
            //     $selected_customer_code = $validate_customer_code['customer_code'];
            //     $customer_code = $selected_customer_code + 1;
            // }

            $customer_code = '9' . date('ymd') . $request->company_id;
            $customer_code = str_pad($customer_code, 11, "0");

            $get_count_customer_code = Customer::withTrashed()->where('check_customer_code','!=','1')->orWhereNull('check_customer_code')->whereDate('created_at',date('Y-m-d'))->orderBy('id', 'DESC')->count();

            if($get_count_customer_code > 0){
                $customer_code = $customer_code + $get_count_customer_code + 1;
            }

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

        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $companyId = Auth::user()->companies[0]->id;

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $customers = Customer::
                with([
                    'schedules' => function($query) use($startDate,$endDate) {
                        $query->where('date', '>=',  $startDate);
                        $query->whereDate('date', '<=',  $endDate);
                    },
                    'schedules.attendances',
                    'schedules.user',
                ])
                ->whereHas('schedules', function ($q) use($selected_user,$startDate,$endDate){
                    $q->whereIn('user_id', $selected_user);
                    $q->where('date', '>=',  $startDate);
                    $q->whereDate('date', '<=', $endDate);
                    $q->where('type','1');
                    $q->where('status','1');
                })->get();
        return $customers;
    }

    public function customerVisitedToday(){
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');

        $companyId = Auth::user()->companies[0]->id;
        $users = User::select('id')->where('company_id',$companyId)->get();
        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        $customers = Customer::
                with([
                    'schedules' => function($query) use($startDate,$endDate) {
                        $query->where('date', '>=',  $startDate);
                        $query->whereDate('date', '<=',  $endDate);
                    },
                    'schedules.attendances',
                    'schedules.user',
                ])
                ->whereHas('schedules', function ($q) use($selected_user,$startDate,$endDate){
                    $q->whereIn('user_id', $selected_user);
                    $q->where('date', '>=',  $startDate);
                    $q->whereDate('date', '<=', $endDate);
                    $q->where('type','1');
                    $q->where('status','1');
                })->get();

        return $customers;
    }

    public function getLastVisitedDate($attendance_id,$user_id){

        $attendance_data = [];
        $attendance = Attendance::where('id' , '=',$attendance_id)->first();

        if($attendance){
            $sign_in = $attendance['sign_in'];
            $sign_in_date = date('Y-m-d',strtotime($attendance['sign_in']));

            $sign_out = Attendance::where('user_id' , '=',$user_id)->whereDate('sign_in' , $sign_in_date)->where('id','<',$attendance_id)->first();

            if($sign_out){
                return $data = [
                    $sign_in,
                    $sign_out['sign_out']
                ];
            }else{
                return '';
            }
        }else{
            return '';
        }
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
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        return $customers = Customer::with(['schedules' => function ($query) use($params) {
                        $query->where('date', '>=', $params['startDate']);
                        $query->where('date', '<=', $params['endDate']);
                        $query->where('type', '1');
                        $query->where('status', '1');
                        $query->orderBy('date', 'DESC');
                        $query->with('attendances','user');
                    }
                    ])
                    ->with('last_visited')
                    ->where('company_id',$companyId)
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
                                },
                                'schedules.user'
                                ])
                                ->whereIn('id',$selected_user_ids)
                                ->get();

        return $users_schedules_data;

    }


    public function getCustomerCodes(){


        $client = new Client();

        $connection = [
            'ashost' => '172.17.2.36',
            'sysnr' => '00',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4'
        ];
        $date = date('Ymd');
        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' =>
                                ['connection' => $connection,
                                    'table' => [
                                        'table' => ['KNA1' => 'do_headers'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                        ],
                                        'options' => [
                                            ['TEXT' => "ERDAT = '$date'"]
                                        ],
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

        return $customers_data = json_decode($customers->getBody(), true);


        $customer_codes_local = Customer::select('customer_code')->get();
        $customer_codes_from_sap = CustomerOrder::select('customer_code')->groupBy('customer_code')
                                                ->where(function($query) use ($customer_codes_local){
                                                    foreach($customer_codes_local as $customer_code){
                                                            $search_customer_code = $customer_code['customer_code'];
                                                            $query = $query->orWhere('customer_code', 'not like', "%$search_customer_code%");
                                                        }
                                                        return $query;
                                                })
                                                ->get();

        return count($customer_codes_from_sap);
    }

    public function getCustomerCodesAll(){

        $company_id = Auth::user()->companies->pluck('id')[0];

        $customer_code_within = Customer::select('customer_code')->where('company_id',$company_id)->get();

        $customer_codes_not = [];
        if($customer_code_within){
            foreach($customer_code_within as $customer){
                $customer_code_selected = str_pad($customer['customer_code'], 10, '0', STR_PAD_LEFT);
                array_push($customer_codes_not,$customer_code_selected);
            }
        }

        return $customer_codes = CustomerCode::select('id','customer_code','name')
                                ->whereNotIn('customer_code',$customer_codes_not)
                                ->where('name','not like','%XXX%')
                                // ->where('name','not like','%XX:%')
                                // ->where('name','not like','%X:%')
                                ->get();
    }

}
