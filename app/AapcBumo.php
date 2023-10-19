<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcBumo extends Model
{
    protected $fillable = [
        'bumos_type_id',
        'weeds_brand_name',
        'insect_type_id',
        'insect_brand_name',
        'disease_type_id',
        'disease_brand_name',
        'aapc_farmer_meeting_id',
    ];

    public function insectType()
    {
        return $this->belongsTo(AapcInsectType::class);
    }

    public function diseaseType()
    {
        return $this->belongsTo(AapcDiseaseType::class);
    }

    public function farmerMeetings()
    {
        return $this->belongsTo(AapcFarmerMeeting::class);
    }
}
