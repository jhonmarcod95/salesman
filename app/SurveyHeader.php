<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyHeader extends Model
{
    protected $fillable = [
        'header',
        'header_description'
    ];

    public function surveyQuestionnaires()
    {
        return $this->hasMany(SurveyQuestionnaire::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
