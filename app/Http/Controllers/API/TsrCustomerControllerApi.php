<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TechnicalSalesRepresentative;
use App\TsrSapCustomer;
use App\TsrValidCustomer;
use Auth;
use App\User;

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
                            // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_pfmc[$x];
                            $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
                            $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
                            $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
                            $city = $customer['customer'] ? $customer['customer']['city'] : "";
                            $tsr_customer_arr[$k]['address'] = $street . $city;
                            // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
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
                            // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_lfug[$x];
                            $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
                            $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
                            $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
                            $city = $customer['customer'] ? $customer['customer']['city'] : "";
                            $tsr_customer_arr[$k]['address'] = $street . $city;
                            // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
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
        return $customer_lists = TsrSapCustomer::with('customer','customer_validity')
                                                ->where('tsr_customer_code',$tsr_customer_code)
                                                ->where('customer_code','!=',$tsr_customer_code)
                                                ->whereHas('customer',function($q){
                                                    $q->where('name','not like','%X:%');
                                                    $q->where('name','not like','%XXX%');
                                                })
                                                ->whereHas('customer_validity',function($q){
                                                    $q->whereNotNull('sales_organization');
                                                    $q->whereNotNull('deletion_flag');
                                                })
                                                ->get();

        // //Check if Valid Customer Status //Not Deleted //Not Block
        // $customer_arr = [];
        // if($customer_lists){
        //     foreach($customer_lists as $k => $item){
        //         $validate_customer = TsrValidCustomer::where('customer_code',$item['customer_code'])
        //                                                 ->where('sales_organization',$item['sales_organization'])
        //                                                 ->where('common_division',$item['common_division'])
        //                                                 ->first();
        //         if($validate_customer){
        //             if($validate_customer['customer_order_block'] == "" && $validate_customer['deletion_flag'] == ""){
        //                 $customer_arr[$k] = $item;
        //             }
        //         }else{
        //             $customer_arr[$k] = $item;
        //         }
        //     }
        // }

        // return $customer_arr;
    }
}
