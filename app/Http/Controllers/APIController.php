<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    //
    public static function readSapTableApi($connection, $table)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://10.96.4.39:8012/api/read-table', ['query' => ['connection' => $connection, 'table' => $table]]);
        return collect(json_decode($response->getBody()));
    }

    public static function executeSapFunction($connection, $function, $parameters, $return){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://10.96.4.39:8012/api/execute-fm', [
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
