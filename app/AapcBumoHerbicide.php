<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcBumoHerbicide extends Model
{
    protected $fillable = [
        'bumos_type_id',
        'aapc_farmer_meeting_id',
        'weeds_brand_name',
        'aapc_herbicide_type_id',
    ];

    public function herbicideType()
    {
        return $this->belongsTo(AapcHerbicideType::class);
    }
}
