<?php

namespace App\Http\Controllers;

use Auth;
use Carbon;
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
        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

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
                    ->orderBy('date', 'desc')->get();
        $new_schedule = [];
        // Executive and VP Roles
        if(Auth::user()->level() >= 6){
            $new_schedule = $schedule; 
        }else{
            foreach($schedule->pluck('user') as $key => $value){
                // AVP and Coordinator  roles
                if(Auth::user()->level() < 6){
                    if($value->roles[0]['level'] < 6){
                        $new_schedule[] = $schedule[$key]; 
                    }
                }else{
                    $new_schedule = []; 
                }
            }
        }
        return $new_schedule;
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

     /**
     * Get all area that is current visiting
     *
     * @return \Illuminate\Http\Response
     */

    public function visiting(){
        return Attendance::with('user', 'schedule')->whereDate('sign_in', Carbon\Carbon::now()->toDateString())
                ->whereNull('sign_out')->orderBy('id','desc')->get();
    }

    /**
     * Get all area that is visited
     *
     * @return \Illuminate\Http\Response
     */
    public function completed(){
        return Attendance::with('user', 'schedule')->whereDate('sign_in', Carbon\Carbon::now()->toDateString())
                ->whereNotNull('sign_out')->orderBy('id','desc')->get();
    }

}