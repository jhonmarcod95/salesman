<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\{
    SapUser
};

class SapUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sap-user.index');
    }

    /**
     * Get all sap users
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData()
    {
        return SapUSer::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sap_id' => 'required',
            'sap_password' => 'required',
            'sap_server' => 'required'
        ]);
        return SapUser::create(['user_id' => Auth::user()->id] + $request->all());
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, SapUser $sapUser)
    {
        $request->validate([
            'sap_id' => 'required',
            'sap_password' => 'required',
            'sap_server' => 'required'
        ]);

        if($sapUser->update(['user_id' => Auth::user()->id] + $request->all())){
            return $sapUser;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapUser $sapUser)
    {
        if($sapUser->delete()){
            return $sapUser;
        }
    }
}
