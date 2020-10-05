<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\{
    Http\Controllers\APIController,
    ExpenseSapIoBudget,
    TechnicalSalesRepresentative,
    SalesmanInternalOrder,
};

class GetSapIoBudget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sap_io_budget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get SAP IO Budget';

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
        $pfmc_io_budgets = $this->getPFMCServers();
        $this->info('Count : ' . $pfmc_io_budgets);

        // $lfug_io_budgets = $this->getLFUGServers();
        // $this->info('Count : ' . $lfug_io_budgets);
    }

    public function getPFMCServers(){

        $sapConnection = [
            'ashost' => '172.17.1.33',
            'sysnr' => '00',
            'client' => '400',
            'user' => 'payproject',
            'passwd' => 'welcome69'
        ];

        $month = date('m');
        // $month = '07';
        $year = date('Y');
        
        //PFMC-FLOUR / PFMC
        $company = '2';
        $sap_server_pfmc = 'PFMC';
        $all_tsr = TechnicalSalesRepresentative::with('company')->where('company_id', $company)->get();
        
        $count = 0;
        foreach($all_tsr as $k => $tsr){
            $io_pfmc = SalesmanInternalOrder::where('user_id', $tsr['user_id'])->where('sap_server',$sap_server_pfmc)->get();
            $ioBalances = [];
            if($io_pfmc){
                foreach($io_pfmc as $k => $tsr_io){
                
                    $io = $tsr_io['internal_order'];
                    try{
                        $budget = APIController::executeSapFunction($sapConnection, 'ZFI_BUDGET_CHK_INTEG', [
                            'P_AUFNR' => $io,
                            'P_BUDAT' => $year . $month . '01',    
                        ],null);
                    }catch (RequestException $e){
                        $budget = [];
                    }
                   
                    if($budget){
                        $expense_tsr_pfmc = [];
                        $expense_tsr_pfmc['user_id'] = $tsr['user_id'];
                        $expense_tsr_pfmc['company_id'] = $company;
                        $expense_tsr_pfmc['io'] = $io;
                        $expense_tsr_pfmc['io_date'] = $year . $month . '01';
                        $expense_tsr_pfmc['budget_balance'] = $budget['GV_OUTPUT'];
                        $expense_tsr_pfmc['planned_budget'] = $budget['GV_T_PLAN_AMT'];

                        $check_expense_sap_budget = ExpenseSapIoBudget::where('io',$io)->where('io_date',$year . $month . '01')->first();

                        if($check_expense_sap_budget){
                            $check_expense_sap_budget->update($expense_tsr_pfmc);
                            // $count++;
                        }else{
                            ExpenseSapIoBudget::create($expense_tsr_pfmc);
                            $count++;
                        }   
                    }

                }
            }
        }
        return  $count;
    }


    public function getLFUGServers(){

        $sapConnection = [
            'ashost' => '172.17.1.37',
            'sysnr' => '00',
            'client' => '100',
            'user' => 'payproject',
            'passwd' => 'welcome69'
        ];

        $month = date('m');
        // $month = '07';
        $year = date('Y');

        
        //PFMC-FLOUR / LFUG
        $company = '2';
        $sap_server_lfug = 'LFUG';
        $all_tsr = TechnicalSalesRepresentative::with('company')->where('company_id', $company)->get();
        
        $count = 0;
        foreach($all_tsr as $k => $tsr){
            $io_lfug = SalesmanInternalOrder::where('user_id', $tsr['user_id'])->where('sap_server',$sap_server_lfug)->get();
            $ioBalances = [];
            if($io_lfug){
                foreach($io_lfug as $k => $tsr_io){
                
                    $io = $tsr_io['internal_order'];
                    try{
                        $budget = APIController::executeSapFunction($sapConnection, 'ZFI_BUDGET_CHK_INTEG', [
                            'P_AUFNR' => $io,
                            'P_BUDAT' => $year . $month . '01',    
                        ],null);
                    }catch (RequestException $e){
                        $budget = [];
                    }
                   
                    if($budget){
                        $expense_tsr_lfug = [];
                        $expense_tsr_lfug['user_id'] = $tsr['user_id'];
                        $expense_tsr_lfug['company_id'] = $company;
                        $expense_tsr_lfug['io'] = $io;
                        $expense_tsr_lfug['io_date'] = $year . $month . '01';
                        $expense_tsr_lfug['budget_balance'] = $budget['GV_OUTPUT'];
                        $expense_tsr_lfug['planned_budget'] = $budget['GV_T_PLAN_AMT'];

                        $check_expense_sap_budget = ExpenseSapIoBudget::where('io',$io)->where('io_date',$year . $month . '01')->first();

                        if($check_expense_sap_budget){
                            $check_expense_sap_budget->update($expense_tsr_lfug);
                            // $count++;
                        }else{
                            ExpenseSapIoBudget::create($expense_tsr_lfug);
                            $count++;
                        }   
                    }

                }
            }
        }
        return  $count;
    }

}
