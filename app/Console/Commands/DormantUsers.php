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
        $users = User::with(['roles'=> function($query){
            $query->where('name','Tsr');
        }])
        ->orderBy('id','desc')->get();
        
        foreach($users as $user){
            $validate_attendance = Attendance::where('user_id',$user->id)->orderBy('sign_out','desc')->get()->first();
            $dormant_days = 0;

            if($user->roles->count() > 0 && $validate_attendance){
                $dormant_days = now()->diffInDays(Carbon::parse($validate_attendance->sign_out));
            }
            else{
               if ($user->last_login_at != null)
                $dormant_days = now()->diffInDays(Carbon::parse($user->last_login_at));
            }

           $user->dormant_days = $dormant_days;

            if($dormant_days == 90){
                $user->remarks = 'Dormant User';
                $user->deleted_at = Carbon::now()->toDateTimeString();
            }
            $user->save();
        }
    }
}
