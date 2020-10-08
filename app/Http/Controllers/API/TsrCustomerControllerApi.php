<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TechnicalSalesRepresentative;
use App\TsrSapCustomer;
use App\TsrValidCustomer;
use Auth;
use DB;
use App\User;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TsrCustomerControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tsrCustomers(Request $request)
    {
        $data = $request->all();
        $tsr = User::where('id',$data['user_id'])->get(['id','name','tsr_customer_code_pfmc','tsr_customer_code_lfug'])->first();

        $tsr_customer_arr = [];

        if($tsr){
            $k = 0;   

            //PFMC Server
            if($tsr['tsr_customer_code_pfmc']){
                $tsr_customer_code_pfmc = explode(',',$tsr['tsr_customer_code_pfmc']);
                for($x=0;$x < count($tsr_customer_code_pfmc) ;$x++){  
                    $get_customer_pfmc = $this->getCustomers($tsr_customer_code_pfmc[$x]);
                    if($get_customer_pfmc){
                        foreach($get_customer_pfmc as $customer){
                            $tsr_customer_arr[$k]['user_id'] = $tsr['id'];
                            $tsr_customer_arr[$k]['customer'] = $customer;
                            // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_pfmc[$x];
                            // $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
                            // $tsr_customer_arr[$k]['sales_organization'] = $customer['sales_organization'];
                            // $tsr_customer_arr[$k]['common_division'] = $customer['common_division'];
                            // $tsr_customer_arr[$k]['division'] = $customer['division'];
                            // $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
                            // $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
                            // $city = $customer['customer'] ? $customer['customer']['city'] : "";
                            // $tsr_customer_arr[$k]['address'] = $street . $city;
                            // // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
                            // $tsr_customer_arr[$k]['customer_validity'] =$customer['customer_validity'];
                            $k++;
                        }
                    }
                }                
            }

            //LFUG Server
            if($tsr['tsr_customer_code_lfug']){
                $tsr_customer_code_lfug = explode(',',$tsr['tsr_customer_code_lfug']);
                for($x=0;$x < count($tsr_customer_code_lfug) ;$x++){  
                    $get_customer_lfug = $this->getCustomers($tsr_customer_code_lfug[$x]);
                    if($get_customer_lfug){
                        foreach($get_customer_lfug as $customer){
                            $tsr_customer_arr[$k]['user_id'] = $tsr['id'];
                            $tsr_customer_arr[$k]['customer'] = $customer;
                            // // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_lfug[$x];
                            // $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
                            // $tsr_customer_arr[$k]['sales_organization'] = $customer['sales_organization'];
                            // $tsr_customer_arr[$k]['common_division'] = $customer['common_division'];
                            // $tsr_customer_arr[$k]['division'] = $customer['division'];
                            // $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
                            // $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
                            // $city = $customer['customer'] ? $customer['customer']['city'] : "";
                            // $tsr_customer_arr[$k]['address'] = $street . $city;
                            // // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
                            // $tsr_customer_arr[$k]['customer_validity'] =$customer['customer_validity'];
                            $k++;
                        }
                    }
                }                
            }
           
        }

        return $tsr_customer_arr;
    }

    public function getCustomers($tsr_customer_code){

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
                                    customer_codes.`name` NOT LIKE '%XX_%' AND
                                    tsr_sap_customers.customer_code <> tsr_sap_customers.tsr_customer_code AND
                                    tsr_sap_customers.tsr_customer_code = '$tsr_customer_code' AND
                                    tsr_sap_customers.customer_code != '$tsr_customer_code'
                                "));
    }
}
