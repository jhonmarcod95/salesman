<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Division;
use App\Company;
use App\User;
use GuzzleHttp\Client;

class SapUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sap_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $caf_users_api = 'http://10.97.70.38/api/users';

        $client = new Client();
        $response = json_decode($client->get($caf_users_api)->getBody(), true);

        if($response){
            foreach($response as $result){
                
                $validate_user_email = User::where('email',$result['email'])->first();
                if(!empty($validate_user_email)){
                    $user_companies = [];
                    $user_divisions = [];

                    foreach($result['companies'] as $companies){
                        $company_id = Company::where('code',$companies['code'])->pluck('id')->first();
                        if($company_id) array_push($user_companies,$company_id);
                    }

                    foreach($result['divisions'] as $divisions){
                        $division_id = Division::where([['code',$divisions['code']],
                        ['sap_server',$divisions['sap_server']],
                        ['company_code',$divisions['company']['code']]])
                        ->pluck('id')->first();

                        if($division_id) array_push($user_divisions,$division_id);
                    }
                   $validate_user_email->divisions()->sync($user_divisions);
                   $validate_user_email->companies()->sync($user_companies);
                }
                
            }
        }

    }
}
