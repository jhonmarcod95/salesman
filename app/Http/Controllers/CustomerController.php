<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        session(['header_text' => 'Customers']);

        $customers = Customer::all();


        return view('customer.index', compact(
            'customers'
        ));
    }

    public function create(){
        return view('customer.create');
    }

}
