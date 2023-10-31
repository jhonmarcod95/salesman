<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcFarmerMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'region_id',
        'city',
        'venue',
        'remarks',
        'date_conducted',
        'store_id',
        'farmer_id',
        'vegestable_id',
        'aapc_activity_type_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(AapcRegion::class);
    }

    public function farmer()
    {
        return $this->belongsTo(AapcFarmer::class);
    }

    public function crop()
    {
        return $this->belongsTo(AapcCrop::class);
    }

    public function bumo()
    {
        return $this->belongsTo(AapcBumo::class);
    }

    public function vegetable()
    {
        return $this->belongsTo(AapcVegetable::class);
    }

    public function tindahan()
    {
        return $this->belongsTo(AapcStore::class,'store_id');
    }

    public function farmerCrops()
    {
        return $this->belongsToMany(AapcCrop::class, 'crop_farmer_meeting','farmer_meeting_id','crop_id')
                    ->withPivot(['others','plant_season_start','plant_season_end'])->withTimestamps();
    }

    public function meetingRecommendations()
    {
        return $this->belongsToMany(AapcRecommendation::class, 'aapc_meeting_recommendations','farmer_meeting_id','aapc_recommendation_id')
                ->withTimestamps();
    }

    public function bumos()
    {
        return $this->hasMany(AapcBumo::class);
    }
    
    public function bumoInsects()
    {
        return $this->hasMany(AapcBumoInsect::class);
    }

    public function bumoDiseases()
    {
        return $this->hasMany(AapcBumoDisease::class);
    }

    public function activityType()
    {
        return $this->belongsTo(AapcActivityType::class,'aapc_activity_type_id');
    }
}
