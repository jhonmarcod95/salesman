<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Planter extends Model
{
    protected $fillable = [
        'user_id',
        'planter_name',
        'contact_number',
        'planter_address',
        'hacienda_loc',
        'total_area',
        'assistance_needed',
        'bir_id',
        'planter_picture',
        'parcellary',
        'area',
        'date_planted',
        'date_estimate_harvest',
        'planter_area_type_id',
        'planter_soil_type_id',
        'planter_soil_condition_id',
        'planter_crop_type_id',
        'remarks',
        'area_converted',
        'crop_tech_remarks',
        'variety',
        'planter_code',
    ];

    protected $casts = [
        // 'n_p' => 'array',
        // 'r1_r2_r3' => 'array',
        // 'empty' => 'array',
        'assistance_needed' => 'array',
    ];

    /**
     * Convet array to string conversion
     */
    // public function setNPAttribute($value)
    // {
    //     $this->attributes['n_p'] = json_encode($value);
    // }

    // public function setR1R2R3Attribute($value)
    // {
    //     $this->attributes['r1_r2_r3'] = json_encode($value);
    // }

    // public function setEmptyAttribute($value)
    // {
    //     $this->attributes['empty'] = json_encode($value);
    // }

    public function setAssitanceNeededAttribute($value)
    {
        $this->attributes['assistance_needed'] = json_encode($value);
    }

    // Relationship model

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planterSoilType()
    {
        return $this->belongsTo(PlanterSoilType::class,'planter_soil_type_id');
    }

    public function planterSoilConditionType()
    {
        return $this->belongsTo(PlanterSoilCondition::class,'planter_soil_condition_id');
    }

    public function planterAreaType()
    {
        return $this->belongsTo(PlanterAreaType::class);
    }

    public function planterCropType()
    {
        return $this->belongsTo(PlanterCropType::class);
    }

    public function planterHacienda()
    {
        return $this->belongsTo(PlanterHacienda::class,'planter_code','planter_code');
    }
}
