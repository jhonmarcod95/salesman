<?php

namespace App\Http\Controllers;

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

        $geocode = Geocoder::getCoordinatesForAddress($request->google_address);
        
        $customers = new Customer;

        $customers->company_id = Auth::user()->companies->pluck('id')[0];
        $customers->classification = $request->classification;
        $customers->customer_code = $request->customer_code;
        $customers->name = $request->name;
        $customers->street = $request->street;
        $customers->town_city = $request->town_city;
        $customers->province_id = $request->province;
        $customers->google_address = $request->google_address;
        $customers->lat = $geocode['lat'];
        $customers->lng = $geocode['lng'];
        $customers->telephone_1 = $request->telephone_1;
        $customers->telephone_2 = $request->telephone_2;
        $customers->fax_number = $request->fax_number;
        $customers->remarks = $request->remarks;

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
        
        $geocode = Geocoder::getCoordinatesForAddress($request->google_address);

        $customer->company_id = Auth::user()->companies->pluck('id')[0];
        $customer->classification = $request->classification;
        $customer->customer_code = $request->customer_code;
        $customer->name = $request->name;
        $customer->street = $request->street;
        $customer->town_city = $request->town_city;
        $customer->province_id = $request->province;
        $customer->google_address = $request->google_address;
        $customer->lat = $geocode['lat'];
        $customer->lng = $geocode['lng'];
        $customer->telephone_1 = $request->telephone_1;
        $customer->telephone_2 = $request->telephone_2;
        $customer->fax_number = $request->fax_number;
        $customer->remarks = $request->remarks;

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
    public function checkCustomerCode(){
        $customer = Customer::withTrashed()->whereNotIn('classification', [1,2])->orderBy('id','asc')->get();
        $customer_code = $customer->last()->customer_code;

        return ++$customer_code;
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
}
