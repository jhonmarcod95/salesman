<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyInputQuestionnaire extends Model
{
    protected $fillable = [
        'questions',
        'header',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
