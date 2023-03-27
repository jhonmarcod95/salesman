<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\CustomerOrder;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class GetCustomerOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:customer_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Customer Order';

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
        $lfug_customers = $this->getLFUGCustomers();
        $pfmc_customers = $this->getPFMCCustomers();
        $this->info( $lfug_customers . ' LFUG ' . $pfmc_customers . ' PFMC');
    }

    public function getLFUGCustomers(){

        $client = new Client();

        $connection = [
            'ashost' => '172.17.2.36',
            'sysnr' => '00',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4'
        ];

        $date_yesterday = date('Ymd', strtotime('-1 days'));

        $customers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                                ['query' => 
                                    ['connection' => $connection,
                                        'table' => [
                                            'table' => ['VBAK' => 'sales_document_headers'],
                                            'fields' => [
                                                'KUNNR' => 'customer_code',
                                                'VBELN' => 'do_number',
                                                'ERDAT' => 'purchase_date',
                                            ],
                                            'options' => [
                                                ['TEXT' => "ERDAT = '$date_yesterday' AND VBTYP = 'C'"]
                                            ]
                                        ]
                                    ]
                                ],
                                ['timeout' => 60],
                                ['delay' => 10000]
                            );

        $customers_data = json_decode($customers->getBody(), true); 

        if($customers_data){
            foreach($customers_data as $customer){
                $purchase_date = $customer['purchase_date'] ? date('Y-m-d',strtotime($customer['purchase_date'])) : "";
                $validate_customer_order = CustomerOrder::where('customer_code',$customer['customer_code'])->where('do_number',$customer['do_number'])->where('purchase_date', $purchase_date)->first();
                if(empty($validate_customer_order)){
                    $data = [
                        'customer_code'=>$customer['customer_code'],
                        'do_number'=>$customer['do_number'],
                        'purchase_date'=> $purchase_date,
                        'server'=> 'LFUG'
                    ];
                    
                    CustomerOrder::create($data);
                }
            }

            return count($customers_data);
        }

    }

    public function getPFMCCustomers(){

        $client = new Client();

        $date_yesterday = date('Ymd', strtotime('-1 days'));

        $connection = [
            'ashost' => '172.17.1.35',
            'sysnr' => '02',
            'client' => '888',
            'user' => 'rfidproject',
            'passwd' => 'P@ssw0rd4',
        ];

        $customers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                                ['query' => 
                                    ['connection' => $connection,
                                        'table' => [
                                            'table' => ['VBAK' => 'sales_document_headers'],
                                            'fields' => [
                                                'KUNNR' => 'customer_code',
                                                'VBELN' => 'do_number',
                                                'ERDAT' => 'purchase_date',
                                            ],
                                            'options' => [
                                                ['TEXT' => "ERDAT = '$date_yesterday' AND VBTYP = 'C'"]
                                            ]
                                        ]
                                    ]
                                ],
                                ['timeout' => 60],
                                ['delay' => 10000]
                            );

        $customers_data = json_decode($customers->getBody(), true); 

        if($customers_data){
            foreach($customers_data as $customer){
                $purchase_date = $customer['purchase_date'] ? date('Y-m-d',strtotime($customer['purchase_date'])) : "";
                $validate_customer_order = CustomerOrder::where('customer_code',$customer['customer_code'])->where('do_number',$customer['do_number'])->where('purchase_date', $purchase_date)->first();
                if(empty($validate_customer_order)){
                    $data = [
                        'customer_code'=>$customer['customer_code'],
                        'do_number'=>$customer['do_number'],
                        'purchase_date'=> $purchase_date,
                        'server'=> 'PFMC'
                    ];
                    CustomerOrder::create($data);
                }
            }

            return count($customers_data);
        }

    }
}
