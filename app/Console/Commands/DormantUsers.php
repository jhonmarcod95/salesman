<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\Attendance;

class DormantUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dormant-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dormant user if dormant days is equal to 90';

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
        $users = User::withTrashed()->with(['roles'=> function($query){
            $query->where('name','Tsr');
        }])
        ->orderBy('id','desc')->get();

        foreach($users as $user){
            $tsr_attendances = Attendance::where('user_id',$user->id)->orderBy('sign_out','desc')->get()->first();
            $dormant_days = 0;
            $last_login_at = $user->last_login_at;
            $created_at = Carbon::parse($user->created_at)->toDateTimeString();
            $last_sign_out_at = !empty($tsr_attendances) ? $tsr_attendances->sign_out : null;
            $deleted_at = null;
            $remarks = null;
            $subtrahed = $created_at;

            if(empty($last_login_at)){
                $subtrahed = !empty($last_sign_out_at) ? $last_sign_out_at : $subtrahed;
            }
            else{
                $subtrahed = !empty($last_sign_out_at) ? $last_login_at > $last_sign_out_at ? $last_login_at : $last_sign_out_at : $last_login_at;
            }

            $dormant_days = now()->diffInDays(Carbon::parse($subtrahed));
            
            if($dormant_days > 90){
                $remarks = 'Dormant User';
                $deleted_at = Carbon::now()->toDateTimeString();
            }
            
            $user->dormant_days = $dormant_days;
            $user->remarks = $remarks;
            $user->deleted_at = $deleted_at;
            $user->save();
        }
    }
}
