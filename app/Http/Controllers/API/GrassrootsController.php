<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Grassroot;
use App\GrassrootsExpenseType;
use Auth;

class GrassrootsController extends Controller
{

    public function grassrootsExpenseTypes()
    {
        $grassrootsExpenseTypes = GrassrootsExpenseType::whereStatus(1)->get();
        return $grassrootsExpenseTypes;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'amount' => 'required',
            'expense_id' => 'required',
            'grassroots_expense_type_id' => 'required'
        ],[
            'grassroots_expense_type_id.required' => 'Grassroot expense type is required'
        ]);

        $grassroot = new Grassroot();
        $grassroot->user_id = Auth::user()->id;
        $grassroot->expense_id = $request->expense_id;
        $grassroot->grassroots_expense_type_id = $request->grassroots_expense_type_id;
        $grassroot->amount = $request->amount;
        $grassroot->remarks = $request->remarks;
        $grassroot->save();

        return $grassroot;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
