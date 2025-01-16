<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    //
    public static function readSapTableApi($connection, $table,$sap_server)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', ($sap_server == 'HANA' ? env('SAP_API_S4') : env('SAP_API')) . '/read-table', ['query' => ['connection' => $connection, 'table' => $table]]);
        return collect(json_decode($response->getBody()));
    }

    public static function executeSapFunction($connection, $function, $parameters, $return,$sap_server){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', ($sap_server == 'HANA' ? env('SAP_API_S4') : env('SAP_API')) . '/execute-fm', [
            'form_params' => [
                'connection' => $connection,
                'function' => $function,
                'parameters' => $parameters,
                'return' => $return,
            ]
        ]);
        return collect(json_decode($response->getBody()));
    }
}
