<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestionnaire extends Model
{
    protected $fillable = [
        'status',
        'question',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surveyHeader()
    {
        return $this->belongsTo(SurveyHeader::class);
    }

}
