<?php

namespace App\Http\Controllers;

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
        session(['header_text' => 'Dashboard']);

        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('home',compact('notification'));
    }
}
