<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Division;
use App\Company;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Division::whereIn('company_code',
        Company::whereIn('id',$request->company)->pluck('code')->all()
        )->orderBy('id', 'desc')->get();
    }
}
