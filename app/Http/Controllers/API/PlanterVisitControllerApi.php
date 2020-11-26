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
                    ->get()
                    ->unique('planter_code');

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
            'assistance_needed' => 'required',
            'planter_area_type_id' => 'required',
        ]);

        $planter = Planter::updateOrCreate(
            [
                'planter_code' => $request->planter_code
            ],
            [
                'user_id' => Auth::user()->id,
                'planter_name' => $request->planter_name,
                'contact_number' => $request->contact_number,
                'planter_address' => $request->planter_address,
                'hacienda_loc' => $request->hacienda_loc,
                'total_area' => 0,
                'area' => $request->area == '' ? 0 : $request->area,
                'date_planted' => $request->date_planted == '' ? null : $request->date_planted,
                'date_estimate_harvest' => $request->date_estimate_harvest == '' ? null : $request->date_estimate_harvest,
                'crop_tech_remarks' => $request->crop_tech_remarks == '' ? "N/A" :  $request->crop_tech_remarks,
                'area_converted' => $request->area_converted == '' ? "N/A" : $request->area_converted,
                'variety' => $request->variety == '' ? "N/A" : $request->variety,
                'remarks' => $request->remarks,
                'assistance_needed' => json_encode($request->input('assistance_needed')),
                'planter_area_type_id' => $request->planter_area_type_id == '' ? 0 :$request->planter_area_type_id,
                'planter_soil_type_id' => $request->planter_soil_type_id == '' ? 0 :$request->planter_soil_type_id,
                'planter_soil_condition_id' => $request->planter_soil_condition_id == '' ? 0 :$request->planter_soil_condition_id,
                'planter_crop_type_id' => $request->planter_crop_type_id == '' ? 0 :$request->planter_crop_type_id,
                'isSync' => 1,
            ]);

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
