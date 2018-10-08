<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        session(['header_text' => 'Users']);

        $users = User::all();

        return view('user.index',compact(
            'users'
        ));
    }


    public function create(){
        return view('user.create');
    }
}
