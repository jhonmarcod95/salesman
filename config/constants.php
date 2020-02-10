<?php 
    return [
        'sap_api' => [
            'connection_lfug' => [
                'ashost' => '172.17.2.36',
                'sysnr' => '00',
                'client' => '888',
                'user' => 'rfidproject',
                'passwd' => 'P@ssw0rd4'
            ],
            'connection_pfmc' => [
                'ashost' => '172.17.1.35',
                'sysnr' => '02',
                'client' => '888',
                'user' => 'rfidproject',
                'passwd' => 'P@ssw0rd4',
            ],
            // 'table_customers' => [
            //     'table' => ['KNA1' => 'customers'],
            //     'fields' => [
            //         'KUNNR' => 'customer_code',
            //         'NAME1' => 'name',
            //         'CASSD' => 'closed',
            //         'ERDAT' => 'created_date',
            //         'AEDAT' => 'modified_date',
            //     ]
            // ],
            // 'table_customer_first_date' => [
            //     'table' => ['VBAK' => 'customer_first_date'],
            //     'fields' => [
            //         'KUNNR' => 'customer_code',
            //         'VBELN' => 'do_number',
            //         'ERDAT' => 'purchase_date',
            //         'VBTYP' => 'vb_type',
            //     ],
            //     'options' => [
            //         ['TEXT' => "KUNNR = '1103002620' AND VBTYP = 'C'"]
            //     ],
            //     // 'count' => '1'
            // ],
            // 'table_customer_last_date' => [
            //     'table' => ['VBUK' => 'customer_first_date'],
            //     'fields' => [
            //         'GBSTK' => 'status',
            //     ],
            //     'options' => [
            //         ['TEXT' => "VBELN = '2110603547' AND GBSTK = 'C'"]
            //     ],
            //     // 'count' => '1'
            // ],
        ]
    ];
