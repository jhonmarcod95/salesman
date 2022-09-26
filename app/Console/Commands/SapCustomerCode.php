<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\CustomerCode;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class SapCustomerCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sap_customer_codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get SAP customer codes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $lfug = $this->get_lfug_sap_customer_codes();
        // $pfmc = $this->get_pfmc_sap_customer_codes();
        $hana = $this->get_hana_sap_customer_codes();
        $this->info( $hana . ' HANA');
        // $this->info( $lfug . ' LFUG ' . ' ' . $pfmc . ' PFMC' . ' ' . $hana . ' HANA');
    }

    public function get_lfug_sap_customer_codes(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.2.36',
            'sysnr' => '00',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4'
        ];

        $date = date('Ymd');
        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection,
                                    'table' => [
                                        'table' => ['KNA1' => 'do_headers'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'NAME1' => 'name',
                                            'STRAS' => 'street',
                                            'ORT01' => 'city',
                                            'KTOKD' => 'account_group'
                                        ],
                                        // 'options' => [
                                        //     ['TEXT' => "ERDAT = '$date'"]
                                        // ],
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

        $customers_data = json_decode($customers->getBody(), true);
        
        $x = 0;
        if($customers_data){
            
            foreach($customers_data as $customer_code){

                if($customer_code['customer_code']){
                    $validate_customer_code = CustomerCode::where('customer_code',$customer_code['customer_code'])->where('server','LFUG')->first();

                    if(empty($validate_customer_code)){

                        $data = [
                            'customer_code'=>$customer_code['customer_code'],
                            'server'=>'LFUG',
                            'name'=>$customer_code['name'],
                            'street'=>$customer_code['street'],
                            'city'=>$customer_code['city'],
                            'account_group'=>$customer_code['account_group'],
                        ];
                        //Create DO Number
                        CustomerCode::create($data);
                        $x++;
                    }else{
                        try{
                            $data = [
                                'name'=>$customer_code['name'],
                                'street'=>$customer_code['street'],
                                'city'=>$customer_code['city'],
                                'account_group'=>$customer_code['account_group'],
                            ];
                            $validate_customer_code->update($data);
                            $x++;
                        }catch (Exception $e) {
                            
                        }catch (QueryException $e) {
                            
                        }
                    }
                }
                
            }
        }

        return $x;

    }

    public function get_pfmc_sap_customer_codes(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.1.35',
            'sysnr' => '02',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4',
        ];

        $date = date('Ymd');
        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection,
                                    'table' => [
                                        'table' => ['KNA1' => 'do_headers'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'NAME1' => 'name',
                                            'STRAS' => 'street',
                                            'ORT01' => 'city',
                                            'KTOKD' => 'account_group'
                                        ],
                                        // 'options' => [
                                        //     ['TEXT' => "ERDAT = '$date'"]
                                        // ],
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

        $customers_data = json_decode($customers->getBody(), true);
        
        $x = 0;
        if($customers_data){
            
            foreach($customers_data as $customer_code){

                if($customer_code['customer_code']){
                    $validate_customer_code = CustomerCode::where('customer_code',$customer_code['customer_code'])->where('server','PFMC')->first();

                    if(empty($validate_customer_code)){

                        $data = [
                            'customer_code'=>$customer_code['customer_code'],
                            'name'=>$customer_code['name'],
                            'server'=>'PFMC',
                            'street'=>$customer_code['street'],
                            'city'=>$customer_code['city'],
                            'account_group'=>$customer_code['account_group'],
                        ];
                        //Create DO Number
                        CustomerCode::create($data);
                        $x++;
                    }else{
                        try{
                            $data = [
                                'name'=>$customer_code['name'],
                                'street'=>$customer_code['street'],
                                'city'=>$customer_code['city'],
                                'account_group'=>$customer_code['account_group'],
                            ];
                            $validate_customer_code->update($data);
                            $x++;
                        }catch (Exception $e) {
                            
                        }catch (QueryException $e) {
                            
                        }
                        
                    }
                }
                
            }
        }

        return $x;

    }

    public function get_hana_sap_customer_codes(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.5.24',
            'sysnr' => '01',
            'client' => '888',
            'user' => 'app_user',
            'passwd' => '{iamprogrammer}'
        ];

        $date = date('Ymd');
        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection,
                                    'table' => [
                                        'table' => ['KNA1' => 'do_headers'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'NAME1' => 'name',
                                            'STRAS' => 'street',
                                            'ORT01' => 'city',
                                            'KTOKD' => 'account_group'
                                        ],
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );
        
        $customers_data = json_decode($customers->getBody(), true);

        $x = 0;
        if($customers_data){
            
            foreach($customers_data as $customer_code){

                if($customer_code['customer_code']){
                    $validate_customer_code = CustomerCode::where('customer_code',$customer_code['customer_code'])->where('server','HANA')->first();

                    if(empty($validate_customer_code)){

                        $data = [
                            'customer_code'=>$customer_code['customer_code'],
                            'name'=>$customer_code['name'],
                            'server'=>'HANA',
                            'street'=>$customer_code['street'],
                            'city'=>$customer_code['city'],
                            'account_group'=>$customer_code['account_group'],
                        ];
                        //Create DO Number
                        CustomerCode::create($data);
                        $x++;
                    }else{
                        try{
                            $data = [
                                'name'=>$customer_code['name'],
                                'street'=>$customer_code['street'],
                                'city'=>$customer_code['city'],
                                'account_group'=>$customer_code['account_group'],
                            ];
                            $validate_customer_code->update($data);
                            $x++;
                        }catch (Exception $e) {
                            
                        }catch (QueryException $e) {
                            
                        }
                        
                    }
                }
                
            }
        }

        return $x;

    }

}
