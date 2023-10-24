<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\AapcFarmerMeeting;
use App\AapcFarmer;
use App\AapcRegion;
use App\AapcCrop;
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
use DB;

class AapcFarmerSurveyControllerApi extends Controller
{
    public function index()
    {
        return AapcFarmerMeeting::orderBy('id','desc')
                    ->with('region',
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

    public function aapcRegion()
    {
        return AapcRegion::orderBy('id','asc')->get();
    }

    public function aapcCrops()
    {
        return AapcCrop::orderBy('id','asc')->get();
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
            'region_id' => 'required',
            'city' => 'required',
            'barangay' => 'required',
            'date_conducted' => 'required',
            'selected_crops' => 'required',
            'farmer_first_name' => 'required',
            'farmer_last_name' => 'required',
            'farmer_contact_number' => 'required',
            'store_address' => 'required',
            'store_zip_code' => 'required',
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
            'region_id.required' => "The region field is required",
            'barangay.required' => "The venue field is required",
            'selected_crops.required' => "The crop field is required",
            'store_address.required' => "The address field is required",
            'store_zip_code.required' => "The zip code field is required",
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

        \Log::info($request->all());

            DB::beginTransaction();

            $farmerMeeting = new AapcFarmerMeeting;
            $farmerMeeting->user_id = Auth::user()->id;
            $farmerMeeting->region_id = $request->region_id;
            $farmerMeeting->city = $request->city;
            $farmerMeeting->venue = $request->barangay;
            $farmerMeeting->remarks = $request->remarks;
            $farmerMeeting->date_conducted = $request->date_conducted;
            $farmerMeeting->store_id = 0;
            $farmerMeeting->farmer_id = 0;
            $farmerMeeting->vegestable_id = 0;
            $farmerMeeting->save();

            // saving selected crop;
            if($request->input('selected_crops')) {

                $selected_crops = $request->input('selected_crops');

                foreach($selected_crops as $item) {
                    if($item === 4) {
                        $farmerMeeting->farmerCrops()->attach($item,
                            ['others' => $request->lowland_others
                        ]);
                    }
                    if($item === 5) {
                        $farmerMeeting->farmerCrops()->attach($item,
                            ['others' => $request->highland_others
                        ]);
                    }
                    if($item <= 3) {
                        $farmerMeeting->farmerCrops()->attach($item);
                    }
                }

            }

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
                $new_farmer->city = $request->farmer_city;
                $new_farmer->region_id = $request->farmer_region_id;
                $new_farmer->zip_code = $request->farmer_zip_code;
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
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    )
                ]);
            }

            // store suking tindahan
            $aapcStore = new AapcStore;
            $aapcStore->name = $request->farmer_first_name.' '.$request->last_name;
            $aapcStore->address = $request->store_address;
            $aapcStore->city = $request->store_address.', '.$request->store_state;
            $aapcStore->zip_code = $request->store_zip_code;
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
