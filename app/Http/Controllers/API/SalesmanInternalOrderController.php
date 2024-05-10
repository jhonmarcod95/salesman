<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesmanInternalOrder;
class SalesmanInternalOrderController extends Controller
{
    public function fetchSalesManInternalOrders(){
        return SalesmanInternalOrder::get()->unique('internal_order')->makeHidden('charge_type');
    }
}
