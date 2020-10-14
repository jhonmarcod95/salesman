<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\TsrSapCustomer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class GetSapTsrCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sap_tsr_customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get customer with tsr';

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
        $pfmc_server = $this->getPFMCServers();
        $lfug_server = $this->getLFUGServers();
        // $this->info('Save PFMC : ' . $pfmc_server);
        $this->info('Save LFUG : ' . $lfug_server . ' Save PFMC : ' . $pfmc_server);
    }

    public function getPFMCServers(){
        $client = new Client();

        $connection_pfmc = [
            'ashost' => '172.17.1.35',
            'sysnr' => '02',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4',
        ];

        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection_pfmc,
                                    'table' => [
                                        'table' => ['KNVP' => 'customers_tsr'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'KUNN2' => 'tsr_customer_code',
                                            'VKORG' => 'sales_organization',
                                            'VTWEG' => 'common_division',
                                            'SPART' => 'division',
                                            'PARVW' => 'partner_function',
                                        ],
                                        'options' => [
                                            ['TEXT' => "PARVW ='Z1'"]
                                        ]
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

        $customers_data = json_decode($customers->getBody(), true);

        $x = 0;
        if($customers_data){
            foreach($customers_data as $k => $data){
                $tsr_customer_arr = [];
                $tsr_customer_arr['server'] = 'PFMC';
            
                $validate = TsrSapCustomer::where('customer_code',$data['customer_code'])
                                ->where('tsr_customer_code',$data['tsr_customer_code'])
                                ->where('sales_organization',$data['sales_organization'])
                                ->where('common_division',$data['common_division'])
                                ->where('division',$data['division'])
                                ->where('partner_function',$data['partner_function'])
                                ->first();
                                
                if(empty($validate)){
                    $tsr_customer_arr['customer_code'] = $data['customer_code'];
                    $tsr_customer_arr['tsr_customer_code'] = $data['tsr_customer_code'];
                    $tsr_customer_arr['sales_organization'] = $data['sales_organization'];
                    $tsr_customer_arr['common_division'] = $data['common_division'];
                    $tsr_customer_arr['division'] = $data['division'];
                    $tsr_customer_arr['partner_function'] = $data['partner_function'];
                    TsrSapCustomer::create($tsr_customer_arr);
                    $x++;
                }
            }
            return $x;
        }
    }

    public function getLFUGServers(){
        $client = new Client();

        $connection_pfmc = [
            'ashost' => '172.17.2.36',
            'sysnr' => '00',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4'
        ];

        $customers = $client->request('GET', 'http://10.96.4.39:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection_pfmc,
                                    'table' => [
                                        'table' => ['KNVP' => 'customers_tsr'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'KUNN2' => 'tsr_customer_code',
                                            'VKORG' => 'sales_organization',
                                            'VTWEG' => 'common_division',
                                            'SPART' => 'division',
                                            'PARVW' => 'partner_function',
                                        ],
                                        'options' => [
                                            ['TEXT' => "PARVW ='ZS'"]
                                        ]
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

        $customers_data = json_decode($customers->getBody(), true);

        $x = 0;
        if($customers_data){
            foreach($customers_data as $k => $data){
                $tsr_customer_arr = [];
                $tsr_customer_arr['server'] = 'LFUG';
            
                $validate = TsrSapCustomer::where('customer_code',$data['customer_code'])
                                ->where('tsr_customer_code',$data['tsr_customer_code'])
                                ->where('sales_organization',$data['sales_organization'])
                                ->where('common_division',$data['common_division'])
                                ->where('division',$data['division'])
                                ->where('partner_function',$data['partner_function'])
                                ->first();
                                
                if(empty($validate)){
                    //LFUG if not same customer
                    if($data['customer_code'] != $data['tsr_customer_code']){
                        $tsr_customer_arr['customer_code'] = $data['customer_code'];
                        $tsr_customer_arr['tsr_customer_code'] = $data['tsr_customer_code'];
                        $tsr_customer_arr['sales_organization'] = $data['sales_organization'];
                        $tsr_customer_arr['common_division'] = $data['common_division'];
                        $tsr_customer_arr['division'] = $data['division'];
                        $tsr_customer_arr['partner_function'] = $data['partner_function'];
                        TsrSapCustomer::create($tsr_customer_arr);
                        $x++;
                    }                    
                }
            }
            return $x;
        }
    }
}
