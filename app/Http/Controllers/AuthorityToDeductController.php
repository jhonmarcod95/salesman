<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthorityToDeductController extends Controller
{

    public function index($user_id) {
        $user = User::find($user_id);
        return view('authority-to-deduct.index')->with([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function login() {
        return view('authority-to-deduct.login');
    }
}
