<?php

namespace App\Http\Controllers;

use Auth;
use App\Customer;
use App\Message;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display customer index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Customers']);

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('customer.index', compact('notification'));
    }

    /**
     * Get all customer
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  Customer::orderBy('id','desc')->get();
    }
    
    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

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
        ]);
        $customers = new Customer;

        $customers->classification = $request->classification;
        $customers->customer_code = $request->customer_code;
        $customers->name = $request->name;
        $customers->street = $request->street;
        $customers->town_city = $request->town_city;
        $customers->province_id = $request->province_id;
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
        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

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
        ]);

        $customer->classification = $request->classification;
        $customer->customer_code = $request->customer_code;
        $customer->name = $request->name;
        $customer->street = $request->street;
        $customer->town_city = $request->town_city;
        $customer->province_id = $request->province_id;
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
        $customer = Customer::where('classification', 3)->orderBy('id','asc')->get();   
        return ++$customer->last()->customer_code; 
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
