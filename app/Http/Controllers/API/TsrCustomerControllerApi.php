<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TechnicalSalesRepresentative;
use App\TsrSapCustomer;
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
        // $tsr = User::where('id',$data['user_id'])->get(['id','name','tsr_customer_code_pfmc','tsr_customer_code_lfug'])->first();
        $tsr = User::whereNotNull('tsr_customer_code_pfmc')
        ->orWhereNotNull('tsr_customer_code_lfug')
        ->take(1)
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
                                'code' => $customer['customer_code'],
                                'name' => $customer['customer'] ? $customer['customer']['name'] : "",
                                'address' => $customer['customer'] ? $customer['customer']['city'] : "",
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
                                'customer_code' => $customer['customer_code'],
                                'customer_name' => $customer['customer'] ? $customer['customer']['name'] : "",
                                'address' => $customer['customer'] ? $customer['customer']['city'] : "",
                            );
                            array_push($tsr_customer_arr, $data);
                        }
                    }
                }
            }

        }

        // return $tsr_customer_arr;

        return collect($tsr_customer_arr)
               ->groupBy('user_id')
               ->values();


        // if($tsr){
        //     $k = 0;

        //     //PFMC Server
            // if($tsr['tsr_customer_code_pfmc']){
            //     $tsr_customer_code_pfmc = explode(',',$tsr['tsr_customer_code_pfmc']);
            //     for($x=0;$x < count($tsr_customer_code_pfmc) ;$x++){
            //         $get_customer_pfmc = $this->getCustomers($tsr_customer_code_pfmc[$x]);
            //         if($get_customer_pfmc){
            //             foreach($get_customer_pfmc as $customer){
            //                 $tsr_customer_arr[$k]['user_id'] = $tsr['id'];
            //                 // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_pfmc[$x];
            //                 $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
            //                 $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
            //                 $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
            //                 $city = $customer['customer'] ? $customer['customer']['city'] : "";
            //                 $tsr_customer_arr[$k]['address'] = $street . $city;
            //                 // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
            //                 $k++;
            //             }
            //         }
            //     }
            // }

        //     //LFUG Server
        //     if($tsr['tsr_customer_code_lfug']){
        //         $tsr_customer_code_lfug = explode(',',$tsr['tsr_customer_code_lfug']);
        //         for($x=0;$x < count($tsr_customer_code_lfug) ;$x++){
        //             $get_customer_lfug = $this->getCustomers($tsr_customer_code_lfug[$x]);
        //             if($get_customer_lfug){
        //                 foreach($get_customer_lfug as $customer){
        //                     $tsr_customer_arr[$k]['user_id'] = $tsr['id'];
        //                     // $tsr_customer_arr[$k]['tsr_customer_code'] = $tsr_customer_code_lfug[$x];
        //                     $tsr_customer_arr[$k]['customer_code'] = $customer['customer_code'];
        //                     $tsr_customer_arr[$k]['customer_name'] = $customer['customer'] ? $customer['customer']['name'] : "";
        //                     $street =$customer['customer'] ? $customer['customer']['street'] . " " : "";
        //                     $city = $customer['customer'] ? $customer['customer']['city'] : "";
        //                     $tsr_customer_arr[$k]['address'] = $street . $city;
        //                     // $tsr_customer_arr[$k]['server'] =$customer['customer'] ? $customer['customer']['server'] : "";
        //                     $k++;
        //                 }
        //             }
        //         }
        //     }

        // }

        // return count($tsr_customer_arr);
    }

    public function getCustomers($tsr_customer_code){
       return  TsrSapCustomer::with('customer')
                ->where('tsr_customer_code',$tsr_customer_code)
                ->where('customer_code','!=',$tsr_customer_code)
                ->whereHas('customer',function($q){
                    $q->where('name','not like','%X:%');
                    // $q->orWhere('name','not like','%XXX_%');
                })
                ->get();
    }
}
