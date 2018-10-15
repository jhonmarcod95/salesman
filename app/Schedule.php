<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Schedule extends Model
{
<<<<<<< HEAD
    public static function createScheduleCode($type){
        $code = collect(DB::select('SELECT f_schedule_id(\'' . $type . '\') AS code'))->first()->code;
        return $type . '-' . $code;
    }
=======
    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

>>>>>>> DailySchedule
}
