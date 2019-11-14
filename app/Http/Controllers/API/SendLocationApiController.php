<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SendLocation;
use Illuminate\Support\Facades\Auth;

class SendLocationApiController extends Controller
{
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
        $this->validate($request, [
            'schedule_id' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'sign_type' => 'required'
        ]);

        $sendLocation = new SendLocation();
        $sendLocation->user_id = Auth::user()->id;
        $sendLocation->schedule_id = $request->input('schedule_id');
        $sendLocation->lat = $request->input('lat');
        $sendLocation->lng = $request->input('lng');
        $sendLocation->sign_type_id = $request->input('sign_type');
        $sendLocation->save();

        return $sendLocation;
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
