<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyInput extends Model
{
    protected $fillable = [
        'remarks',
        'customer_photo',
        'answer',
        'customer_id',
        'survey_input_questionnaire_id',
        'user_id',
    ];

    /**
     * Model relationshipt
     */
    public function surveyInputQuestionnaire()
    {
        return $this->belongsTo(SurveyInputQuestionnaire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    } 
}
