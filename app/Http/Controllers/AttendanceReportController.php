<?php

namespace App\Http\Controllers;

use Auth;
use App\Message;
use App\Attendance;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('attendance-report.index',compact('notification'));
    }

    
    /**
     * Get all Attendance Report
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return User::with('schedules', 'attendances')
                    ->whereHas('roles', function($q) {
                        $q->where('role_id', 3);
                    })->get();
    }

    /**
     * Get all Schedule by date
     *
     * @return \Illuminate\Http\Response
     */

    public function generateBydate(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $schedule = Schedule::with('user', 'attendances')
                    ->whereDate('date', '>=',  $request->startDate)
                    ->whereDate('date' ,'<=', $request->endDate)
                    ->orderBy('id', 'desc')->get();

        return $schedule;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
