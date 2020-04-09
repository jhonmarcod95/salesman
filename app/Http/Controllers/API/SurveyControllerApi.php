<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyReportResource;
use App\Http\Resources\SurveyHeaderResource;
use App\SurveyHeader;
use App\Brand;
use App\Survey;

class SurveyControllerApi extends Controller
{

    public function surveyCompany(Request $request)
    {

        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate'
        ]);

        $company = $request->company;

        $surveys = Survey::whereHas('user.company', function($query) use ($company) {
            $query->where('id',$company);
        })
        ->whereDate('created_at', '>=',  $request->startDate)
        ->whereDate('created_at' ,'<=', $request->endDate)
        ->orderBy('created_at', 'desc')->get();

        return SurveyReportResource::collection($surveys);
    }

    /**
     * Survey Questionnaires
     */
    public function surveyQuestionnaires()
    {

        // check user company
        // show only status active
        $user_company = Auth::user()->company->id;
        
        $questionnaires = SurveyHeader::where('company_id', $user_company)
                        ->whereHas('surveyQuestionnaires', function($q) {
                            $q->where('status',1);
                        })
                        ->with('surveyQuestionnaires')
                        ->get();

        return SurveyHeaderResource::collection($questionnaires);

    }

    /**
     * Show all brand based on auth
     */
    public function brands()
    {
        // $brands = Brand::all();

        $brands = Brand::where('company_id', Auth::user()->company->id)
                        ->get();

        return BrandResource::collection($brands);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $surveys = Survey::orderBy('id','DESC')
                        ->where('user_id', Auth::user()->id)
                        ->get();
        
        return SurveyResource::collection($surveys); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'brands' => 'required',
            'ranks' => 'required',
            // 'customer_photo' => 'required'
        ]);

        $survey = new Survey;
        $survey->user_id = Auth::user()->id;
        $survey->customer_id = $request->customer_id;
        $survey->ranks = json_encode($request->input('ranks'));
        $survey->brands = json_encode($request->input('brands'));
        $survey->remarks = $request->remarks;
        $survey->save();

        // if($survey) {
        //     $this->uploadSurveyPhoto($request->customer_photo, $survey->id);
        // }

        return new SurveyResource($survey);

    }

     /**
     * Upload Survey reciept
     *
     * @param Survey $survey
     * @return response
     */
    public function uploadSurveyPhoto(Request $request, Survey $survey)
    {

        $newimg = file_put_contents(public_path('storage/surveys/') . $request->header('File-Name'), file_get_contents('php://input'));

        $survey->customer_photo = 'surveys/'. $request->header('File-Name');
        $survey->save();

        return $survey;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey)
    {
        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
