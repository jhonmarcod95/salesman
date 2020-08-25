<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerVisitedExportMail;

use App\User;


class EmailCustomerVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:customer_visit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Customer Visit';

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
        $email_customer_visit = $this->email_customer_visit();
        $this->info( $email_customer_visit);
    }

    public function email_customer_visit(){

        //PFMC-FLOUR
        $company_id = '1';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }

        //PFMC-FEEDS
        $company_id = '2';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }

        //AAPC
        $company_id = '3';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }
        
        //LFUGC
        $company_id = '4';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }

        //PLILI
        $company_id = '5';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }

        //MTPCI
        $company_id = '6';
        if($company_id){
            $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }

        //LFMI
        $company_id = '7';
        if($company_id){
           $users = User::with('roles')->where('company_id',$company_id)->whereHas('roles', function($q){
                $q->whereIn('slug',['vp','evp']);
            })->get();
            if($users){
                $to_arr = [];
                $cc_arr = [];

                foreach($users as $user){
                    if($user->roles){
                        if($user['roles'][0]['slug'] == 'vp'){
                            array_push($cc_arr,$user['email']);
                        }elseif($user['roles'][0]['slug'] == 'evp'){
                            array_push($to_arr,$user['email']);
                        }
                    }               
                }
                Mail::to('arjay.lumagdong@lafilgroup.com')->send(new CustomerVisitedExportMail($company_id));
            }
        }
        
        return 'sent';
    }
}
