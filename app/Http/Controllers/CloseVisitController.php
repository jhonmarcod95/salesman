<?php

namespace App\Http\Controllers;

use App\Http\Resources\CloseVisitResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\CloseVisit;
use Carbon\Carbon;

class CloseVisitController extends Controller
{

    public function index() {
        return view('close-visits.index');
    }   

    public function searchCloseVisit(Request $request) {

        $this->validate($request, [
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $request_status = $request->request_status;

        $closeVisits = CloseVisit::when($request_status || $request_status == '0'  , function($q) use ($request_status){
                    $q->where('isApproved', $request_status);
                })
                ->whereDate('created_at', '>=',  $request->startDate)
                ->whereDate('created_at' ,'<=', $request->endDate)
                ->orderBy('id','desc')
                ->get();

        return CloseVisitResource::collection($closeVisits);


    }

    public function requestToClose() {


        $closevisits = CloseVisit::whereHas('user', function($q) {
            $q->where('company_id',Auth::user()->company_id);
        })->orderBy('id','desc')->get();

        return CloseVisitResource::collection($closevisits);
    
    }

    public function closeVisit(Request $request, CloseVisit $closeVisit)
    {
        $this->validate($request, [
            'isApproved' => 'required',
        ]);

        $closeVisit->isApproved = $request->isApproved;
        $closeVisit->approved_date = Carbon::today();
        $closeVisit->confirmedBy()->associate(Auth::user()->id);
        $closeVisit->save();

        return new CloseVisitResource($closeVisit);
    }
}
