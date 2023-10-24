<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcBumoDisease extends Model
{
    public function diseaseType()
    {
        return $this->belongsTo(AapcDiseaseType::class);
    }
}
