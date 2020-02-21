<?php

namespace App\Http\Controllers;

use App\PaymentHeader;
use Auth;
use Illuminate\Http\Request;

use App\Message;

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
         $pay = PaymentHeader::with('company.sapServers', 'company.bankGls')
            ->whereDoesntHave('checkVoucher') // payment header without CV
            ->get();
         return $pay[0]->company->bankGls->where('id', '1')->first()->gl_account;

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
}
