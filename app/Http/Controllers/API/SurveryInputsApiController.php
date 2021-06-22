<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SurveyInput;
use App\SurveyInputQuestionnaire;
use Illuminate\Support\Facades\Auth;

class SurveryInputsApiController extends Controller
{

    /**
     * Show questinnaire
     */
    public function questionnaires()
    {
        $questionnaire = SurveyInputQuestionnaire::where('user_id', Auth::user()->company->id)
                                        ->where('status',1)
                                        ->get();

        return $questionnaire;
    }

    /**
     * Store 
     */
    public function store()
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'answer' => 'required',
            'survey_input_questionnaire_id' > 'required'
        ]);

        $survey = SurveyInput::create([
            'user_id' => Auth::user()->id,
            'customer_id' => $request->customer_id,
            'survey_input_questionnaire_id' => $request->survey_input_questionnaire_id,
            'answer' => $request->answer,
            'remarks' => $request->remarks,
        ]);
    }

    /**
     * Upload Survey reciept
     *
     * @param Survey $survey
     * @return response
     */
    public function uploadSurveyPhoto(Request $request, SurveyInput $surveyInput)
    {

        $newimg = file_put_contents(public_path('storage/surveys/') . $request->header('File-Name'), file_get_contents('php://input'));

        $surveyInput->customer_photo = 'surveys-input/'. $request->header('File-Name');
        $surveyInput->save();

        return $surveyInput;

    }
}
