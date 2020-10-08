<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Http\Controllers\Controller;
use App\TechnicalSalesRepresentative;
use App\TsrSapCustomer;
use Auth;
use App\User;
use App\TsrValidCustomer;
use DB;

class TsrCustomerControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tsrCustomers(Request $request)
    {
        ini_set('max_execution_time', 300);

        $tsr = User::whereNotNull('tsr_customer_code_pfmc')
        ->orWhereNotNull('tsr_customer_code_lfug')
        // ->take(1)
        ->get(['id','name','tsr_customer_code_pfmc','tsr_customer_code_lfug']);

        $tsr_customer_arr = [];

        foreach($tsr as $item) {

            // for PFMC Server
            if ($item->tsr_customer_code_pfmc != '' || $item->tsr_customer_code_pfmc != null) {
                // convert to array
                $tsr_customer_code_pfmc = explode(',', $item->tsr_customer_code_pfmc);
                for ($x = 0; $x < count($tsr_customer_code_pfmc); $x++) {
                    $get_customer_pfmc = $this->getCustomers($tsr_customer_code_pfmc[$x]);
                    if ($get_customer_pfmc) {
                        foreach ($get_customer_pfmc as $customer) {
                            $data = array(
                                'user_id' => $item->id,
                                'salesman_name' => $item->name,
                                'code' => $customer->customer_code,
                                'name' => $customer->name,
                                'tsr_customer_code' =>  $customer->tsr_customer_code,
                                'address' => $customer ? $customer->city : "",
                                'sales_organization' => $customer->sales_organization,
                                'common_division' => $customer->common_division,
                                'division' => $customer->division,
                                'customer_order_block' => $customer->customer_order_block,
                                'deletion_flag' => $customer->deletion_flag,
                                'partner_function' => $customer->partner_function,
                                "server" => "PFMC"
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }

            // for LFUG server
            if ($item->tsr_customer_code_lfug != '' || $item->tsr_customer_code_lfug != null) {
                // convert to array
                $tsr_customer_code_lfug = explode(',', $item->tsr_customer_code_lfug);
                for ($x = 0; $x < count($tsr_customer_code_lfug); $x++) {
                    $get_customer_pfmc = $this->getCustomers($tsr_customer_code_lfug[$x]);
                    if ($get_customer_pfmc) {
                        foreach ($get_customer_pfmc as $customer) {
                            $data = array(
                                'user_id' => $item->id,
                                'salesman_name' => $item->name,
                                'code' => $customer->customer_code,
                                'name' => $customer->name,
                                'tsr_customer_code' =>  $customer->tsr_customer_code,
                                'address' => $customer ? $customer->city : "",
                                'sales_organization' => $customer->sales_organization,
                                'common_division' => $customer->common_division,
                                'division' => $customer->division,
                                'customer_order_block' => $customer->customer_order_block,
                                'deletion_flag' => $customer->deletion_flag,
                                'partner_function' => $customer->partner_function,
                                "server" => "LFUG"
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }

        }

        return collect($tsr_customer_arr)
               ->groupBy('user_id')
               ->map(function ($item, $key) {
                   return collect($item)->unique('code')->values();
               })
               ->values();
    }

    // public function getCustomers($tsr_customer_code){
    //     return TsrSapCustomer::with('customer', 'customer_validity')
    //         ->where('tsr_customer_code', $tsr_customer_code)
    //         ->where('customer_code', '!=', $tsr_customer_code)
    //         ->whereHas('customer', function ($q) {
    //             $q->where('name', 'not like', '%X:%');
    //             $q->where('name', c);
    //         })
    //         ->whereHas('customer_validity', function ($q) {
    //             $q->whereNotNull('sales_organization');
    //             $q->whereNotNull('deletion_flag');
    //         })
    //         ->get();
    // }


    public function getCustomers($tsr_customer_code)
    {
        return $customer_lists = DB::select(DB::raw("
                SELECT
                tsr_sap_customers.id,
                tsr_sap_customers.customer_code,
                tsr_sap_customers.tsr_customer_code,
                tsr_sap_customers.sales_organization,
                tsr_sap_customers.common_division,
                tsr_sap_customers.division,
                tsr_sap_customers.partner_function,
                tsr_sap_customers.`server`,
                tsr_sap_customers.created_at,
                tsr_sap_customers.updated_at,
                tsr_valid_customers.deletion_flag,
                tsr_valid_customers.customer_order_block,
                customer_codes.`name`,
                customer_codes.street,
                customer_codes.city
                FROM
                tsr_sap_customers
                INNER JOIN tsr_valid_customers ON tsr_sap_customers.customer_code = tsr_valid_customers.customer_code AND tsr_sap_customers.sales_organization = tsr_valid_customers.sales_organization AND tsr_sap_customers.common_division = tsr_valid_customers.common_division AND tsr_sap_customers.division = tsr_valid_customers.division
                INNER JOIN customer_codes ON tsr_sap_customers.customer_code = customer_codes.customer_code
                WHERE
                tsr_valid_customers.deletion_flag = '' AND
                tsr_valid_customers.customer_order_block = '' AND
                customer_codes.`name` NOT LIKE '%X:%' AND
                customer_codes.`name` NOT LIKE '%XXX%' AND
                customer_codes.`name` NOT LIKE '%ONETIME%' AND
                customer_codes.`name` NOT LIKE '%XX_%' AND
                customer_codes.`name` NOT LIKE '%ONE TIME%' AND
                customer_codes.account_group != 'EMPL' AND
                tsr_sap_customers.tsr_customer_code = '$tsr_customer_code' AND
                tsr_sap_customers.customer_code != '$tsr_customer_code'
                "));
    }

}
