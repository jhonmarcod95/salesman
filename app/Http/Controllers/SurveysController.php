<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Survey;
use App\SurveyHeader;
use App\SurveyQuestionnaire;
use DB;

class SurveysController extends Controller
{
    public function index()
    {
        return view('surveys.index');
    }

    public function surveyHome()
    {
        return view('surveys.home');
    }

    public function checkIfAlreadySurveyed($customer_id)
    {
        $survey = Survey::where('customer_id',$customer_id)
                        ->whereDate('created_at',Carbon::today())
                        ->where('user_id',Auth::user()->id)
                        ->exists();

        if($survey === true) {
            return 1;
        }

        return 0;
    }

    public function createQuestionnaire(Request $request)
    {
        $request->validate([
            'company'=> 'required',
            'header'=> 'required',
            'questionnaire'=> 'required',
        ]);

        $company = $request->company;
        $header = $request->header;
        $questionnaire = json_decode($request->questionnaire);

        DB::beginTransaction();
        $checkStatus = SurveyHeader::where('company_id',$company)
            ->whereHas('surveyQuestionnaires', function ($q){
                $q->where('status', 1);
            })
            ->orderBy('id','desc')->first();

        if ($checkStatus != '' || $checkStatus != null) {
            $updateQuestionnaire = SurveyQuestionnaire::with('surveyHeader')->where('survey_header_id',$checkStatus->id)
                ->update([
                    'status' => 0
                ]);
        }

        $saveHeader = new SurveyHeader;
        $saveHeader->header = $header;
        $saveHeader->company_id = $company;
        $saveHeader->save();

        foreach ($questionnaire as $q) {
            $saveQuestion = new SurveyQuestionnaire;
            $saveQuestion->status = 1;
            $saveQuestion->question = $q->quest;
            $saveQuestion->user_id = auth()->user()->id;
            $saveQuestion->survey_header_id = $saveHeader->id;
            $saveQuestion->rating = 1;
            $saveQuestion->save();
        }

        DB::commit();

        $dataResult = SurveyHeader::with(['company', 'surveyQuestionnaires'])->where('id', $saveHeader->id)->first();

        return $dataResult;
    }

    public function editQuestionnaire(Request $request)
    {
        $request->validate([
            'id'=> 'required',
            'company'=> 'required',
            'header'=> 'required',
            'questionnaire'=> 'required',
        ]);

        $id = $request->id;
        $company = $request->company;
        $header = $request->header;
        $questionnaire = json_decode($request->questionnaire);

        DB::beginTransaction();

        $saveHeader = SurveyHeader::with('surveyQuestionnaires')->where('id',$id)->first();
        $saveHeader->header = $header;
        $saveHeader->company_id = $company;
        $saveHeader->save();

        $checkStatus = SurveyHeader::where('company_id',$company)
            ->whereHas('surveyQuestionnaires', function ($q){
                $q->where('status', 1);
            })
            ->orderBy('id','desc')->first();

        if ($checkStatus != '' || $checkStatus != null) {
            $updateQuestionnaire = SurveyQuestionnaire::with('surveyHeader')->where('survey_header_id',$checkStatus->id)
                ->update([
                    'status' => 0
                ]);
        }

        $deleteSurvey = SurveyQuestionnaire::where('survey_header_id',$id)->delete();
        
        foreach ($questionnaire as $q) {
            $saveQuestion = new SurveyQuestionnaire;
            $saveQuestion->rating = 1;
            $saveQuestion->status = 1;
            $saveQuestion->survey_header_id = $id;
            $saveQuestion->question = $q->quest;
            $saveQuestion->user_id = auth()->user()->id;
            $saveQuestion->save();
        }

        DB::commit();

        return 'Survey Successfully Updated';
    }

    public function deleteQuestionnaire(Request $request)
    {
        DB::beginTransaction();
        $deleteHeader = SurveyHeader::where('id',$request->id)->delete();
        $deleteQuestion = SurveyQuestionnaire::where('survey_header_id',$request->id)->delete();
        DB::commit();

        return 'Survey Successfully Deleted';
    }

    public function displayQuestionnaire(Request $request)
    {
        session(['header_text' => 'Created Survey']);
        return view('surveys.created_survey');
    }

    public function dataQuestionnaire(){
        $survey = SurveyHeader::with(['company', 'surveyQuestionnaires'])->orderBy('created_at', 'desc')->get();
        return $survey;
    }

    public function fetchQuestionnaire(Request $request)
    {
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required|after_or_equal:startDate',
        ]);

        $company = $request->company != "" ? $request->company : Auth::user()->company->id;

        $survey = SurveyHeader::with(['company', 'surveyQuestionnaires'])->where('company_id', $company)
        ->whereDate('created_at', '>=',  $request->startDate)
        ->whereDate('created_at' ,'<=', $request->endDate)
        ->orderBy('created_at', 'desc')
        ->get();

        return $survey;
    }
}
