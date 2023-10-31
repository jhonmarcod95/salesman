<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcCultivatedCrop extends Model
{
    protected $fillable = [
        'aapc_farmer_id',
        'crop_name',
        'plant_season_start',
        'plant_season_end',
    ];
}
