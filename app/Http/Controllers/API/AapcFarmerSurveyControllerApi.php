<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\AapcFarmerMeeting;
use App\AapcFarmer;
use App\AapcRegion;
use App\AapcCrop;
use App\AapcRecommendation;
use App\AapcStore;
use App\AapcVegetable;
use App\AapcInsectType;
use App\AapcDiseaseType;
use App\AapcBumo;
use App\AapcBumosType;
use Carbon\Carbon;
use App\AapcCultivatedCrop;
use App\AapcBumoInsect;
use App\AapcBumoDisease;
use App\AapcActivityType;
use App\AapcCultivatedCropName;
use DB;

class AapcFarmerSurveyControllerApi extends Controller
{
    public function index(Request $request)
    {
        // (['farmername','selected_cultivated_crops','region_id','city','store_name']))

        $farmer_name = $request->farmername;
        $cultivated_crops = $request->cultivated_crops;
        $region_id = $request->region_id;
        $barangay = $request->barangay;
        $store_name = $request->store_name;
        $date_conducted = $request->date_conducted;

        $checkRole = Auth::user()->roles[0]->slug === 'tsr' ? true : null;

        return AapcFarmerMeeting::orderBy('id','desc')
                    ->when($checkRole, function ($query) use ($checkRole) {
                        return $query->where('user_id',$checkRole);
                    })
                    ->when($farmer_name, function ($query) use ($farmer_name) {
                        return $query->whereHas('farmer', function($q) use ($farmer_name) {
                            $q->where('first_name','like', '%'.$farmer_name.'%');
                        });
                    })
                    ->when($cultivated_crops, function ($query) use ($cultivated_crops) {
                        $query->whereHas('farmer.cultivatedCrops', function($q) use ($cultivated_crops) {
                            $q->where('crop_name','like', '%'.$cultivated_crops.'%');
                        });
                    })
                    ->when($region_id, function ($query) use ($region_id) {
                        $query->whereHas('region', function($q) use ($region_id) {
                            $q->where('id',$region_id);
                        });
                    })
                    ->when($barangay, function ($query) use ($barangay) {
                        $query->where('venue', 'like', '%'.$barangay.'%');
                    })
                    ->when($store_name, function ($query) use ($store_name) {
                        $query->whereHas('tindahan', function($q) use ($store_name) {
                            $q->where('name', 'like', '%'.$store_name.'%');
                        });
                    })
                    ->when($date_conducted, function ($query) use ($date_conducted) {
                        $query->whereDate('date_conducted',Carbon::parse($date_conducted));
                    })
                    ->with('activityType',
                            'region',
                            'farmer',
                            'farmer.cultivatedCrops',
                            'vegetable',
                            'tindahan',
                            'farmerCrops',
                            'bumos',
                            'bumoInsects',
                            'bumoDiseases',
                            'user')
                    ->paginate(10);
    }

    public function aapcCultivatedCropName()
    {
        return AapcCultivatedCropName::orderBy('id','desc')->get();
    }

    public function aapcActivityType()
    {
        return AapcActivityType::orderBy('id','desc')->get();
    }

    public function aapcRegion()
    {
        return AapcRegion::orderBy('id','asc')->get();
    }

    public function aapcCrops()
    {
        return AapcCrop::orderBy('id','asc')->get();
    }

    public function aapcRecommendations()
    {
        $recommendations = AapcRecommendation::orderBy('id','asc')->get();

        return collect($recommendations)->groupBy('brand_type')->all();
    }

    public function aapcVegetable()
    {
        return AapcVegetable::orderBy('id','asc')->get();
    }
    
    public function aapcInsectTypes()
    {
        return AapcInsectType::orderBy('id','asc')->get();
    }

    public function aapcDiseaseTypes()
    {
        return AapcDiseaseType::orderBy('id','asc')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'activity_type_id' => 'required',
            'region_name' => 'required',
            'store_name' => 'required',
            'city' => 'required',
            'plant_season_start' => 'required',
            'plant_season_end' => 'required',
            'barangay' => 'required',
            'date_conducted' => 'required',
            'selected_crops' => 'required',
            'farmer_first_name' => 'required',
            'farmer_last_name' => 'required',
            'farmer_contact_number' => 'required',
            'store_address' => 'required',
            // 'store_zip_code' => 'required',
            'farmer_crop_cultivated' => 'required',
            'farmer_hectares' => 'required',
            'bumo_weeds_brand_name' => 'required',
            'bumo_insect_type_id' => 'required',
            'bumo_insect_brand_name' => 'required',
            'bumo_disease_type_id' => 'required',
            'bumo_disesse_brand_name' => 'required',
            'c_bumo_insect_type_id' => 'required',
            'c_bumo_insect_brand_name' => 'required',
            'c_bumo_disease_type_id' => 'required',
            'c_bumo_disesse_brand_name' => 'required',
        ],[
            'activity_type_id.required' => "The activity type field is required",
            'region_name.required' => "The region field is required",
            'store_name.required' => "The store name field is required",
            'barangay.required' => "The venue field is required",
            'plant_season_start.required' => "The planting season start is required",
            'plant_season_end.required' => "The planting season end is required",
            'selected_crops.required' => "The crop field is required",
            'store_address.required' => "The address field is required",
            // 'store_zip_code.required' => "The zip code field is required",
            'bumo_weeds_brand_name.required' => "The brand name field is required",
            'bumo_insect_type_id.required' => "The insect type field is required",
            'bumo_insect_brand_name.required' => "The insect brand name field is required",
            'bumo_disease_type_id.required' => "The disease type field is required",
            'bumo_disesse_brand_name.required' => "The disease brand name field is required",
            'c_bumo_insect_type_id.required' => "The insect type field is required",
            'c_bumo_insect_brand_name.required' => "The insect brand name field is required",
            'c_bumo_disease_type_id.required' => "The disease type field is required",
            'c_bumo_disesse_brand_name.required' => "The disease brand name field is required",
        ]);

            DB::beginTransaction();

            $farmerMeeting = new AapcFarmerMeeting;
            $farmerMeeting->user_id = Auth::user()->id;
            $farmerMeeting->region_name = $request->region_name;
            $farmerMeeting->region_id = 0;
            $farmerMeeting->city = $request->city['name'];
            $farmerMeeting->venue = $request->barangay;
            $farmerMeeting->remarks = $request->remarks;
            $farmerMeeting->date_conducted = $request->date_conducted;
            $farmerMeeting->store_id = 0;
            $farmerMeeting->farmer_id = 0;
            $farmerMeeting->vegestable_id = 0;
            $farmerMeeting->aapc_activity_type_id = $request->activity_type_id;
            $farmerMeeting->save();

            // saving aapc recommendations
            if($request->input('selected_recommendations')) {
                $farmerMeeting->meetingRecommendations()->attach($request->selected_recommendations);
            }

            // saving selected crop;
            if($request->input('selected_crops')) {

                $selected_crops = $request->input('selected_crops');

                foreach($selected_crops as $item) {
                    if($item === 1) {
                        $farmerMeeting->farmerCrops()->attach($item,
                            ['others' => $request->rice_others
                        ]);
                    }
                    if($item === 4) {
                        $farmerMeeting->farmerCrops()->attach($item,
                            ['others' => $request->lowland_others
                        ]);
                    }
                    if($item === 5) {
                        $farmerMeeting->farmerCrops()->attach($item,[
                            'others' => $request->highland_others,
                        ]);
                    }
                    if($item == 2 || $item == 3) {
                        $farmerMeeting->farmerCrops()->attach($item);
                    }
                }

            }

            // $farmerMeeting->activityType()->associate($request->activity_type_id);

            if($request->vegetable_id) {
                $farmerMeeting->vegetable()->associate($request->vegetable_id);
            }

            // find if the farmer is existing in the farmers database; else create one
            $farmer = AapcFarmer::where('first_name', $request->farmer_first_name)
                                ->where('last_name',$request->farmer_last_name)
                                ->first();

            $saved_farmer = array();
            if(!$farmer) {

                $new_farmer = new AapcFarmer;
                $new_farmer->first_name = $request->farmer_first_name;
                $new_farmer->last_name = $request->farmer_last_name;
                $new_farmer->contact_number = $request->farmer_contact_number;
                $new_farmer->address = $request->farmer_address;
                $new_farmer->city = $request->farmer_city['name'];
                $new_farmer->region_id = 0; // set to default
                $new_farmer->region_name = $request->farmer_region_name;
                $new_farmer->zip_code = 'N/A';
                $new_farmer->crops_cultivated = $request->farmer_crop_cultivated;
                $new_farmer->land_hectares = $request->farmer_hectares;
                $new_farmer->save();

                $saved_farmer = $new_farmer;

                $farmerMeeting->farmer()->associate($new_farmer->id);
            }
            
            if($farmer) {
                $farmerMeeting->farmer()->associate($farmer->id);
            }
            
            $crops = $request->farmer_all_cultivated_crops;
            foreach($crops as $crop) {
                AapcCultivatedCrop::insert([
                    array(
                        'aapc_farmer_id' => $farmer ? $farmer->id : $saved_farmer->id,
                        'crop_name' => $crop['value'],
                        'plant_season_start' => $crop['plant_season_start'],
                        'plant_season_end' => $crop['plant_season_end'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            // store suking tindahan
            $aapcStore = new AapcStore;
            $aapcStore->name = $request->store_name;
            $aapcStore->address = $request->store_address;
            $aapcStore->city = $request->store_city;
            $aapcStore->zip_code = 'N/A';
            $aapcStore->save();

            if($aapcStore) {
                $farmerMeeting->store_id = $aapcStore->id;
                $farmerMeeting->save();
            }

            // primary

            $bumo_insects = $request->bumo_for_insects_all;
            foreach($bumo_insects as $insect) {
                AapcBumoInsect::insert([
                    array(
                        'bumos_type_id' => 1,
                        'aapc_farmer_meeting_id' => $farmerMeeting->id,
                        'weeds_brand_name' => $request->bumo_weeds_brand_name,
                        'insect_type_id' => $insect['bumo_insect_type_id'],
                        'insect_brand_name' => $insect['bumo_insect_brand_name'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            $bumo_diseases = $request->bumo_for_diseases_all;
            foreach($bumo_diseases as $disease) {
                AapcBumoDisease::insert([
                    array(
                        'bumos_type_id' => 1,
                        'aapc_farmer_meeting_id' => $farmerMeeting->id,
                        'weeds_brand_name' => $request->bumo_weeds_brand_name,
                        'disease_type_id' => $disease['bumo_disease_type_id'],
                        'disease_brand_name' => $disease['bumo_disesse_brand_name'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            $cons_bumo_insects = $request->cons_bumo_for_insects_all;
            foreach($cons_bumo_insects as $insect) {
                AapcBumoInsect::insert([
                    array(
                        'bumos_type_id' => 2,
                        'aapc_farmer_meeting_id' => $farmerMeeting->id,
                        'weeds_brand_name' => $request->c_bumo_weeds_brand_name,
                        'insect_type_id' => $insect['bumo_insect_type_id'],
                        'insect_brand_name' => $insect['bumo_insect_brand_name'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            $cons_bumo_diseases = $request->cons_bumo_for_diseases_all;
            foreach($cons_bumo_diseases as $disease) {
                AapcBumoDisease::insert([
                    array(
                        'bumos_type_id' => 2,
                        'aapc_farmer_meeting_id' => $farmerMeeting->id,
                        'weeds_brand_name' => $request->c_bumo_weeds_brand_name,
                        'disease_type_id' => $disease['bumo_disease_type_id'],
                        'disease_brand_name' => $disease['bumo_disesse_brand_name'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            // secondary
            $aapcBumo_consider = new AapcBumo;
            $aapcBumo_consider->bumos_type_id = 2; // secondary; for consideration
            $aapcBumo_consider->aapc_farmer_meeting_id = $farmerMeeting->id; // primary
            $aapcBumo_consider->weeds_brand_name = $request->c_bumo_weeds_brand_name;
            $aapcBumo_consider->insect_type_id = $request->c_bumo_insect_type_id;
            $aapcBumo_consider->insect_brand_name = $request->c_bumo_insect_brand_name;
            $aapcBumo_consider->disease_type_id = $request->c_bumo_disease_type_id;
            $aapcBumo_consider->disease_brand_name = $request->c_bumo_disesse_brand_name;
            $aapcBumo_consider->save();

            DB::commit();

            return $farmerMeeting;
    }
}
