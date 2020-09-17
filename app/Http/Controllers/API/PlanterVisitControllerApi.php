<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PlanterVisitResource;
use App\Http\Resources\CustomerResource;
use App\Planter;
use App\PlanterSoilType;
use App\PlanterSoilCondition;
use App\Customer;
use App\PlanterAreaType;
use App\PlanterCropType;

class PlanterVisitControllerApi extends Controller
{

    public function getPlanterCropTypes()
    {
        $crop_types = PlanterCropType::orderBy('id','desc')->get();
        return $crop_types;
    }

    public function getPlanterAreaTypes()
    {
        $area_types = PlanterAreaType::orderBy('id','desc')->get();
        return $area_types;
    }

    public function getPlanterCustomer()
    {
        $customers = Customer::orderBy('id','desc')
                    ->where('classification', 17)
                    ->get();

        return CustomerResource::collection($customers);

    }

    /**
     * Display planter soil type
     */
    public function soilTypes()
    {
        $soil_types = PlanterSoilType::orderBy('id','desc')->get();
        return $soil_types;
    }

    /**
     * Display planter soil condition
     */
    public function soilConditions()
    {
        $soil_conditions = PlanterSoilCondition::orderBy('id','desc')->get();
        return $soil_conditions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $planters = Planter::orderBy('id','DESC')
                    ->where('user_id',Auth::user()->id)
                    ->get();

        return PlanterVisitResource::collection($planters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'planter_name' => 'required',
            'contact_number' => 'required',
            'planter_address' => 'required',
            'hacienda_loc' => 'required',
            // 'total_area' => 'required',
            // 'n_p' => 'required',
            // 'r1_r2_r3' => 'required',
            // 'empty' => 'required',
            // 'planter_soil_type_id' => 'required',
            // 'planter_soil_condition_id' => 'required',
            // 'tons_cane' => 'required',
            // 'tons_yields' => 'required',
            'assistance_needed' => 'required',
            // 'planter_crop_type_id' => 'required',
            'planter_area_type_id' => 'required',
        ]);

        $planter = new Planter;
        $planter->user_id = Auth::user()->id;
        $planter->planter_name = $request->planter_name;
        $planter->contact_number = $request->contact_number;
        $planter->planter_address = $request->planter_address;
        $planter->hacienda_loc = $request->hacienda_loc;
        $planter->total_area = 0; // temporarily
        // $planter->tons_cane = $request->tons_cane;
        // $planter->tons_yields = $request->tons_yields;
        $planter->area = $request->area == '' ? 0 : $request->area;
        $planter->date_planted = $request->date_planted == '' ? null : $request->date_planted;
        $planter->date_estimate_harvest = $request->date_estimate_harvest == '' ? null : $request->date_estimate_harvest;
        $planter->crop_tech_remarks = $request->crop_tech_remarks == '' ? "N/A" :  $request->crop_tech_remarks;
        $planter->area_converted = $request->area_converted == '' ? "N/A" : $request->area_converted;
        $planter->variety = $request->variety == '' ? "N/A" : $request->variety;
        // $planter->n_p = json_encode($request->input('n_p'));
        // $planter->r1_r2_r3 = json_encode($request->input('r1_r2_r3'));
        // $planter->empty = json_encode($request->input('empty'));
        $planter->remarks = $request->remarks;
        $planter->assistance_needed = json_encode($request->input('assistance_needed'));
        $planter->planterAreaType()->associate($request->input('planter_area_type_id'));

        if($request->planter_soil_type_id) {
            $planter->planterSoilType()->associate($request->input('planter_soil_type_id'));
        } else {
            $planter->planter_soil_type_id = 0;
        }

        if($request->planter_soil_condition_id) {
            $planter->planterSoilConditionType()->associate($request->input('planter_soil_condition_id'));
        } else {
            $planter->planter_soil_condition_id = 0;
        }

        if($request->planter_crop_type_id) {
            $planter->planterCropType()->associate($request->input('planter_crop_type_id'));
        } else {
            $planter->planter_crop_type_id = 0;
        }
        $planter->save();

        return new PlanterVisitResource($planter);

    }

    /**
     * Upload BIR ID photo
     *
     * @param Planter $planter
     * @return response
     */
    public function uploadBirIdPhoto(Request $request, Planter $planter)
    {

        file_put_contents(public_path('storage/planters/') . $request->header('File-Name'), file_get_contents('php://input'));

        $planter->bir_id = 'planters/'. $request->header('File-Name');
        $planter->save();

        return $planter;

    }

    /**
     * Upload Planter photo
     *
     * @param Planter $planter
     * @return response
     */
    public function uploadPlanterPhoto(Request $request, Planter $planter)
    {

        file_put_contents(public_path('storage/planters/') . $request->header('File-Name'), file_get_contents('php://input'));

        $planter->planter_picture = 'planters/'. $request->header('File-Name');
        $planter->save();

        return $planter;

    }

    /**
     * Upload Planter photo
     *
     * @param Planter $planter
     * @return response
     */
    public function uploadParcellaryPhoto(Request $request, Planter $planter)
    {

        file_put_contents(public_path('storage/planters/') . $request->header('File-Name'), file_get_contents('php://input'));

        $planter->parcellary = 'planters/'. $request->header('File-Name');
        $planter->save();

        return $planter;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
