<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Planter extends Model
{
    protected $fillable = [
        'planter_name',
        'contact_number',
        'planter_address',
        'hacienda_loc',
        'total_area',
        'n_p',
        'r1_r2_r3',
        'empty',
        'soil_type',
        'soil_condition',
        'tons_cane',
        'tons_yields',
        'assistance_needed',
        'bir_id',
        'planter_picture',
        'parcellary',
    ];

    protected $casts = [
        'n_p' => 'array',
        'r1_r2_r3' => 'array',
        'empty' => 'array',
        'assistance_needed' => 'array',
    ];

    /**
     * Convet array to string conversion
     */
    public function setNPAttribute($value)
    {
        $this->attributes['n_p'] = json_encode($value);
    }

    public function setR1R2R3Attribute($value)
    {
        $this->attributes['r1_r2_r3'] = json_encode($value);
    }

    public function setEmptyAttribute($value)
    {
        $this->attributes['empty'] = json_encode($value);
    }

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
        return $this->belongsTo(PlanterSoilType::class);
    }

    public function planterSoilConditionType()
    {
        return $this->belongsTo(PlanterSoilConditionType::class);
    }
}
