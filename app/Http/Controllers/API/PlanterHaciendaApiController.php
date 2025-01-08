<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\Http\Resources\HaciendaResource;
use App\Http\Controllers\Controller;
use App\PlanterHacienda;
use App\SapServer;
use App\SapUser;

class PlanterHaciendaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $haciendas = PlanterHacienda::orderBy('id','asc')->get();
        return HaciendaResource::collection($haciendas);
    }

    /**
     * Debounce search for planters
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $search = $request->search;

        $haciendas = PlanterHacienda::take(10)->get();

        if($search != "") {
            $haciendas = PlanterHacienda::where('name', 'LIKE', "%$search%")->get();
        }

        // return $haciendas;
        return HaciendaResource::collection($haciendas);
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
            'hacienda_loc' => 'required',
            'planter_id' => 'required'
        ]);

        $hacienda = new PlanterHacienda();
        $hacienda->hacienda_loc = $request->hacienda_loc;
        $hacienda->planter()->associate($request->input('planter_id'));
        $hacienda->save();

        return $hacienda;

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

    /**
     * Fetch SAP Hacienda array
     */
    public function fetchHacienda()
    {
        // $sap_server = SapServer::where('sap_server', 'PFMC')->first();
        // $sap_user = SapUser::where('user_id', 175)->where('sap_server', 'PFMC')->first();

        // $connection = [
        //     'ashost' => $sap_server->app_server,
        //     'sysnr' => $sap_server->system_number,
        //     'client' => $sap_server->client,
        //     'user' => $sap_user->sap_id,
        //     'passwd' => $sap_user->sap_password,
        // ];

        $connection = [
            'ashost' => "172.17.1.34",
            'sysnr' => "00",
            'client' => "778",
            'user' => "payproject",
            'passwd' => "welcome69+",
        ];

        $planters = APIController::executeSapFunction($connection, 'ZFM_CMS', [], null,'PFMC');

        return $planters['CMS_OUT'];
    }
}
