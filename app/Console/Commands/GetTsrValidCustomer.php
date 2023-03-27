<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\TsrValidCustomer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;


class GetTsrValidCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tsr_valid_customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Status of Valid Customers';

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

        $valid_customers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                                ['query' => 
                                    ['connection' => $connection_pfmc,
                                        'table' => [
                                            'table' => ['KNVV' => 'customers_tsr_validity'],
                                            'fields' => [
                                                'KUNNR' => 'customer_code',
                                                'VKORG' => 'sales_organization',
                                                'VTWEG' => 'common_division',
                                                'SPART' => 'division',
                                                'AUFSD' => 'customer_order_block',
                                                'LOEVM' => 'deletion_flag',
                                            ]
                                        ]
                                    ]
                                ],
                                ['timeout' => 60],
                                ['delay' => 10000]
                            );

        $customers_data = json_decode($valid_customers->getBody(), true);

        $x = 0;
        if($customers_data){
            foreach($customers_data as $k => $data){
                $tsr_customer_arr = $data;
                $tsr_customer_arr['server'] = "PFMC";

                $validate = TsrValidCustomer::where('customer_code',$data['customer_code'])
                                ->where('sales_organization',$data['sales_organization'])
                                ->where('common_division',$data['common_division'])
                                ->where('division',$data['division'])
                                ->where('customer_order_block',$data['customer_order_block'])
                                ->where('deletion_flag',$data['deletion_flag'])
                                ->first();

                if(empty($validate)){
                    TsrValidCustomer::create($tsr_customer_arr);
                    $x++;
                }else{
                    $x++;
                    $validate->update($tsr_customer_arr);
                }
            }
        }
        return $x;
    }

    public function getLFUGServers(){


        $client = new Client();

        $connection_lfug = [
            'ashost' => '172.17.2.36',
            'sysnr' => '00',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4'
        ];

        $valid_customers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                                ['query' => 
                                    ['connection' => $connection_lfug,
                                        'table' => [
                                            'table' => ['KNVV' => 'customers_tsr_validity'],
                                            'fields' => [
                                                'KUNNR' => 'customer_code',
                                                'VKORG' => 'sales_organization',
                                                'VTWEG' => 'common_division',
                                                'SPART' => 'division',
                                                'AUFSD' => 'customer_order_block',
                                                'LOEVM' => 'deletion_flag',
                                            ]
                                        ]
                                    ]
                                ],
                                ['timeout' => 60],
                                ['delay' => 10000]
                            );

        $customers_data = json_decode($valid_customers->getBody(), true);

        $x = 0;
        if($customers_data){
            foreach($customers_data as $k => $data){
                $tsr_customer_arr = $data;
                $tsr_customer_arr['server'] = "LFUG";

                $validate = TsrValidCustomer::where('customer_code',$data['customer_code'])
                                ->where('sales_organization',$data['sales_organization'])
                                ->where('common_division',$data['common_division'])
                                ->where('division',$data['division'])
                                ->where('customer_order_block',$data['customer_order_block'])
                                ->where('deletion_flag',$data['deletion_flag'])
                                ->first();

                if(empty($validate)){
                    TsrValidCustomer::create($tsr_customer_arr);
                    $x++;
                }else{
                    $x++;
                    $validate->update($tsr_customer_arr);
                }
            }
        }
        return $x;
    }
}
