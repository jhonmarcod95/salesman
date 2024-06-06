<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesmanInternalOrder;
class SalesmanInternalOrderController extends Controller
{
    public function fetchSalesManInternalOrders($sap_server){
       return SalesmanInternalOrder::with('user:id,name,email')->where('sap_server', $sap_server)->get()->unique('internal_order')->makeHidden('charge_type')->values();
    }
}
