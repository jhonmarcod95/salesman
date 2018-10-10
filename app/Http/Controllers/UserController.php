<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

     /**
     *  Display user index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Users']);
        
        return view('user.index');
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(){
        return  User::orderBy('id','desc')->get();
    }

    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        if($user->save()){
            return ['redirect' => route('tsr_list')];
        }
    }

}
