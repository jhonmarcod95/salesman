<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcFarmer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'address',
        'city',
        'region_id',
        'zip_code',
        'crops_cultivated',
        'land_hectares',
        'region_name',
    ];

    public function cultivatedCrops()
    {
        return $this->hasMany(AapcCultivatedCrop::class);
    }
}
