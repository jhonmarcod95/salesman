<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthorityToDeductController extends Controller
{

    public function index() {
        return view('authority-to-deduct.index');
        // if(!isset($user_id)) {
        //     return;
        // }

        // $user = User::find($user_id);
        // return view('authority-to-deduct.index')->with([
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'atd_accepted' => $user->atd_accepted ? true : 'false',
        //     'atd_accepted_date' => $user->atd_accepted_date
        // ]);
    }

    /**
     * Verify use credential
     *
     */
    public function verifyUserCredential(Request $request)
    {
        $request->validate([
            'email'     => 'required|string',
            'password'  => 'required|string'
        ]);

        $user = User::where('email', trim($request->email))->first();
        if(!$user) {
            return response(['email' => ['Credentials not match.']], 401);
        }

        //Validate password
        if (!Hash::check($request->password, $user->password)) {
            return response(['password' => ['Password not match.']], 401);
        }

        //Update user info for accepting ATD
        if(!$user->atd_accepted) {
            $user->atd_accepted = 1;
            $user->atd_accepted_date = now();
            $user->save();
        }

        return [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'name' => $user->name,
            'atd_accepted' => $user->atd_accepted,
            'atd_accepted_date' => "$user->atd_accepted_date"
        ];
    }
}
