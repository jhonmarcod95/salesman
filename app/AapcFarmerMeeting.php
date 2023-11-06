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
        'aapc_activity_type_id',
        'region_name',
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

    /**
     * scope
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['farmername'] ?? null, function ($query, $farmername) {
            $query->whereHas('farmer', function($q, $farmername) {
                $q->where('first_name', 'like', '%'.$farmername.'%')
                ->orWhere('last_name', 'like', '%'.$farmername.'%');
            });
        })->when($filters['cultivated_crops'] ?? null, function ($query, $cultivated_crops) {
            $query->whereHas('farmer.crops', function($q, $cultivated_crops) {
                $q->where('crop_name','like', '%'.$cultivated_crops.'%');
            });
        })->when($filters['region_id'] ?? null, function ($query, $region_id) {
            $query->whereHas('region', function($q,$region_id) {
                $q->where('id',$region_id);
            });
        })->when($filters['city'] ?? null, function ($query, $city) {
            $query->where('city', 'like', '%'.$city.'%');
        })->when($filters['store_name'] ?? null, function ($query, $store_name) {
            $query->whereHas('tindahan', function($q,$store_name) {
                $q->where('name', 'like', '%'.$store_name.'%');
            });
        });
    }
}
