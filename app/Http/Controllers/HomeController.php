<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Message;
use App\Schedule;
use App\Customer;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->hasRole(['hr','ap','tax','audit'])){
            session(['header_text' => 'Dashboard']);

            $message = Message::where('user_id', '!=', Auth::user()->id)->get();
            $notification = 0;  
            foreach($message as $notif){
    
                $ids = collect(json_decode($notif->seen, true))->pluck('id');
                if(!$ids->contains(Auth::user()->id)){
                    $notification++;
                }
            }
    
            return view('home',compact('notification'));
        }elseif(Auth::user()->hasRole('ap')){
            session(['header_text' => 'Payment']);

            return view('payment.index');

        }elseif(Auth::user()->hasRole(['tax', 'audit'])){
            session(['header_text' => 'Payment Posted']);

            return view('payment-posted.index');
        }else{
            session(['header_text' => 'Attendance Report']);

            return view('attendance-report.index');
        }

    }

    public function dashboard(){
        session(['header_text' => 'Dashboard']);
        return view('dashboard');
    }

    public function scheduleForVisit(){
        $date_today = date('Y-m-d');

        $companyId = Auth::user()->companies[0]->id;

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return $schedule_for_visit = Schedule::with('attendances','user','user.companies','customer.provinces.regions')
                                                ->whereIn('user_id', $selected_user)
                                                ->where('date', '=' , $date_today)
                                                ->where('type','1')
                                                ->orderBY('name','ASC')
                                                ->get();
    }

}
