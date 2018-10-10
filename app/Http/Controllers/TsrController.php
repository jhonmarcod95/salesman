<?php

namespace App\Http\Controllers;

use App\TechnicalSalesRepresentative;
use Illuminate\Http\Request;

class TsrController extends Controller
{
    /**
     *  Display tsr index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Technical Sales Representative']);

        return view('tsr.index');
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  TechnicalSalesRepresentative::orderBy('id','desc')->get();
    }

    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('tsr.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $tsr = new TechnicalSalesRepresentative;

        $tsr->last_name = $request->last_name;
        $tsr->first_name = $request->first_name;
        $tsr->middle_name = $request->middle_name;
        $tsr->middle_initial = $request->middle_initial;
        $tsr->suffix = $request->suffix;
        $tsr->email = $request->email;
        $tsr->address = $request->address;
        $tsr->contact_number = $request->contact_number;
        $tsr->date_of_birth = $request->date_of_birth;
        $tsr->date_hired = $request->date_hired;

        if($tsr->save()){
            return ['redirect' => route('tsr_list')];
        }
    }
    /**
     * Show the edit form
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('tsr.edit', compact('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TechnicalSalesRepresentative::findOrFail($id);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicalSalesRepresentative $technicalSalesRepresentative)
    {
        
        $technicalSalesRepresentative->last_name = $request->last_name;
        $technicalSalesRepresentative->first_name = $request->first_name;
        $technicalSalesRepresentative->middle_name = $request->middle_name;
        $technicalSalesRepresentative->middle_initial = $request->middle_initial;
        $technicalSalesRepresentative->suffix = $request->suffix;
        $technicalSalesRepresentative->email = $request->email;
        $technicalSalesRepresentative->address = $request->address;
        $technicalSalesRepresentative->contact_number = $request->contact_number;
        $technicalSalesRepresentative->date_of_birth = $request->date_of_birth;
        $technicalSalesRepresentative->date_hired = $request->date_hired;

        if($technicalSalesRepresentative->save()){
            return ['redirect' => route('customers_list')];
        }
    }
}
