<?php

namespace App\Http\Controllers;

use App\HRISUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SSOLoginController extends Controller
{
    public function login(Request $request)
    {
        $email   = $request->get('email');
        $expires  = $request->get('expires');
        $signature = $request->get('signature');

        if ($expires < time()) {
            abort(401, 'Token expired');
        }

        $expected = hash_hmac('sha256', $email . '|' . $expires, env('SSO_KEY'));

        if (!hash_equals($expected, $signature)) {
            abort(401, 'Invalid signature');
        }

        $hrisUser = HRISUser::where('email', $request->email)->firstOrFail();
        $user = User::where('email', $hrisUser->email)->first();
        abort_unless($user, Response::HTTP_FORBIDDEN);

        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect('/');
    }
}