<?php

namespace App\Http\Controllers;
use App\Schedule;
use Carbon\Carbon;
use DB;
use Auth;
use App\Customer;
use App\Message;
use Illuminate\Http\Request;
use Spatie\Geocoder\Facades\Geocoder;

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
        $request->validate([
            'customer_code' => 'required|unique:customers,customer_code',
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
            'customer_code' => 'required|unique:customers,customer_code,'. $customer->id,
            'name' => 'required',
            'classification' => 'required',
            'street' => 'required',
            'town_city' => 'required',
            'province' => 'required',
            'google_address' => 'required',
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
                    }])
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
            'selectedDate' => 'required',
            'selectedUser' => 'required',
        ]);

        return $schedules = Schedule::with('attendances')
                                        ->leftJoin('attendances','schedules.id','=','attendances.schedule_id')
                                        ->where('schedules.date' , $params['selectedDate'])
                                        ->where('schedules.user_id' , $params['selectedUser'])
                                        ->select('attendances.*','schedules.*')
                                        ->get();
                                        
    }

}
