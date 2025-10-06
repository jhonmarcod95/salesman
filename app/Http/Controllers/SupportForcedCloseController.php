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
        $file_name = '';
        if($request->hasFile('file'))
        {
            $original_name = str_replace(' ', '',$request->file('file')->getClientOriginalName());
            $name = time().'_'.$original_name;

            $request->file('file')->move(public_path('storage/attendance/'), $name);
            $ext = pathinfo(public_path('storage/attendance/').$file_name, PATHINFO_EXTENSION);
            $file_name = 'attendance/'.$name;
        }
        $sched = Schedule::with('schedule_attendances')->where('id',intval($request->schedule_id))->first();
        $sched->status = 1;
        $sched->isCurrent = 0;
        $sched->save();

        $attend = Attendance::where('schedule_id',intval($request->schedule_id))->get();
        if ($attend) {
            $signIn = date('Y-m-d H:i:s',strtotime($request->new_start_date . ' ' . $request->new_start_time));
            $signOut = date('Y-m-d H:i:s',strtotime($request->new_end_date . ' ' . $request->new_end_time));

            if ($request->new_start_date && $request->new_start_time && $request->new_end_date && $request->new_end_time) {
                if (!empty($file_name)) {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_out_image' => $file_name,
                        'sign_in_image' => $file_name,
                        'sign_in' => $signIn,
                        'sign_out' => $signOut,
                        'force_close_sign_in_date' => date('Y-m-d H:i:s'),
                        'force_close_sign_out_time' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'sign_out_remarks' => 'Requested to force close Sign Out.',
                        'isSync' => 1,
                        'sign_in_by' => auth()->user()->id,
                        'sign_out_by' => auth()->user()->id,
                    ]);
                } else {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_in' => $signIn,
                        'sign_out' => $signOut,
                        'force_close_sign_in_date' => date('Y-m-d H:i:s'),
                        'force_close_sign_out_time' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'sign_out_remarks' => 'Requested to force close Sign Out.',
                        'isSync' => 1,
                        'sign_in_by' => auth()->user()->id,
                        'sign_out_by' => auth()->user()->id,
                    ]);
                }

            } elseif ($request->new_start_date && $request->new_start_time) {
                if (!empty($file_name)) {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_in_image' => $file_name,
                        'sign_in' => $signIn,
                        'force_close_sign_in_date' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'isSync' => 1,
                        'sign_in_by' => auth()->user()->id,
                    ]);
                } else {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_in' => $signIn,
                        'force_close_sign_in_date' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'isSync' => 1,
                        'sign_in_by' => auth()->user()->id,
                    ]);
                }
            } else {
                if (!empty($file_name)) {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_out_image' => $file_name,
                        'sign_out' => $signOut,
                        'force_close_sign_out_time' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'sign_out_remarks' => 'Requested to force close Sign Out.',
                        'isSync' => 1,
                        'sign_out_by' => auth()->user()->id,
                    ]);
                } else {
                    Attendance::where('schedule_id',intval($request->schedule_id))->update([
                        'sign_out' => $signOut,
                        'force_close_sign_out_time' => date('Y-m-d H:i:s'),
                        'sign_in_remarks' => 'Requested to force close Sign In.',
                        'sign_out_remarks' => 'Requested to force close Sign Out.',
                        'isSync' => 1,
                        'sign_out_by' => auth()->user()->id,
                    ]);
                }
            }
        }
        DB::commit();

        return Schedule::with('schedule_attendances')->where('id',intval($request->schedule_id))->first();
    }
}
