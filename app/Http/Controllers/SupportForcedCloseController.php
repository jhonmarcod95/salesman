<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Schedule;
use App\Attendance;
use Illuminate\Support\Facades\DB;

class SupportForcedCloseController extends Controller
{
    public function index(){
        return view('forced-close/index');
    }

    public function indexData(Request $request){
        session(['header_text' => 'Force Close Schedule']);
        // $request
        $agents = User::with('companies')
            ->orderBy('name','asc')
            ->get();

        return [
            'agents' => $agents,
        ];
    }

    public function fetchSchedule(Request $request){
        $request->validate([
            'agent' => 'required',
            'date' => 'required',
        ]);

        $agent = $request->agent;
        $user_id = intval($agent['id']);
        $date = date('Y-m-d',strtotime($request->date));
        $schedules = Schedule::with('schedule_attendances')->where('user_id',$user_id)->where('date',$date)->get();
        return $schedules;
    }

    public function closeAttendance(Request $request){
        $request->validate([
            'schedule_id' => 'required',
        ]);

        DB::beginTransaction();
        $sched = Schedule::where('id',intval($request->schedule_id))->first();
        $sched->status = 1;
        $sched->isCurrent = 0;
        $sched->save();

        $attend = Attendance::where('schedule_id',intval($request->schedule_id))->get();
        if ($attend) {
            $signOut = date('Y-m-d H:i:s',strtotime($request->new_date . ' ' . $request->new_time));
            Attendance::where('schedule_id',intval($request->schedule_id))->update([
                'sign_out' => $signOut,
                'remarks' => 'Requested to force close',
                'isSync' => 1,
            ]);
        }
        DB::commit();

        return $sched;
    }
}
