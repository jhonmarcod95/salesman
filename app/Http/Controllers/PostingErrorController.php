<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\PaymentHeaderError;
use App\PaymentDetailError;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostingErrorController extends Controller
{

     /**
     *  Display user index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Posting Errors']);

        return view('auto-posting-error.index');
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(Request $request){
        $postingErrors = DB::table('payment_header_errors')
        ->select('payment_header_errors.id',
        'payment_header_errors.cover_week',
        'payment_detail_errors.return_message_description as return_message',
        'payment_detail_errors.created_at as creation_date',
        'payment_detail_errors.updated_at as update_date',
        'users.name as username',
        'users.email as email',
        'companies.name as company_name')
        ->join('users','users.id','=','payment_header_errors.user_id')
        ->join('payment_detail_errors','payment_detail_errors.payment_header_error_id','=','payment_header_errors.id')
        ->join('companies','companies.id','=','users.company_id')
        //search filters
        ->where('users.name','like','%'.$request->username.'%')
        ->whereBetween('payment_detail_errors.updated_at',[date($request->start_date),date($request->end_date)])
        ->orderBy('update_date','desc')
        ->get();

        return $postingErrors;
    }
    
}
