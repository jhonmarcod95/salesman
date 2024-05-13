<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesmanInternalOrder;
class SalesmanInternalOrderController extends Controller
{
    public function fetchSalesManInternalOrders($sap_server){
        return SalesmanInternalOrder::whereHas('user')->where('sap_server', $sap_server)->get(['internal_order', 'sap_server'])->unique();;
    }
}
