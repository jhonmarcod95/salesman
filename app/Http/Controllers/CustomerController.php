<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        session(['header_text' => 'Customers']);

        $customers = Customer::orderBy('id','desc')->paginate(10);


        return view('customer.index', compact(
            'customers'
        ));
    }

    public function create(){
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $customers = new Customer;

        $customers->area = $request->area;
        $customers->classification = $request->classification;
        $customers->customer = $request->customer_code;
        $customers->name = $request->name;
        $customers->group = $request->group;
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
        return view('customer.edit', compact('id'));
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
        dd($customer);
    }

}
