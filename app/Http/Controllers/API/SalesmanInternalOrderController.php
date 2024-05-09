<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesmanInternalOrder;
class SalesmanInternalOrderController extends Controller
{
    public function fetchSalesManInternalOrders(){
        return SalesmanInternalOrder::all();
    }
}
